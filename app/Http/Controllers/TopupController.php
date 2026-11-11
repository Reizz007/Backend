<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\TopupGame;
use App\Models\TopupOrder;
use App\Models\TopupPackage;
use App\Services\TopupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TopupController extends Controller
{
    public function __construct(protected TopupService $topupService) {}

    /**
     * Public Catalog of active games and packages
     */
    public function catalog(): JsonResponse
    {
        try {
            $games = TopupGame::query()
                ->where('is_active', true)
                ->with(['packages' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $games,
            ]);
        } catch (\Throwable $e) {
            Log::error('Topup catalog error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Unable to retrieve top-up catalog.'], 500);
        }
    }

    /**
     * Show single game by ID or Code
     */
    public function showGame(string $idOrCode): JsonResponse
    {
        $game = TopupGame::query()
            ->where('id', $idOrCode)
            ->orWhere('code', $idOrCode)
            ->firstOrFail();

        $game->load(['packages' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')]);

        return response()->json([
            'success' => true,
            'data' => $game,
        ]);
    }

    /**
     * In-game Player ID and Zone Verification (FlashTopUp API)
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $gameCode = (string) ($request->input('game_code') ?? $request->input('validation_code') ?? 'mlbb');
        $playerId = (string) ($request->input('player_id') ?? $request->input('user_id') ?? '');
        $zoneId = (string) ($request->input('zone_id') ?? $request->input('server_id') ?? '');

        // Automatically extract combined format: e.g. "702425515(10301)" or "702425515 (10301)"
        if (preg_match('/^(\d+)\s*[\(\[]([^\)\]]+)[\)\]]$/', trim($playerId), $matches)) {
            $playerId = $matches[1];
            if (empty($zoneId)) {
                $zoneId = $matches[2];
            }
        }

        if (empty($gameCode) || empty($playerId)) {
            return response()->json([
                'success' => false,
                'message' => 'game_code and player_id are required.',
            ], 422);
        }

        $result = $this->topupService->checkPlayerUsername($gameCode, $playerId, $zoneId);

        if (! empty($result['success']) && ! empty($result['player_name'])) {
            return response()->json([
                'success' => true,
                'message' => 'Player found',
                'result' => [
                    'player_name' => $result['player_name'],
                    'username' => $result['player_name'],
                    'name' => $result['player_name'],
                    'raw_data' => $result['data'] ?? [],
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Player not found with provided ID and Zone.',
            'error' => $result,
        ], 422);
    }

    /**
     * Create Top-up Order & Generate Payment Checkout URL
     */
    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game_code' => ['required', 'string'],
            'package_id' => ['required', 'integer'],
            'player_id' => ['required', 'string'],
            'player_username' => ['nullable', 'string'],
            'zone_id' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
        ]);

        // Automatically extract combined format: e.g. "702425515(10301)" or "702425515 (10301)"
        if (preg_match('/^(\d+)\s*[\(\[]([^\)\]]+)[\)\]]$/', trim($validated['player_id']), $matches)) {
            $validated['player_id'] = $matches[1];
            if (empty($validated['zone_id'])) {
                $validated['zone_id'] = $matches[2];
            }
        }

        $game = TopupGame::query()
            ->where('code', strtolower(trim($validated['game_code'])))
            ->orWhere('id', $validated['game_code'])
            ->firstOrFail();

        $package = TopupPackage::query()
            ->where('id', $validated['package_id'])
            ->where('topup_game_id', $game->id)
            ->firstOrFail();

        $defaultMethod = Setting::get('topup_payment_method', env('PAYMENT_METHOD', 'payway'));
        $paymentMethod = ! empty($validated['payment_method']) ? strtolower($validated['payment_method']) : $defaultMethod;
        if (in_array($paymentMethod, ['default', 'none', ''])) {
            $paymentMethod = 'payway';
        }

        $orderNo = 'ORD_'.now()->format('YmdHis').'_'.Str::upper(Str::random(6));

        $order = TopupOrder::create([
            'order_no' => $orderNo,
            'topup_game_id' => $game->id,
            'topup_package_id' => $package->id,
            'player_id' => trim($validated['player_id']),
            'player_username' => trim($validated['player_username'] ?? ''),
            'zone_id' => trim($validated['zone_id'] ?? ''),
            'payment_method' => $paymentMethod,
            'amount' => $package->price,
            'diamond_amount' => $package->diamond_amount,
            'status' => 'pending',
        ]);

        [$checkoutUrl, $paymentData] = $this->topupService->buildCheckout($order);

        $order->update([
            'gateway_transaction_id' => $paymentData['transaction_id'] ?? $order->order_no,
            'gateway_checkout_url' => $checkoutUrl,
            'gateway_hash' => $paymentData['hash'] ?? null,
            'gateway_payload' => $paymentData,
        ]);

        // Dispatch alert to Telegram
        $this->topupService->sendTelegramAlert($order, 'created');

        AuditLog::log('topup.order_created', "Topup order {$order->order_no} created for {$game->name} ({$package->name})");

        return response()->json([
            'success' => true,
            'message' => 'Checkout generated successfully.',
            'order' => $order->load(['game', 'package']),
            'checkout_url' => $checkoutUrl,
        ], 201);
    }

    /**
     * Show or track Top-up Order status
     */
    public function showOrder(string $idOrOrderNo): JsonResponse
    {
        $order = TopupOrder::query()
            ->where('order_no', $idOrOrderNo)
            ->orWhere('id', $idOrOrderNo)
            ->orWhere('gateway_transaction_id', $idOrOrderNo)
            ->with(['game', 'package'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'status' => strtolower($order->status),
            'order' => [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'status' => strtolower($order->status),
                'player_username' => $order->player_username,
                'player_id' => $order->player_id,
                'zone_id' => $order->zone_id,
                'amount' => $order->amount,
                'diamond_amount' => $order->diamond_amount,
                'paid_at' => $order->paid_at,
                'success_at' => $order->success_at,
            ],
            'data' => $order,
        ]);
    }

    /**
     * Unified Payment Webhook (ABA PayWay & Dynamic KHQR)
     */
    public function khqrWebhook(Request $request): JsonResponse
    {
        Log::info('Topup Webhook Received:', $request->all());

        // 1. Supplier fulfillment callback
        if ($request->has('event') || $request->has('order_status')) {
            $referenceId = $request->input('reference_id');
            $orderStatus = strtolower((string) $request->input('order_status'));

            $order = TopupOrder::where('order_no', $referenceId)->first();
            if ($order) {
                if ($orderStatus === 'completed') {
                    $order->update(['status' => 'success', 'success_at' => now()]);
                    $this->topupService->sendTelegramAlert($order, 'success');

                    return response()->json(['success' => true, 'message' => 'Fulfillment completed.']);
                }
                if (in_array($orderStatus, ['failed', 'canceled', 'refunded'])) {
                    $order->update(['status' => 'failed', 'failed_at' => now()]);
                    $this->topupService->sendTelegramAlert($order, 'failed');

                    return response()->json(['success' => false, 'message' => 'Order failed.']);
                }
            }

            return response()->json(['success' => true, 'message' => 'Supplier callback acknowledged.']);
        }

        // 2. Bank Payment Webhook (PayWay / KHQR)
        $transactionId = $request->input('transaction_id')
            ?? $request->input('tran_id')
            ?? $request->input('merchant_ref_no')
            ?? $request->input('reference_id');

        $status = $request->input('status') ?? $request->input('payment_status');

        if (! $transactionId || $status === null) {
            return response()->json(['message' => 'Invalid webhook payload.'], 400);
        }

        $cleanKey = trim(str_replace('#', '', (string) $transactionId));

        $order = TopupOrder::where('gateway_transaction_id', $cleanKey)
            ->orWhere('order_no', $cleanKey)
            ->orWhere('gateway_transaction_id', 'ORD_'.$cleanKey)
            ->orWhere('order_no', str_replace('ORD_ORD_', 'ORD_', $cleanKey))
            ->first();

        if (! $order) {
            if (str_contains(strtolower($cleanKey), 'test')) {
                return response()->json(['success' => true, 'message' => 'Test webhook received.']);
            }

            return response()->json(['message' => 'Order not found.'], 404);
        }

        $normalizedStatus = strtolower(trim((string) $status));
        $isPaid = in_array($normalizedStatus, ['00', '0', 'success', 'paid', 'completed']);

        if ($isPaid) {
            if (in_array($order->status, ['processing', 'success'])) {
                return response()->json(['success' => true, 'status' => $order->status, 'message' => 'Already processed.']);
            }

            $order->update(['status' => 'processing', 'paid_at' => now()]);
            $this->topupService->sendTelegramAlert($order, 'paid');

            // Check if auto-fulfillment is enabled in Admin settings
            $autoFulfill = (bool) Setting::get('topup_auto_fulfill', true);
            if ($autoFulfill) {
                $fulfillRes = $this->topupService->fulfillOrder($order);
                Log::info("Auto fulfillment result for {$order->order_no}:", $fulfillRes);
            }

            return response()->json(['success' => true, 'status' => 'processing', 'message' => 'Payment recorded successfully.']);
        }

        return response()->json(['message' => 'Non-success payment status.'], 400);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Management Endpoints
    |--------------------------------------------------------------------------
    */

    public function adminDashboard(): JsonResponse
    {
        $games = TopupGame::with(['packages' => fn ($q) => $q->orderBy('sort_order')])->orderBy('name')->get();
        $packagesCount = TopupPackage::count();
        $ordersCount = TopupOrder::count();
        $successCount = TopupOrder::where('status', 'success')->count();
        $pendingCount = TopupOrder::where('status', 'pending')->count();
        $holdCount = TopupOrder::where('status', 'manual_hold')->count();
        $totalRevenue = TopupOrder::whereIn('status', ['paid', 'processing', 'success'])->sum('amount');

        return response()->json([
            'success' => true,
            'stats' => [
                'totalRevenue' => (float) $totalRevenue,
                'ordersCount' => $ordersCount,
                'successCount' => $successCount,
                'pendingCount' => $pendingCount,
                'holdCount' => $holdCount,
                'gamesCount' => $games->count(),
                'packagesCount' => $packagesCount,
            ],
            'games' => $games,
        ]);
    }

    public function adminOrders(Request $request): JsonResponse
    {
        $query = TopupOrder::with(['game', 'package'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('order_no', 'like', "%{$s}%")
                    ->orWhere('player_id', 'like', "%{$s}%")
                    ->orWhere('player_username', 'like', "%{$s}%")
                    ->orWhere('gateway_transaction_id', 'like', "%{$s}%");
            });
        }

        $orders = $query->paginate($request->input('per_page', 50));

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    public function storeGame(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:191', 'unique:topup_games,code'],
            'name' => ['required', 'string', 'max:255'],
            'api_game_id' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $game = TopupGame::create([
            'code' => strtolower(trim($validated['code'])),
            'name' => trim($validated['name']),
            'api_game_id' => $validated['api_game_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::log('topup.game_created', "Created topup game {$game->name} ({$game->code})");

        return response()->json([
            'success' => true,
            'message' => 'Game created successfully.',
            'data' => $game,
        ], 201);
    }

    public function updateGame(Request $request, int $id): JsonResponse
    {
        $game = TopupGame::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:191', 'unique:topup_games,code,'.$game->id],
            'name' => ['required', 'string', 'max:255'],
            'api_game_id' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $game->update([
            'code' => strtolower(trim($validated['code'])),
            'name' => trim($validated['name']),
            'api_game_id' => $validated['api_game_id'] ?? $game->api_game_id,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $game->is_active,
        ]);

        AuditLog::log('topup.game_updated', "Updated topup game {$game->name}");

        return response()->json([
            'success' => true,
            'message' => 'Game updated successfully.',
            'data' => $game,
        ]);
    }

    public function destroyGame(int $id): JsonResponse
    {
        $game = TopupGame::findOrFail($id);
        $name = $game->name;
        $game->delete();

        AuditLog::log('topup.game_deleted', "Deleted topup game {$name}");

        return response()->json([
            'success' => true,
            'message' => 'Game deleted successfully.',
        ]);
    }

    public function storePackage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game_id' => ['required', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'diamond_amount' => ['required', 'integer', 'min:1'],
            'sku' => ['nullable', 'string', 'max:191'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $name = ! empty($validated['name']) ? trim($validated['name']) : $validated['diamond_amount'].' Diamonds';

        $package = TopupPackage::create([
            'topup_game_id' => $validated['game_id'],
            'name' => $name,
            'price' => $validated['price'],
            'diamond_amount' => $validated['diamond_amount'],
            'sku' => ! empty($validated['sku']) ? trim($validated['sku']) : null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::log('topup.package_created', "Created topup package {$package->name} for game #{$package->topup_game_id}");

        return response()->json([
            'success' => true,
            'message' => 'Package created successfully.',
            'package' => $package->fresh(['game']),
        ], 201);
    }

    public function updatePackage(Request $request, int $id): JsonResponse
    {
        $package = TopupPackage::findOrFail($id);

        $validated = $request->validate([
            'game_id' => ['nullable', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'diamond_amount' => ['nullable', 'integer', 'min:1'],
            'sku' => ['nullable', 'string', 'max:191'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $package->update([
            'topup_game_id' => $validated['game_id'] ?? $package->topup_game_id,
            'name' => $request->has('name') ? trim($validated['name']) : $package->name,
            'price' => $validated['price'] ?? $package->price,
            'diamond_amount' => $validated['diamond_amount'] ?? $package->diamond_amount,
            'sku' => $request->has('sku') ? (! empty($validated['sku']) ? trim($validated['sku']) : null) : $package->sku,
            'sort_order' => $validated['sort_order'] ?? $package->sort_order,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $package->is_active,
        ]);

        AuditLog::log('topup.package_updated', "Updated package {$package->name}");

        return response()->json([
            'success' => true,
            'message' => 'Package updated successfully.',
            'package' => $package->fresh(['game']),
        ]);
    }

    public function destroyPackage(int $id): JsonResponse
    {
        $package = TopupPackage::findOrFail($id);
        $name = $package->name;
        $package->delete();

        AuditLog::log('topup.package_deleted', "Deleted package {$name}");

        return response()->json([
            'success' => true,
            'message' => 'Package deleted successfully.',
        ]);
    }

    public function updateOrderStatus(Request $request, int $id): JsonResponse
    {
        $order = TopupOrder::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,success,failed,manual_hold'],
        ]);

        $order->update(['status' => $validated['status']]);

        AuditLog::log('topup.order_status_updated', "Order {$order->order_no} status changed to {$order->status}");

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'data' => $order,
        ]);
    }

    /**
     * Manual verification: pushes order directly to FlashTopUp API
     */
    public function manualVerifyOrder(int $id): JsonResponse
    {
        $order = TopupOrder::findOrFail($id);

        if ($order->status === 'success') {
            return response()->json([
                'success' => false,
                'message' => 'Order is already marked as success.',
            ], 400);
        }

        $order->update(['status' => 'processing', 'paid_at' => $order->paid_at ?: now()]);

        $result = $this->topupService->fulfillOrder($order);

        AuditLog::log('topup.manual_verify', "Manual verify & push triggered for order {$order->order_no}");

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'order' => $order->fresh(['game', 'package']),
            'result' => $result,
        ]);
    }

    /**
     * Inspect FlashTopUp Catalog & live balance from whitelisted server
     */
    public function flashCatalog(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id', 3);
        $products = $this->topupService->callFlashApi('GET', '/api/reseller/v2/products');
        $services = $this->topupService->callFlashApi('GET', "/api/reseller/v2/services?product_id={$productId}");

        return response()->json([
            'success' => true,
            'products' => $products,
            'services' => $services,
        ]);
    }

    public function destroyOrder(int $id): JsonResponse
    {
        $order = TopupOrder::findOrFail($id);
        $orderNo = $order->order_no;
        $order->delete();

        AuditLog::log('topup.order_deleted', "Deleted topup order {$orderNo}");

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }
}

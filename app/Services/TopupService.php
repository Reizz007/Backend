<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\TopupOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TopupService
{
    /**
     * Retrieve FlashTopUp API Configuration dynamically (Settings DB overrides .env)
     */
    public function getFlashConfig(): array
    {
        $apiId = trim((string) Setting::get('topup_flash_api_id', config('services.flash_topup.api_id', env('FLASH_TOPUP_API_ID', 'RSMNGJ90S66GU8IC'))));
        $secretKey = trim((string) Setting::get('topup_flash_secret_key', config('services.flash_topup.secret_key', env('FLASH_TOPUP_SECRET_KEY', '1c5e38d93eadd3f18ff717f3d2d3a925e3549190ce373690c5e68917aa6e9497'))));
        $baseUrl = rtrim((string) Setting::get('topup_flash_base_url', config('services.flash_topup.base_url', env('FLASH_TOPUP_BASE_URL', 'https://flashtopup.com'))), '/');

        if (str_ends_with($baseUrl, '/api/reseller/v2')) {
            $baseUrl = substr($baseUrl, 0, -strlen('/api/reseller/v2'));
        }

        return [
            'api_id' => $apiId,
            'secret_key' => $secretKey,
            'base_url' => $baseUrl,
        ];
    }

    /**
     * Check game Player ID / Zone with FlashTopUp Check-ID API
     */
    public function checkPlayerUsername(string $gameCode, string $playerId, string $zoneId = ''): array
    {
        $normalizedCode = strtolower(trim($gameCode));
        if (in_array($normalizedCode, [
            'mlbb',
            'mobile_legends',
            'mobile_legend',
            'mobile-legends',
            'mobilelegends',
            'topup_mobile_legends',
            'topup_mobile_legends_exclusive',
            'mlbb_exclusive',
            'mlbb_br',
        ])) {
            $validationCode = 'mlbb';
        } elseif (in_array($normalizedCode, ['mlbb_adventure', 'topup_mobile_legends_adventure'])) {
            $validationCode = 'mlbb_adventure';
        } elseif (in_array($normalizedCode, ['magic_chest_gogo', 'magic_chess_gogo', 'magic_chest', 'magic_chess'])) {
            $validationCode = 'magic_chess_gogo';
        } elseif (in_array($normalizedCode, ['freefire_global', 'free_fire', 'freefire'])) {
            $validationCode = 'free_fire';
        } else {
            $validationCode = $normalizedCode;
        }

        $config = $this->getFlashConfig();
        $apiId = $config['api_id'];
        $secretKey = $config['secret_key'];
        $baseUrl = $config['base_url'];

        $path = '/api/reseller/v2/check-id';
        $method = 'POST';
        $timestamp = (string) time();
        $nonce = bin2hex(random_bytes(16));

        $bodyData = [
            'server_id' => trim($zoneId),
            'user_id' => trim($playerId),
            'validation_code' => $validationCode,
        ];
        ksort($bodyData);

        $rawJsonBody = json_encode($bodyData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $bodyHash = hash('sha256', $rawJsonBody);
        $canonical = implode("\n", [$method, $path, $timestamp, $nonce, $bodyHash]);
        $signature = hash_hmac('sha256', $canonical, $secretKey);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-FT-API-ID' => $apiId,
                'X-FT-Timestamp' => $timestamp,
                'X-FT-Nonce' => $nonce,
                'X-FT-Signature' => $signature,
            ])
                ->withoutVerifying()
                ->timeout(25)
                ->withBody($rawJsonBody, 'application/json')
                ->post($baseUrl.$path);

            if ($response->successful()) {
                $apiData = $response->json();
                $playerName = $apiData['account_name']
                    ?? $apiData['data']['account_name']
                    ?? $apiData['player_name']
                    ?? null;

                return [
                    'success' => true,
                    'player_name' => $playerName,
                    'username' => $playerName,
                    'data' => $apiData,
                ];
            }

            $rawJson = $response->json();
            $errorMessage = $rawJson['error']['message'] ?? $rawJson['message'] ?? 'Player ID verification rejected by supplier API.';

            if (isset($rawJson['error']['code']) && $rawJson['error']['code'] === 'IP_NOT_ALLOWED') {
                $detectedIp = $rawJson['error']['details']['ip'] ?? 'server IP';
                $errorMessage = "FlashTopUp API: Server IP ({$detectedIp}) is not whitelisted. Please add {$detectedIp} to your FlashTopUp IP Whitelist in your reseller dashboard.";
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'raw' => $rawJson ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('FlashTopUp Check-ID Exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Connection to game verification service timed out.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build payment checkout URL (supports ABA PayWay Link and dynamic KHQR)
     */
    public function buildCheckout(TopupOrder $order): array
    {
        return $this->buildKhqrCheckout($order);
    }

    /**
     * ABA PayWay checkout generator - forwards to unified dyzzstore KHQR
     */
    public function buildPaywayCheckout(TopupOrder $order): array
    {
        return $this->buildKhqrCheckout($order);
    }

    /**
     * Dynamic KHQR checkout generator using the unified dyzzstore AnajakPay / KHQR gateway
     */
    public function buildKhqrCheckout(TopupOrder $order): array
    {
        /** @var KhqrService $khqrService */
        $khqrService = app(KhqrService::class);

        $orderNo = $order->order_no ?? ('ORD_'.now()->format('YmdHis').'_'.Str::upper(Str::random(5)));
        $transactionId = $order->gateway_transaction_id ?: (str_starts_with($orderNo, 'ORD_') ? $orderNo : 'ORD_'.$orderNo);
        $amount = (float) ($order->amount ?? 0);

        $order->loadMissing('game');
        $gameCode = strtoupper($order->game?->code ?? 'GAME');
        $playerId = $order->player_id ?? '';
        $zoneId = $order->zone_id ?? '';

        $remark = ! empty($zoneId)
            ? sprintf('%s|ID:%s|S:%s', $gameCode, $playerId, $zoneId)
            : sprintf('%s|ID:%s', $gameCode, $playerId);

        $successUrl = url('/payment-success.html');

        $khqrData = $khqrService->createPayment(
            amount: $amount,
            transactionId: $transactionId,
            remark: $remark,
            successUrl: $successUrl
        );

        $checkoutUrl = $khqrData['checkout_url'];
        $iframeUrl = $khqrData['iframe_url'] ?: ($checkoutUrl.'&iframe=1');

        $order->update([
            'gateway_transaction_id' => $transactionId,
            'gateway_checkout_url' => $checkoutUrl,
            'gateway_payload' => $khqrData,
            'payment_method' => 'khqr',
        ]);

        return [$iframeUrl, $khqrData];
    }

    /**
     * Reusable helper to make HMAC-SHA256 signed requests to FlashTopUp API
     */
    public function callFlashApi(string $method, string $path, ?array $body = null): array
    {
        $config = $this->getFlashConfig();
        $apiId = $config['api_id'];
        $secretKey = $config['secret_key'];
        $baseUrl = $config['base_url'];

        $timestamp = (string) time();
        $nonce = bin2hex(random_bytes(16));
        $rawJson = ($body !== null && strtoupper($method) === 'POST')
            ? json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : '';
        $bodyHash = hash('sha256', $rawJson);

        // FlashTopUp HMAC canonical path should only include the URL path without query params
        $canonicalPath = parse_url($path, PHP_URL_PATH) ?: $path;
        $canonical = implode("\n", [strtoupper($method), $canonicalPath, $timestamp, $nonce, $bodyHash]);
        $signature = hash_hmac('sha256', $canonical, $secretKey);

        $headers = [
            'Content-Type' => 'application/json',
            'X-FT-API-ID' => $apiId,
            'X-FT-Timestamp' => $timestamp,
            'X-FT-Nonce' => $nonce,
            'X-FT-Signature' => $signature,
        ];

        try {
            $req = Http::withHeaders($headers)->withoutVerifying()->timeout(25);
            $response = strtoupper($method) === 'POST'
                ? $req->withBody($rawJson, 'application/json')->post($baseUrl.$path)
                : $req->get($baseUrl.$path);

            return [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json() ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 500,
                'successful' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Auto Push / Fulfill Diamond Order to FlashTopUp API
     */
    public function fulfillOrder(TopupOrder $order): array
    {
        $order->loadMissing(['game', 'package']);
        $skuValue = trim((string) ($order->package?->sku ?? ''));
        $diamondAmount = (int) ($order->package?->diamond_amount ?? $order->diamond_amount ?? 0);
        $packageName = strtolower($order->package?->name ?? '');
        $gameCode = strtolower($order->game?->code ?? '');

        $productId = 3449;
        $productCode = 'TOPUP_MOBILE_LEGENDS';
        $serviceId = null;
        $serviceCode = null;

        // 1. Direct SKU overrides if already a full service string
        if (str_starts_with($skuValue, 'TOPUP_') || str_starts_with($skuValue, 'MOBILE_LEGENDS_')) {
            $serviceCode = $skuValue;
            $productId = $order->game?->api_game_id ?: 3449;
            $productCode = $productId === 3454 ? 'TOPUP_MOBILE_LEGENDS_EXCLUSIVE' : 'TOPUP_MOBILE_LEGENDS';
        } elseif (str_contains($skuValue, '|')) {
            $parts = explode('|', $skuValue);
            $productId = (int) trim($parts[0]);
            $serviceCode = trim($parts[1]);
            $productCode = $productId === 3454 ? 'TOPUP_MOBILE_LEGENDS_EXCLUSIVE' : 'TOPUP_MOBILE_LEGENDS';
        }
        // 2. Mobile Legends Exclusive / Small Denominations (Product 3454: 5, 11, 22, 33 Diamonds)
        elseif ($diamondAmount === 5 || $skuValue === '267') {
            $productId = 3454;
            $productCode = 'TOPUP_MOBILE_LEGENDS_EXCLUSIVE';
            $serviceId = 57041;
            $serviceCode = 'MOBILE_LEGENDS_EXCLUSIVE_5_DIAMONDS';
        } elseif ($diamondAmount === 11 || $skuValue === '139' || $skuValue === '268') {
            $productId = 3454;
            $productCode = 'TOPUP_MOBILE_LEGENDS_EXCLUSIVE';
            $serviceId = 57042;
            $serviceCode = 'MOBILE_LEGENDS_EXCLUSIVE_11_DIAMONDS';
        } elseif ($diamondAmount === 22) {
            $productId = 3454;
            $serviceId = 57043;
            $serviceCode = 'MOBILE_LEGENDS_EXCLUSIVE_22_DIAMONDS';
        } elseif ($diamondAmount === 33 || $skuValue === '269') {
            $productId = 3454;
            $serviceId = 57044;
            $serviceCode = 'MOBILE_LEGENDS_EXCLUSIVE_33_DIAMONDS';
        } elseif ($diamondAmount === 55 && ($gameCode === 'topup_mobile_legends_exclusive' || $skuValue === '270')) {
            $productId = 3454;
            $serviceId = 57046;
            $serviceCode = 'MOBILE_LEGENDS_EXCLUSIVE_55_DIAMONDS';
        } elseif ($diamondAmount === 56 || $skuValue === '271' || $skuValue === '272') {
            $productId = 3454;
            $serviceId = 57045;
            $serviceCode = 'MOBILE_LEGENDS_EXCLUSIVE_56_DIAMONDS';
        } elseif ($diamondAmount === 112 || $skuValue === '273') {
            $productId = 3454;
            $serviceId = 57048;
            $serviceCode = 'MOBILE_LEGENDS_EXCLUSIVE_112_DIAMONDS';
        } elseif ($diamondAmount === 165 && ($gameCode === 'topup_mobile_legends_exclusive' || $skuValue === '274')) {
            $productId = 3454;
            $serviceId = 57050;
            $serviceCode = 'MOBILE_LEGENDS_EXCLUSIVE_165_DIAMONDS';
        }
        // 3. Mobile Legends Regular Moonton Denominations (Product 3449)
        elseif (str_contains($packageName, 'weekly elite') || $skuValue === '140') {
            $productId = 3449;
            $serviceId = 56904;
            $serviceCode = 'MOBILE_LEGENDS_WEEKLY_ELITE_PACK';
        } elseif (str_contains($packageName, 'monthly elite')) {
            $productId = 3449;
            $serviceId = 56911;
            $serviceCode = 'MOBILE_LEGENDS_MONTHLY_ELITE_PACK';
        } elseif (str_contains($packageName, 'weekly') || str_contains($packageName, 'pass') || $skuValue === '142') {
            $productId = 3449;
            $serviceId = 56906;
            $serviceCode = 'MOBILE_LEGENDS_WEEKLY';
        } elseif (str_contains($packageName, 'diamond pass') || str_contains($packageName, 'weekly diamond')) {
            $productId = 3449;
            $serviceId = 64495;
            $serviceCode = 'MOBILE_LEGENDS_DIAMOND_PASS';
        } elseif (str_contains($packageName, 'twilight')) {
            $productId = 3449;
            $serviceCode = 'MOBILE_LEGENDS_TWILIGHT';
        } elseif ($diamondAmount === 55 || $skuValue === '38' || $skuValue === '55') {
            $productId = 3449;
            $serviceId = 56903;
            $serviceCode = 'MOBILE_LEGENDS_55_DIAMONDS';
        } elseif ($diamondAmount === 86 || $skuValue === '86' || $skuValue === '89') {
            $productId = 3449;
            $serviceId = 56905;
            $serviceCode = 'MOBILE_LEGENDS_86_DIAMONDS';
        } elseif ($diamondAmount === 165 || $skuValue === '40') {
            $productId = 3449;
            $serviceId = 56907;
            $serviceCode = 'MOBILE_LEGENDS_165_DIAMONDS';
        } elseif ($diamondAmount === 172 || $skuValue === '41') {
            $productId = 3449;
            $serviceId = 56908;
            $serviceCode = 'MOBILE_LEGENDS_172_DIAMONDS';
        } elseif ($diamondAmount === 257 || $skuValue === '42') {
            $productId = 3449;
            $serviceId = 56909;
            $serviceCode = 'MOBILE_LEGENDS_257_DIAMONDS';
        } elseif ($diamondAmount === 275 || $skuValue === '43') {
            $productId = 3449;
            $serviceId = 56910;
            $serviceCode = 'MOBILE_LEGENDS_275_DIAMONDS';
        } elseif ($diamondAmount === 343 || $skuValue === '44') {
            $productId = 3449;
            $serviceId = 56912;
            $serviceCode = 'MOBILE_LEGENDS_343_DIAMONDS';
        } elseif ($diamondAmount === 429 || $skuValue === '429' || $skuValue === '430') {
            $productId = 3449;
            $serviceId = 56914;
            $serviceCode = 'MOBILE_LEGENDS_429_DIAMONDS';
        } elseif ($diamondAmount === 514 || $skuValue === '514' || $skuValue === '516') {
            $productId = 3449;
            $serviceId = 56916;
            $serviceCode = 'MOBILE_LEGENDS_514_DIAMONDS';
        } elseif ($diamondAmount === 706 || $skuValue === '706') {
            $productId = 3449;
            $serviceId = 56920;
            $serviceCode = 'MOBILE_LEGENDS_706_DIAMONDS';
        } elseif ($diamondAmount === 792 || $skuValue === '792') {
            $productId = 3449;
            $serviceId = 56922;
            $serviceCode = 'MOBILE_LEGENDS_792_DIAMONDS';
        }
        // 4. Magic Chess Gogo (Product 107)
        elseif ((int) $skuValue >= 2134 && (int) $skuValue <= 2150) {
            $productId = 107;
            $serviceId = (int) $skuValue;
            $mcMap = [
                '2134' => '5_DIAMONDS',
                '2135' => '11_DIAMONDS',
                '2136' => '22_DIAMONDS',
                '2137' => '55_DIAMONDS',
                '2138' => '56_DIAMONDS',
                '2139' => '86_DIAMONDS',
                '2140' => '112_DIAMONDS',
            ];
            $mcStr = $mcMap[$skuValue] ?? '55_DIAMONDS';
            $serviceCode = "TOPUP_MAGIC_CHESS_GOGO_107_{$mcStr}_{$skuValue}";
        }
        // 5. Fallback defaults
        elseif (! empty($skuValue) && is_numeric($skuValue)) {
            $productId = $order->game?->api_game_id ?: 3449;
            $serviceId = (int) $skuValue;
        } else {
            $productId = $order->game?->api_game_id ?: 3449;
            $serviceCode = 'MOBILE_LEGENDS_55_DIAMONDS';
            $serviceId = 38;
        }

        $orderBody = [
            'product_id' => (int) $productId,
            'product_code' => $productCode ?: ($productId === 3454 ? 'TOPUP_MOBILE_LEGENDS_EXCLUSIVE' : 'TOPUP_MOBILE_LEGENDS'),
            'quantity' => 1,
            'reference_id' => (string) $order->order_no,
            'server_id' => (string) trim($order->zone_id ?? ''),
            'user_id' => (string) trim($order->player_id),
        ];

        if (! empty($serviceCode)) {
            $orderBody['service_code'] = (string) trim($serviceCode);
        }

        if (! empty($serviceId) && is_numeric($serviceId)) {
            $orderBody['service_id'] = (int) $serviceId;
        } elseif (! empty($skuValue) && is_numeric($skuValue)) {
            $orderBody['service_id'] = (int) $skuValue;
        }

        Log::info("Dispatching FlashTopUp Order for {$order->order_no}:", $orderBody);

        $res = $this->callFlashApi('POST', '/api/reseller/v2/order', $orderBody);
        $resBody = is_array($res['body']) ? $res['body'] : json_decode((string) $res['body'], true) ?? [];

        if (! empty($res['successful'])) {
            $order->update([
                'status' => 'success',
                'success_at' => now(),
                'supplier_order_id' => $resBody['data']['order_id'] ?? $resBody['order_id'] ?? null,
                'supplier_payload' => $resBody,
                'failure_reason' => null,
            ]);

            $this->sendTelegramAlert($order, 'success');

            return [
                'success' => true,
                'message' => 'Diamond order dispatched successfully to FlashTopUp API.',
                'data' => $resBody,
            ];
        }

        Log::error("FlashTopUp API Order Refused: {$order->order_no}", [
            'status' => $res['status'] ?? 0,
            'response' => $resBody,
            'payload' => $orderBody,
        ]);

        $failureMessage = $resBody['error']['message'] ?? $resBody['message'] ?? 'FlashTopUp rejected order request.';
        if (! empty($resBody['error']['details'])) {
            $failureMessage .= ' ('.(is_string($resBody['error']['details']) ? $resBody['error']['details'] : json_encode($resBody['error']['details'])).')';
        }
        if (isset($resBody['error']['code']) && $resBody['error']['code'] === 'IP_NOT_ALLOWED') {
            $detectedIp = $resBody['error']['details']['ip'] ?? 'server IP';
            $failureMessage = "Server IP {$detectedIp} not whitelisted in FlashTopUp portal.";
        }

        $order->update([
            'status' => 'manual_hold',
            'failure_reason' => $failureMessage,
            'supplier_payload' => $resBody,
        ]);

        $this->sendTelegramAlert($order, 'manual_hold');

        return [
            'success' => false,
            'message' => $failureMessage,
            'raw' => $resBody,
        ];
    }

    /**
     * Dispatch alert to configured Telegram bot
     */
    public function sendTelegramAlert(TopupOrder $order, string $event = 'created'): void
    {
        $botToken = Setting::get('telegram_bot_token', env('TELEGRAM_BOT_TOKEN', ''));
        $chatId = Setting::get('telegram_chat_id', env('TELEGRAM_CHAT_ID', ''));

        if (empty($botToken) || empty($chatId)) {
            return;
        }

        $order->loadMissing(['game', 'package']);
        $gameName = $order->game?->name ?? 'Game';

        $statusLabel = match ($event) {
            'paid' => 'Payment received 💰',
            'success' => 'Diamonds Delivered ✅',
            'manual_hold' => 'Manual Attention Required ⚠️',
            'failed' => 'Order failed ❌',
            default => 'New order created 🛒',
        };

        $msgLines = [
            '🎮 <b>Dyzzstore Top-up Alert</b>',
            "Event: {$statusLabel}",
            "Order No: <code>{$order->order_no}</code>",
            "Game: {$gameName}",
            'Package: '.($order->package?->name ?? '-').' ('.($order->diamond_amount ?? 0).' Diamonds)',
            "Player ID: <code>{$order->player_id}</code>",
        ];

        if ($order->player_username) {
            $msgLines[] = "Username: <b>{$order->player_username}</b>";
        }
        if (! empty($order->zone_id)) {
            $msgLines[] = "Server / Zone: <code>{$order->zone_id}</code>";
        }

        $msgLines[] = 'Amount: $'.number_format((float) ($order->amount ?? 0), 2);
        $msgLines[] = 'Status: '.strtoupper($order->status ?? 'pending');

        try {
            Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => implode("\n", $msgLines),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram alert dispatch error: '.$e->getMessage());
        }
    }
}

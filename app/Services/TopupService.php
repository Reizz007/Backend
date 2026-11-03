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
        $defaultMethod = Setting::get('topup_payment_method', env('PAYMENT_METHOD', 'payway'));
        $method = strtolower($order->payment_method ?: $defaultMethod);

        if (in_array($method, ['khqr', 'khqrcc', 'bakong'])) {
            return $this->buildKhqrCheckout($order);
        }

        return $this->buildPaywayCheckout($order);
    }

    /**
     * ABA PayWay checkout generator
     */
    public function buildPaywayCheckout(TopupOrder $order): array
    {
        $checkoutUrl = Setting::get('topup_payway_checkout_url', Setting::get('aba_pay_link', env('PAYWAY_CHECKOUT_URL', 'https://link.payway.com.kh/ABAPAY9s540501U')));
        if (empty($checkoutUrl)) {
            $checkoutUrl = 'https://link.payway.com.kh/ABAPAY9s540501U';
        }

        $orderNo = $order->order_no ?? ('ORD_'.now()->format('YmdHis').'_'.Str::upper(Str::random(5)));
        $transactionId = $order->gateway_transaction_id ?: ('ORD_'.$orderNo);
        $amount = number_format((float) ($order->amount ?? 0), 2, '.', '');

        $order->loadMissing('game');
        $gameCode = strtoupper($order->game?->code ?? 'GAME');
        $playerId = $order->player_id ?? '';
        $zoneId = $order->zone_id ?? '';

        $remark = ! empty($zoneId)
            ? sprintf('%s|ID:%s|S:%s', $gameCode, $playerId, $zoneId)
            : sprintf('%s|ID:%s', $gameCode, $playerId);

        $paymentData = [
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'remark' => substr($remark, 0, 50),
            'hash' => sha1($transactionId.$amount.$remark),
        ];

        return [$checkoutUrl, $paymentData];
    }

    /**
     * Dynamic KHQR checkout generator
     */
    public function buildKhqrCheckout(TopupOrder $order): array
    {
        $gatewayUrl = Setting::get('khqr_gateway_url', config('services.khqr.gateway_url', env('KHQR_GATEWAY_URL', 'https://khqr.cc/api/payment/requestv2')));
        $profileId = Setting::get('khqr_profile_id', config('services.khqr.profile_id', env('KHQR_PROFILE_ID', 'tW6PHjgPyzISFi3KK22hKZ57rag1cWHS')));
        $secretKey = Setting::get('khqr_secret_key', config('services.khqr.secret_key', env('KHQR_SECRET_KEY', 'zsFq7SWHV4gYFSAdfg2ud8WV747tBOei')));

        $orderNo = $order->order_no ?? ('ORD_'.now()->format('YmdHis').'_'.Str::upper(Str::random(5)));
        $transactionId = $order->gateway_transaction_id ?: ('ORD_'.$orderNo);
        $amount = number_format((float) ($order->amount ?? 0), 2, '.', '');

        $order->loadMissing('game');
        $gameCode = strtoupper($order->game?->code ?? 'GAME');
        $playerId = $order->player_id ?? '';
        $zoneId = $order->zone_id ?? '';

        $remark = ! empty($zoneId)
            ? sprintf('%s|ID:%s|S:%s', $gameCode, $playerId, $zoneId)
            : sprintf('%s|ID:%s', $gameCode, $playerId);

        $paymentData = [
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'remark' => substr($remark, 0, 50),
        ];

        $paymentData['hash'] = sha1(
            $secretKey
            .$paymentData['transaction_id']
            .$paymentData['amount']
            .$paymentData['remark']
        );

        $checkoutUrl = rtrim($gatewayUrl, '/').'/'.$profileId.'?'.http_build_query($paymentData);

        return [$checkoutUrl, $paymentData];
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

        // Smart FlashTopUp Product ID & Service Code Resolver
        $mlbbServiceCodes = [
            '38' => 'MOBILE_LEGENDS_55_DIAMONDS',
            '55' => 'MOBILE_LEGENDS_55_DIAMONDS',
            '86' => 'MOBILE_LEGENDS_86_DIAMONDS',
            '89' => 'MOBILE_LEGENDS_86_DIAMONDS',
            '142' => 'MOBILE_LEGENDS_WEEKLY',
            '165' => 'MOBILE_LEGENDS_165_DIAMONDS',
            '172' => 'MOBILE_LEGENDS_172_DIAMONDS',
            '257' => 'MOBILE_LEGENDS_257_DIAMONDS',
            '275' => 'MOBILE_LEGENDS_275_DIAMONDS',
            '343' => 'MOBILE_LEGENDS_343_DIAMONDS',
            '344' => 'MOBILE_LEGENDS_344_DIAMONDS',
            '429' => 'MOBILE_LEGENDS_429_DIAMONDS',
            '430' => 'MOBILE_LEGENDS_430_DIAMONDS',
            '514' => 'MOBILE_LEGENDS_514_DIAMONDS',
            '516' => 'MOBILE_LEGENDS_516_DIAMONDS',
            '565' => 'MOBILE_LEGENDS_565_DIAMONDS',
            '600' => 'MOBILE_LEGENDS_600_DIAMONDS',
            '602' => 'MOBILE_LEGENDS_602_DIAMONDS',
            '706' => 'MOBILE_LEGENDS_706_DIAMONDS',
            '792' => 'MOBILE_LEGENDS_792_DIAMONDS',
        ];

        if (str_starts_with($skuValue, 'MOBILE_LEGENDS_') || str_starts_with($skuValue, 'TOPUP_')) {
            $serviceCode = $skuValue;
            $productId = $order->game?->api_game_id ?: 3;
        } elseif (isset($mlbbServiceCodes[$skuValue])) {
            $serviceCode = $mlbbServiceCodes[$skuValue];
            $productId = 3;
        } elseif (str_contains($packageName, 'weekly elite')) {
            $serviceCode = 'MOBILE_LEGENDS_WEEKLY_ELITE_PACK';
            $productId = 3;
        } elseif (str_contains($packageName, 'monthly elite')) {
            $serviceCode = 'MOBILE_LEGENDS_MONTHLY_ELITE_PACK';
            $productId = 3;
        } elseif (str_contains($packageName, 'weekly') || str_contains($packageName, 'pass')) {
            $serviceCode = 'MOBILE_LEGENDS_WEEKLY';
            $productId = 3;
        } elseif (str_contains($packageName, 'twilight')) {
            $serviceCode = 'MOBILE_LEGENDS_TWILIGHT';
            $productId = 3;
        } elseif (isset($mlbbServiceCodes[(string) $diamondAmount])) {
            $serviceCode = $mlbbServiceCodes[(string) $diamondAmount];
            $productId = 3;
        } elseif ((int) $skuValue >= 267 && (int) $skuValue <= 350) {
            $productId = 5;
            $diamondsMap = [
                '267' => '5_DIAMONDS',
                '268' => '11_DIAMONDS',
                '269' => '22_DIAMONDS',
                '270' => '33_DIAMONDS',
                '271' => '55_DIAMONDS',
                '272' => '56_DIAMONDS',
                '273' => '112_DIAMONDS',
            ];
            $diamondStr = $diamondsMap[$skuValue] ?? '55_DIAMONDS';
            $serviceCode = "TOPUP_MOBILE_LEGENDS_EXCLUSIVE_5_{$diamondStr}_{$skuValue}";
        } elseif ((int) $skuValue >= 2134 && (int) $skuValue <= 2150) {
            $productId = 107;
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
        } elseif (str_contains($skuValue, '|')) {
            $parts = explode('|', $skuValue);
            $productId = (int) trim($parts[0]);
            $serviceCode = trim($parts[1]);
        } elseif (! empty($skuValue)) {
            $serviceCode = $skuValue;
            $productId = $order->game?->api_game_id ?: 3;
        } else {
            $serviceCode = 'MOBILE_LEGENDS_55_DIAMONDS';
            $productId = 3;
        }

        $config = $this->getFlashConfig();
        $apiId = $config['api_id'];
        $secretKey = $config['secret_key'];
        $baseUrl = $config['base_url'];

        $path = '/api/reseller/v2/order';
        $timestamp = (string) time();
        $nonce = bin2hex(random_bytes(16));

        $orderBody = [
            'product_id' => (int) $productId,
            'quantity' => 1,
            'reference_id' => (string) $order->order_no,
            'server_id' => (string) trim($order->zone_id ?? ''),
            'service_code' => (string) trim($serviceCode),
            'user_id' => (string) trim($order->player_id),
        ];

        ksort($orderBody);
        $orderJson = json_encode($orderBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $orderBodyHash = hash('sha256', $orderJson);
        $orderCanonical = implode("\n", ['POST', $path, $timestamp, $nonce, $orderBodyHash]);
        $orderSignature = hash_hmac('sha256', $orderCanonical, $secretKey);

        try {
            $flashResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-FT-API-ID' => $apiId,
                'X-FT-Timestamp' => $timestamp,
                'X-FT-Nonce' => $nonce,
                'X-FT-Signature' => $orderSignature,
            ])
                ->withoutVerifying()
                ->timeout(30)
                ->withBody($orderJson, 'application/json')
                ->post($baseUrl.$path);

            $resBody = $flashResponse->json() ?? [];

            if ($flashResponse->successful()) {
                $order->update([
                    'status' => 'success',
                    'success_at' => now(),
                    'supplier_order_id' => $resBody['data']['order_id'] ?? $resBody['order_id'] ?? null,
                    'supplier_payload' => $resBody,
                ]);

                $this->sendTelegramAlert($order, 'success');

                return [
                    'success' => true,
                    'message' => 'Diamond order dispatched successfully to FlashTopUp API.',
                    'data' => $resBody,
                ];
            }

            Log::error("FlashTopUp API Order Refused: {$order->order_no}", [
                'status' => $flashResponse->status(),
                'response' => $resBody,
            ]);

            $failureMessage = $resBody['error']['message'] ?? $resBody['message'] ?? 'FlashTopUp rejected order request.';
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
        } catch (\Throwable $ex) {
            Log::critical("FlashTopUp Dispatch Exception: {$order->order_no} - ".$ex->getMessage());

            $order->update([
                'status' => 'manual_hold',
                'failure_reason' => $ex->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Network error connecting to supplier: '.$ex->getMessage(),
            ];
        }
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

<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Send a raw message to Telegram via Bot API.
     */
    public static function sendMessage(string $text, ?string $chatId = null, ?string $token = null): array
    {
        $botToken = $token ?: Setting::get('telegram_bot_token', env('TELEGRAM_BOT_TOKEN'));
        $targetChatId = $chatId ?: Setting::get('telegram_chat_id', env('TELEGRAM_CHAT_ID'));

        if (empty($botToken) || empty($targetChatId)) {
            return [
                'success' => false,
                'message' => 'Telegram Bot Token ($token) or Chat ID ($chat_id) is not configured in Admin settings.',
            ];
        }

        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $response = Http::timeout(10)->withoutVerifying()->post($url, [
                'chat_id' => $targetChatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Telegram alert sent successfully.',
                    'data' => $response->json(),
                ];
            }

            $errorData = $response->json();
            $errorMessage = $errorData['description'] ?? ('Telegram API error with HTTP status '.$response->status());
            Log::warning('Telegram send error: '.$errorMessage);

            return [
                'success' => false,
                'message' => $errorMessage,
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('Telegram notification exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Network error connecting to Telegram API: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send structured alert for Order / Payment events.
     */
    public static function sendOrderAlert($order, string $event = 'New Order Received'): array
    {
        $enabled = Setting::get('telegram_enabled', true);
        if (! $enabled) {
            return ['success' => false, 'message' => 'Telegram alerts disabled in settings.'];
        }

        $orderNumber = $order->order_number ?? ($order['order_number'] ?? 'N/A');
        $customerName = $order->customer_name ?? ($order['customer_name'] ?? 'Customer');
        $customerEmail = $order->customer_email ?? ($order['customer_email'] ?? 'N/A');
        $company = $order->company ?? ($order['company'] ?? '');
        $amount = number_format((float) ($order->amount ?? ($order['amount'] ?? 0)), 2);
        $method = $order->payment_method ?? ($order['payment_method'] ?? 'KHQR Gateway');
        $status = $order->status ?? ($order['status'] ?? 'Pending');
        $items = $order->items ?? ($order['items'] ?? []);
        $itemsList = is_array($items) ? implode(', ', $items) : (string) $items;

        $icon = ($status === 'Verified' || $status === 'Completed') ? '✅' : '🔔';

        $text = "<b>{$icon} {$event}!</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "💳 <b>Order #:</b> <code>{$orderNumber}</code>\n";
        $text .= '👤 <b>Customer:</b> '.htmlspecialchars($customerName)."\n";
        $text .= '📧 <b>Email:</b> <code>'.htmlspecialchars($customerEmail)."</code>\n";
        if (! empty($company)) {
            $text .= '🏢 <b>Company:</b> '.htmlspecialchars($company)."\n";
        }
        $text .= "💰 <b>Amount:</b> <b>\${$amount} USD</b>\n";
        $text .= '🏷 <b>Method:</b> '.htmlspecialchars($method)."\n";
        if (! empty($itemsList)) {
            $text .= '📦 <b>Items:</b> '.htmlspecialchars($itemsList)."\n";
        }
        $text .= "⚡ <b>Status:</b> <b>{$status}</b>\n";
        $text .= '🕒 <b>Time:</b> '.now()->format('Y-m-d H:i:s')." UTC\n";

        return self::sendMessage($text);
    }

    /**
     * Send specific "Order Successful & Verified!" Telegram Alert.
     */
    public static function sendOrderSuccessAlert($order): array
    {
        $enabled = Setting::get('telegram_enabled', true);
        if (! $enabled) {
            return ['success' => false, 'message' => 'Telegram alerts disabled in settings.'];
        }

        $orderNumber = $order->order_number ?? ($order['order_number'] ?? 'N/A');
        $customerName = $order->customer_name ?? ($order['customer_name'] ?? 'Valued Customer');
        $customerEmail = $order->customer_email ?? ($order['customer_email'] ?? 'N/A');
        $company = $order->company ?? ($order['company'] ?? '');
        $amount = number_format((float) ($order->amount ?? ($order['amount'] ?? 0)), 2);
        $method = $order->payment_method ?? ($order['payment_method'] ?? 'Cambodia National KHQR Gateway');
        $apiKey = $order->api_key ?? ($order['api_key'] ?? '');
        $items = $order->items ?? ($order['items'] ?? []);
        $itemsList = is_array($items) ? implode(', ', $items) : (string) $items;

        $text = "🎉 <b>Order Successful & Verified!</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "💳 <b>Order #:</b> <code>{$orderNumber}</code>\n";
        $text .= '👤 <b>Customer:</b> '.htmlspecialchars($customerName)."\n";
        $text .= '📧 <b>Email:</b> <code>'.htmlspecialchars($customerEmail)."</code>\n";
        if (! empty($company)) {
            $text .= '🏢 <b>Company:</b> '.htmlspecialchars($company)."\n";
        }
        $text .= "💰 <b>Amount Paid:</b> <b>\${$amount} USD</b>\n";
        $text .= '🏷 <b>Payment Method:</b> '.htmlspecialchars($method)."\n";
        if (! empty($itemsList)) {
            $text .= '📦 <b>Items Purchased:</b> '.htmlspecialchars($itemsList)."\n";
        }
        if (! empty($apiKey)) {
            $maskedKey = substr($apiKey, 0, 14).'...'.substr($apiKey, -6);
            $text .= "🔑 <b>License Key:</b> <code>{$maskedKey}</code>\n";
        }
        $text .= "⚡ <b>Status:</b> <b>Verified & Activated ✅</b>\n";
        $text .= '🕒 <b>Time:</b> '.now()->format('Y-m-d H:i:s')." UTC\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= '🚀 <i>Dyzzstore Notification</i>';

        return self::sendMessage($text);
    }

    /**
     * Resolve the Telegram chat and bot token for device security alerts.
     * Prefers dedicated security group ID / token, falls back to default store bot/chat.
     */
    public static function getDeviceAlertTarget(?string $customGroupChatId = null, ?string $customBotToken = null): array
    {
        $groupChatId = $customGroupChatId ?: Setting::get('telegram_device_chat_id');
        $botToken = $customBotToken ?: Setting::get('telegram_device_bot_token');

        $finalChatId = ! empty($groupChatId) ? trim((string) $groupChatId) : trim((string) Setting::get('telegram_chat_id', env('TELEGRAM_CHAT_ID', '')));
        $finalBotToken = ! empty($botToken) ? trim((string) $botToken) : trim((string) Setting::get('telegram_bot_token', env('TELEGRAM_BOT_TOKEN', '')));

        return [
            'chat_id' => $finalChatId,
            'token' => $finalBotToken,
            'is_group' => ! empty($groupChatId),
        ];
    }

    /**
     * Send instant security alert to Telegram when a login occurs, especially on another device.
     */
    public static function sendLoginAlert(array $params): array
    {
        $enabled = Setting::get('telegram_enabled', true);
        $deviceAlertsEnabled = Setting::get('telegram_device_alerts_enabled', true);
        if (! $enabled || ! $deviceAlertsEnabled) {
            return ['success' => false, 'message' => 'Telegram alerts disabled in settings.'];
        }

        $isAnotherDevice = ! empty($params['is_another_device']);
        $alertOnNewDevice = Setting::get('telegram_alert_on_new_device', true);
        $alertOnPrimary = Setting::get('telegram_alert_on_primary_login', true);

        if ($isAnotherDevice && ! $alertOnNewDevice) {
            return ['success' => true, 'message' => 'New device alerts suppressed by settings.'];
        }

        if (! $isAnotherDevice && ! $alertOnPrimary) {
            return ['success' => true, 'message' => 'Primary device alerts suppressed by settings.'];
        }

        $email = htmlspecialchars($params['user_email'] ?? 'N/A');
        $deviceName = htmlspecialchars($params['device_name'] ?? 'Unknown Device');
        $deviceId = htmlspecialchars($params['device_id'] ?? 'N/A');
        $ip = htmlspecialchars($params['ip_address'] ?? 'N/A');
        $settingsHidden = ! empty($params['settings_hidden']);
        $time = $params['time'] ?? now()->format('Y-m-d H:i:s').' UTC';

        $target = self::getDeviceAlertTarget();

        if ($isAnotherDevice) {
            $text = "🚨 <b>SECURITY ALERT: Login from Another Device!</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
            $text .= "👤 <b>Account:</b> <code>{$email}</code>\n";
            $text .= "📱 <b>Device:</b> <b>{$deviceName}</b>\n";
            $text .= "🆔 <b>Device ID:</b> <code>{$deviceId}</code>\n";
            $text .= "🌐 <b>IP Address:</b> <code>{$ip}</code>\n";
            if ($settingsHidden) {
                $text .= "🔒 <b>Access Protection:</b> <b>Settings tab is HIDDEN & BLOCKED on this device!</b>\n";
            }
            $text .= "🕒 <b>Time:</b> {$time}\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
            $text .= '🛡️ <i>Only your primary authorized device can view and modify store settings.</i>';
        } else {
            $text = "🔔 <b>Primary Device Login Verified</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
            $text .= "👤 <b>Account:</b> <code>{$email}</code>\n";
            $text .= "📱 <b>Device:</b> <b>{$deviceName}</b> (Authorized Primary Device)\n";
            $text .= "🆔 <b>Device ID:</b> <code>{$deviceId}</code>\n";
            $text .= "🌐 <b>IP Address:</b> <code>{$ip}</code>\n";
            $text .= "⚡ <b>Settings Access:</b> <b>Unlocked & Verified ✅</b>\n";
            $text .= "🕒 <b>Time:</b> {$time}\n";
        }

        return self::sendMessage($text, $target['chat_id'], $target['token']);
    }

    /**
     * Send Login OTP Verification Code to Telegram.
     */
    public static function sendOtpAlert(string $recipient, string $otp, string $channel = 'Email'): array
    {
        $enabled = Setting::get('telegram_enabled', true);
        if (! $enabled) {
            return ['success' => false, 'message' => 'Telegram alerts disabled in settings.'];
        }

        $text = "🔐 <b>Dyzzstore Login Verification Code</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "🔑 <b>One-Time Code:</b> <code>{$otp}</code>\n";
        $text .= '👤 <b>Recipient:</b> <code>'.htmlspecialchars($recipient)."</code>\n";
        $text .= "📡 <b>Delivery:</b> {$channel}\n";
        $text .= "⏱️ <b>Validity:</b> 10 Minutes\n";
        $text .= '🕒 <b>Time:</b> '.now()->format('Y-m-d H:i:s')." UTC\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= '🔒 <i>Do not share this verification code with anyone.</i>';

        return self::sendMessage($text);
    }

    /**
     * Send alert when Primary Device is changed or re-authorized.
     */
    public static function sendDeviceAuthAlert(array $params): array
    {
        $enabled = Setting::get('telegram_enabled', true);
        $deviceAlertsEnabled = Setting::get('telegram_device_alerts_enabled', true);
        if (! $enabled || ! $deviceAlertsEnabled) {
            return ['success' => false, 'message' => 'Telegram alerts disabled in settings.'];
        }

        $deviceName = htmlspecialchars($params['device_name'] ?? 'This Device');
        $deviceId = htmlspecialchars($params['device_id'] ?? 'N/A');
        $ip = htmlspecialchars($params['ip_address'] ?? 'N/A');
        $time = $params['time'] ?? now()->format('Y-m-d H:i:s').' UTC';

        $target = self::getDeviceAlertTarget();

        $text = "🛡️ <b>Primary Authorized Device Updated!</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "📱 <b>New Primary Device:</b> <b>{$deviceName}</b>\n";
        $text .= "🆔 <b>Device ID:</b> <code>{$deviceId}</code>\n";
        $text .= "🌐 <b>IP Address:</b> <code>{$ip}</code>\n";
        $text .= "🔒 <b>Security Rule:</b> <b>Settings are unlocked on this device and hidden everywhere else.</b>\n";
        $text .= "🕒 <b>Time:</b> {$time}\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= '🚀 <i>Dyzzstore Security Manager</i>';

        return self::sendMessage($text, $target['chat_id'], $target['token']);
    }

    /**
     * Send test alert specifically to the Telegram Security / Device Alert Group.
     */
    public static function sendDeviceGroupTestAlert(?string $customGroupChatId = null, ?string $customBotToken = null): array
    {
        $target = self::getDeviceAlertTarget($customGroupChatId, $customBotToken);
        $finalChatId = $target['chat_id'];
        $finalToken = $target['token'];

        if (empty($finalToken) || empty($finalChatId)) {
            return [
                'success' => false,
                'message' => 'Please provide a Telegram Group Chat ID (e.g. -100xxxxxxxxxx) and ensure a Bot Token is set.',
            ];
        }

        $primaryDeviceName = Setting::get('primary_device_name', 'Windows PC • Chrome');
        $primaryDeviceId = Setting::get('primary_device_id', 'dev_nku878yn_mvncy0wo');

        $text = "🚀 <b>Dyzzstore — Telegram Security Alert Group Connected!</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "✅ <b>Status:</b> Telegram Security Channel Connected Successfully\n";
        $text .= "👥 <b>Group Chat ID:</b> <code>{$finalChatId}</code>\n";
        $text .= "📱 <b>Authorized Primary Device:</b> <b>{$primaryDeviceName}</b>\n";
        $text .= "🆔 <b>Primary Device ID:</b> <code>{$primaryDeviceId}</code>\n";
        $text .= "🔒 <b>Privacy Rule:</b> <b>Settings tab will ONLY show on this primary device.</b>\n";
        $text .= "🚨 <b>Instant Alert:</b> Any login from another device or unrecognized IP will be alerted here immediately.\n";
        $text .= '🕒 <b>Timestamp:</b> '.now()->format('Y-m-d H:i:s')." UTC\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= '🛡️ <i>Dyzzstore Security Manager</i>';

        $res = self::sendMessage($text, $finalChatId, $finalToken);
        if ($res['success']) {
            $res['message'] = "Test alert sent to Telegram Group ({$finalChatId}) successfully!";
            $res['target_chat_id'] = $finalChatId;
        }

        return $res;
    }
}

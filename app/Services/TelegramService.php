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
            $response = Http::timeout(10)->post($url, [
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
            $errorMessage = $errorData['description'] ?? ('Telegram API error with HTTP status ' . $response->status());
            Log::warning('Telegram send error: ' . $errorMessage);

            return [
                'success' => false,
                'message' => $errorMessage,
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('Telegram notification exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Network error connecting to Telegram API: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send structured alert for Order / Payment events.
     */
    public static function sendOrderAlert($order, string $event = 'New Order Received'): array
    {
        $enabled = Setting::get('telegram_enabled', true);
        if (!$enabled) {
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
        $text .= "👤 <b>Customer:</b> " . htmlspecialchars($customerName) . "\n";
        $text .= "📧 <b>Email:</b> <code>" . htmlspecialchars($customerEmail) . "</code>\n";
        if (!empty($company)) {
            $text .= "🏢 <b>Company:</b> " . htmlspecialchars($company) . "\n";
        }
        $text .= "💰 <b>Amount:</b> <b>\${$amount} USD</b>\n";
        $text .= "🏷 <b>Method:</b> " . htmlspecialchars($method) . "\n";
        if (!empty($itemsList)) {
            $text .= "📦 <b>Items:</b> " . htmlspecialchars($itemsList) . "\n";
        }
        $text .= "⚡ <b>Status:</b> <b>{$status}</b>\n";
        $text .= "🕒 <b>Time:</b> " . now()->format('Y-m-d H:i:s') . " UTC\n";

        return self::sendMessage($text);
    }
}

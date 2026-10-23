<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getSettings(): JsonResponse
    {
        $abaPayLink = Setting::get('aba_pay_link', '');
        $announcementText = Setting::get('announcement_text', 'Synapse Omni-LLM 4.5 & VideoGen 4D are now live! Instant perpetual licenses & Cambodian KHQR payment.');
        $maintenanceMode = Setting::get('maintenance_mode', false);
        $systemStatus = Setting::get('system_status', 'All AI Clusters Operational (99.99% SLA)');
        $promoCodes = Setting::get('promo_codes', [
            ['code' => 'NEURON20', 'percent' => 20, 'active' => true],
            ['code' => 'AIWELCOME', 'amount' => 10, 'active' => true],
        ]);

        $khqrGatewayUrl = Setting::get('khqr_gateway_url', config('services.khqr.gateway_url', 'https://khqr.cc/api/payment/requestv2'));
        $khqrProfileId = Setting::get('khqr_profile_id', config('services.khqr.profile_id', 'tW6PHjgPyzISFi3KK22hKZ57rag1cWHS'));
        $khqrSecretKey = Setting::get('khqr_secret_key', config('services.khqr.secret_key', 'zsFq7SWHV4gYFSAdfg2ud8WV747tBOei'));

        // Telegram Notification Credentials
        $telegramBotToken = Setting::get('telegram_bot_token', env('TELEGRAM_BOT_TOKEN', ''));
        $telegramChatId = Setting::get('telegram_chat_id', env('TELEGRAM_CHAT_ID', ''));
        $telegramEnabled = (bool) Setting::get('telegram_enabled', ! empty($telegramBotToken) && ! empty($telegramChatId));

        $settings = [
            'abaPayLink' => $abaPayLink,
            'khqrGatewayUrl' => $khqrGatewayUrl,
            'khqrProfileId' => $khqrProfileId,
            'khqrSecretKey' => $khqrSecretKey,
            'announcementText' => $announcementText,
            'maintenanceMode' => (bool) $maintenanceMode,
            'systemStatus' => $systemStatus,
            'promoCodes' => $promoCodes,
            'telegramBotToken' => $telegramBotToken,
            'telegramChatId' => $telegramChatId,
            'telegramEnabled' => $telegramEnabled,
        ];

        return response()->json([
            'success' => true,
            'settings' => $settings,
            'abaPayLink' => $abaPayLink,
            'khqrGatewayUrl' => $khqrGatewayUrl,
            'khqrProfileId' => $khqrProfileId,
            'telegramBotToken' => $telegramBotToken,
            'telegramChatId' => $telegramChatId,
            'telegramEnabled' => $telegramEnabled,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        if ($request->has('abaPayLink')) {
            Setting::set('aba_pay_link', $request->abaPayLink);
        }
        if ($request->has('khqrGatewayUrl')) {
            Setting::set('khqr_gateway_url', $request->khqrGatewayUrl);
        }
        if ($request->has('khqrProfileId')) {
            Setting::set('khqr_profile_id', $request->khqrProfileId);
        }
        if ($request->has('khqrSecretKey')) {
            Setting::set('khqr_secret_key', $request->khqrSecretKey);
        }
        if ($request->has('announcementText')) {
            Setting::set('announcement_text', $request->announcementText);
        }
        if ($request->has('maintenanceMode')) {
            Setting::set('maintenance_mode', $request->maintenanceMode);
        }
        if ($request->has('systemStatus')) {
            Setting::set('system_status', $request->systemStatus);
        }
        if ($request->has('promoCodes')) {
            Setting::set('promo_codes', $request->promoCodes);
        }

        // Telegram Settings
        if ($request->has('telegramBotToken') || $request->has('telegram_bot_token') || $request->has('token')) {
            $token = $request->input('telegramBotToken', $request->input('telegram_bot_token', $request->input('token')));
            Setting::set('telegram_bot_token', (string) $token);
        }
        if ($request->has('telegramChatId') || $request->has('telegram_chat_id') || $request->has('chat_id')) {
            $chatId = $request->input('telegramChatId', $request->input('telegram_chat_id', $request->input('chat_id')));
            Setting::set('telegram_chat_id', (string) $chatId);
        }
        if ($request->has('telegramEnabled') || $request->has('telegram_enabled')) {
            $enabled = $request->input('telegramEnabled', $request->input('telegram_enabled'));
            Setting::set('telegram_enabled', (bool) $enabled);
        }

        return $this->getSettings();
    }

    /**
     * Test connection to Telegram Bot API with current or provided credentials.
     */
    public function testTelegram(Request $request): JsonResponse
    {
        $token = $request->input('botToken') ?: $request->input('token') ?: Setting::get('telegram_bot_token');
        $chatId = $request->input('chatId') ?: $request->input('chat_id') ?: Setting::get('telegram_chat_id');

        if (empty($token) || empty($chatId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide both Telegram Bot Token and Chat ID to run connection test.',
            ], 422);
        }

        $testText = "🚀 <b>Synapse AI — Telegram Alert Connection Verified!</b>\n";
        $testText .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $testText .= "✅ <b>Status:</b> Connected to Admin Bot\n";
        $testText .= "🔔 <b>Notifications:</b> Order & Payment alerts are active\n";
        $testText .= '🕒 <i>Timestamp: '.now()->format('Y-m-d H:i:s').' UTC</i>';

        $result = TelegramService::sendMessage($testText, (string) $chatId, (string) $token);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Test alert sent! Check your Telegram chat or channel.',
                'data' => $result['data'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to send message via Telegram Bot API.',
        ], 400);
    }
}

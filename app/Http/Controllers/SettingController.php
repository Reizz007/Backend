<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\PlasgateService;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getSettings(?Request $request = null): JsonResponse
    {
        $request = $request ?? request();
        $deviceId = trim((string) ($request->header('X-Device-Id') ?? $request->query('device_id') ?? $request->input('device_id') ?? ''));
        $primaryDeviceId = Setting::get('primary_device_id', '');
        $primaryDeviceName = Setting::get('primary_device_name', 'Primary Authorized Device');
        $primaryDeviceAt = Setting::get('primary_device_at', '');
        $singleDeviceLocked = (bool) Setting::get('single_device_settings_only', true);

        $primaryDeviceIp = Setting::get('primary_device_ip', $request->ip() ?: '127.0.0.1');

        // If no primary device is registered yet and a deviceId is present, auto-claim as primary
        if (empty($primaryDeviceId) && ! empty($deviceId)) {
            $deviceName = $request->header('X-Device-Name', 'Primary Device');
            Setting::set('primary_device_id', $deviceId);
            Setting::set('primary_device_name', $deviceName);
            Setting::set('primary_device_ip', $request->ip() ?: '127.0.0.1');
            Setting::set('primary_device_at', now()->toIso8601String());
            $primaryDeviceId = $deviceId;
            $primaryDeviceName = $deviceName;
            $primaryDeviceIp = $request->ip() ?: '127.0.0.1';
            $primaryDeviceAt = now()->toIso8601String();
        }

        $isPrimaryDevice = (! empty($primaryDeviceId) && ! empty($deviceId) && $deviceId === $primaryDeviceId);
        if (empty($primaryDeviceId)) {
            $isPrimaryDevice = true;
        }
        $settingsHidden = $singleDeviceLocked && ! $isPrimaryDevice;

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

        // Telegram Device / Security Alert Group (gp) Credentials
        $telegramDeviceChatId = Setting::get('telegram_device_chat_id', '');
        $telegramDeviceBotToken = Setting::get('telegram_device_bot_token', '');
        $telegramDeviceAlertsEnabled = (bool) Setting::get('telegram_device_alerts_enabled', true);
        $telegramAlertOnNewDevice = (bool) Setting::get('telegram_alert_on_new_device', true);
        $telegramAlertOnPrimaryLogin = (bool) Setting::get('telegram_alert_on_primary_login', true);

        // Plasgate SMS Gateway Credentials (https://cloudapi.plasgate.com/api/send)
        $plasgatePrivateKey = Setting::get('plasgate_private_key', env('PLASGATE_PRIVATE_KEY', ''));
        $plasgateSecretKey = Setting::get('plasgate_secret_key', env('PLASGATE_SECRET_KEY', ''));
        $plasgateSenderId = Setting::get('plasgate_sender_id', env('PLASGATE_SENDER_ID', 'SMS Info'));
        $plasgateEnabled = (bool) Setting::get('plasgate_enabled', ! empty($plasgatePrivateKey) && ! empty($plasgateSecretKey));

        // If settings are hidden on this device, mask sensitive credentials
        $safeTelegramToken = $settingsHidden ? '••••••••••••••••••••••••••••••••' : $telegramBotToken;
        $safeTelegramChatId = $settingsHidden ? '••••••••' : $telegramChatId;
        $safeTelegramDeviceChatId = $settingsHidden ? '••••••••' : $telegramDeviceChatId;
        $safeTelegramDeviceBotToken = $settingsHidden ? '••••••••••••••••••••••••••••••••' : $telegramDeviceBotToken;
        $safeKhqrSecret = $settingsHidden ? '••••••••••••••••••••••••••••••••' : $khqrSecretKey;
        $safePlasgatePriv = $settingsHidden ? '••••••••••••••••••••••••••••••••' : $plasgatePrivateKey;
        $safePlasgateSec = $settingsHidden ? '••••••••••••••••••••••••••••••••' : $plasgateSecretKey;

        $clientIp = $request->ip() ?: '127.0.0.1';

        $settings = [
            'abaPayLink' => $settingsHidden ? '' : $abaPayLink,
            'khqrGatewayUrl' => $khqrGatewayUrl,
            'khqrProfileId' => $khqrProfileId,
            'khqrSecretKey' => $safeKhqrSecret,
            'announcementText' => $announcementText,
            'maintenanceMode' => (bool) $maintenanceMode,
            'systemStatus' => $systemStatus,
            'promoCodes' => $promoCodes,
            'telegramBotToken' => $safeTelegramToken,
            'telegramChatId' => $safeTelegramChatId,
            'telegramEnabled' => $telegramEnabled,
            'telegramDeviceChatId' => $safeTelegramDeviceChatId,
            'telegramDeviceBotToken' => $safeTelegramDeviceBotToken,
            'telegramDeviceAlertsEnabled' => $telegramDeviceAlertsEnabled,
            'telegramAlertOnNewDevice' => $telegramAlertOnNewDevice,
            'telegramAlertOnPrimaryLogin' => $telegramAlertOnPrimaryLogin,
            'plasgatePrivateKey' => $safePlasgatePriv,
            'plasgateSecretKey' => $safePlasgateSec,
            'plasgateSenderId' => $plasgateSenderId,
            'plasgateEnabled' => $plasgateEnabled,
            'singleDeviceLocked' => $singleDeviceLocked,
            'isPrimaryDevice' => $isPrimaryDevice,
            'settingsHidden' => $settingsHidden,
            'primaryDeviceName' => $primaryDeviceName,
            'primaryDeviceIp' => $primaryDeviceIp,
            'primaryDeviceAt' => $primaryDeviceAt,
            'currentDeviceId' => $deviceId,
            'clientIp' => $clientIp,
        ];

        return response()->json([
            'success' => true,
            'settings' => $settings,
            'abaPayLink' => $settingsHidden ? '' : $abaPayLink,
            'khqrGatewayUrl' => $khqrGatewayUrl,
            'khqrProfileId' => $khqrProfileId,
            'khqrSecretKey' => $safeKhqrSecret,
            'telegramBotToken' => $safeTelegramToken,
            'telegramChatId' => $safeTelegramChatId,
            'telegramEnabled' => $telegramEnabled,
            'telegramDeviceChatId' => $safeTelegramDeviceChatId,
            'telegramDeviceBotToken' => $safeTelegramDeviceBotToken,
            'telegramDeviceAlertsEnabled' => $telegramDeviceAlertsEnabled,
            'telegramAlertOnNewDevice' => $telegramAlertOnNewDevice,
            'telegramAlertOnPrimaryLogin' => $telegramAlertOnPrimaryLogin,
            'plasgatePrivateKey' => $safePlasgatePriv,
            'plasgateSecretKey' => $safePlasgateSec,
            'plasgateSenderId' => $plasgateSenderId,
            'plasgateEnabled' => $plasgateEnabled,
            'singleDeviceLocked' => $singleDeviceLocked,
            'isPrimaryDevice' => $isPrimaryDevice,
            'settingsHidden' => $settingsHidden,
            'primaryDeviceName' => $primaryDeviceName,
            'primaryDeviceIp' => $primaryDeviceIp,
            'primaryDeviceAt' => $primaryDeviceAt,
            'currentDeviceId' => $deviceId,
            'clientIp' => $clientIp,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $deviceId = trim((string) ($request->header('X-Device-Id') ?? $request->input('device_id') ?? ''));
        $primaryDeviceId = Setting::get('primary_device_id', '');
        $singleDeviceLocked = (bool) Setting::get('single_device_settings_only', true);

        if ($singleDeviceLocked && ! empty($primaryDeviceId) && ! empty($deviceId) && $deviceId !== $primaryDeviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Security Error: Settings are locked. Only your primary authorized device can view and modify platform settings.',
            ], 403);
        }

        if ($request->has('singleDeviceLocked') || $request->has('single_device_settings_only')) {
            $locked = $request->input('singleDeviceLocked', $request->input('single_device_settings_only'));
            Setting::set('single_device_settings_only', (bool) $locked);
        }

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

        // Dedicated Telegram Security / Device Alert Group Settings
        if ($request->has('telegramDeviceChatId') || $request->has('telegram_device_chat_id')) {
            $val = $request->input('telegramDeviceChatId', $request->input('telegram_device_chat_id'));
            Setting::set('telegram_device_chat_id', trim((string) $val));
        }
        if ($request->has('telegramDeviceBotToken') || $request->has('telegram_device_bot_token')) {
            $val = $request->input('telegramDeviceBotToken', $request->input('telegram_device_bot_token'));
            Setting::set('telegram_device_bot_token', trim((string) $val));
        }
        if ($request->has('telegramDeviceAlertsEnabled') || $request->has('telegram_device_alerts_enabled')) {
            $val = $request->input('telegramDeviceAlertsEnabled', $request->input('telegram_device_alerts_enabled'));
            Setting::set('telegram_device_alerts_enabled', (bool) $val);
        }
        if ($request->has('telegramAlertOnNewDevice') || $request->has('telegram_alert_on_new_device')) {
            $val = $request->input('telegramAlertOnNewDevice', $request->input('telegram_alert_on_new_device'));
            Setting::set('telegram_alert_on_new_device', (bool) $val);
        }
        if ($request->has('telegramAlertOnPrimaryLogin') || $request->has('telegram_alert_on_primary_login')) {
            $val = $request->input('telegramAlertOnPrimaryLogin', $request->input('telegram_alert_on_primary_login'));
            Setting::set('telegram_alert_on_primary_login', (bool) $val);
        }

        // Plasgate SMS Settings
        if ($request->has('plasgatePrivateKey') || $request->has('plasgate_private_key') || $request->has('private_key')) {
            $privKey = $request->input('plasgatePrivateKey', $request->input('plasgate_private_key', $request->input('private_key')));
            Setting::set('plasgate_private_key', (string) $privKey);
        }
        if ($request->has('plasgateSecretKey') || $request->has('plasgate_secret_key') || $request->has('secret_key') || $request->has('x_secret')) {
            $secKey = $request->input('plasgateSecretKey', $request->input('plasgate_secret_key', $request->input('secret_key', $request->input('x_secret'))));
            Setting::set('plasgate_secret_key', (string) $secKey);
        }
        if ($request->has('plasgateSenderId') || $request->has('plasgate_sender_id') || $request->has('sender')) {
            $sender = $request->input('plasgateSenderId', $request->input('plasgate_sender_id', $request->input('sender')));
            Setting::set('plasgate_sender_id', (string) $sender);
        }
        if ($request->has('plasgateEnabled') || $request->has('plasgate_enabled')) {
            $enabled = $request->input('plasgateEnabled', $request->input('plasgate_enabled'));
            Setting::set('plasgate_enabled', (bool) $enabled);
        }

        return $this->getSettings($request);
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

    /**
     * Test connection to Plasgate SMS Gateway with current or provided credentials.
     */
    public function testPlasgate(Request $request): JsonResponse
    {
        $phone = $request->input('phone') ?: $request->input('to') ?: $request->input('phone_number');
        if (empty($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid phone number (e.g. 012 345 678 or 855...) to test SMS sending.',
            ], 422);
        }

        $privateKey = $request->input('privateKey') ?: $request->input('private_key') ?: Setting::get('plasgate_private_key');
        $secretKey = $request->input('secretKey') ?: $request->input('secret_key') ?: Setting::get('plasgate_secret_key');
        $sender = $request->input('sender') ?: $request->input('senderId') ?: Setting::get('plasgate_sender_id', 'SMS Info');

        if (empty($privateKey) || empty($secretKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Plasgate Private Key or Secret Key is missing. Please enter them in the form before testing.',
            ], 422);
        }

        $message = 'Dyzzstore SMS Gateway Test: Your Plasgate SMS integration is connected successfully at '.now()->format('H:i:s').'!';
        $result = PlasgateService::sendSms((string) $phone, $message, (string) $privateKey, (string) $secretKey, (string) $sender);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'normalized_phone' => $result['normalized_phone'] ?? null,
                'data' => $result['data'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to send SMS via Plasgate.',
            'normalized_phone' => $result['normalized_phone'] ?? null,
            'data' => $result['data'] ?? null,
        ], 400);
    }

    /**
     * Authorize this device as the Primary Authorized Device.
     */
    public function authorizeDevice(Request $request): JsonResponse
    {
        $deviceId = trim((string) ($request->header('X-Device-Id') ?? $request->input('device_id') ?? ''));
        $deviceName = trim((string) ($request->header('X-Device-Name') ?? $request->input('device_name') ?? 'This Device'));
        $primaryDeviceId = Setting::get('primary_device_id', '');
        $singleDeviceLocked = (bool) Setting::get('single_device_settings_only', true);

        if (empty($deviceId)) {
            return response()->json([
                'success' => false,
                'message' => 'Device ID is required to authorize device.',
            ], 422);
        }

        // Security check: If already locked and not the existing primary device, reject unless authenticated
        if ($singleDeviceLocked && ! empty($primaryDeviceId) && $deviceId !== $primaryDeviceId && ! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Security Error: Platform is locked to another authorized primary device. Settings can only be unlocked from your registered device.',
            ], 403);
        }

        Setting::set('primary_device_id', $deviceId);
        Setting::set('primary_device_name', $deviceName);
        Setting::set('primary_device_ip', $request->ip() ?: '127.0.0.1');
        Setting::set('primary_device_at', now()->toIso8601String());

        TelegramService::sendDeviceAuthAlert([
            'device_name' => $deviceName,
            'device_id' => $deviceId,
            'ip_address' => $request->ip(),
            'time' => now()->format('Y-m-d H:i:s').' UTC',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Device '{$deviceName}' is now registered as the Primary Authorized Device. Settings are unlocked exclusively on this device.",
            'primary_device_id' => $deviceId,
            'primary_device_name' => $deviceName,
            'primary_device_ip' => $request->ip() ?: '127.0.0.1',
        ]);
    }

    /**
     * Test sending new device login alert to Telegram.
     */
    public function testDeviceAlert(Request $request): JsonResponse
    {
        $deviceId = trim((string) ($request->header('X-Device-Id') ?? $request->input('device_id') ?? 'dev_demo_test_8877'));
        $deviceName = trim((string) ($request->header('X-Device-Name') ?? $request->input('device_name') ?? 'MacBook Pro • Safari (Demo Device)'));

        $res = TelegramService::sendLoginAlert([
            'user_email' => 'admin@dyzzstore.com',
            'user_name' => 'Security Test',
            'device_name' => $deviceName,
            'device_id' => $deviceId,
            'ip_address' => $request->ip() ?: '127.0.0.1',
            'user_agent' => $request->userAgent() ?: 'Demo Browser',
            'is_another_device' => true,
            'settings_hidden' => true,
            'time' => now()->format('Y-m-d H:i:s').' UTC',
        ]);

        if ($res['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Test unauthorized device login alert dispatched to Telegram successfully!',
                'data' => $res['data'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $res['message'] ?? 'Failed to send alert via Telegram Bot API.',
        ], 400);
    }

    /**
     * Direct test connection to Telegram Security Alert Group (gp).
     */
    public function testDeviceGroupAlert(Request $request): JsonResponse
    {
        $customChatId = $request->input('chat_id') ?: $request->input('telegramDeviceChatId') ?: $request->input('group_id');
        $customToken = $request->input('bot_token') ?: $request->input('telegramDeviceBotToken') ?: $request->input('token');

        $res = TelegramService::sendDeviceGroupTestAlert($customChatId, $customToken);

        if ($res['success']) {
            return response()->json([
                'success' => true,
                'message' => $res['message'],
                'target_chat_id' => $res['target_chat_id'] ?? null,
                'data' => $res['data'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $res['message'] ?? 'Failed to send test message to Telegram Group.',
        ], 400);
    }
}

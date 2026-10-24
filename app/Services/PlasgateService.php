<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlasgateService
{
    public const DEFAULT_BASE_URL = 'https://cloudapi.plasgate.com/api/send';

    public const FALLBACK_BASE_URL = 'https://cloudapi.plasgate.com/rest/send';

    /**
     * Normalize Cambodian or international phone numbers to E.164 without '+'
     * Examples:
     *   "012 345 678" -> "85512345678"
     *   "+855 98 765 432" -> "85598765432"
     *   "85512345678" -> "85512345678"
     */
    public static function normalizePhoneNumber(string $phone): string
    {
        // Strip everything except digits
        $digits = preg_replace('/\D/', '', $phone);

        // If starts with leading '0', replace with '855' (Cambodia country code)
        if (str_starts_with($digits, '0')) {
            $digits = '855'.substr($digits, 1);
        }

        // If local number without 0 (e.g., 8 or 9 digits like 12345678 or 98765432)
        if (! str_starts_with($digits, '855') && (strlen($digits) === 8 || strlen($digits) === 9)) {
            $digits = '855'.$digits;
        }

        return $digits;
    }

    /**
     * Send an SMS using Plasgate Cloud API
     *
     * @param  string  $to  Recipient phone number (local or international)
     * @param  string  $content  SMS message text content
     * @param  string|null  $privateKey  Optional private_key (defaults to setting/env)
     * @param  string|null  $secretKey  Optional X-Secret (defaults to setting/env)
     * @param  string|null  $sender  Optional sender ID (defaults to setting/env or 'SMS Info')
     * @return array{success: bool, message: string, data?: mixed, normalized_phone?: string}
     */
    public static function sendSms(
        string $to,
        string $content,
        ?string $privateKey = null,
        ?string $secretKey = null,
        ?string $sender = null
    ): array {
        $privateKey = $privateKey ?: Setting::get('plasgate_private_key', env('PLASGATE_PRIVATE_KEY'));
        $secretKey = $secretKey ?: Setting::get('plasgate_secret_key', env('PLASGATE_SECRET_KEY'));
        $senderId = $sender ?: Setting::get('plasgate_sender_id', env('PLASGATE_SENDER_ID', 'SMS Info'));

        if (empty($privateKey) || empty($secretKey)) {
            return [
                'success' => false,
                'message' => 'Plasgate Private Key or Secret Key is not configured in Admin Dashboard Settings.',
            ];
        }

        $normalizedPhone = self::normalizePhoneNumber($to);
        if (strlen($normalizedPhone) < 9) {
            return [
                'success' => false,
                'message' => "Invalid phone number format: '{$to}'. Please enter a valid mobile number.",
            ];
        }

        // Target URL with private_key query parameter
        $url = self::DEFAULT_BASE_URL.'?private_key='.urlencode(trim((string) $privateKey));

        $payload = [
            'sender' => trim((string) $senderId) ?: 'SMS Info',
            'to' => $normalizedPhone,
            'content' => $content,
        ];

        try {
            $response = Http::timeout(15)
                ->withoutVerifying()
                ->withHeaders([
                    'X-Secret' => trim((string) $secretKey),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            $responseData = $response->json();

            // Try fallback URL if main returns 404
            if ($response->status() === 404) {
                $fallbackUrl = self::FALLBACK_BASE_URL.'?private_key='.urlencode(trim((string) $privateKey));
                $response = Http::timeout(15)
                    ->withoutVerifying()
                    ->withHeaders([
                        'X-Secret' => trim((string) $secretKey),
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])
                    ->post($fallbackUrl, $payload);
                $responseData = $response->json();
            }

            if ($response->successful()) {
                Log::info('Plasgate SMS delivered successfully to '.$normalizedPhone);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully to '.$normalizedPhone,
                    'normalized_phone' => $normalizedPhone,
                    'data' => $responseData,
                ];
            }

            $errorMessage = $responseData['message']
                ?? $responseData['error']
                ?? ('Plasgate API error (HTTP '.$response->status().')');

            Log::warning("Plasgate SMS error to {$normalizedPhone}: {$errorMessage}", [
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message' => $errorMessage,
                'status' => $response->status(),
                'normalized_phone' => $normalizedPhone,
                'data' => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error('Plasgate SMS exception: '.$e->getMessage(), [
                'phone' => $normalizedPhone,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to connect to Plasgate SMS Gateway: '.$e->getMessage(),
                'normalized_phone' => $normalizedPhone,
            ];
        }
    }

    /**
     * Send OTP verification code via Plasgate SMS
     */
    public static function sendOtp(
        string $phone,
        string $otp,
        ?string $privateKey = null,
        ?string $secretKey = null,
        ?string $sender = null
    ): array {
        $content = "Dyzzstore: Your verification code is {$otp}. Valid for 10 minutes. Do not share this code.";

        return self::sendSms($phone, $content, $privateKey, $secretKey, $sender);
    }
}

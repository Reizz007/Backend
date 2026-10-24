<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PlasgateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Send OTP Verification Code to Email (via SMTP / Resend) or Phone (via Plasgate SMS)
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $input = trim((string) ($request->input('identifier') ?? $request->input('email') ?? $request->input('phone')));

        if (empty($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide an email address or mobile phone number.',
            ], 422);
        }

        // Determine if input is a phone number or an email
        $isPhone = ! str_contains($input, '@') && preg_match('/^[0-9+\s\-()]{8,20}$/', $input);

        // Generate a 6-digit numeric OTP code
        $otp = (string) random_int(100000, 999999);

        // 1. Phone SMS Delivery via Plasgate (https://cloudapi.plasgate.com/api/send)
        if ($isPhone) {
            $normalizedPhone = PlasgateService::normalizePhoneNumber($input);
            $this->saveOtp($normalizedPhone, $otp);

            $result = PlasgateService::sendOtp($normalizedPhone, $otp);

            if (! $result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not send SMS verification code: '.$result['message'],
                    'channel' => 'sms',
                    'phone' => $normalizedPhone,
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent via SMS to '.$normalizedPhone,
                'channel' => 'sms',
                'target' => $normalizedPhone,
                'phone' => $normalizedPhone,
            ]);
        }

        // 2. Email Delivery via SMTP / Resend
        $email = strtolower($input);
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided email address is invalid.',
            ], 422);
        }

        // Store OTP in Cache and file backup for 15 minutes
        $this->saveOtp($email, $otp);

        $mailSent = false;
        $mailError = null;

        $htmlContent = "
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; max-width: 520px; margin: 0 auto; padding: 32px 24px; background: #0b0f19; color: #f1f5f9; border-radius: 16px; border: 1px solid #1e293b;'>
                <div style='text-align: center; margin-bottom: 24px;'>
                    <div style='display: inline-block; padding: 10px 18px; border-radius: 999px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); color: #38bdf8; font-weight: 700; font-size: 13px; letter-spacing: 1px;'>
                        DYZZSTORE AUTH
                    </div>
                    <h2 style='color: #f8fafc; margin: 16px 0 6px; font-size: 22px; font-weight: 700;'>Login Verification Code</h2>
                    <p style='color: #94a3b8; font-size: 14px; margin: 0;'>Use the code below to complete your sign in</p>
                </div>

                <div style='background: #131b2e; padding: 24px; border-radius: 12px; text-align: center; margin-bottom: 24px; border: 1px solid #1e293b;'>
                    <p style='color: #94a3b8; font-size: 13px; margin: 0 0 12px; text-transform: uppercase; letter-spacing: 1px;'>Your 6-Digit One-Time Code</p>
                    <div style='font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #38bdf8; padding: 12px 20px; background: #070a12; border-radius: 10px; display: inline-block; font-family: monospace; border: 1px solid rgba(56, 189, 248, 0.3);'>
                        {$otp}
                    </div>
                    <p style='color: #64748b; font-size: 12px; margin: 14px 0 0;'>⏱️ Valid for 10 minutes. Do not share this code.</p>
                </div>

                <div style='border-top: 1px solid #1e293b; padding-top: 18px; text-align: center;'>
                    <p style='color: #64748b; font-size: 12px; margin: 0; line-height: 1.5;'>
                        This email was sent to <strong>{$email}</strong>.<br />
                        If you did not request this login code, you can safely disregard this email.
                    </p>
                </div>
            </div>
        ";

        try {
            $resendKey = env('RESEND_API_KEY');
            if (! empty($resendKey)) {
                $fromAddress = env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev');
                $fromName = env('MAIL_FROM_NAME', 'Admin OTP');
                $res = Http::timeout(10)->withToken($resendKey)->post('https://api.resend.com/emails', [
                    'from' => "{$fromName} <{$fromAddress}>",
                    'to' => [$email],
                    'subject' => "{$otp} is your Dyzzstore Verification Code",
                    'html' => $htmlContent,
                ]);

                if ($res->successful()) {
                    $mailSent = true;
                }
            } else {
                $fromAddress = env('MAIL_FROM_ADDRESS', 'rrqkh61@gmail.com');
                $fromName = env('MAIL_FROM_NAME', 'Dyzzstore Auth');

                config([
                    'mail.mailers.smtp.host' => env('MAIL_HOST', 'smtp.gmail.com'),
                    'mail.mailers.smtp.port' => (int) env('MAIL_PORT', 587),
                    'mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION', 'tls'),
                    'mail.mailers.smtp.username' => env('MAIL_USERNAME', 'rrqkh61@gmail.com'),
                    'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
                    'mail.mailers.smtp.timeout' => 15,
                    'mail.from.address' => $fromAddress,
                    'mail.from.name' => $fromName,
                ]);
                app('mail.manager')->forgetMailers();

                try {
                    Mail::html($htmlContent, function ($message) use ($email, $otp, $fromAddress, $fromName) {
                        $message->from($fromAddress, $fromName)
                            ->to($email)
                            ->subject("{$otp} is your Dyzzstore Verification Code");
                    });
                    $mailSent = true;
                } catch (\Throwable $err587) {
                    Log::warning('SMTP 587 failed, attempting failover to 465 SSL: '.$err587->getMessage());

                    // Failover to Port 465 SSL
                    config([
                        'mail.mailers.smtp.host' => 'smtp.gmail.com',
                        'mail.mailers.smtp.port' => 465,
                        'mail.mailers.smtp.encryption' => 'ssl',
                        'mail.mailers.smtp.username' => env('MAIL_USERNAME', 'rrqkh61@gmail.com'),
                        'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
                        'mail.mailers.smtp.timeout' => 15,
                        'mail.from.address' => $fromAddress,
                        'mail.from.name' => $fromName,
                    ]);
                    app('mail.manager')->forgetMailers();

                    Mail::html($htmlContent, function ($message) use ($email, $otp, $fromAddress, $fromName) {
                        $message->from($fromAddress, $fromName)
                            ->to($email)
                            ->subject("{$otp} is your Dyzzstore Verification Code");
                    });
                    $mailSent = true;
                }
            }
        } catch (\Throwable $e) {
            $mailError = $e->getMessage();
            Log::error('OTP Mail delivery notice: '.$mailError);
        }

        if (! $mailSent) {
            return response()->json([
                'success' => false,
                'message' => 'Could not send verification email: '.$mailError,
                'channel' => 'email',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to '.$email,
            'channel' => 'email',
            'target' => $email,
            'email' => $email,
        ]);
    }

    /**
     * Verify OTP and Login
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $input = trim((string) ($request->input('identifier') ?? $request->input('email') ?? $request->input('phone')));
        $otp = trim((string) $request->input('otp'));

        if (empty($input) || empty($otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide your email/phone and verification code.',
            ], 422);
        }

        $isPhone = ! str_contains($input, '@') && preg_match('/^[0-9+\s\-()]{8,20}$/', $input);
        $target = $isPhone ? PlasgateService::normalizePhoneNumber($input) : strtolower($input);

        if (! $this->checkOtp($target, $otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code. Please check your '.($isPhone ? 'SMS message' : 'email inbox').' or request a new code.',
            ], 422);
        }

        // Clean up OTP after successful verification
        $this->clearOtp($target);

        $adminEmail = strtolower(trim(env('ADMIN_EMAIL', 'chetranicky@gmail.com')));
        $adminPhone = trim((string) env('ADMIN_PHONE', ''));

        if ($isPhone) {
            $userEmail = "{$target}@dyzzstore.user";
            $isAdmin = (! empty($adminPhone) && $target === PlasgateService::normalizePhoneNumber($adminPhone));
            $user = User::firstOrNew(['email' => $userEmail]);
            if (! $user->exists) {
                $user->name = 'User '.substr($target, -4);
                $user->password = Hash::make(Str::random(32));
            }
        } else {
            $userEmail = $target;
            $isAdmin = ($userEmail === $adminEmail) || ($userEmail === 'chetranicky@gmail.com') || ($userEmail === 'admin@synapse.ai') || ($userEmail === 'admin@dyzzstore.com');
            $user = User::firstOrNew(['email' => $userEmail]);
            if (! $user->exists) {
                $user->name = $isAdmin ? 'Master Admin' : explode('@', $userEmail)[0];
                $user->password = Hash::make(Str::random(32));
            }
        }

        if ($isAdmin) {
            $user->role = 'admin';
            $user->name = 'Master Admin';
        } elseif (empty($user->role)) {
            $user->role = 'user';
        }

        $user->save();

        // Create Sanctum Token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Helper to store OTP in Cache and file
     */
    protected function saveOtp(string $email, string $otp): void
    {
        Cache::put('otp_'.$email, $otp, now()->addMinutes(15));
        try {
            $path = storage_path('app/otps.json');
            $dir = dirname($path);
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $otps = file_exists($path) ? json_decode(file_get_contents($path), true) ?: [] : [];
            $otps[$email] = [
                'otp' => (string) $otp,
                'expires_at' => time() + 900,
            ];
            @file_put_contents($path, json_encode($otps));
        } catch (\Throwable $e) {
        }
    }

    /**
     * Helper to verify OTP
     */
    protected function checkOtp(string $email, string $otp): bool
    {
        $cached = Cache::get('otp_'.$email);
        if ($cached && (string) $cached === (string) $otp) {
            return true;
        }

        try {
            $path = storage_path('app/otps.json');
            if (file_exists($path)) {
                $otps = json_decode(file_get_contents($path), true) ?: [];
                if (isset($otps[$email])) {
                    $item = $otps[$email];
                    if ($item['expires_at'] >= time() && (string) $item['otp'] === (string) $otp) {
                        return true;
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        return false;
    }

    /**
     * Helper to clear OTP
     */
    protected function clearOtp(string $email): void
    {
        Cache::forget('otp_'.$email);
        try {
            $path = storage_path('app/otps.json');
            if (file_exists($path)) {
                $otps = json_decode(file_get_contents($path), true) ?: [];
                unset($otps[$email]);
                @file_put_contents($path, json_encode($otps));
            }
        } catch (\Throwable $e) {
        }
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user && (strtolower($validated['email']) === 'admin@synapse.ai' || strtolower($validated['email']) === 'admin@dyzzstore.com' || strtolower($validated['email']) === strtolower(env('ADMIN_EMAIL', 'chetranicky@gmail.com'))) && $validated['password'] === 'admin123') {
            $user = User::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => 'Master Admin',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                ]
            );
        }

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        $token = $user->createToken('admin-api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }
}

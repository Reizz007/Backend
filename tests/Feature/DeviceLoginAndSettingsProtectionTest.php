<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeviceLoginAndSettingsProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 123]], 200),
            'https://api.resend.com/*' => Http::response(['id' => 'res_123'], 200),
        ]);
        Setting::set('telegram_bot_token', 'fake-bot-token');
        Setting::set('telegram_chat_id', '123456789');
        Setting::set('telegram_enabled', '1');
    }

    public function test_send_otp_succeeds_without_timeout(): void
    {
        $response = $this->postJson('/api/auth/send-otp', [
            'email' => 'chetranicky@gmail.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_device_login_detects_primary_and_another_device(): void
    {
        $user = User::factory()->create([
            'email' => 'chetranicky@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // 1. Primary device logs in
        $response1 = $this->withHeaders([
            'X-Device-Id' => 'dev_primary_001',
            'X-Device-Name' => 'Primary Laptop • Chrome',
        ])->postJson('/api/auth/login', [
            'email' => 'chetranicky@gmail.com',
            'password' => 'admin123',
        ]);

        $response1->assertStatus(200)
            ->assertJsonPath('device.is_primary', true)
            ->assertJsonPath('device.is_another_device', false)
            ->assertJsonPath('device.settings_hidden', false);

        // 2. Another device logs in (e.g. unknown phone)
        $response2 = $this->withHeaders([
            'X-Device-Id' => 'dev_phone_999',
            'X-Device-Name' => 'iPhone (iOS) • Safari',
        ])->postJson('/api/auth/login', [
            'email' => 'chetranicky@gmail.com',
            'password' => 'admin123',
        ]);

        $response2->assertStatus(200)
            ->assertJsonPath('device.is_primary', false)
            ->assertJsonPath('device.is_another_device', true)
            ->assertJsonPath('device.settings_hidden', true);
    }

    public function test_settings_are_hidden_on_another_device(): void
    {
        Setting::set('primary_device_id', 'dev_primary_001');
        Setting::set('single_device_settings_only', '1');
        Setting::set('khqr_secret_key', 'super-secret-key-123');

        // Request from primary device: can view secret key
        $primaryResp = $this->withHeaders([
            'X-Device-Id' => 'dev_primary_001',
        ])->getJson('/api/settings');

        $primaryResp->assertStatus(200)
            ->assertJsonPath('settings.settingsHidden', false)
            ->assertJsonPath('settings.isPrimaryDevice', true)
            ->assertJsonPath('settings.khqrSecretKey', 'super-secret-key-123');

        // Request from another device: secret key is masked and settings are marked hidden
        $otherResp = $this->withHeaders([
            'X-Device-Id' => 'dev_another_device_002',
        ])->getJson('/api/settings');

        $otherResp->assertStatus(200)
            ->assertJsonPath('settings.settingsHidden', true)
            ->assertJsonPath('settings.isPrimaryDevice', false);

        $this->assertStringContainsString('•••', $otherResp->json('settings.khqrSecretKey'));
    }

    public function test_unauthorized_device_cannot_update_settings(): void
    {
        Setting::set('primary_device_id', 'dev_primary_001');
        Setting::set('single_device_settings_only', '1');

        $updateResp = $this->withHeaders([
            'X-Device-Id' => 'dev_hacker_or_another_phone',
        ])->putJson('/api/settings', [
            'announcementText' => 'Hacked Announcement',
        ]);

        $updateResp->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_device_reauthorization_endpoint(): void
    {
        $response = $this->postJson('/api/settings/authorize-device', [
            'device_id' => 'dev_new_authorized_laptop',
            'device_name' => 'Owner New Laptop',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'primary_device_id' => 'dev_new_authorized_laptop',
            ]);

        $this->assertEquals('dev_new_authorized_laptop', Setting::get('primary_device_id'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\TopupGame;
use App\Models\TopupPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopupFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_topup_catalog(): void
    {
        $game = TopupGame::create([
            'name' => 'Mobile Legends Test',
            'code' => 'mlbb_test',
            'is_active' => true,
        ]);

        TopupPackage::create([
            'topup_game_id' => $game->id,
            'name' => '55 Diamonds Test',
            'diamond_amount' => 55,
            'price' => 0.88,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/topup/games');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_can_create_topup_order(): void
    {
        $game = TopupGame::create([
            'name' => 'Mobile Legends Test',
            'code' => 'mlbb_order_test',
            'is_active' => true,
        ]);

        $package = TopupPackage::create([
            'topup_game_id' => $game->id,
            'name' => '86 Diamonds Test',
            'diamond_amount' => 86,
            'price' => 1.30,
            'sku' => '89',
            'is_active' => true,
        ]);

        $payload = [
            'game_code' => $game->code,
            'package_id' => $package->id,
            'player_id' => '123456789',
            'zone_id' => '1234',
            'player_username' => 'ProPlayer',
            'payment_method' => 'payway',
        ];

        $response = $this->postJson('/api/topup/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'order' => ['id', 'order_no', 'amount', 'diamond_amount', 'status'],
                'checkout_url',
            ]);

        $this->assertDatabaseHas('topup_orders', [
            'player_id' => '123456789',
            'zone_id' => '1234',
            'player_username' => 'ProPlayer',
            'topup_game_id' => $game->id,
        ]);
    }

    public function test_admin_can_create_and_update_package(): void
    {
        $game = TopupGame::create([
            'name' => 'Mobile Legends',
            'code' => 'mlbb_admin_test',
            'is_active' => true,
        ]);

        $payload = [
            'game_id' => $game->id,
            'name' => '86 Diamonds Test',
            'diamond_amount' => 86,
            'price' => 1.30,
            'sku' => '89',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/admin/topup/packages', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('package.name', '86 Diamonds Test')
            ->assertJsonPath('package.diamond_amount', 86)
            ->assertJsonPath('package.price', '1.30')
            ->assertJsonPath('package.sku', '89')
            ->assertJsonPath('package.is_active', true);

        $this->assertDatabaseHas('topup_packages', [
            'topup_game_id' => $game->id,
            'name' => '86 Diamonds Test',
            'diamond_amount' => 86,
            'price' => 1.30,
            'sku' => '89',
            'is_active' => 1,
        ]);

        $packageId = $response->json('package.id');

        $updateResponse = $this->patchJson("/api/admin/topup/packages/{$packageId}", [
            'price' => 1.25,
            'sku' => 'MOBILE_LEGENDS_86_DIAMONDS',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('package.price', '1.25')
            ->assertJsonPath('package.sku', 'MOBILE_LEGENDS_86_DIAMONDS');

        $this->assertDatabaseHas('topup_packages', [
            'id' => $packageId,
            'price' => 1.25,
            'sku' => 'MOBILE_LEGENDS_86_DIAMONDS',
        ]);
    }
}

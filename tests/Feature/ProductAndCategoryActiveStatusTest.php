<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAndCategoryActiveStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_created_with_active_and_inactive_status(): void
    {
        $responseActive = $this->postJson('/api/categories', [
            'name' => 'Vision AI',
            'status' => 'Active',
        ]);

        $responseActive->assertStatus(201)
            ->assertJsonPath('category.is_active', true)
            ->assertJsonPath('category.status', 'Active');

        $responseInactive = $this->postJson('/api/categories', [
            'name' => 'Legacy Models',
            'status' => 'Inactive',
        ]);

        $responseInactive->assertStatus(201)
            ->assertJsonPath('category.is_active', false)
            ->assertJsonPath('category.status', 'Inactive');
    }

    public function test_category_can_be_updated_to_inactive_and_active(): void
    {
        $category = Category::create([
            'name' => 'Audio Tools',
            'slug' => 'audio-tools',
            'is_active' => true,
        ]);

        $updateResponse = $this->putJson("/api/categories/{$category->id}", [
            'status' => 'Inactive',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('category.is_active', false)
            ->assertJsonPath('category.status', 'Inactive');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);

        // Toggle back to active
        $this->putJson("/api/categories/{$category->id}", [
            'is_active' => true,
        ])->assertStatus(200)
            ->assertJsonPath('category.is_active', true)
            ->assertJsonPath('category.status', 'Active');
    }

    public function test_category_index_can_filter_by_status(): void
    {
        Category::create(['name' => 'Active Cat', 'slug' => 'active-cat', 'is_active' => true]);
        Category::create(['name' => 'Inactive Cat', 'slug' => 'inactive-cat', 'is_active' => false]);

        $allResponse = $this->getJson('/api/categories');
        $allResponse->assertStatus(200);
        $this->assertCount(2, $allResponse->json('categories'));

        $activeOnlyResponse = $this->getJson('/api/categories?status=active');
        $activeOnlyResponse->assertStatus(200);
        $this->assertCount(1, $activeOnlyResponse->json('categories'));
        $this->assertEquals('Active Cat', $activeOnlyResponse->json('categories.0.name'));

        $inactiveOnlyResponse = $this->getJson('/api/categories?status=inactive');
        $inactiveOnlyResponse->assertStatus(200);
        $this->assertCount(1, $inactiveOnlyResponse->json('categories'));
        $this->assertEquals('Inactive Cat', $inactiveOnlyResponse->json('categories.0.name'));
    }

    public function test_product_can_be_created_with_active_and_inactive_status(): void
    {
        $responseActive = $this->postJson('/api/products', [
            'name' => 'Omni LLM Turbo',
            'status' => 'Active',
            'price_monthly' => 29,
            'price_lifetime' => 199,
        ]);

        $responseActive->assertStatus(201)
            ->assertJsonPath('product.status', 'Active')
            ->assertJsonPath('product.is_active', true);

        $responseInactive = $this->postJson('/api/products', [
            'name' => 'Deprecated LLM v1',
            'status' => 'Inactive',
            'price_monthly' => 19,
            'price_lifetime' => 99,
        ]);

        $responseInactive->assertStatus(201)
            ->assertJsonPath('product.status', 'Inactive')
            ->assertJsonPath('product.is_active', false);
    }

    public function test_product_can_be_updated_to_inactive_and_active(): void
    {
        $product = Product::create([
            'name' => 'Test Agent X',
            'slug' => 'test-agent-x',
            'product_id' => 'test-agent-x',
            'status' => 'Active',
            'is_active' => true,
        ]);

        $updateResponse = $this->putJson("/api/products/{$product->id}", [
            'status' => 'Inactive',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('product.status', 'Inactive')
            ->assertJsonPath('product.is_active', false);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'Inactive',
            'is_active' => false,
        ]);

        // Toggle back via is_active = true
        $this->putJson("/api/products/{$product->id}", [
            'is_active' => true,
        ])->assertStatus(200)
            ->assertJsonPath('product.status', 'Active')
            ->assertJsonPath('product.is_active', true);
    }

    public function test_product_index_can_filter_by_status(): void
    {
        Product::create([
            'name' => 'Active Neural Engine',
            'slug' => 'active-neural-engine',
            'product_id' => 'active-neural-engine',
            'status' => 'Active',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Inactive Beta Model',
            'slug' => 'inactive-beta-model',
            'product_id' => 'inactive-beta-model',
            'status' => 'Inactive',
            'is_active' => false,
        ]);

        $allResponse = $this->getJson('/api/products');
        $allResponse->assertStatus(200);
        $this->assertCount(2, $allResponse->json('products'));

        $activeOnlyResponse = $this->getJson('/api/products?status=active');
        $activeOnlyResponse->assertStatus(200);
        $this->assertCount(1, $activeOnlyResponse->json('products'));
        $this->assertEquals('Active Neural Engine', $activeOnlyResponse->json('products.0.name'));

        $inactiveOnlyResponse = $this->getJson('/api/products?status=inactive');
        $inactiveOnlyResponse->assertStatus(200);
        $this->assertCount(1, $inactiveOnlyResponse->json('products'));
        $this->assertEquals('Inactive Beta Model', $inactiveOnlyResponse->json('products.0.name'));
    }
}

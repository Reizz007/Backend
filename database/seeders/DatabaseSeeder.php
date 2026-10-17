<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@synapse.ai'],
            [
                'name' => 'Chief Platform Architect',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 2. Create Categories
        $categoriesData = [
           
        ];

        $categoriesMap = [];
        foreach ($categoriesData as $cData) {
            $cat = Category::updateOrCreate(['slug' => $cData['slug']], $cData);
            $categoriesMap[$cData['slug']] = $cat->id;
        }

        // 3. Create Products / AI Tools
        $tools = [
           
        ];

        foreach ($tools as $tool) {
            $catId = $categoriesMap[$tool['category_slug']] ?? null;
            $tool['category_id'] = $catId;
            Product::updateOrCreate(['slug' => $tool['slug']], $tool);
        }

       

        // 5. Seed Initial Orders
        $orders = [
           
        ];

        foreach ($orders as $ord) {
            Order::updateOrCreate(['order_number' => $ord['order_number']], $ord);
        }

        // 6. Seed Initial Subscribers
        $subscribers = [
            
           
        ];

        foreach ($subscribers as $sub) {
            Subscriber::updateOrCreate(['email' => $sub['email']], $sub);
        }
    }
}

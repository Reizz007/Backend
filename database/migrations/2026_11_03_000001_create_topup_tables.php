<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('topup_games')) {
            Schema::create('topup_games', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('api_game_id')->nullable();
                $table->string('code', 191)->unique();
                $table->string('name');
                $table->string('icon_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('topup_packages')) {
            Schema::create('topup_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('topup_game_id')->constrained('topup_games')->cascadeOnDelete();
                $table->string('name');
                $table->unsignedInteger('diamond_amount');
                $table->decimal('price', 12, 2);
                $table->string('sku', 191)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('topup_orders')) {
            Schema::create('topup_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_no', 191)->unique();
                $table->foreignId('topup_game_id')->constrained('topup_games')->restrictOnDelete();
                $table->foreignId('topup_package_id')->constrained('topup_packages')->restrictOnDelete();
                $table->string('player_id');
                $table->string('player_username', 191)->nullable();
                $table->string('zone_id')->nullable();
                $table->string('payment_method')->default('payway');
                $table->decimal('amount', 12, 2);
                $table->unsignedInteger('diamond_amount');
                $table->string('status')->default('pending');
                $table->string('gateway_transaction_id', 191)->nullable()->unique();
                $table->text('gateway_checkout_url')->nullable();
                $table->string('gateway_hash')->nullable();
                $table->json('gateway_payload')->nullable();
                $table->string('supplier_order_id')->nullable();
                $table->json('supplier_payload')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('processing_at')->nullable();
                $table->timestamp('success_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->text('failure_reason')->nullable();
                $table->timestamps();
            });
        }

        // Copy existing catalog from dyzzstore if available
        try {
            $hasDyzzstoreGames = DB::select("SHOW TABLES FROM dyzzstore LIKE 'topup_games'");
            if (! empty($hasDyzzstoreGames) && DB::table('topup_games')->count() === 0) {
                DB::statement('INSERT IGNORE INTO topup_games (id, api_game_id, code, name, is_active, created_at, updated_at) SELECT id, api_game_id, code, name, is_active, created_at, updated_at FROM dyzzstore.topup_games');
                DB::statement('INSERT IGNORE INTO topup_packages (id, topup_game_id, name, diamond_amount, price, sku, sort_order, is_active, created_at, updated_at) SELECT id, topup_game_id, name, diamond_amount, price, sku, sort_order, is_active, created_at, updated_at FROM dyzzstore.topup_packages');
                DB::statement('INSERT IGNORE INTO topup_orders (id, order_no, topup_game_id, topup_package_id, player_id, player_username, zone_id, payment_method, amount, diamond_amount, status, gateway_transaction_id, gateway_checkout_url, gateway_hash, gateway_payload, supplier_order_id, supplier_payload, paid_at, processing_at, success_at, failed_at, failure_reason, created_at, updated_at) SELECT id, order_no, topup_game_id, topup_package_id, player_id, player_username, zone_id, payment_method, amount, diamond_amount, status, gateway_transaction_id, gateway_checkout_url, gateway_hash, gateway_payload, supplier_order_id, supplier_payload, paid_at, processing_at, success_at, failed_at, failure_reason, created_at, updated_at FROM dyzzstore.topup_orders');
            }
        } catch (Throwable $e) {
            // Fail gracefully if cross-database permission is not allowed
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_orders');
        Schema::dropIfExists('topup_packages');
        Schema::dropIfExists('topup_games');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_email')->nullable()->index();
            $table->string('action', 50)->index(); // CREATE, UPDATE, DELETE, BULK_DELETE, SETTINGS, AUTH
            $table->string('entity_type', 50)->index(); // Product, Category, Order, Subscriber, Setting, Auth
            $table->string('entity_id', 100)->nullable()->index();
            $table->string('entity_name')->nullable();
            $table->text('description');
            $table->string('device_id')->nullable()->index();
            $table->string('device_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('changes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

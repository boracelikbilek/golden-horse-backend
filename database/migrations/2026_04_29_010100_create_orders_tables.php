<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bayi_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['preparing', 'ready', 'completed', 'cancelled'])->default('completed');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('currency', 3)->default('TRY');
            $table->unsignedInteger('stars_earned')->default(0);
            $table->text('note')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
            $table->index(['bayi_id', 'created_at']);
            $table->index(['store_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('line_total', 10, 2);
            $table->json('modifiers')->nullable();
            $table->timestamps();
        });

        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['earn', 'spend', 'adjust', 'reward']);
            $table->integer('points');
            $table->integer('balance_after');
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'user_id']);
        });

        Schema::create('qr_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_by_cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('customer_tenant_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('tier', ['green', 'gold'])->default('green');
            $table->unsignedInteger('stars')->default(0);
            $table->unsignedInteger('star_target')->default(150);
            $table->unsignedInteger('reward_drinks_available')->default(0);
            $table->unsignedInteger('lifetime_orders')->default(0);
            $table->decimal('lifetime_spent', 12, 2)->default(0);
            $table->timestamp('last_order_at')->nullable();
            $table->foreignId('favorite_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_tenant_stats');
        Schema::dropIfExists('qr_sessions');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

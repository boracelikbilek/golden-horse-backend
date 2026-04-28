<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('TRY');
            $table->string('image')->nullable();
            $table->boolean('is_new')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('calories')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('star_reward')->default(1);
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('product_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->enum('type', ['single', 'multi'])->default('single');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_modifier_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_modifier_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->decimal('price_delta', 10, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bayi_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('phone')->nullable();
            $table->string('opening_time')->nullable();
            $table->string('closing_time')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('tags')->nullable();
            $table->boolean('coming_soon')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image')->nullable();
            $table->json('gradient')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('reward_text')->nullable();
            $table->string('cta_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('stars_required')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('badge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at')->useCurrent();
            $table->unique(['user_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('product_modifier_options');
        Schema::dropIfExists('product_modifiers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};

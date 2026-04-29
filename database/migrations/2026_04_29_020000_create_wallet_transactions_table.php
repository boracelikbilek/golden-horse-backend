<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bayi_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();

            // topup: kasiyer TL yukledi (+)
            // purchase: musteri harcadi (-)  (sadece bakiyeden odeme yapildiysa balance'i etkiler)
            // refund: iade (+)
            // adjust: manuel duzeltme (+/-)
            $table->enum('type', ['topup', 'purchase', 'refund', 'adjust']);

            // signed: + credit, - debit
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);

            // Eger siparis kismen/tamamen bakiyeden odendiyse, ne kadarinin bakiyeden odendigini goruyoruz
            // (orders.total ile karistirmamak icin ayri tutuluyor)
            $table->string('currency', 3)->default('TRY');

            $table->string('reason')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'user_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['store_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

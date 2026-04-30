<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // order_items.order_id -> her siparis detayi yuklemesinde Seq Scan oluyordu
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'order_items_order_id_index');
            $table->index(['product_id', 'created_at'], 'order_items_product_id_created_at_index');
        });

        // point_transactions: timeline ve analitik icin
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'point_transactions_user_id_created_at_index');
            $table->index(['tenant_id', 'created_at'], 'point_transactions_tenant_id_created_at_index');
        });

        // qr_sessions: temizlik / expire taramasi
        Schema::table('qr_sessions', function (Blueprint $table) {
            $table->index('expires_at', 'qr_sessions_expires_at_index');
            $table->index(['user_id', 'created_at'], 'qr_sessions_user_id_created_at_index');
        });

        // orders.cashier_id -> kasiyer bazli sorgular icin
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['cashier_id', 'created_at'], 'orders_cashier_id_created_at_index');
        });

        // wallet_transactions zaten yeterli indeksli, dokunma.

        // PG istatistiklerini guncelle (planner sagligi icin)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ANALYZE order_items, point_transactions, qr_sessions, orders');
        }
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_id_index');
            $table->dropIndex('order_items_product_id_created_at_index');
        });
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropIndex('point_transactions_user_id_created_at_index');
            $table->dropIndex('point_transactions_tenant_id_created_at_index');
        });
        Schema::table('qr_sessions', function (Blueprint $table) {
            $table->dropIndex('qr_sessions_expires_at_index');
            $table->dropIndex('qr_sessions_user_id_created_at_index');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_cashier_id_created_at_index');
        });
    }
};

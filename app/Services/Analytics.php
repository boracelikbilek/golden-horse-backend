<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard analitik sorgulari.
 * Tum sorgular order_items + orders join'i uzerinden calisir,
 * kapsam (tenant/bayi/store) base query ile sinirlanir.
 */
class Analytics
{
    /**
     * @param  Builder  $orderQuery  scope'lanmis Order builder (forTenant, forBayi vs.)
     */
    public function __construct(public readonly Builder $orderQuery)
    {
    }

    /** Son N gunluk gunluk ciro + siparis sayisi */
    public function dailyRevenue(int $days = 30): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $rows = (clone $this->orderQuery)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, count(*) as c, sum(total) as r')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $row = $rows->get($date);
            $out[] = [
                'date'    => $date,
                'orders'  => (int) ($row->c ?? 0),
                'revenue' => (float) ($row->r ?? 0),
            ];
        }
        return $out;
    }

    /** Saatlik dagilim (24 saat) — son N gun */
    public function hourlyDistribution(int $days = 30): array
    {
        $rows = (clone $this->orderQuery)
            ->where('created_at', '>=', Carbon::today()->subDays($days - 1))
            ->selectRaw('EXTRACT(HOUR FROM created_at)::int as h, count(*) as c')
            ->groupBy('h')
            ->orderBy('h')
            ->get()
            ->keyBy('h');

        $out = [];
        for ($h = 0; $h < 24; $h++) {
            $out[] = [
                'hour'   => $h,
                'orders' => (int) ($rows->get($h)->c ?? 0),
            ];
        }
        return $out;
    }

    /** En çok satan ürünler (adet bazinda) */
    public function topProducts(int $limit = 10): array
    {
        $orderIds = (clone $this->orderQuery)->pluck('orders.id');

        return DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->selectRaw('name, sum(quantity) as qty, sum(line_total) as revenue')
            ->groupBy('name')
            ->orderByDesc('qty')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'name'    => $r->name,
                'qty'     => (int) $r->qty,
                'revenue' => (float) $r->revenue,
            ])->all();
    }

    /** Bayilere göre satış (tenant_owner ve superadmin için anlamlı) */
    public function topBayis(int $limit = 10): array
    {
        return (clone $this->orderQuery)
            ->join('bayis', 'bayis.id', '=', 'orders.bayi_id')
            ->selectRaw('bayis.id, bayis.name, count(orders.id) as orders, sum(orders.total) as revenue')
            ->groupBy('bayis.id', 'bayis.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'id'      => $r->id,
                'name'    => $r->name,
                'orders'  => (int) $r->orders,
                'revenue' => (float) $r->revenue,
            ])->all();
    }

    /** Şubelere göre satış */
    public function topStores(int $limit = 10): array
    {
        return (clone $this->orderQuery)
            ->join('stores', 'stores.id', '=', 'orders.store_id')
            ->selectRaw('stores.id, stores.name, count(orders.id) as orders, sum(orders.total) as revenue')
            ->groupBy('stores.id', 'stores.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'id'      => $r->id,
                'name'    => $r->name,
                'orders'  => (int) $r->orders,
                'revenue' => (float) $r->revenue,
            ])->all();
    }

    /** En çok harcayan müşteriler */
    public function topCustomers(int $limit = 10): array
    {
        return (clone $this->orderQuery)
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->selectRaw('users.id, users.name, users.email, users.phone, count(orders.id) as orders, sum(orders.total) as spent, sum(orders.stars_earned) as stars')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
            ->orderByDesc('spent')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'id'     => $r->id,
                'name'   => $r->name,
                'email'  => $r->email,
                'phone'  => $r->phone,
                'orders' => (int) $r->orders,
                'spent'  => (float) $r->spent,
                'stars'  => (int) $r->stars,
            ])->all();
    }

    /** Her bayinin kendi top ürünü (tenant_owner görmek icin faydali) */
    public function topProductPerBayi(): array
    {
        $orderIds = (clone $this->orderQuery)->pluck('orders.id');

        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('bayis', 'bayis.id', '=', 'orders.bayi_id')
            ->whereIn('orders.id', $orderIds)
            ->selectRaw('bayis.id as bayi_id, bayis.name as bayi_name, order_items.name as product, sum(order_items.quantity) as qty')
            ->groupBy('bayis.id', 'bayis.name', 'order_items.name')
            ->orderBy('bayis.id')
            ->orderByDesc('qty')
            ->get();

        $byBayi = [];
        foreach ($rows as $r) {
            if (! isset($byBayi[$r->bayi_id])) {
                $byBayi[$r->bayi_id] = [
                    'bayi_id'   => (int) $r->bayi_id,
                    'bayi_name' => $r->bayi_name,
                    'product'   => $r->product,
                    'qty'       => (int) $r->qty,
                ];
            }
        }
        return array_values($byBayi);
    }

    /** Tenant'lara göre satış (sadece superadmin) */
    public function topTenants(int $limit = 10): array
    {
        return (clone $this->orderQuery)
            ->join('tenants', 'tenants.id', '=', 'orders.tenant_id')
            ->selectRaw('tenants.id, tenants.name, count(orders.id) as orders, sum(orders.total) as revenue')
            ->groupBy('tenants.id', 'tenants.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'id'      => $r->id,
                'name'    => $r->name,
                'orders'  => (int) $r->orders,
                'revenue' => (float) $r->revenue,
            ])->all();
    }
}

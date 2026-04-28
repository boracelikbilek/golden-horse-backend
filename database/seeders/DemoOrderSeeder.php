<?php

namespace Database\Seeders;

use App\Models\CustomerTenantStat;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'golden-horse')->first();
        if (! $tenant) return;

        // Bu tenant'in zaten siparisleri varsa tekrar kosma (idempotent)
        if (Order::where('tenant_id', $tenant->id)->count() > 5) {
            $this->command?->info('DemoOrderSeeder: skipped (orders already exist)');
            return;
        }

        // Demo amacli tum subelerden siparis (coming_soon olanlar dahil — gercek senaryoda backend perspektifinden)
        $stores  = Store::where('tenant_id', $tenant->id)->get();
        $products = Product::where('tenant_id', $tenant->id)->where('is_active', true)->get();
        $cashier = User::where('email', 'kasa@goldenhorse.coffee')->first();

        if ($stores->isEmpty() || $products->isEmpty() || ! $cashier) return;

        // Ek 8 demo musteri (cesitlilik icin)
        $customers = collect();
        $customers->push(User::where('email', 'demo@goldenhorse.coffee')->first());

        $names = ['Ayşe', 'Mehmet', 'Fatma', 'Ali', 'Zeynep', 'Mustafa', 'Elif', 'Hüseyin'];
        foreach ($names as $i => $n) {
            $email = strtolower($n).$i.'@demo.goldenhorse.coffee';
            $u = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'      => $n.' Demo',
                    'phone'     => '+9050000001'.str_pad((string)($i+1), 2, '0', STR_PAD_LEFT),
                    'password'  => Hash::make('demo1234'),
                    'role'      => User::ROLE_CUSTOMER,
                    'tier'      => 'green',
                    'join_date' => now()->subDays(rand(30, 180)),
                ]
            );
            CustomerTenantStat::firstOrCreate(
                ['user_id' => $u->id, 'tenant_id' => $tenant->id],
                ['tier' => 'green', 'stars' => 0, 'star_target' => 150]
            );
            $customers->push($u);
        }

        $totalOrders = 220;
        DB::transaction(function () use ($tenant, $stores, $products, $customers, $cashier, $totalOrders) {
            for ($i = 0; $i < $totalOrders; $i++) {
                $store = $stores->random();
                $customer = $customers->random();

                // Son 30 gun icinde rastgele zaman + saat dagilimi (sabah/aksam tipik kahve saatleri)
                $daysAgo = rand(0, 30);
                $hour    = $this->weightedHour();
                $minute  = rand(0, 59);
                $placedAt = now()->subDays($daysAgo)->setTime($hour, $minute);

                // 1-3 farkli urun, 1-2'serlik
                $itemCount = rand(1, 3);
                $picked    = $products->random($itemCount);
                $items     = [];
                $subtotal  = 0;

                foreach (is_iterable($picked) ? $picked : [$picked] as $p) {
                    $qty   = rand(1, 2);
                    $line  = $p->price * $qty;
                    $subtotal += $line;
                    $items[] = [
                        'product_id' => $p->id,
                        'name'       => $p->name,
                        'unit_price' => $p->price,
                        'quantity'   => $qty,
                        'line_total' => $line,
                    ];
                }

                $stars = max(1, (int) floor($subtotal / 25));

                $order = Order::create([
                    'tenant_id'    => $tenant->id,
                    'bayi_id'      => $store->bayi_id,
                    'store_id'     => $store->id,
                    'user_id'      => $customer->id,
                    'cashier_id'   => $cashier->id,
                    'status'       => 'completed',
                    'subtotal'     => $subtotal,
                    'total'        => $subtotal,
                    'currency'     => 'TRY',
                    'stars_earned' => $stars,
                    'placed_at'    => $placedAt,
                    'created_at'   => $placedAt,
                    'updated_at'   => $placedAt,
                ]);

                foreach ($items as $it) {
                    OrderItem::create($it + ['order_id' => $order->id, 'created_at' => $placedAt, 'updated_at' => $placedAt]);
                }

                // Stats guncelle
                $stats = $customer->statsFor($tenant->id);
                $stats->increment('stars', $stars);
                $stats->increment('lifetime_orders');
                $stats->increment('lifetime_spent', $subtotal);
                $stats->update(['last_order_at' => $placedAt]);
                if ($stats->stars >= $stats->star_target && $stats->tier === 'green') {
                    $stats->update(['tier' => 'gold']);
                }
            }
        });

        $this->command?->info("DemoOrderSeeder: {$totalOrders} sipariş oluşturuldu.");
    }

    /** Sabah/oglen/aksam yogun olacak sekilde aglirlikli saat */
    private function weightedHour(): int
    {
        $weights = [
            7 => 4, 8 => 8, 9 => 10, 10 => 8, 11 => 6,
            12 => 9, 13 => 8, 14 => 5, 15 => 5, 16 => 6,
            17 => 8, 18 => 7, 19 => 5, 20 => 3, 21 => 2,
        ];
        $total = array_sum($weights);
        $rand  = rand(1, $total);
        $cum = 0;
        foreach ($weights as $hour => $w) {
            $cum += $w;
            if ($rand <= $cum) return $hour;
        }
        return 10;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PointTransaction;
use App\Models\Product;
use App\Models\WalletTransaction;
use App\Services\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MeController extends Controller
{
    public function orders(Request $request, TenantContext $tenants)
    {
        $tenant = $tenants->resolve($request);
        $user   = $request->user();

        $orders = Order::forTenant($tenant)
            ->where('user_id', $user->id)
            ->with(['items', 'store'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json($orders->map(fn ($o) => [
            'id'          => $o->id,
            'status'      => $o->status,
            'total'       => (float) $o->total,
            'currency'    => 'TL',
            'starsEarned' => $o->stars_earned,
            'storeName'   => $o->store?->name,
            'placedAt'    => optional($o->placed_at ?? $o->created_at)->toIso8601String(),
            'items'       => $o->items->map(fn ($i) => [
                'name'      => $i->name,
                'quantity'  => $i->quantity,
                'unitPrice' => (float) $i->unit_price,
                'lineTotal' => (float) $i->line_total,
                'modifiers' => $i->modifiers ?? [],
            ])->all(),
        ]));
    }

    public function stats(Request $request, TenantContext $tenants)
    {
        $tenant = $tenants->resolve($request);
        $user   = $request->user();
        $stats  = $user->statsFor($tenant);

        $orders = Order::forTenant($tenant)->where('user_id', $user->id)->get();

        // Favori urun
        $favorite = OrderItem::whereIn('order_id', $orders->pluck('id'))
            ->selectRaw('product_id, name, sum(quantity) as qty')
            ->groupBy('product_id', 'name')
            ->orderByDesc('qty')
            ->first();

        $favoriteProduct = null;
        if ($favorite && $favorite->product_id) {
            $product = Product::find($favorite->product_id);
            if ($product) {
                $favoriteProduct = [
                    'id'    => $product->slug,
                    'name'  => $product->name,
                    'image' => $product->image,
                    'count' => (int) $favorite->qty,
                ];
            }
        }

        // Son 12 hafta sipariş eğrisi
        $weeks = collect();
        for ($i = 11; $i >= 0; $i--) {
            $start = Carbon::now()->startOfWeek()->subWeeks($i);
            $end   = $start->copy()->endOfWeek();
            $count = $orders->filter(fn ($o) => $o->created_at->between($start, $end))->count();
            $weeks->push([
                'weekStart' => $start->toDateString(),
                'count'     => $count,
            ]);
        }

        // En cok alinan 5 urun
        $top5 = OrderItem::whereIn('order_id', $orders->pluck('id'))
            ->selectRaw('name, product_id, sum(quantity) as qty, sum(line_total) as total_spent')
            ->groupBy('name', 'product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        return response()->json([
            'totalOrders'    => $orders->count(),
            'totalSpent'     => (float) $orders->sum('total'),
            'totalStars'     => $stats->stars,
            'currentTier'    => $stats->tier,
            'starTarget'     => $stats->star_target,
            'lifetimeOrders' => $stats->lifetime_orders,
            'lifetimeSpent'  => (float) $stats->lifetime_spent,
            'favorite'       => $favoriteProduct,
            'weeklyOrders'   => $weeks->all(),
            'topProducts'    => $top5->map(fn ($t) => [
                'name'       => $t->name,
                'count'      => (int) $t->qty,
                'totalSpent' => (float) $t->total_spent,
            ])->all(),
        ]);
    }

    /**
     * Birlesik hesap hareketleri timeline'i.
     * - Tum siparisler -> 'purchase' (orders kanonik kayit)
     * - Bakiye hareketleri (sadece order_id null olanlar) -> 'topup' / 'refund' / 'adjust'
     * - Yildiz hareketleri -> 'stars_earn' / 'stars_spend' / 'stars_adjust' / 'stars_reward'
     *
     * Query: ?type=all|purchase|wallet|stars  ?limit=50
     */
    public function transactions(Request $request, TenantContext $tenants)
    {
        $tenant = $tenants->resolve($request);
        $user   = $request->user();

        $filter = $request->query('type', 'all');
        $limit  = min(200, max(10, (int) $request->query('limit', 50)));

        $events = collect();

        // Siparisler (harcamalar)
        if ($filter === 'all' || $filter === 'purchase') {
            $orders = Order::forTenant($tenant)
                ->where('user_id', $user->id)
                ->with(['items', 'store', 'bayi'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

            foreach ($orders as $o) {
                $events->push([
                    'id'        => 'order-'.$o->id,
                    'kind'      => 'purchase',
                    'occurredAt'=> optional($o->placed_at ?? $o->created_at)->toIso8601String(),
                    'amount'    => -1 * (float) $o->total,
                    'currency'  => 'TL',
                    'title'     => 'Sipariş #'.$o->id,
                    'subtitle'  => $o->store?->name ?? $o->bayi?->name ?? 'Şube',
                    'storeName' => $o->store?->name,
                    'bayiName'  => $o->bayi?->name,
                    'starsDelta'=> (int) $o->stars_earned,
                    'items'     => $o->items->map(fn ($i) => [
                        'name'     => $i->name,
                        'quantity' => $i->quantity,
                    ])->all(),
                ]);
            }
        }

        // Cuzdan hareketleri (bagimsiz olanlar)
        if ($filter === 'all' || $filter === 'wallet') {
            $wallet = WalletTransaction::forTenant($tenant)
                ->where('user_id', $user->id)
                ->whereNull('order_id')
                ->with(['store', 'bayi', 'cashier'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

            foreach ($wallet as $w) {
                $events->push([
                    'id'         => 'wallet-'.$w->id,
                    'kind'       => $w->type, // topup | refund | adjust | purchase (purchase olmaz cunku order_id null filtrelendi)
                    'occurredAt' => $w->created_at->toIso8601String(),
                    'amount'     => (float) $w->amount,
                    'currency'   => 'TL',
                    'title'      => $this->walletTitle($w->type),
                    'subtitle'   => $w->reason ?? ($w->cashier?->name ? 'Kasiyer: '.$w->cashier->name : null),
                    'storeName'  => $w->store?->name,
                    'bayiName'   => $w->bayi?->name,
                    'balanceAfter' => (float) $w->balance_after,
                ]);
            }
        }

        // Yildiz hareketleri
        if ($filter === 'all' || $filter === 'stars') {
            $points = PointTransaction::forTenant($tenant)
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

            foreach ($points as $p) {
                $events->push([
                    'id'         => 'star-'.$p->id,
                    'kind'       => 'stars_'.$p->type, // earn | spend | adjust | reward
                    'occurredAt' => $p->created_at->toIso8601String(),
                    'amount'     => 0,
                    'starsDelta' => (int) $p->points,
                    'starsBalanceAfter' => (int) $p->balance_after,
                    'title'      => $this->starTitle($p->type),
                    'subtitle'   => $p->reason,
                ]);
            }
        }

        $sorted = $events->sortByDesc('occurredAt')->values()->take($limit);

        return response()->json([
            'cardBalance' => (float) $user->fresh()->card_balance,
            'currency'    => 'TL',
            'stars'       => $user->statsFor($tenant)->stars,
            'items'       => $sorted->all(),
        ]);
    }

    private function walletTitle(string $type): string
    {
        return match ($type) {
            'topup'    => 'Bakiye yükleme',
            'refund'   => 'İade',
            'adjust'   => 'Bakiye düzeltme',
            'purchase' => 'Bakiyeden harcama',
            default    => 'Bakiye hareketi',
        };
    }

    private function starTitle(string $type): string
    {
        return match ($type) {
            'earn'   => 'Yıldız kazanıldı',
            'spend'  => 'Yıldız kullanıldı',
            'reward' => 'Ödül',
            'adjust' => 'Yıldız düzeltme',
            default  => 'Yıldız hareketi',
        };
    }
}

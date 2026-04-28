<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
}

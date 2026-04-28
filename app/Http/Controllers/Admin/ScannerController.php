<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\QrSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScannerController extends Controller
{
    public function show()
    {
        return view('admin.scanner');
    }

    public function resolve(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $cashier = $request->user();

        $session = QrSession::with('user')
            ->where('token', $request->input('token'))
            ->first();

        if (! $session || ! $session->isValid()) {
            return response()->json(['ok' => false, 'message' => 'QR kod geçersiz veya süresi dolmuş.'], 422);
        }

        $tenantId = $cashier->tenant_id;
        $stats    = $session->user->statsFor($tenantId);

        return response()->json([
            'ok' => true,
            'session' => [
                'token'     => $session->token,
                'expiresAt' => $session->expires_at->toIso8601String(),
            ],
            'user' => [
                'id'         => $session->user->id,
                'name'       => $session->user->name,
                'email'      => $session->user->email,
                'phone'      => $session->user->phone,
                'tier'       => $stats->tier,
                'stars'      => $stats->stars,
                'starTarget' => $stats->star_target,
                'lifetimeOrders' => $stats->lifetime_orders,
                'lifetimeSpent'  => (float) $stats->lifetime_spent,
            ],
        ]);
    }

    public function createOrder(Request $request)
    {
        $cashier = $request->user();

        $data = $request->validate([
            'token'      => ['required', 'string'],
            'total'      => ['required', 'numeric', 'min:0.01'],
            'note'       => ['nullable', 'string', 'max:500'],
            'items'      => ['nullable', 'array'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.name'       => ['required_with:items', 'string'],
            'items.*.unit_price' => ['required_with:items', 'numeric'],
            'items.*.quantity'   => ['required_with:items', 'integer', 'min:1'],
        ]);

        $session = QrSession::where('token', $data['token'])->first();
        if (! $session || ! $session->isValid()) {
            return back()->withErrors(['token' => 'QR kod geçersiz.']);
        }

        $customer = $session->user;
        $tenantId = $cashier->tenant_id;

        if ($cashier->isCashier() && $cashier->store_id === null) {
            return back()->withErrors(['store' => 'Kasiyere şube atanmamış.']);
        }

        $order = DB::transaction(function () use ($cashier, $customer, $tenantId, $data, $session) {
            $stars = max(1, (int) floor($data['total'] / 25)); // 25 TL = 1 yildiz

            $order = Order::create([
                'tenant_id'    => $tenantId,
                'bayi_id'      => $cashier->bayi_id,
                'store_id'     => $cashier->store_id,
                'user_id'      => $customer->id,
                'cashier_id'   => $cashier->id,
                'status'       => 'completed',
                'subtotal'     => $data['total'],
                'total'        => $data['total'],
                'currency'     => 'TRY',
                'stars_earned' => $stars,
                'note'         => $data['note'] ?? null,
                'placed_at'    => now(),
            ]);

            foreach ($data['items'] ?? [] as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'name'       => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity'   => $item['quantity'],
                    'line_total' => $item['unit_price'] * $item['quantity'],
                    'modifiers'  => $item['modifiers'] ?? null,
                ]);
            }

            // Yildiz odullendir + stats guncelle
            $stats = $customer->statsFor($tenantId);
            $stats->awardStars($stars, $order, "Sipariş #{$order->id}");
            $stats->increment('lifetime_orders');
            $stats->increment('lifetime_spent', (float) $order->total);
            $stats->update(['last_order_at' => now()]);

            // QR session'i yak
            $session->update([
                'used_at' => now(),
                'used_by_cashier_id' => $cashier->id,
            ]);

            return $order;
        });

        return redirect()->route('admin.scanner')->with('success', "Sipariş #{$order->id} oluşturuldu, +{$order->stars_earned}⭐");
    }
}

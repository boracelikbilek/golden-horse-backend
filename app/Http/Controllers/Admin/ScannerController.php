<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\QrSession;
use App\Models\User;
use App\Models\WalletTransaction;
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
            'pay_from_balance' => ['sometimes', 'boolean'],
            'items'      => ['nullable', 'array'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.name'       => ['required_with:items', 'string'],
            'items.*.unit_price' => ['required_with:items', 'numeric'],
            'items.*.quantity'   => ['required_with:items', 'integer', 'min:1'],
        ]);

        $tenantId = $cashier->tenant_id;

        if ($cashier->isCashier() && $cashier->store_id === null) {
            return back()->withErrors(['store' => 'Kasiyere şube atanmamış.']);
        }

        $payFromBalance = (bool) ($data['pay_from_balance'] ?? false);

        try {
            $order = DB::transaction(function () use ($cashier, $tenantId, $data, $payFromBalance) {
                // QR oturumunu KILITLE -> ayni anda iki kasiyer ayni token ile gelirse ikincisi bekler
                $session = QrSession::where('token', $data['token'])->lockForUpdate()->first();
                if (! $session || ! $session->isValid()) {
                    throw new \RuntimeException('QR kod geçersiz veya süresi dolmuş.');
                }

                // Bakiye degisecekse musteriyi de kilitle (race-free balance check)
                $customer = $payFromBalance
                    ? User::lockForUpdate()->find($session->user_id)
                    : User::find($session->user_id);
                if (! $customer) {
                    throw new \RuntimeException('Müşteri bulunamadı.');
                }

                if ($payFromBalance && (float) $customer->card_balance < (float) $data['total']) {
                    throw new \RuntimeException('Müşterinin bakiyesi yetersiz.');
                }

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

                $stats = $customer->statsFor($tenantId);
                $stats->awardStars($stars, $order, "Sipariş #{$order->id}");
                $stats->increment('lifetime_orders');
                $stats->increment('lifetime_spent', (float) $order->total);
                $stats->update(['last_order_at' => now()]);

                if ($payFromBalance) {
                    WalletTransaction::record([
                        'tenant_id'  => $tenantId,
                        'user_id'    => $customer->id,
                        'bayi_id'    => $cashier->bayi_id,
                        'store_id'   => $cashier->store_id,
                        'order_id'   => $order->id,
                        'cashier_id' => $cashier->id,
                        'type'       => WalletTransaction::TYPE_PURCHASE,
                        'amount'     => -1 * (float) $order->total,
                        'reason'     => "Sipariş #{$order->id}",
                    ]);
                }

                // QR session'i yak (kilit hala bizde, baska transaction'lar bunu goremez)
                $session->update([
                    'used_at' => now(),
                    'used_by_cashier_id' => $cashier->id,
                ]);

                return $order;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['token' => $e->getMessage()]);
        }

        return redirect()->route('admin.scanner')->with('success', "Sipariş #{$order->id} oluşturuldu, +{$order->stars_earned}⭐");
    }
}

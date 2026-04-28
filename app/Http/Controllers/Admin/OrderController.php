<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Order::with(['user', 'tenant', 'bayi', 'store', 'cashier']);

        if ($user->isCashier()) {
            $q->where('cashier_id', $user->id);
        } elseif ($user->isBayiOwner()) {
            $q->where('bayi_id', $user->bayi_id);
        } elseif ($user->isTenantOwner()) {
            $q->where('tenant_id', $user->tenant_id);
        }

        $orders = $q->orderByDesc('created_at')->paginate(50);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order, Request $request)
    {
        $user = $request->user();
        $this->authorizeFor($user, $order);
        $order->load(['items.product', 'user', 'cashier', 'bayi', 'store', 'tenant']);
        return view('admin.orders.show', compact('order'));
    }

    private function authorizeFor($user, Order $order): void
    {
        if ($user->isSuperadmin()) return;
        if ($user->isTenantOwner() && $order->tenant_id === $user->tenant_id) return;
        if ($user->isBayiOwner() && $order->bayi_id === $user->bayi_id) return;
        if ($user->isCashier() && $order->cashier_id === $user->id) return;
        abort(403);
    }
}

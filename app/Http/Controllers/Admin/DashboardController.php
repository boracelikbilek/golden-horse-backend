<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bayi;
use App\Models\CustomerTenantStat;
use App\Models\Order;
use App\Models\Store;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isCashier()) {
            return redirect()->route('admin.scanner');
        }

        if ($user->isSuperadmin()) {
            return $this->superadminDashboard();
        }

        if ($user->isTenantOwner()) {
            return $this->tenantDashboard($user->tenant_id);
        }

        if ($user->isBayiOwner()) {
            return $this->bayiDashboard($user->bayi_id);
        }

        abort(403);
    }

    private function superadminDashboard()
    {
        $tenants = Tenant::with('owner')->withCount(['bayis', 'stores', 'orders', 'customers'])->get();

        $totals = [
            'tenants'   => $tenants->count(),
            'bayis'     => Bayi::count(),
            'stores'    => Store::count(),
            'orders'    => Order::count(),
            'customers' => CustomerTenantStat::count(),
            'revenue'   => (float) Order::sum('total'),
        ];

        $recentOrders = Order::with(['user', 'tenant', 'bayi', 'store'])
            ->orderByDesc('created_at')->limit(20)->get();

        return view('admin.dashboard.superadmin', compact('tenants', 'totals', 'recentOrders'));
    }

    private function tenantDashboard(int $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $bayis  = Bayi::where('tenant_id', $tenantId)->withCount(['stores', 'orders'])->get();
        $stores = Store::forTenant($tenant)->with('bayi')->get();

        $orders = Order::forTenant($tenant);
        $totals = [
            'bayis'     => $bayis->count(),
            'stores'    => $stores->count(),
            'orders'    => (clone $orders)->count(),
            'customers' => CustomerTenantStat::where('tenant_id', $tenantId)->count(),
            'revenue'   => (float) (clone $orders)->sum('total'),
        ];

        $perBayi = $bayis->map(function ($b) use ($tenantId) {
            $bayiOrders = Order::where('tenant_id', $tenantId)->where('bayi_id', $b->id);
            return [
                'bayi'     => $b,
                'orders'   => (clone $bayiOrders)->count(),
                'revenue'  => (float) (clone $bayiOrders)->sum('total'),
                'last7d'   => (clone $bayiOrders)->where('created_at', '>=', now()->subDays(7))->count(),
            ];
        });

        $recentOrders = Order::forTenant($tenant)
            ->with(['user', 'bayi', 'store'])
            ->orderByDesc('created_at')->limit(20)->get();

        return view('admin.dashboard.tenant', compact('tenant', 'bayis', 'stores', 'totals', 'perBayi', 'recentOrders'));
    }

    private function bayiDashboard(?int $bayiId)
    {
        $bayi = Bayi::with('tenant')->findOrFail($bayiId);
        $stores = Store::where('bayi_id', $bayi->id)->get();

        $orders = Order::where('bayi_id', $bayi->id);
        $totals = [
            'stores'   => $stores->count(),
            'orders'   => (clone $orders)->count(),
            'revenue'  => (float) (clone $orders)->sum('total'),
            'last7d'   => (clone $orders)->where('created_at', '>=', now()->subDays(7))->count(),
            'today'    => (clone $orders)->whereDate('created_at', today())->count(),
        ];

        $perStore = $stores->map(function ($s) {
            $sOrders = Order::where('store_id', $s->id);
            return [
                'store'   => $s,
                'orders'  => (clone $sOrders)->count(),
                'revenue' => (float) (clone $sOrders)->sum('total'),
                'today'   => (clone $sOrders)->whereDate('created_at', today())->count(),
            ];
        });

        $topCustomers = CustomerTenantStat::where('tenant_id', $bayi->tenant_id)
            ->with('user')->orderByDesc('lifetime_orders')->limit(10)->get();

        $recentOrders = Order::where('bayi_id', $bayi->id)
            ->with(['user', 'store'])
            ->orderByDesc('created_at')->limit(20)->get();

        return view('admin.dashboard.bayi', compact('bayi', 'stores', 'totals', 'perStore', 'topCustomers', 'recentOrders'));
    }
}

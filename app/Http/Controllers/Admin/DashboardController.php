<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bayi;
use App\Models\CustomerTenantStat;
use App\Models\Order;
use App\Models\Store;
use App\Models\Tenant;
use App\Services\Analytics;
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

        $analytics = new Analytics(Order::query()->from('orders'));

        $charts = [
            'topTenants'   => $analytics->topTenants(),
            'topProducts'  => $analytics->topProducts(),
            'topBayis'     => $analytics->topBayis(),
            'topCustomers' => $analytics->topCustomers(),
            'daily'        => $analytics->dailyRevenue(30),
            'hourly'       => $analytics->hourlyDistribution(30),
        ];

        $recentOrders = Order::with(['user', 'tenant', 'bayi', 'store'])
            ->orderByDesc('created_at')->limit(10)->get();

        return view('admin.dashboard.superadmin', compact('tenants', 'totals', 'recentOrders', 'charts'));
    }

    private function tenantDashboard(int $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $bayis  = Bayi::where('tenant_id', $tenantId)->withCount(['stores', 'orders'])->get();
        $stores = Store::forTenant($tenant)->with('bayi')->get();

        $base = Order::query()->from('orders')->where('tenant_id', $tenantId);

        $totals = [
            'bayis'     => $bayis->count(),
            'stores'    => $stores->count(),
            'orders'    => (clone $base)->count(),
            'customers' => CustomerTenantStat::where('tenant_id', $tenantId)->count(),
            'revenue'   => (float) (clone $base)->sum('total'),
        ];

        $analytics = new Analytics($base);
        $charts = [
            'topBayis'         => $analytics->topBayis(),
            'topStores'        => $analytics->topStores(),
            'topProducts'      => $analytics->topProducts(),
            'topCustomers'     => $analytics->topCustomers(),
            'topProductPerBayi'=> $analytics->topProductPerBayi(),
            'daily'            => $analytics->dailyRevenue(30),
            'hourly'           => $analytics->hourlyDistribution(30),
        ];

        $perBayi = $bayis->map(function ($b) use ($tenantId) {
            $bayiOrders = Order::where('tenant_id', $tenantId)->where('bayi_id', $b->id);
            return [
                'bayi'    => $b,
                'orders'  => (clone $bayiOrders)->count(),
                'revenue' => (float) (clone $bayiOrders)->sum('total'),
                'last7d'  => (clone $bayiOrders)->where('created_at', '>=', now()->subDays(7))->count(),
            ];
        });

        $recentOrders = Order::where('tenant_id', $tenantId)
            ->with(['user', 'bayi', 'store'])
            ->orderByDesc('created_at')->limit(10)->get();

        return view('admin.dashboard.tenant', compact('tenant', 'bayis', 'stores', 'totals', 'charts', 'perBayi', 'recentOrders'));
    }

    private function bayiDashboard(?int $bayiId)
    {
        $bayi = Bayi::with('tenant')->findOrFail($bayiId);
        $stores = Store::where('bayi_id', $bayi->id)->get();

        $base = Order::query()->from('orders')->where('bayi_id', $bayi->id);

        $totals = [
            'stores'   => $stores->count(),
            'orders'   => (clone $base)->count(),
            'revenue'  => (float) (clone $base)->sum('total'),
            'last7d'   => (clone $base)->where('created_at', '>=', now()->subDays(7))->count(),
            'today'    => (clone $base)->whereDate('created_at', today())->count(),
        ];

        $analytics = new Analytics($base);
        $charts = [
            'topStores'    => $analytics->topStores(),
            'topProducts'  => $analytics->topProducts(),
            'topCustomers' => $analytics->topCustomers(),
            'daily'        => $analytics->dailyRevenue(30),
            'hourly'       => $analytics->hourlyDistribution(30),
        ];

        $recentOrders = Order::where('bayi_id', $bayi->id)
            ->with(['user', 'store'])
            ->orderByDesc('created_at')->limit(10)->get();

        return view('admin.dashboard.bayi', compact('bayi', 'stores', 'totals', 'charts', 'recentOrders'));
    }
}

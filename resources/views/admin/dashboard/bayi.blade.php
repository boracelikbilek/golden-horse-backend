@extends('admin.layout')
@section('title', $bayi->name.' — Bayi Paneli')
@section('content')

<div class="d-flex align-items-center mb-4">
    <h3 class="mb-0">{{ $bayi->name }}</h3>
    <span class="badge text-bg-secondary ms-2">Bayi Sahibi</span>
    <small class="text-muted ms-3">{{ $bayi->tenant?->name }}</small>
</div>

<div class="row g-3 mb-4">
    <div class="col"><div class="card stat-card"><div class="label">Şube</div><div class="value">{{ $totals['stores'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Toplam Sipariş</div><div class="value">{{ $totals['orders'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Bugün</div><div class="value">{{ $totals['today'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Son 7 Gün</div><div class="value">{{ $totals['last7d'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Toplam Ciro</div><div class="value">{{ number_format($totals['revenue'], 2, ',', '.') }} ₺</div></div></div>
</div>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="mb-3">Şubelere göre performans</h5>
                <table class="table table-clean align-middle">
                    <thead><tr><th>Şube</th><th>Bugün</th><th>Toplam</th><th>Ciro</th></tr></thead>
                    <tbody>
                    @foreach($perStore as $s)
                        <tr>
                            <td><strong>{{ $s['store']->name }}</strong><br><small class="text-muted">{{ $s['store']->district }}</small></td>
                            <td>{{ $s['today'] }}</td>
                            <td>{{ $s['orders'] }}</td>
                            <td>{{ number_format($s['revenue'], 2, ',', '.') }} ₺</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Son Siparişler</h5>
                @include('admin.dashboard._orders_table', ['rows' => $recentOrders, 'showBayi' => false])
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">En sadık müşteriler</h5>
                <table class="table table-clean align-middle">
                    <thead><tr><th>Müşteri</th><th>Sipariş</th><th>⭐</th><th>Tier</th></tr></thead>
                    <tbody>
                    @forelse($topCustomers as $c)
                        <tr>
                            <td>{{ $c->user?->name }}<br><small class="text-muted">{{ $c->user?->phone }}</small></td>
                            <td>{{ $c->lifetime_orders }}</td>
                            <td>{{ $c->stars }}</td>
                            <td><span class="badge badge-tier-{{ $c->tier }}">{{ ucfirst($c->tier) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Henüz müşteri verisi yok.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

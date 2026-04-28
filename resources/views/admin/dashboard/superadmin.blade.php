@extends('admin.layout')
@section('title', 'Süperadmin Paneli')
@section('content')

<h3 class="mb-4">Platform Özeti</h3>

<div class="row g-3 mb-4">
    <div class="col"><div class="card stat-card"><div class="label">Tenant</div><div class="value">{{ $totals['tenants'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Bayi</div><div class="value">{{ $totals['bayis'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Şube</div><div class="value">{{ $totals['stores'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Müşteri</div><div class="value">{{ $totals['customers'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Sipariş</div><div class="value">{{ $totals['orders'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Toplam Ciro</div><div class="value">{{ number_format($totals['revenue'], 2, ',', '.') }} ₺</div></div></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3">Tenant'lar</h5>
        <table class="table table-clean align-middle">
            <thead><tr><th>Firma</th><th>Sahip</th><th>Bayi</th><th>Şube</th><th>Müşteri</th><th>Sipariş</th></tr></thead>
            <tbody>
            @foreach($tenants as $t)
                <tr>
                    <td><strong>{{ $t->name }}</strong><br><small class="text-muted">{{ $t->slug }}</small></td>
                    <td>{{ $t->owner?->name ?? '—' }}</td>
                    <td>{{ $t->bayis_count }}</td>
                    <td>{{ $t->stores_count }}</td>
                    <td>{{ $t->customers_count }}</td>
                    <td>{{ $t->orders_count }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Son Siparişler (tüm platform)</h5>
        @include('admin.dashboard._orders_table', ['rows' => $recentOrders, 'showTenant' => true])
    </div>
</div>

@endsection

@extends('admin.layout')
@section('title', $tenant->name.' — Yönetim')
@section('content')

<div class="d-flex align-items-center mb-4">
    <h3 class="mb-0">{{ $tenant->name }}</h3>
    <span class="badge text-bg-secondary ms-2">Firma Sahibi</span>
</div>

<div class="row g-3 mb-4">
    <div class="col"><div class="card stat-card"><div class="label">Bayi</div><div class="value">{{ $totals['bayis'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Şube</div><div class="value">{{ $totals['stores'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Müşteri</div><div class="value">{{ $totals['customers'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Toplam Sipariş</div><div class="value">{{ $totals['orders'] }}</div></div></div>
    <div class="col"><div class="card stat-card"><div class="label">Toplam Ciro</div><div class="value">{{ number_format($totals['revenue'], 2, ',', '.') }} ₺</div></div></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3">Bayilere göre satış</h5>
        <table class="table table-clean align-middle">
            <thead><tr><th>Bayi</th><th>Sahip</th><th>Şube</th><th>Sipariş</th><th>Son 7 gün</th><th>Ciro</th></tr></thead>
            <tbody>
            @foreach($perBayi as $b)
                <tr>
                    <td><strong>{{ $b['bayi']->name }}</strong><br><small class="text-muted">{{ $b['bayi']->city }} {{ $b['bayi']->district }}</small></td>
                    <td>{{ $b['bayi']->owner?->name ?? '—' }}</td>
                    <td>{{ $b['bayi']->stores_count }}</td>
                    <td>{{ $b['orders'] }}</td>
                    <td>{{ $b['last7d'] }}</td>
                    <td>{{ number_format($b['revenue'], 2, ',', '.') }} ₺</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3">Şubeler</h5>
        <div class="row g-3">
            @foreach($stores as $s)
                <div class="col-md-4">
                    <div class="card border h-100">
                        <div class="card-body">
                            <h6>{{ $s->name }}</h6>
                            <small class="text-muted d-block">{{ $s->bayi?->name ?? '—' }}</small>
                            <small class="text-muted">{{ $s->city }} / {{ $s->district }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Son Siparişler</h5>
        @include('admin.dashboard._orders_table', ['rows' => $recentOrders, 'showTenant' => false, 'showBayi' => true])
    </div>
</div>
@endsection

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

@include('admin.dashboard._charts', ['charts' => $charts])

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3">Son Siparişler</h5>
        @include('admin.dashboard._orders_table', ['rows' => $recentOrders, 'showBayi' => false])
    </div>
</div>

@endsection

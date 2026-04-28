@extends('admin.layout')
@section('title', 'Sipariş #'.$order->id)
@section('content')
<a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Geri</a>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <h5>Sipariş #{{ $order->id }}</h5>
                <small class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</small>

                <table class="table mt-3">
                    <thead><tr><th>Ürün</th><th>Adet</th><th>Birim</th><th>Toplam</th></tr></thead>
                    <tbody>
                    @forelse($order->items as $i)
                        <tr>
                            <td>{{ $i->name }}</td>
                            <td>{{ $i->quantity }}</td>
                            <td>{{ number_format($i->unit_price,2,',','.') }} ₺</td>
                            <td>{{ number_format($i->line_total,2,',','.') }} ₺</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Manuel girilmiş tutar (kalem yok)</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end"><strong>Toplam</strong></td><td><strong>{{ number_format($order->total,2,',','.') }} ₺</strong></td></tr>
                    </tfoot>
                </table>
                @if($order->note)<div class="alert alert-light"><strong>Not:</strong> {{ $order->note }}</div>@endif
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h6>Müşteri</h6>
                <p class="mb-1"><strong>{{ $order->user?->name }}</strong></p>
                <p class="text-muted mb-3">{{ $order->user?->email }} · {{ $order->user?->phone }}</p>
                <hr>
                <h6>Şube</h6>
                <p>{{ $order->store?->name ?? '—' }}<br><small class="text-muted">{{ $order->bayi?->name ?? '—' }}</small></p>
                <hr>
                <h6>Kasiyer</h6>
                <p>{{ $order->cashier?->name ?? '—' }}</p>
                <hr>
                <p class="mb-0"><strong>+{{ $order->stars_earned }}⭐</strong> kazandırıldı</p>
            </div>
        </div>
    </div>
</div>
@endsection

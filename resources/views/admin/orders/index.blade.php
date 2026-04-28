@extends('admin.layout')
@section('title', 'Siparişler')
@section('content')
<h3 class="mb-3">Siparişler</h3>
<div class="card"><div class="card-body">
    @include('admin.dashboard._orders_table', ['rows' => $orders, 'showTenant' => auth()->user()->isSuperadmin(), 'showBayi' => true])
    {{ $orders->links() }}
</div></div>
@endsection

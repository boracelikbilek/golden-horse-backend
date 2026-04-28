@extends('admin.layout')
@section('title', 'Menü Yönetimi')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Menü</h3>
    <a href="{{ route('admin.products.create') }}" class="btn btn-dark">+ Yeni Ürün</a>
</div>
<div class="card"><div class="card-body">
<table class="table table-clean align-middle">
    <thead><tr><th>Ürün</th><th>Kategori</th><th>Fiyat</th><th>⭐</th><th>Durum</th><th></th></tr></thead>
    <tbody>
    @foreach($products as $p)
        <tr>
            <td>{{ $p->image }} <strong>{{ $p->name }}</strong></td>
            <td>{{ $p->category?->name }}</td>
            <td>{{ number_format($p->price, 2, ',', '.') }} ₺</td>
            <td>{{ $p->star_reward }}</td>
            <td>
                @if($p->is_active)<span class="badge text-bg-success">Aktif</span>@else<span class="badge text-bg-secondary">Pasif</span>@endif
                @if($p->is_new)<span class="badge text-bg-warning">Yeni</span>@endif
            </td>
            <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.edit', $p) }}">Düzenle</a>
                <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Silinsin mi?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Sil</button></form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $products->links() }}
</div></div>
@endsection

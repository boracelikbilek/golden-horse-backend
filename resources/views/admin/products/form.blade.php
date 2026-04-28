@extends('admin.layout')
@section('title', $product->exists ? 'Ürün Düzenle' : 'Yeni Ürün')
@section('content')
<a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Menüye dön</a>
<h3 class="mb-3">{{ $product->exists ? 'Ürün Düzenle' : 'Yeni Ürün' }}</h3>

<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Ad</label>
            <input class="form-control" name="name" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Slug</label>
            <input class="form-control" name="slug" value="{{ old('slug', $product->slug) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Kategori</label>
            <select class="form-select" name="category_id" required>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Fiyat (₺)</label>
            <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price', $product->price) }}" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">İkon</label>
            <input class="form-control" name="image" value="{{ old('image', $product->image) }}" maxlength="32">
        </div>
        <div class="col-md-2">
            <label class="form-label">⭐ Ödül</label>
            <input type="number" class="form-control" name="star_reward" value="{{ old('star_reward', $product->star_reward ?? 1) }}" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Açıklama</label>
            <textarea class="form-control" name="description">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="col-md-12">
            <div class="form-check form-check-inline">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active" @checked(old('is_active', $product->is_active ?? true))>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="hidden" name="is_new" value="0">
                <input class="form-check-input" type="checkbox" name="is_new" value="1" id="new" @checked(old('is_new', $product->is_new))>
                <label class="form-check-label" for="new">Yeni</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="hidden" name="is_recommended" value="0">
                <input class="form-check-input" type="checkbox" name="is_recommended" value="1" id="rec" @checked(old('is_recommended', $product->is_recommended))>
                <label class="form-check-label" for="rec">Önerilen</label>
            </div>
        </div>
    </div>
    <button class="btn btn-dark mt-3">Kaydet</button>
</form>
@endsection

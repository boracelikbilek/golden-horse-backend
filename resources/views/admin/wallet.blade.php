@extends('admin.layout')
@section('title', 'TL Yükle')
@section('content')

<h3 class="mb-4">Müşteriye TL Yükle</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.wallet.topup') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Müşteri tanımlama yöntemi</label>
                        <select name="method" class="form-select" required>
                            <option value="phone" {{ old('method', 'phone') === 'phone' ? 'selected' : '' }}>Telefon</option>
                            <option value="email" {{ old('method') === 'email' ? 'selected' : '' }}>E-posta</option>
                            <option value="qr" {{ old('method') === 'qr' ? 'selected' : '' }}>QR Token</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Müşteri (telefon / e-posta / QR token)</label>
                        <input type="text" name="identifier" value="{{ old('identifier') }}" class="form-control" placeholder="ör. +905551112233" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Yüklenecek tutar (TL)</label>
                        <input type="number" min="1" max="10000" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Not (ops.)</label>
                        <input type="text" name="note" value="{{ old('note') }}" class="form-control" maxlength="500">
                    </div>

                    <button type="submit" class="btn btn-primary">Yükle</button>
                    <a href="{{ route('admin.scanner') }}" class="btn btn-link">QR Tarayıcı</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h6>Notlar</h6>
                <ul class="small mb-0">
                    <li>QR yöntemi seçildiğinde, müşterinin uygulamasındaki anlık QR token'ı kullanılır (~60 sn geçerli).</li>
                    <li>Telefon yöntemi ülke kodu dahil tam numara ister (ör. +905551112233).</li>
                    <li>Yüklenen tutar müşterinin tenant'ına göre kendi cüzdanına işlenir; her hareket loglanır.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş — Golden Horse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(160deg,#1A1410,#2B1F14 60%,#CDA863); height: 100vh; display:flex; align-items:center; justify-content:center; font-family: system-ui; }
        .login-card { background:#fff; border-radius:18px; padding:2.5rem; width:380px; box-shadow:0 10px 30px rgba(0,0,0,.25); }
        .gh-logo { font-size:2rem; text-align:center; color:#CDA863; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="gh-logo">⬢ Golden Horse</div>
    <h5 class="text-center mb-4 text-muted">Yönetim Paneli</h5>

    @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">E-posta veya telefon</label>
            <input class="form-control" name="identifier" value="{{ old('identifier') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Şifre</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button class="btn btn-dark w-100" type="submit">Giriş yap</button>
    </form>

    <hr class="my-4">
    <div class="small text-muted">
        <strong>Demo:</strong><br>
        Superadmin: super@goldenhorse.coffee / super1234<br>
        Firma sahibi: sahip@goldenhorse.coffee / sahip1234<br>
        Bayi sahibi: canakkale@goldenhorse.coffee / bayi1234<br>
        Kasiyer: kasa@goldenhorse.coffee / kasa1234
    </div>
</div>
</body>
</html>

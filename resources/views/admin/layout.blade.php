<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Golden Horse Yönetim')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #F5F1EA; font-family: system-ui, -apple-system, sans-serif; }
        .navbar-brand { font-weight: 700; letter-spacing: 0.5px; }
        .gh-brand { color: #CDA863; }
        .card { border: 0; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .stat-card { padding: 1.25rem; }
        .stat-card .value { font-size: 1.8rem; font-weight: 700; color: #1A1410; }
        .stat-card .label { color: #777; font-size: .875rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-clean th { color: #888; font-size: .8rem; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }
        .badge-tier-green { background: #4F8A5C; }
        .badge-tier-gold  { background: #CDA863; }
        .qr-region { max-width: 480px; margin: 0 auto; }
        .reader-box { border: 3px dashed #CDA863; border-radius: 14px; padding: 8px; background: #FFF; }
    </style>
    @yield('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#1A1410;">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <span class="gh-brand">⬢</span> Golden Horse
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            @auth
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Panel</a></li>
                @if(auth()->user()->isCashier() || auth()->user()->isAdminLike())
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.scanner') }}">📷 QR Tara</a></li>
                @endif
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}">Siparişler</a></li>
                @if(auth()->user()->isTenantOwner() || auth()->user()->isSuperadmin())
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Menü</a></li>
                @endif
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link">
                        {{ auth()->user()->name }}
                        <span class="badge text-bg-light">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                    </span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">@csrf
                        <button class="btn btn-outline-light btn-sm">Çıkış</button>
                    </form>
                </li>
            </ul>
            @endauth
        </div>
    </div>
</nav>

<main class="container-fluid py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>

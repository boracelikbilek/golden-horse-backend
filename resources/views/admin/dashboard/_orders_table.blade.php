<table class="table table-clean align-middle">
    <thead>
    <tr>
        <th>#</th>
        @if(($showTenant ?? false))<th>Firma</th>@endif
        @if(($showBayi ?? true))<th>Bayi</th>@endif
        <th>Şube</th>
        <th>Müşteri</th>
        <th>Tutar</th>
        <th>⭐</th>
        <th>Tarih</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $o)
        <tr>
            <td><a href="{{ route('admin.orders.show', $o) }}">#{{ $o->id }}</a></td>
            @if(($showTenant ?? false))<td>{{ $o->tenant?->name }}</td>@endif
            @if(($showBayi ?? true))<td>{{ $o->bayi?->name ?? '—' }}</td>@endif
            <td>{{ $o->store?->name ?? '—' }}</td>
            <td>{{ $o->user?->name }}</td>
            <td>{{ number_format($o->total, 2, ',', '.') }} ₺</td>
            <td>{{ $o->stars_earned }}</td>
            <td><small>{{ $o->created_at->format('d.m.Y H:i') }}</small></td>
        </tr>
    @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Henüz sipariş yok.</td></tr>
    @endforelse
    </tbody>
</table>

{{-- Reusable analytics charts. Caller passes $charts array with optional keys. --}}
<style>
    .chart-card { padding: 1rem; }
    .chart-card h6 { font-weight: 700; color: #555; margin-bottom: .75rem; letter-spacing: .3px; }
    .chart-card canvas { max-height: 280px; }
</style>

<div class="row g-3 mb-4">
    @if(! empty($charts['daily']))
    <div class="col-lg-8">
        <div class="card chart-card">
            <h6>Son 30 Gün — Günlük Ciro & Sipariş</h6>
            <canvas id="dailyChart"></canvas>
        </div>
    </div>
    @endif

    @if(! empty($charts['hourly']))
    <div class="col-lg-4">
        <div class="card chart-card">
            <h6>Saatlik Yoğunluk</h6>
            <canvas id="hourlyChart"></canvas>
        </div>
    </div>
    @endif
</div>

<div class="row g-3 mb-4">
    @if(! empty($charts['topProducts']))
    <div class="col-lg-6">
        <div class="card chart-card">
            <h6>En Çok Satan Ürünler (Top 10 — adet)</h6>
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>
    @endif

    @if(! empty($charts['topBayis']))
    <div class="col-lg-6">
        <div class="card chart-card">
            <h6>Bayilere Göre Ciro</h6>
            <canvas id="topBayisChart"></canvas>
        </div>
    </div>
    @elseif(! empty($charts['topStores']))
    <div class="col-lg-6">
        <div class="card chart-card">
            <h6>Şubelere Göre Ciro</h6>
            <canvas id="topStoresChart"></canvas>
        </div>
    </div>
    @endif

    @if(! empty($charts['topTenants']))
    <div class="col-lg-6">
        <div class="card chart-card">
            <h6>Firmalara (Tenant) Göre Ciro</h6>
            <canvas id="topTenantsChart"></canvas>
        </div>
    </div>
    @endif

    @if(! empty($charts['topCustomers']))
    <div class="col-lg-6">
        <div class="card chart-card">
            <h6>En Çok Harcayan 10 Müşteri</h6>
            <canvas id="topCustomersChart"></canvas>
        </div>
    </div>
    @endif
</div>

@if(! empty($charts['topProductPerBayi']))
<div class="card chart-card mb-4">
    <h6>Her Bayinin En Çok Sattığı Ürün</h6>
    <table class="table table-clean align-middle mb-0">
        <thead><tr><th>Bayi</th><th>En Çok Satan Ürün</th><th>Adet</th></tr></thead>
        <tbody>
        @foreach($charts['topProductPerBayi'] as $r)
            <tr>
                <td><strong>{{ $r['bayi_name'] }}</strong></td>
                <td>{{ $r['product'] }}</td>
                <td><span class="badge text-bg-warning">{{ $r['qty'] }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@push('chart-init')
<script>
(function() {
    const goldColor = '#CDA863';
    const darkColor = '#1A1410';

    Chart.defaults.font.family = 'system-ui,-apple-system,sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#555';

    @if(! empty($charts['daily']))
    {
        const data = @json($charts['daily']);
        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: data.map(d => d.date.slice(5)),
                datasets: [
                    { label: 'Ciro (₺)', data: data.map(d => d.revenue), borderColor: goldColor, backgroundColor: 'rgba(205,168,99,0.15)', tension: .3, yAxisID: 'y', fill: true },
                    { label: 'Sipariş', data: data.map(d => d.orders), borderColor: darkColor, borderDash: [4, 3], tension: .3, yAxisID: 'y1' }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y:  { position: 'left',  title: { display: true, text: '₺' } },
                    y1: { position: 'right', title: { display: true, text: 'sipariş' }, grid: { drawOnChartArea: false } }
                }
            }
        });
    }
    @endif

    @if(! empty($charts['hourly']))
    {
        const data = @json($charts['hourly']);
        new Chart(document.getElementById('hourlyChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.hour + ':00'),
                datasets: [{ label: 'Sipariş', data: data.map(d => d.orders), backgroundColor: goldColor }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
    @endif

    @if(! empty($charts['topProducts']))
    {
        const data = @json($charts['topProducts']);
        new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.name),
                datasets: [{ label: 'Adet', data: data.map(d => d.qty), backgroundColor: goldColor }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
    @endif

    @if(! empty($charts['topBayis']))
    {
        const data = @json($charts['topBayis']);
        new Chart(document.getElementById('topBayisChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.name),
                datasets: [
                    { label: 'Ciro (₺)',  data: data.map(d => d.revenue), backgroundColor: goldColor },
                    { label: 'Sipariş',   data: data.map(d => d.orders),  backgroundColor: darkColor }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
    @endif

    @if(empty($charts['topBayis']) && ! empty($charts['topStores']))
    {
        const data = @json($charts['topStores']);
        new Chart(document.getElementById('topStoresChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.name),
                datasets: [
                    { label: 'Ciro (₺)', data: data.map(d => d.revenue), backgroundColor: goldColor },
                    { label: 'Sipariş',  data: data.map(d => d.orders),  backgroundColor: darkColor }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
    @endif

    @if(! empty($charts['topTenants']))
    {
        const data = @json($charts['topTenants']);
        new Chart(document.getElementById('topTenantsChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.name),
                datasets: [{ label: 'Ciro (₺)', data: data.map(d => d.revenue), backgroundColor: goldColor }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
    @endif

    @if(! empty($charts['topCustomers']))
    {
        const data = @json($charts['topCustomers']);
        new Chart(document.getElementById('topCustomersChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.name),
                datasets: [
                    { label: 'Harcama (₺)', data: data.map(d => d.spent),  backgroundColor: goldColor },
                    { label: 'Sipariş',     data: data.map(d => d.orders), backgroundColor: darkColor }
                ]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
        });
    }
    @endif
})();
</script>
@endpush

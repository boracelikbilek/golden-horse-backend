@extends('admin.layout')
@section('title', 'QR Tarayıcı')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">📷 Müşteri QR Kodunu Tara</h5>
                <div class="qr-region">
                    <div class="reader-box">
                        <div id="reader" style="width:100%;"></div>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-secondary btn-sm" id="restart-btn" style="display:none;">Yeniden Başlat</button>
                    </div>
                </div>
                <div id="status" class="alert alert-info mt-3" style="display:none;"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card" id="customer-card" style="display:none;">
            <div class="card-body">
                <h5 class="mb-3">Müşteri Bilgisi</h5>
                <div id="customer-info"></div>

                <hr>
                <h6 class="mt-3">Sipariş Tutarını Gir</h6>
                <form method="POST" action="{{ route('admin.scanner.order') }}" id="order-form">
                    @csrf
                    <input type="hidden" name="token" id="qr-token">
                    <div class="mb-3">
                        <label class="form-label">Toplam tutar (₺)</label>
                        <input class="form-control form-control-lg" type="number" step="0.01" min="0.01" name="total" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Not (opsiyonel)</label>
                        <input class="form-control" name="note" maxlength="500">
                    </div>
                    <button class="btn btn-dark btn-lg w-100">Siparişi Onayla & Yıldız Ver</button>
                </form>
            </div>
        </div>

        <div class="card" id="empty-card">
            <div class="card-body text-center text-muted py-5">
                <div style="font-size:3rem;">⬢</div>
                <p>QR kod taradığında müşteri bilgisi burada görünecek.</p>
                <p class="small">Her 25 ₺ harcama için 1 yıldız.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const reader = new Html5Qrcode("reader");
const status = document.getElementById('status');
const customerCard = document.getElementById('customer-card');
const emptyCard = document.getElementById('empty-card');
const customerInfo = document.getElementById('customer-info');
const tokenInput = document.getElementById('qr-token');
const restartBtn = document.getElementById('restart-btn');
let scanning = false;

function showStatus(msg, type='info') {
    status.style.display = 'block';
    status.className = 'alert mt-3 alert-' + type;
    status.textContent = msg;
}

async function onScan(decoded) {
    if (!scanning) return;
    scanning = false;
    await reader.stop();
    showStatus('QR okundu, doğrulanıyor...', 'info');

    try {
        const r = await fetch('{{ route("admin.scanner.resolve") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({token: decoded})
        });
        const data = await r.json();
        if (!data.ok) {
            showStatus(data.message || 'Geçersiz QR.', 'danger');
            restartBtn.style.display = 'inline-block';
            return;
        }
        const u = data.user;
        tokenInput.value = data.session.token;
        customerInfo.innerHTML = `
            <div class="d-flex align-items-center mb-2">
                <div style="font-size:2rem;margin-right:10px;">👤</div>
                <div>
                    <div style="font-size:1.2rem;font-weight:600;">${u.name}</div>
                    <small class="text-muted">${u.email} • ${u.phone || '—'}</small>
                </div>
            </div>
            <div class="row text-center mt-3">
                <div class="col"><div class="text-muted small">Tier</div><strong><span class="badge badge-tier-${u.tier}">${u.tier.toUpperCase()}</span></strong></div>
                <div class="col"><div class="text-muted small">Yıldız</div><strong>${u.stars} / ${u.starTarget}</strong></div>
                <div class="col"><div class="text-muted small">Toplam Sipariş</div><strong>${u.lifetimeOrders}</strong></div>
                <div class="col"><div class="text-muted small">Toplam Harcama</div><strong>${Number(u.lifetimeSpent).toFixed(2)} ₺</strong></div>
            </div>`;
        customerCard.style.display = 'block';
        emptyCard.style.display = 'none';
        status.style.display = 'none';
        document.querySelector('input[name=total]').focus();
    } catch (e) {
        showStatus('Bağlantı hatası: ' + e.message, 'danger');
        restartBtn.style.display = 'inline-block';
    }
}

async function startScanner() {
    scanning = true;
    restartBtn.style.display = 'none';
    customerCard.style.display = 'none';
    emptyCard.style.display = 'block';
    status.style.display = 'none';
    try {
        await reader.start({facingMode:"environment"}, {fps:10, qrbox:{width:240,height:240}}, onScan, () => {});
    } catch (e) {
        showStatus('Kameraya erişilemedi: ' + e, 'warning');
    }
}

restartBtn.addEventListener('click', startScanner);
startScanner();
</script>
@endsection

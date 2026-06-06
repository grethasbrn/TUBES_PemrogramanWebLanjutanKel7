@extends('layouts.apoteker')

@section('content')

<div class="page-section active" id="sec-dashboard">

  <div class="page-header">
    <div>
      <div class="page-title">Dashboard</div>
      <div class="page-sub">Ringkasan aktivitas farmasi hari ini</div>
    </div>
    <a href="{{ route('apoteker.stock', ['add' => 'true']) }}" class="btn btn-primary">+ Input Batch Obat</a>
  </div>

  <!-- Metrics -->
  <div class="metrics">
    <div class="metric">
      <div class="metric-label">Total Jenis Obat</div>
      <div class="metric-val" id="m-total">{{ $totalJenisObat }}</div>
      <div class="metric-sub {{ $tambahBulanIni >= 0 ? 'up' : 'dn' }}">
        {{ $tambahBulanIni >= 0 ? '+' : '' }}{{ $tambahBulanIni }} bulan ini
      </div>
    </div>
    <div class="metric">
      <div class="metric-label">Resep Hari Ini</div>
      <div class="metric-val" id="m-resep">{{ $resepHariIni }}</div>
      <div class="metric-sub {{ $selisihKemarin >= 0 ? 'up' : 'dn' }}">
        {{ $selisihKemarin >= 0 ? '+' : '' }}{{ $selisihKemarin }} vs kemarin
      </div>
    </div>
    <div class="metric">
      <div class="metric-label">Stok Kritis</div>
      <div class="metric-val dn" id="m-kritis">{{ $stokKritis > 0 ? $stokKritis : '—' }}</div>
      <div class="metric-sub dn">Perlu restock</div>
    </div>
    <div class="metric">
      <div class="metric-label">Mendekati Expired</div>
      <div class="metric-val warn" id="m-exp">{{ $mendekatiExpired > 0 ? $mendekatiExpired : '—' }}</div>
      <div class="metric-sub warn">Dalam 90 hari</div>
    </div>
  </div>

  <!-- Alert Banner -->
  <div id="alertBanner" class="alert-banner danger" style="display:none">
    <span>⚠</span>
    <div class="alert-marquee">
      <div class="alert-track" id="alertTrack"></div>
    </div>
  </div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Resep masuk — 7 hari terakhir</div>
      <canvas id="chartResep" height="180"></canvas>
    </div>
    <div class="card">
      <div class="card-title">Alert aktif</div>
      <div id="alertList"><p style="color:#aaa;font-size:13px">Memuat...</p></div>
    </div>
  </div>

  <div class="grid22">
    <div class="card">
      <div class="card-title">Aktivitas Terbaru</div>
      <div id="activityList"><p style="color:#aaa;font-size:13px">Memuat...</p></div>
    </div>
    <div class="card">
      <div class="card-title">Distribusi Tipe Obat</div>
      <canvas id="chartTipe" height="200"></canvas>
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script>
let chartResepInstance = null;
let chartTipeInstance  = null;

async function loadDashboard() {
    try {
        const res  = await fetch('/apoteker/api/dashboard');
        const data = await res.json();

        renderAlertBanner(data.alerts ?? []);
        renderAlertList(data.alerts ?? []);
        renderActivityList(data.aktivitas ?? []);
        renderChartResep(data.resep7hari ?? []);
        renderChartTipe(data.distribusiTipe ?? []);

    } catch (e) {
        console.error('Gagal load dashboard:', e);
    }
}

// ── Alert Banner (marquee) ──────────────────────────────
function renderAlertBanner(alerts) {
    const banner = document.getElementById('alertBanner');
    const track  = document.getElementById('alertTrack');
    if (!alerts.length) { banner.style.display = 'none'; return; }

    banner.style.display = 'flex';
    const msgs = alerts.map(a => `${a.icon ?? '⚠'} ${a.pesan}`).join('   •   ');
    track.textContent = msgs + '   •   ' + msgs; // duplikat biar marquee seamless
}

// ── Alert List (card kanan) ─────────────────────────────
function renderAlertList(alerts) {
    const el = document.getElementById('alertList');
    if (!alerts.length) {
        el.innerHTML = '<p style="color:#aaa;font-size:13px;padding:8px 0">Tidak ada alert aktif. 🎉</p>';
        return;
    }
    el.innerHTML = alerts.map(a => `
        <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid #f0ece4">
            <span style="font-size:18px">${a.icon ?? '⚠'}</span>
            <div>
                <div style="font-size:13px;font-weight:500;color:#2C2416">${a.pesan}</div>
                <div style="font-size:11px;color:#A8998A;margin-top:2px">${a.sub ?? ''}</div>
            </div>
        </div>
    `).join('');
}

// ── Aktivitas Terbaru ───────────────────────────────────
function renderActivityList(aktivitas) {
    const el = document.getElementById('activityList');
    if (!aktivitas.length) {
        el.innerHTML = '<p style="color:#aaa;font-size:13px;padding:8px 0">Belum ada aktivitas.</p>';
        return;
    }
    el.innerHTML = aktivitas.map(a => `
        <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid #f0ece4">
            <span style="font-size:16px">${a.icon ?? '📋'}</span>
            <div style="flex:1">
                <div style="font-size:13px;color:#2C2416">${a.pesan}</div>
                <div style="font-size:11px;color:#A8998A;margin-top:2px">${a.waktu ?? ''}</div>
            </div>
        </div>
    `).join('');
}

// ── Chart Resep 7 Hari ──────────────────────────────────
function renderChartResep(data) {
    const ctx = document.getElementById('chartResep').getContext('2d');
    if (chartResepInstance) chartResepInstance.destroy();

    chartResepInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.tanggal),
            datasets: [{
                label: 'Resep masuk',
                data: data.map(d => d.jumlah),
                backgroundColor: 'rgba(139,125,184,0.7)',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
}

// ── Chart Distribusi Tipe ───────────────────────────────
function renderChartTipe(data) {
    const ctx = document.getElementById('chartTipe').getContext('2d');
    if (chartTipeInstance) chartTipeInstance.destroy();

    if (!data.length) return;

    chartTipeInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.tipe || 'Lainnya'),
            datasets: [{
                data: data.map(d => d.jumlah),
                backgroundColor: [
                    '#8B7DB8','#2A9D8F','#C9972A','#E63946',
                    '#52B788','#F4A261','#6B5E9A','#1F7A6F',
                ],
                borderWidth: 2,
                borderColor: '#FDFAF5',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 12 } } }
            }
        }
    });
}

// ── Init ────────────────────────────────────────────────
loadDashboard();
</script>
@endpush
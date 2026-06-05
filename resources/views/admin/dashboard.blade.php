@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-dashboard">
  <div class="page-header">
    <div>
      <div class="page-title">Dashboard</div>
      <div class="page-sub">Ringkasan operasional rumah sakit hari ini</div>
    </div>
    <a href="{{ route('pasien.create') }}" class="btn btn-primary">+ Daftar Pasien Baru</a>
  </div>

  <div class="metrics">
    <div class="metric">
      <div class="metric-label">Total Pasien Hari Ini</div>
      <div class="metric-val" id="m-total-pasien">0</div>
      <div class="metric-sub up" id="m-total-sub">Loading...</div>
    </div>
    <div class="metric">
      <div class="metric-label">Menunggu Validasi</div>
      <div class="metric-val warn" id="m-validasi">0</div>
      <div class="metric-sub warn">Perlu dikonfirmasi</div>
    </div>
    <div class="metric">
      <div class="metric-label">Invoice Masuk</div>
      <div class="metric-val" id="m-invoice" style="color:var(--blue)">0</div>
      <div class="metric-sub info">Dari apoteker</div>
    </div>
    <div class="metric">
      <div class="metric-label">Pemasukan Hari Ini</div>
      <div class="metric-val" id="m-pemasukan" style="font-size:20px">Rp 0</div>
      <div class="metric-sub up" id="m-pemasukan-sub">0 transaksi lunas</div>
    </div>
  </div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Antrian Pasien per Poli</div>
      <div id="dashAntrianPoli"></div>
    </div>
    <div class="card">
      <div class="card-title">Aktivitas Terbaru</div>
      <div id="dashAktivitas"></div>
    </div>
  </div>

  <div class="grid22">
    <div class="card" style="position: relative; height: 320px; min-height: 320px;">
        <div class="card-title" style="margin-bottom: 15px;">Status Pasien Hari Ini</div>
        <div style="position: relative; height: 230px; width: 100%;">
            <canvas id="chartStatusPasien"></canvas>
        </div>
    </div>
    <div class="card" style="position: relative; height: 320px; min-height: 320px;">
      <div class="card-title" style="margin-bottom: 15px;">Pemasukan 7 Hari Terakhir</div>
      <div style="position: relative; height: 230px; width: 100%;">
          <canvas id="chartPemasukan"></canvas>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('/admin/api/stats')
        .then(r => r.json())
        .then(data => {
            // ===== METRICS =====
            document.getElementById('m-total-pasien').innerText  = data.total_hari_ini;
            document.getElementById('m-total-sub').innerText     = data.total_hari_ini + ' pasien terdaftar hari ini';
            document.getElementById('m-validasi').innerText      = data.menunggu_validasi;
            document.getElementById('m-invoice').innerText       = data.invoice;
            document.getElementById('m-pemasukan').innerText     = 'Rp ' + data.pemasukan.toLocaleString('id-ID');
            document.getElementById('m-pemasukan-sub').innerText = data.transaksi_lunas + ' transaksi lunas';

            // ===== 2. ANTRIAN PER POLI =====
            const poliWrap = document.getElementById('dashAntrianPoli');
            if (!data.antri_per_poli || data.antri_per_poli.length === 0) {
                poliWrap.innerHTML = '<div style="color:#C4B5A5;font-size:13px;padding:16px">Belum ada antrian hari ini</div>';
            } else {
                poliWrap.innerHTML = data.antri_per_poli.map(p => `
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f7f3f0">
                        <span style="font-size:13px;color:#2d2016">${p.poli_tujuan ? p.poli_tujuan : 'Umum'}</span>
                        <span style="font-size:13px;font-weight:700;color:#c0825a">${p.total ?? 0} pasien</span>
                    </div>
                `).join('');
            }

            // ===== 3. AKTIVITAS TERBARU =====
            const aktWrap = document.getElementById('dashAktivitas');
            if (!data.aktivitas || data.aktivitas.length === 0) {
                aktWrap.innerHTML = '<div style="color:#C4B5A5;font-size:13px;padding:16px">Belum ada aktivitas</div>';
            } else {
                aktWrap.innerHTML = data.aktivitas.map(a => {
                    const inisial = a.nama ? a.nama.charAt(0).toUpperCase() : '?';
                    return `
                        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f7f3f0">
                            <div style="width:36px;height:36px;border-radius:50%;background:#fde8d8;display:flex;align-items:center;justify-content:center;font-weight:700;color:#c0825a;font-size:14px;flex-shrink:0">
                                ${inisial}
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:600;color:#2d2016">${a.nama ?? 'Tanpa Nama'}</div>
                                <div style="font-size:11px;color:#A8998A">${a.no_rm ?? '-'} · ${a.status ?? 'Aktif'}</div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // ===== 4. CHART STATUS PASIEN =====
            const ctxStatus = document.getElementById('chartStatusPasien').getContext('2d');
            const statusLabels = data.status_pasien ? data.status_pasien.map(s => s.status ?? 'N/A') : [];
            const statusData   = data.status_pasien ? data.status_pasien.map(s => s.total ?? 0) : [];
            
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels.length ? statusLabels : ['Belum ada data'],
                    datasets: [{
                        data: statusData.length ? statusData : [1],
                        backgroundColor: ['#c0825a', '#4caf50', '#1976d2', '#ff9800'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    cutout: '65%',
                }
            });

            // ===== CHART PEMASUKAN 7 HARI (dari DB) =====
            const ctxPemasukan = document.getElementById('chartPemasukan').getContext('2d');
            new Chart(ctxPemasukan, {
                type: 'bar',
                data: {
                    labels: data.labels_7hari,
                    datasets: [{
                        label: 'Pemasukan (Rp)',
                        data: data.pemasukan_7hari,
                        backgroundColor: '#c0825a',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: 11 },
                                callback: v => 'Rp ' + v.toLocaleString('id-ID')
                            }
                        },
                        x: { ticks: { font: { size: 11 } } }
                    }
                }
            });
        })
        .catch(err => {
            console.error('Gagal ambil data dashboard:', err);
        });
});
</script>
@endpush
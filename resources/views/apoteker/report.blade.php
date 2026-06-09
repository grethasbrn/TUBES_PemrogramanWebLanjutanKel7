@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-laporan">
  <div class="page-header">
    <div>
      <div class="page-title">Laporan Bulanan</div>
      <div class="page-sub">Ringkasan aktivitas, pasien, dan keuangan</div>
    </div>
    <div class="no-print" style="display:flex;gap:8px;align-items:center">
        <div class="custom-dropdown" id="bulanDropdown">

            <div class="dropdown-selected">
                Juni 2026
                <span>▼</span>
            </div>

            <div class="dropdown-options">
                <div class="dropdown-option" data-value="1">Januari 2026</div>
                <div class="dropdown-option" data-value="2">Februari 2026</div>
                <div class="dropdown-option" data-value="3">Maret 2026</div>
                <div class="dropdown-option" data-value="4">April 2026</div>
                <div class="dropdown-option" data-value="5">Mei 2026</div>
                <div class="dropdown-option" data-value="6">Juni 2026</div>
                <div class="dropdown-option" data-value="7">Juli 2026</div>
                <div class="dropdown-option" data-value="8">Agustus 2026</div>
                <div class="dropdown-option" data-value="9">September 2026</div>
                <div class="dropdown-option" data-value="10">Oktober 2026</div>
                <div class="dropdown-option" data-value="11">November 2026</div>
                <div class="dropdown-option" data-value="12">Desember 2026</div>
            </div>

            <input type="hidden" id="laporanBulan" value="6">
        </div>

        <button class="btn btn-teal no-print" onclick="window.print()">
            Cetak
        </button>

        <button class="btn btn-primary no-print" onclick="exportCSV()">
            Export CSV
        </button>
    </div>
  </div>

  <!-- Stats row -->
  <div class="grid3" id="laporanStats">
    <div class="card" style="text-align:center"><div class="card-title">Memuat...</div></div>
  </div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Tren Kunjungan Pasien</div>
      <canvas id="chartKunjungan" height="200"></canvas>
    </div>
    <div class="card">
      <div class="card-title">Distribusi Jenis Pasien</div>
      <canvas id="chartJenisPasien" height="200"></canvas>
    </div>
  </div>

  <div class="grid22">
    <div class="card">
      <div class="card-title">Pasien per Poli</div>
      <div id="laporanPerPoli"><p style="color:#888">Memuat...</p></div>
    </div>
    <div class="card">
      <div class="card-title">Ringkasan Keuangan</div>
      <div id="laporanKeuangan"><p style="color:#888">Memuat...</p></div>
    </div>
  </div>

  <!-- Detail Table -->
  <div class="card">
    <div class="card-title">Detail Transaksi</div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>No. Transaksi</th>
            <th>Pasien</th>
            <th>Poli</th>
            <th>Jenis</th>
            <th>Total</th>
            <th>Referensi</th>
            <th>Status</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody id="tblLaporanDetail">
          <tr><td colspan="8" style="text-align:center">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const bulanDropdown = document.getElementById('bulanDropdown');
const bulanSelected = bulanDropdown.querySelector('.dropdown-selected');
const bulanOptions = bulanDropdown.querySelector('.dropdown-options');
const laporanBulan = document.getElementById('laporanBulan');

bulanSelected.addEventListener('click', () => {
    bulanOptions.classList.toggle('show');
});

bulanDropdown.querySelectorAll('.dropdown-option').forEach(option => {

    option.addEventListener('click', () => {

        bulanSelected.innerHTML =
            option.textContent + '<span>▼</span>';

        laporanBulan.value = option.dataset.value;

        bulanOptions.classList.remove('show');

    });

});

document.addEventListener('click', (e) => {

    if (!bulanDropdown.contains(e.target)) {
        bulanOptions.classList.remove('show');
    }

});

  // ── State ──────────────────────────────────────────────
  let chartKunjungan = null;
  let chartJenis     = null;
  let currentDetail  = [];

  // ── Helpers ────────────────────────────────────────────
  const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');

  const badgeStatus = s => {
    const map = { 'Lunas': 'teal', 'Belum Lunas': 'orange', 'Batal': 'red' };
    const color = map[s] ?? 'gray';
    return `<span style="background:${color};color:#fff;padding:2px 8px;border-radius:99px;font-size:12px">${s}</span>`;
  };

  // ── Main Load ───────────────────────────────────────────
  async function loadLaporan(bulan) {
    try {
      const res  = await fetch(`{{ route('admin.report.stats') }}?bulan=${bulan}&tahun=2026`);
      const data = await res.json();

      renderStats(data.stats);
      renderChartKunjungan(data.trend);
      renderChartJenis(data.jenis_pasien);
      renderPerPoli(data.per_poli);
      renderKeuangan(data.keuangan);
      renderDetail(data.detail);
      currentDetail = data.detail;
    } catch (e) {
      console.error('Gagal load laporan:', e);
    }
  }

  // ── Stats Cards ─────────────────────────────────────────
  function renderStats(s) {
    document.getElementById('laporanStats').innerHTML = `
      <div class="card" style="text-align:center">
        <div style="font-size:28px;font-weight:700;color:#0d9488">${s.total_pasien}</div>
        <div class="card-title" style="margin-top:4px">Total Pasien</div>
      </div>
      <div class="card" style="text-align:center">
        <div style="font-size:28px;font-weight:700;color:#3b82f6">${s.total_invoice}</div>
        <div class="card-title" style="margin-top:4px">Total Invoice</div>
      </div>
      <div class="card" style="text-align:center">
        <div style="font-size:22px;font-weight:700;color:#10b981">${rupiah(s.total_pemasukan)}</div>
        <div class="card-title" style="margin-top:4px">Pemasukan (Lunas)</div>
      </div>
    `;
  }

  // ── Chart Kunjungan ─────────────────────────────────────
  function renderChartKunjungan(trend) {
    if (chartKunjungan) chartKunjungan.destroy();
    chartKunjungan = new Chart(document.getElementById('chartKunjungan'), {
      type: 'bar',
      data: {
        labels: trend.labels.length ? trend.labels : ['Tidak ada data'],
        datasets: [{
          label: 'Kunjungan',
          data: trend.values.length ? trend.values : [0],
          backgroundColor: '#0d9488',
          borderRadius: 6,
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });
  }

  // ── Chart Jenis Pasien ──────────────────────────────────
  function renderChartJenis(data) {
    if (chartJenis) chartJenis.destroy();
    if (!data.length) {
      document.getElementById('chartJenisPasien').parentElement.innerHTML +=
        '<p style="color:#888;text-align:center">Tidak ada data</p>';
      return;
    }
    chartJenis = new Chart(document.getElementById('chartJenisPasien'), {
      type: 'doughnut',
      data: {
        labels: data.map(d => d.jenis || 'Lainnya'),
        datasets: [{
          data: data.map(d => d.total),
          backgroundColor: ['#0d9488','#3b82f6','#f59e0b','#ef4444','#8b5cf6'],
        }]
      },
      options: { responsive: true }
    });
  }

  // ── Per Poli ────────────────────────────────────────────
  function renderPerPoli(data) {
    if (!data.length) {
      document.getElementById('laporanPerPoli').innerHTML = '<p style="color:#888">Tidak ada data</p>';
      return;
    }
    const max = Math.max(...data.map(d => d.total));
    document.getElementById('laporanPerPoli').innerHTML = data.map(d => `
      <div style="margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;margin-bottom:3px">
          <span>${d.poli_tujuan}</span><strong>${d.total}</strong>
        </div>
        <div style="background:#e5e7eb;border-radius:99px;height:8px">
          <div style="background:#0d9488;height:8px;border-radius:99px;width:${(d.total/max*100).toFixed(1)}%"></div>
        </div>
      </div>
    `).join('');
  }

  // ── Ringkasan Keuangan ──────────────────────────────────
  function renderKeuangan(k) {
    const total = Number(k.lunas) + Number(k.belum_lunas);
    document.getElementById('laporanKeuangan').innerHTML = `
      <table style="width:100%;border-collapse:collapse">
        <tr style="border-bottom:1px solid #e5e7eb">
          <td style="padding:8px 4px">Total Tagihan</td>
          <td style="padding:8px 4px;text-align:right;font-weight:600">${rupiah(total)}</td>
        </tr>
        <tr style="border-bottom:1px solid #e5e7eb">
          <td style="padding:8px 4px;color:#10b981">✔ Sudah Lunas</td>
          <td style="padding:8px 4px;text-align:right;font-weight:600;color:#10b981">${rupiah(k.lunas)}</td>
        </tr>
        <tr>
          <td style="padding:8px 4px;color:#f59e0b">⏳ Belum Lunas</td>
          <td style="padding:8px 4px;text-align:right;font-weight:600;color:#f59e0b">${rupiah(k.belum_lunas)}</td>
        </tr>
      </table>
    `;
  }

  // ── Detail Tabel ────────────────────────────────────────
  function renderDetail(rows) {
    const tbody = document.getElementById('tblLaporanDetail');
    if (!rows.length) {
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#888">Tidak ada transaksi bulan ini</td></tr>';
      return;
    }
    tbody.innerHTML = rows.map((r, i) => `
      <tr>
        <td>${r.no_invoice}</td>
        <td>${r.nama}</td>
        <td>${r.poli_tujuan}</td>
        <td>${r.jenis ?? '-'}</td>
        <td>${rupiah(r.total_tagihan)}</td>
        <td>${r.no_referensi}</td>
        <td>${badgeStatus(r.status)}</td>
        <td>${new Date(r.created_at).toLocaleDateString('id-ID')}</td>
      </tr>
    `).join('');
  }

  // ── Export CSV ──────────────────────────────────────────
  function exportCSV() {
    if (!currentDetail.length) return alert('Tidak ada data untuk diexport');
    const header = ['No Invoice','Pasien','Poli','Jenis','Total','Referensi','Status','Tanggal'];
    const rows   = currentDetail.map(r => [
      r.no_invoice, r.nama, r.poli_tujuan, r.jenis ?? '-',
      r.total_tagihan, r.no_referensi, r.status,
      new Date(r.created_at).toLocaleDateString('id-ID')
    ]);
    const csv  = [header, ...rows].map(r => r.join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `laporan_bulan_${document.getElementById('laporanBulan').value}.csv`;
    a.click();
  }

  // ── Event: filter bulan berubah ─────────────────────────
  document.getElementById('laporanBulan').addEventListener('change', function () {
    loadLaporan(this.value);
  });

  // ── Init ────────────────────────────────────────────────
  const bulanAwal = new Date().getMonth() + 1;
  document.getElementById('laporanBulan').value = bulanAwal;
  loadLaporan(bulanAwal);
</script>
@endpush
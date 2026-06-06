@extends('layouts.apoteker')

@section('content')

<div class="page-section active" id="sec-report">
  <div class="page-header">
    <div>
      <div class="page-title">Report</div>
      <div class="page-sub">Laporan bulanan aktivitas farmasi</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      @php
        $months = [
          '2026-06' => 'Juni 2026',
          '2026-05' => 'Mei 2026',
          '2026-04' => 'April 2026',
          '2026-03' => 'Maret 2026',
          '2026-02' => 'Februari 2026',
          '2026-01' => 'Januari 2026',
        ];
      @endphp

      <div class="custom-dropdown">
        <div class="dropdown-selected">Juni 2026 <span>▼</span></div>
        <div class="dropdown-options">
          @foreach($months as $val => $label)
            <div class="dropdown-option" data-value="{{ $val }}">{{ $label }}</div>
          @endforeach
        </div>
        <input type="hidden" id="reportMonth" value="2026-06">
      </div>

      <button class="btn btn-teal" onclick="exportPDF()">⬇ Export PDF</button>
    </div>
  </div>

  {{-- Metrics --}}
  <div class="metrics" style="margin-bottom:14px">
    <div class="metric">
      <div class="metric-label">Total Resep</div>
      <div class="metric-val" id="m-totalResep">—</div>
      <div class="metric-sub" id="m-pctResep">memuat...</div>
    </div>
    <div class="metric">
      <div class="metric-label">Resep Selesai</div>
      <div class="metric-val" id="m-resepSelesai">—</div>
      <div class="metric-sub" id="m-completion">memuat...</div>
    </div>
    <div class="metric">
      <div class="metric-label">Total Pendapatan</div>
      <div class="metric-val" id="m-pendapatan" style="font-size:20px">—</div>
      <div class="metric-sub" id="m-pctPendapatan">memuat...</div>
    </div>
    <div class="metric">
      <div class="metric-label">Obat Terlaris</div>
      <div class="metric-val" id="m-topObat">—</div>
      <div class="metric-sub">jenis obat bulan ini</div>
    </div>
  </div>

  {{-- Charts --}}
  <div class="grid22">
    <div class="card">
      <div class="card-title">10 Obat Terlaris</div>
      <div id="topDrugsChart" style="margin-top:12px"></div>
    </div>
    <div class="card">
      <div class="card-title">Pendapatan per Minggu</div>
      <canvas id="chartPendapatan" height="220" style="margin-top:12px"></canvas>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// ── Dropdown ─────────────────────────────────────────────
document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
    const selected = dropdown.querySelector('.dropdown-selected');
    const options  = dropdown.querySelector('.dropdown-options');
    const hidden   = dropdown.querySelector('input[type="hidden"]');

    selected.addEventListener('click', (e) => {
        e.stopPropagation();
        options.classList.toggle('show');
    });

    dropdown.querySelectorAll('.dropdown-option').forEach(option => {
        option.addEventListener('click', () => {
            selected.innerHTML = option.textContent + ' <span>▼</span>';
            hidden.value = option.dataset.value;
            options.classList.remove('show');
            if (hidden.id === 'reportMonth') loadReport();
        });
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-options').forEach(opt => opt.classList.remove('show'));
});

// ── Load Report ───────────────────────────────────────────
let chartInstance = null;

async function loadReport() {
    const monthVal = document.getElementById('reportMonth').value;
    const [year, month] = monthVal.split('-');

    try {
        const res  = await fetch(`/apoteker/api/report?month=${month}&year=${year}`);
        const data = await res.json();

        // Metrics
        document.getElementById('m-totalResep').textContent   = data.totalResep;
        document.getElementById('m-resepSelesai').textContent = data.resepSelesai;
        document.getElementById('m-topObat').textContent      = data.topObat.length;

        const pend = data.totalPendapatan;
        document.getElementById('m-pendapatan').textContent =
            pend >= 1000000
                ? 'Rp ' + (pend / 1000000).toFixed(1) + 'jt'
                : 'Rp ' + Number(pend).toLocaleString('id-ID');

        const elPctR = document.getElementById('m-pctResep');
        elPctR.textContent = (data.pctResep >= 0 ? '+' : '') + data.pctResep + '% vs bulan lalu';
        elPctR.className   = 'metric-sub ' + (data.pctResep >= 0 ? 'up' : 'dn');

        const elComp = document.getElementById('m-completion');
        elComp.textContent = data.completionRate + '% completion';
        elComp.className   = 'metric-sub up';

        const elPctP = document.getElementById('m-pctPendapatan');
        elPctP.textContent = (data.pctPendapatan >= 0 ? '+' : '') + data.pctPendapatan + '% vs bulan lalu';
        elPctP.className   = 'metric-sub ' + (data.pctPendapatan >= 0 ? 'up' : 'dn');

        renderTopObat(data.topObat);
        renderChartPendapatan(data.pendapatanMinggu);

    } catch (err) {
        console.error('Gagal load report:', err);
    }
}

// ── Top Obat ──────────────────────────────────────────────
function renderTopObat(topObat) {
    const container = document.getElementById('topDrugsChart');
    if (!topObat.length) {
        container.innerHTML = '<div style="text-align:center;color:var(--text3);padding:20px">Belum ada data</div>';
        return;
    }
    const max = topObat[0]?.qty || 1;
    container.innerHTML = topObat.map(o => {
        const pct = Math.round((o.qty / max) * 100);
        return `
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                <div style="width:90px;font-size:12px;color:var(--text2);text-align:right;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                     title="${o.nama}">${o.nama}</div>
                <div style="flex:1;background:var(--cream3);border-radius:4px;height:18px;overflow:hidden">
                    <div style="width:${pct}%;background:var(--purple2);height:100%;border-radius:4px;
                                transition:width .4s ease"></div>
                </div>
                <div style="width:30px;font-size:12px;color:var(--text);font-weight:500">${o.qty}</div>
            </div>`;
    }).join('');
}

// ── Chart Pendapatan ──────────────────────────────────────
function renderChartPendapatan(weeks) {
    const ctx = document.getElementById('chartPendapatan').getContext('2d');
    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: weeks.map(w => w.label),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: weeks.map(w => w.pendapatan),
                backgroundColor: 'rgba(139, 126, 200, 0.6)',
                borderColor: 'rgba(139, 126, 200, 1)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => {
                            if (val >= 1000000) return 'Rp ' + (val/1000000).toFixed(1) + 'jt';
                            if (val >= 1000)    return 'Rp ' + (val/1000).toFixed(0) + 'rb';
                            return 'Rp ' + val;
                        }
                    }
                }
            }
        }
    });
}

function exportPDF() { window.print(); }

document.addEventListener('DOMContentLoaded', loadReport);
</script>
@endpush
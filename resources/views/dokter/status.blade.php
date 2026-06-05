@extends('layouts.dokter')

@section('content')

<div class="page-section active" id="sec-resep-status">
  <div class="page-header">
    <div>
      <div class="page-title">Status Resep</div>
      <div class="page-sub">Pantau validasi dan proses resep oleh apoteker</div>
    </div>
    <button class="btn" onclick="loadStatusResep()">🔄 Refresh</button>
  </div>

  <div class="tabs" style="margin-bottom:16px">
    <button class="tab-btn active" onclick="filterStatus('semua',this)">Semua</button>
    <button class="tab-btn" onclick="filterStatus('baru',this)">Baru</button>
    <button class="tab-btn" onclick="filterStatus('validasi',this)">Divalidasi</button>
    <button class="tab-btn" onclick="filterStatus('siap',this)">Siap Ambil</button>
    <button class="tab-btn" onclick="filterStatus('selesai',this)">Selesai</button>
  </div>

  <div class="rx-layout" style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div id="statusResepList">
      <div class="empty-state card" style="text-align:center;padding:40px;color:#aaa">
        <div style="font-size:32px;margin-bottom:10px">⏳</div>
        <div>Memuat data...</div>
      </div>
    </div>
    <div id="statusResepDetail">
      <div class="empty-state card" style="text-align:center;padding:40px;color:#aaa">
        <div style="font-size:32px;margin-bottom:10px">📋</div>
        <div>Pilih resep untuk melihat detail</div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
let allResepStatus = @json($resepJson);
let statusFilter   = 'semua';
let selectedResepId = null;

function loadStatusResep() {
    renderStatusList();
}

function filterStatus(f, el) {
    statusFilter = f;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    if (el) el.classList.add('active');
    renderStatusList();
}

function renderStatusList() {
    let data = [...allResepStatus];
    if (statusFilter !== 'semua') {
        data = data.filter(r => r.status === statusFilter);
    }

    const container = document.getElementById('statusResepList');
    if (!data.length) {
        container.innerHTML = `<div class="card" style="text-align:center;padding:40px;color:#aaa">
            <div style="font-size:32px;margin-bottom:10px">📭</div>
            <div>Tidak ada resep</div>
        </div>`;
        return;
    }

    const statusColor = {
        baru: '#fff3e0', validasi: '#e8f0fe',
        siap: '#e6f4ea', selesai: '#f3e8ff', ditolak: '#fce8e6', draft: '#f5f5f5'
    };
    const statusLabel = {
        baru: 'Baru', validasi: 'Divalidasi', siap: 'Siap Ambil',
        selesai: 'Selesai', ditolak: 'Ditolak', draft: 'Draft'
    };

    container.innerHTML = data.map(r => `
        <div onclick="selectResepStatus('${r.id}')"
             style="border:1px solid #eee;border-radius:12px;padding:14px 16px;
                    margin-bottom:10px;cursor:pointer;transition:background .15s;
                    background:${selectedResepId === r.id ? '#f0ebff' : '#fff'}">
            <div style="display:flex;justify-content:space-between;align-items:flex-start">
                <div>
                    <div style="font-weight:600;font-size:14px">${r.no_resep}</div>
                    <div style="font-size:12px;color:#888;margin-top:2px">${r.pasien} · ${r.rm}</div>
                    <div style="font-size:12px;color:#888;margin-top:2px">📅 ${r.tanggal}</div>
                </div>
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                             background:${statusColor[r.status]||'#f5f5f5'}">
                    ${statusLabel[r.status] || r.status}
                </span>
            </div>
            <div style="font-size:12px;color:#666;margin-top:6px">🩺 ${r.diagnosa}</div>
        </div>
    `).join('');
}

function selectResepStatus(id) {
    selectedResepId = id;
    const r = allResepStatus.find(x => x.id === id);
    if (!r) return;

    renderStatusList();

    const statusLabel = {
        baru: '🆕 Baru — menunggu diproses apoteker',
        validasi: '✅ Sedang divalidasi apoteker',
        siap: '📦 Obat siap diambil',
        selesai: '✔️ Selesai',
        ditolak: '❌ Ditolak',
        draft: '💾 Draft'
    };

    document.getElementById('statusResepDetail').innerHTML = `
        <div class="card" style="padding:20px">
            <div style="font-family:'Cormorant Garamond',serif;font-size:20px;
                        font-weight:600;margin-bottom:4px">${r.no_resep}</div>
            <div style="font-size:13px;color:#888;margin-bottom:16px">${r.pasien} · ${r.rm}</div>

            <div style="background:#f9f7f5;border-radius:10px;padding:12px;margin-bottom:14px">
                <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                            letter-spacing:.06em;margin-bottom:6px">Status</div>
                <div style="font-size:14px;font-weight:600">${statusLabel[r.status] || r.status}</div>
            </div>

            <div style="background:#f9f7f5;border-radius:10px;padding:12px;margin-bottom:14px">
                <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                            letter-spacing:.06em;margin-bottom:6px">Diagnosa</div>
                <div style="font-size:13px">${r.diagnosa}</div>
            </div>

            ${r.tanggal_kontrol && r.tanggal_kontrol !== '-' ? `
            <div style="background:#e8f5e9;border-radius:10px;padding:12px;margin-bottom:14px">
                <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                            letter-spacing:.06em;margin-bottom:4px">Tanggal Kontrol</div>
                <div style="font-size:13px;color:#2e7d32;font-weight:600">📅 ${r.tanggal_kontrol}</div>
            </div>` : ''}

            <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                        letter-spacing:.06em;margin-bottom:8px">Daftar Obat</div>
            ${r.obat && r.obat.length ? r.obat.map(o => `
                <div style="border:1px solid #eee;border-radius:8px;padding:10px 12px;
                            margin-bottom:6px;font-size:13px">
                    <div style="font-weight:500">${o.nama || '-'}</div>
                    <div style="font-size:11px;color:#888;margin-top:2px">
                        ${o.dosis || ''} · Jumlah: ${o.jumlah || '-'}
                    </div>
                </div>`).join('')
            : `<div style="color:#aaa;font-size:13px">Tidak ada obat</div>`}
        </div>
    `;
}

// Init
document.addEventListener('DOMContentLoaded', function () {
    loadStatusResep();
    setInterval(loadStatusResep, 30000);
});
</script>
@endsection
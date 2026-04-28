@extends('layouts.apoteker')

@section('content')

<div class="page-section active" id="sec-resep">
  <div class="page-header">
    <div>
      <div class="page-title">Validasi Resep</div>
      <div class="page-sub">Verifikasi dan proses resep dokter</div>
    </div>
  </div>

  <div class="rx-layout">
    <div>
      <div class="tabs">
        <button class="tab-btn active" onclick="filterResep('semua',this)">Semua</button>
        <button class="tab-btn" onclick="filterResep('baru',this)">Baru</button>
        <button class="tab-btn" onclick="filterResep('validasi',this)">Divalidasi</button>
        <button class="tab-btn" onclick="filterResep('siap',this)">Siap Ambil</button>
        <button class="tab-btn" onclick="filterResep('selesai',this)">Selesai</button>
      </div>
      <div id="resepList"></div>
    </div>
    <div id="resepDetail">
      <div class="card" style="text-align:center;padding:40px 20px;color:var(--text3)">
        <div style="font-size:32px;margin-bottom:10px">📋</div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:16px">Pilih resep untuk detail</div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Substitusi -->
<div class="modal-overlay" id="modalSubstitusi">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalSubstitusi')">✕</button>
    <h3>Substitusi Obat</h3>
    <div id="subsInfo" style="margin-bottom:14px;font-size:13px;color:var(--text2)"></div>
    <div class="fg"><label>Obat Pengganti</label>
      <select id="subsObat"></select>
    </div>
    <div class="fg"><label>Alasan Substitusi</label>
      <select id="subsAlasan">
        <option>Stok habis</option>
        <option>Obat tidak tersedia di formularium</option>
        <option>Harga terlalu tinggi (BPJS)</option>
        <option>Obat mendekati expired</option>
        <option>Permintaan pasien</option>
      </select>
    </div>
    <div class="fg"><label>Catatan Tambahan</label>
      <textarea id="subsCatatan" rows="2" placeholder="Keterangan penggantian obat..."></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeModal('modalSubstitusi')">Batal</button>
      <button class="btn btn-amber" onclick="simpanSubstitusi()">🔄 Konfirmasi Substitusi</button>
    </div>
  </div>
</div>
<script>
// =============================================
// APOTEKER — Load & tampilkan resep dari DB
// =============================================
let allResepApoteker = [];
let selectedResepApoteker = null;

async function loadResepApoteker() {
    try {
        const res = await fetch('/dokter/api/resep');
        allResepApoteker = await res.json();
        renderResepApoteker('semua');
    } catch (err) {
        console.error('Gagal load resep:', err);
    }
}

function filterResep(status, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    renderResepApoteker(status);
}

function renderResepApoteker(status) {
    const container = document.getElementById('resepList');
    if (!container) return;

    const list = status === 'semua' ? allResepApoteker : allResepApoteker.filter(r => r.status === status);

    if (list.length === 0) {
        container.innerHTML = `<div class="empty-state card"><div class="icon">📋</div><div class="label">Tidak ada resep</div></div>`;
        return;
    }

    const statusBadge = {
        baru: 'b-baru', draft: 'b-mandiri',
        validasi: 'b-validasi', siap: 'b-siap',
        selesai: 'b-selesai', ditolak: 'b-ditolak',
    };

    container.innerHTML = list.map(r => `
        <div class="rx-item" onclick='showResepApoteker(${JSON.stringify(JSON.stringify(r))})'>
            <div class="rx-no">${r.no_resep || 'RX-???'} <span class="badge ${statusBadge[r.status] || ''}">${r.status}</span></div>
            <div class="rx-meta">${r.pasien} · RM: ${r.rm} · ${r.bayar}</div>
            <div style="font-size:11px;color:#8FA3B1;margin-top:4px">${r.diagnosa} · ${r.tanggal}</div>
        </div>
    `).join('');
}

function showResepApoteker(jsonStr) {
    selectedResepApoteker = JSON.parse(jsonStr);
    const container = document.getElementById('resepDetail');
    if (!container) return;

    const obat = selectedResepApoteker.obat || [];
    const obatHtml = obat.length === 0
        ? '<div style="font-size:12px;color:#8FA3B1">-</div>'
        : obat.map(o => `
            <div style="border:1px solid #E4DDD0;border-radius:8px;padding:10px 12px;margin-bottom:8px;border-left:3px solid ${o.kategori === 'standar' ? '#2A9D8F' : '#8B7DB8'}">
                <div style="font-weight:600;font-size:13px">${o.nama}</div>
                <div style="font-size:11px;color:#4A6275">${o.dosis} · ${o.durasi} · ${o.instruksi || ''}</div>
                ${o.catatan ? `<div style="font-size:11px;color:#8FA3B1">Catatan: ${o.catatan}</div>` : ''}
            </div>
        `).join('');

    container.innerHTML = `
        <div class="detail-panel">
            <div class="detail-header">
                <div class="detail-header-title">${selectedResepApoteker.no_resep}</div>
                <div class="detail-header-sub">${selectedResepApoteker.pasien} · ${selectedResepApoteker.bayar}</div>
            </div>
            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-item"><label>No. RM</label><span>${selectedResepApoteker.rm}</span></div>
                    <div class="info-item"><label>Status</label><span>${selectedResepApoteker.status}</span></div>
                    <div class="info-item"><label>Diagnosa</label><span>${selectedResepApoteker.diagnosa}</span></div>
                    <div class="info-item"><label>Tanggal</label><span>${selectedResepApoteker.tanggal}</span></div>
                </div>
                <div style="margin-bottom:14px">
                    <div style="font-size:11px;color:#8FA3B1;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Obat Diresepkan</div>
                    ${obatHtml}
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <button class="btn btn-teal btn-sm" onclick="updateStatusResep('${selectedResepApoteker.id}', 'validasi')">✅ Validasi</button>
                    <button class="btn btn-green btn-sm" onclick="updateStatusResep('${selectedResepApoteker.id}', 'siap')">📦 Siap Ambil</button>
                    <button class="btn btn-sm" onclick="updateStatusResep('${selectedResepApoteker.id}', 'selesai')">🏁 Selesai</button>
                    <button class="btn btn-danger btn-sm" onclick="updateStatusResep('${selectedResepApoteker.id}', 'ditolak')">❌ Tolak</button>
                </div>
            </div>
        </div>
    `;
}

async function updateStatusResep(id, status) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    try {
        const res = await fetch(`/apoteker/api/resep/${id}/status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ status }),
        });
        const data = await res.json();
        if (data.success) {
            showToastApoteker(`Status resep diupdate ke: ${status}`, 'success');
            loadResepApoteker();
        }
    } catch (err) {
        showToastApoteker('Gagal update status', 'danger');
    }
}

function showToastApoteker(msg, type) {
    let c = document.getElementById('toastContainerA');
    if (!c) { c = document.createElement('div'); c.id = 'toastContainerA'; c.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px'; document.body.appendChild(c); }
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

document.addEventListener('DOMContentLoaded', loadResepApoteker);
</script>
@endsection
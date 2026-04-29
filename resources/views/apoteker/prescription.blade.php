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
    {{-- KIRI: List Resep --}}
    <div>
      <div class="tabs">
        <button class="tab-btn active" onclick="filterResep('semua',this)">Semua</button>
        <button class="tab-btn" onclick="filterResep('baru',this)">Baru</button>
        <button class="tab-btn" onclick="filterResep('validasi',this)">Divalidasi</button>
        <button class="tab-btn" onclick="filterResep('siap',this)">Siap Ambil</button>
        <button class="tab-btn" onclick="filterResep('selesai',this)">Selesai</button>
      </div>
      <div id="resepList">
        <div class="empty-state card" style="text-align:center;padding:40px 20px;color:var(--text3)">
          <div style="font-size:32px;margin-bottom:10px">📋</div>
          <div style="font-family:'Cormorant Garamond',serif;font-size:16px">Memuat data resep...</div>
        </div>
      </div>
    </div>

    {{-- KANAN: Detail Resep --}}
    <div id="resepDetail">
      <div class="card" style="text-align:center;padding:40px 20px;color:var(--text3)">
        <div style="font-size:32px;margin-bottom:10px">📋</div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:16px">Pilih invoice untuk preview</div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Substitusi --}}
<div class="modal-overlay" id="modalSubstitusi">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalSubstitusi')">✕</button>
    <h3>Substitusi Obat</h3>
    <div id="subsInfo" style="margin-bottom:14px;font-size:13px;color:var(--text2)"></div>
    <div class="fg">
      <label>Obat Pengganti</label>
      <select id="subsObat"></select>
    </div>
    <div class="fg">
      <label>Alasan Substitusi</label>
      <select id="subsAlasan">
        <option>Stok habis</option>
        <option>Obat tidak tersedia di formularium</option>
        <option>Harga terlalu tinggi (BPJS)</option>
        <option>Obat mendekati expired</option>
        <option>Permintaan pasien</option>
      </select>
    </div>
    <div class="fg">
      <label>Catatan Tambahan</label>
      <textarea id="subsCatatan" rows="2" placeholder="Keterangan penggantian obat..."></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeModal('modalSubstitusi')">Batal</button>
      <button class="btn btn-amber" onclick="simpanSubstitusi()">🔄 Konfirmasi Substitusi</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let allResep = [];
let selectedResep = null;
let currentObatIndex = null;

// ── Load resep dari API ──
async function loadResep() {
    try {
        const res = await fetch('/apoteker/api/resep');
        allResep = await res.json();
        renderList('semua');
    } catch (err) {
        document.getElementById('resepList').innerHTML = `
            <div class="card" style="text-align:center;padding:30px;color:var(--text3)">
                Gagal memuat data resep
            </div>`;
    }
}

function filterResep(status, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    renderList(status);
}

// ── Render list kiri ──
function renderList(status) {
    const container = document.getElementById('resepList');
    const list = status === 'semua' ? allResep : allResep.filter(r => r.status === status);

    if (!list.length) {
        container.innerHTML = `
            <div class="card" style="text-align:center;padding:30px;color:var(--text3)">
                <div style="font-size:28px;margin-bottom:8px">📋</div>
                <div>Tidak ada resep</div>
            </div>`;
        return;
    }

    const badgeMap = {
        baru: 'b-baru', validasi: 'b-validasi',
        siap: 'b-siap', selesai: 'b-selesai', ditolak: 'b-ditolak'
    };
    const labelMap = {
        baru: 'Baru', validasi: 'Divalidasi',
        siap: 'Siap Ambil', selesai: 'Selesai', ditolak: 'Ditolak'
    };

    container.innerHTML = list.map((r, i) => `
        <div class="rx-item" id="rx-item-${i}" onclick="showDetail(${i})">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px">
                <span class="rx-no">${r.no_resep || 'RX-???'}</span>
                <span class="badge ${badgeMap[r.status] || ''}">${labelMap[r.status] || r.status}</span>
            </div>
            <div class="rx-meta">${r.pasien || '-'} · ${r.dokter || ''}</div>
            <div style="display:flex;gap:6px;margin-top:6px;align-items:center">
                <span class="badge ${r.bayar === 'BPJS' ? 'b-siap' : 'b-mandiri'}">${r.bayar || 'Mandiri'}</span>
                <span style="font-size:11px;color:var(--text3)">${r.tanggal || ''}</span>
            </div>
        </div>
    `).join('');
}

// ── Render detail kanan ──
function showDetail(index) {
    // Highlight item yang dipilih
    document.querySelectorAll('.rx-item').forEach(el => el.classList.remove('selected'));
    const el = document.getElementById(`rx-item-${index}`);
    if (el) el.classList.add('selected');

    const r = allResep[index];
    if (!r) return;
    selectedResep = r;

    const obat = r.obat || [];
    const obatHtml = obat.length === 0
        ? '<p style="font-size:12px;color:var(--text3)">Tidak ada obat diresepkan</p>'
        : obat.map((o, oi) => {
            const tersedia = o.stok > 0;
            return `
            <div class="drug-row ${tersedia ? '' : 'unavail'}" style="margin-bottom:8px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div>
                        <div class="drug-name">${o.nama}</div>
                        <div class="drug-info">${o.dosis || ''} · ${o.durasi || ''} · ${o.instruksi || ''}</div>
                        ${o.catatan ? `<div style="font-size:11px;color:var(--text3);margin-top:2px">📝 ${o.catatan}</div>` : ''}
                    </div>
                    <span class="badge ${tersedia ? 'b-siap' : 'b-ditolak'}" style="white-space:nowrap;margin-left:8px">
                        ${tersedia ? 'Tersedia' : 'Tidak tersedia'}
                    </span>
                </div>
                ${!tersedia ? `
                <div class="drug-actions">
                    <span style="font-size:11px;color:var(--red)">⚠ Stok tidak ada</span>
                    <button class="btn btn-sm btn-amber" onclick="openSubstitusi(${oi})">🔄 Substitusi</button>
                </div>` : `
                <div class="drug-actions">
                    <button class="btn btn-sm btn-amber" onclick="openSubstitusi(${oi})">🔄 Substitusi</button>
                </div>`}
            </div>`;
        }).join('');

    // Alergi
    const alergiHtml = r.alergi
        ? `<div style="background:var(--red-light);border:1px solid #f7c1c1;border-radius:8px;padding:8px 12px;margin-bottom:14px;font-size:12px;color:var(--red);display:flex;align-items:center;gap:6px">
                <span>⚠</span> Alergi ${r.alergi}
           </div>`
        : '';

    document.getElementById('resepDetail').innerHTML = `
        <div class="detail-panel card" style="padding:0;overflow:hidden">
            {{-- Header detail --}}
            <div style="padding:16px 18px;border-bottom:1px solid var(--cream3);display:flex;justify-content:space-between;align-items:flex-start">
                <div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:600;color:var(--text)">${r.no_resep || 'RX-???'}</div>
                    <div style="font-size:12px;color:var(--text3);margin-top:2px">${r.tanggal || ''}</div>
                </div>
                <span class="badge b-baru">${r.status || 'Baru'}</span>
            </div>

            <div style="padding:16px 18px">
                ${alergiHtml}

                {{-- Info grid --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
                    <div>
                        <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">Pasien</div>
                        <div style="font-size:13px;font-weight:500;color:var(--text)">${r.pasien || '-'}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">No. RM</div>
                        <div style="font-size:13px;color:var(--text)">${r.rm || '-'}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">Dokter</div>
                        <div style="font-size:13px;color:var(--text)">${r.dokter || '-'}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">Pembayaran</div>
                        <span class="badge ${r.bayar === 'BPJS' ? 'b-siap' : 'b-mandiri'}">${r.bayar || 'Mandiri'}</span>
                    </div>
                </div>

                {{-- Daftar Obat --}}
                <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Daftar Obat</div>
                ${obatHtml}

                {{-- Action buttons --}}
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;padding-top:14px;border-top:1px solid var(--cream3)">
                    <button class="btn btn-teal btn-sm" onclick="updateStatus('${r.id}', 'validasi')">✅ Validasi Resep</button>
                    <button class="btn btn-sm" style="background:var(--green);color:white;border-color:var(--green)" onclick="updateStatus('${r.id}', 'siap')">📦 Siap Ambil</button>
                    <button class="btn btn-sm" onclick="updateStatus('${r.id}', 'selesai')">🏁 Selesai</button>
                    <button class="btn btn-danger btn-sm" onclick="updateStatus('${r.id}', 'ditolak')">✕ Tolak</button>
                </div>
            </div>
        </div>`;
}

// ── Update status resep ──
async function updateStatus(id, status) {
    try {
        await fetch(`/apoteker/api/resep/${id}/status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ status })
        });
        showToast('Status resep diperbarui', 'success');
        await loadResep();
        // Re-render detail kalau masih ada
        const idx = allResep.findIndex(r => r.id == id);
        if (idx >= 0) showDetail(idx);
    } catch {
        showToast('Gagal memperbarui status', 'danger');
    }
}

// ── Substitusi ──
function openSubstitusi(obatIndex) {
    currentObatIndex = obatIndex;
    const o = (selectedResep?.obat || [])[obatIndex];
    document.getElementById('subsInfo').innerHTML = o
        ? `Mengganti: <strong>${o.nama}</strong>`
        : '';
    document.getElementById('modalSubstitusi').classList.add('open');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

function simpanSubstitusi() {
    showToast('Substitusi tersimpan', 'success');
    closeModal('modalSubstitusi');
}

// ── Toast ──
function showToast(msg, type = 'success') {
    const wrap = document.getElementById('toast-wrap') || (() => {
        const d = document.createElement('div');
        d.id = 'toast-wrap';
        d.className = 'toast-container';
        document.body.appendChild(d);
        return d;
    })();
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.textContent = msg;
    wrap.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', loadResep);
</script>
@endpush
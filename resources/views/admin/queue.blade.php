@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-antrian">
  <div class="page-header">
    <div>
      <div class="page-title">Antrian & Poli haloo</div>
      <div class="page-sub">Kirim pasien ke dokter sesuai poli tujuan</div>
    </div>
    <button class="btn btn-primary" style="display: flex !important; align-items: center !important; justify-content: center !important; width: 200px; height: 42px; gap: 8px;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display: inline-block !important; flex-shrink: 0; margin: 0;">
        <line x1="22" y1="2" x2="11" y2="13"/>
        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
      </svg>
      <span style="white-space: nowrap !important; font-size: 13px; display: inline-block !important; line-height: 1;">Kirim Semua ke Dokter</span>
    </button>
  </div>

  {{-- KANBAN BOARD --}}
  <div class="antrian-section-label">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;vertical-align:middle"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    Ringkasan per Poli
  </div>
  <div id="kanbanBoard" class="kanban-board"></div>

  {{-- TABLE --}}
  <div class="card antrian-card">
    <div class="antrian-card-header">
      <div class="card-title" style="margin-bottom:0">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;vertical-align:middle;color:#c0825a"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        Pasien Belum Dikirim ke Dokter
      </div>
      <div id="antrian-count-badge" class="antrian-count-badge">0 pasien</div>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>No. RM</th>
            <th>Nama Pasien</th>
            <th>Poli Tujuan</th>
            <th>Jenis</th>
            <th>No. Antrian</th>
            <th>Status Kirim</th>
            <th style="text-align:center">Aksi</th>
          </tr>
        </thead>
        <tbody id="tblAntrianBody"></tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<style>
/* ===== SECTION LABEL ===== */
.antrian-section-label {
  font-size: 12px; font-weight: 600; color: #A8998A;
  text-transform: uppercase; letter-spacing: .06em;
  margin-bottom: 12px; display: flex; align-items: center;
}

/* ===== KANBAN BOARD ===== */
.kanban-board {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 24px;
}
@media(max-width: 900px){ .kanban-board { grid-template-columns: repeat(2,1fr); } }
@media(max-width: 480px){ .kanban-board { grid-template-columns: 1fr; } }

/* ===== ANTRIAN CARD ===== */
.antrian-card { overflow: hidden; }
.antrian-card-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px; border-bottom: 1px solid #f0ebe6; background: #fdfaf8;
}
.antrian-count-badge {
  background: #fde8d8; color: #c0825a;
  font-size: 12px; font-weight: 600;
  padding: 4px 12px; border-radius: 20px;
}

/* ===== TABLE ROWS ===== */
.antrian-row { transition: background .12s; }
.antrian-row:hover { background: #fdf9f7; }
.antrian-rm {
  font-family: monospace; font-size: 12px;
  background: #f5f0ec; color: #7a5c46;
  padding: 3px 8px; border-radius: 5px; font-weight: 600;
}
.antrian-nama-cell { display: flex; align-items: center; gap: 10px; }
.antrian-avatar {
  width: 32px; height: 32px; border-radius: 50%;
  background: #fde8d8; color: #c0825a;
  font-size: 13px; font-weight: 700;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.antrian-nama-txt { font-size: 13px; font-weight: 600; color: #2d2016; }
.antrian-no-badge {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 32px; height: 28px;
  background: #fff3e0; color: #c0825a;
  border-radius: 7px; font-size: 13px; font-weight: 700; padding: 0 8px;
}

/* ===== BADGES ===== */
.badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.b-bpjs    { background: #e3f2fd; color: #1565c0; }
.b-mandiri { background: #f3e5f5; color: #6a1b9a; }
.b-kirim   { background: #e8f5e9; color: #2e7d32; }
.b-belum   { background: #fff8e1; color: #e65100; }
.poli-chip { display: inline-block; padding: 3px 10px; border-radius: 6px; background: #f5f0ec; font-size: 12px; color: #6b5248; font-weight: 500; }

/* ===== ACTION BUTTON ===== */
.btn-kirim-dokter {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 8px;
  background: #c0825a; color: #fff; border: none;
  font-size: 12px; font-weight: 600; cursor: pointer;
  transition: all .15s;
}
.btn-kirim-dokter:hover { background: #a86b45; transform: translateY(-1px); box-shadow: 0 3px 10px rgba(192,130,90,.3); }
.btn-sudah-dikirim {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 8px;
  background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;
  font-size: 12px; font-weight: 600; cursor: default;
}

/* ===== EMPTY STATE ===== */
.antrian-empty {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 48px 20px; text-align: center;
}
.antrian-empty-icon { margin-bottom: 14px; opacity: .5; }
.antrian-empty-text { font-size: 15px; font-weight: 600; color: #A8998A; }
.antrian-empty-sub  { font-size: 12px; color: #C4B5A5; margin-top: 4px; }
</style>
<script>
const pasienData = @json($belumDikirimJson ?? []);

/* ===== RENDER KANBAN ===== */
function renderKanban() {
  const poliCount = {};
  pasienData.forEach(p => {
    poliCount[p.poli] = (poliCount[p.poli] || 0) + 1;
  });

  const board = document.getElementById('kanbanBoard');
  if (!Object.keys(poliCount).length) {
    board.innerHTML = `<div style="grid-column:1/-1;text-align:center;color:#C4B5A5;padding:20px;font-size:13px">Tidak ada antrian</div>`;
    return;
  }

  board.innerHTML = Object.entries(poliCount).map(([poli, count]) => `
    <div style="background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f0ebe6">
      <div style="font-size:12px;color:#A8998A;font-weight:600;margin-bottom:8px">${poli}</div>
      <div style="font-size:24px;font-weight:700;color:#c0825a">${count}</div>
      <div style="font-size:11px;color:#C4B5A5;margin-top:2px">pasien menunggu</div>
    </div>
  `).join('');
}

/* ===== RENDER TABLE ===== */
function renderTable() {
  const tbody = document.getElementById('tblAntrianBody');
  document.getElementById('antrian-count-badge').textContent = `${pasienData.length} pasien`;

  if (!pasienData.length) {
    tbody.innerHTML = `
      <tr><td colspan="7">
        <div class="antrian-empty">
          <div class="antrian-empty-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#C4B5A5" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
          </div>
          <div class="antrian-empty-text">Tidak ada pasien dalam antrian</div>
          <div class="antrian-empty-sub">Semua pasien sudah dikirim ke dokter</div>
        </div>
      </td></tr>`;
    return;
  }

  tbody.innerHTML = pasienData.map((p, i) => `
    <tr class="antrian-row" id="row-${p.id}">
      <td><span class="antrian-rm">${p.rm}</span></td>
      <td>
        <div class="antrian-nama-cell">
          <div class="antrian-avatar">${p.nama.charAt(0).toUpperCase()}</div>
          <span class="antrian-nama-txt">${p.nama}</span>
        </div>
      </td>
      <td><span class="poli-chip">${p.poli}</span></td>
      <td><span class="badge ${p.jenis === 'BPJS' ? 'b-bpjs' : 'b-mandiri'}">${p.jenis}</span></td>
      <td><span class="antrian-no-badge">${i + 1}</span></td>
      <td><span class="badge b-belum">Belum Dikirim</span></td>
      <td style="text-align:center">
        <button class="btn-kirim-dokter" onclick="kirimPasien(${p.id})">
          ✈ Kirim ke Dokter
        </button>
      </td>
    </tr>
  `).join('');
}

/* ===== KIRIM 1 PASIEN ===== */
function kirimPasien(id) {
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  fetch(`/admin/pasien/${id}/kirim`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Hapus dari array lokal
      const idx = pasienData.findIndex(p => p.id == id);
      if (idx !== -1) pasienData.splice(idx, 1);
      renderTable();
      renderKanban();
      showToast(`${data.nama} berhasil dikirim ke dokter`, 'success');
    } else {
      showToast(data.message || 'Gagal mengirim pasien', 'danger');
    }
  })
  .catch(() => showToast('Terjadi kesalahan koneksi', 'danger'));
}

/* ===== KIRIM SEMUA ===== */
document.querySelector('.btn.btn-primary').addEventListener('click', function () {
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  fetch('/admin/pasien/kirim-semua', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      pasienData.length = 0;
      renderTable();
      renderKanban();
      showToast(data.message, 'success');
    } else {
      showToast(data.message || 'Gagal', 'danger');
    }
  })
  .catch(() => showToast('Terjadi kesalahan koneksi', 'danger'));
});

/* ===== TOAST ===== */
function showToast(msg, type) {
  const colors = { success: '#4caf50', danger: '#e53935', info: '#1976d2' };
  const t = document.createElement('div');
  t.style.cssText = `position:fixed;bottom:24px;right:24px;background:${colors[type]||colors.info};color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,.2)`;
  t.innerText = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3000);
}

/* ===== INIT ===== */
document.addEventListener('DOMContentLoaded', () => {
  renderTable();
  renderKanban();
});
</script>
@endpush
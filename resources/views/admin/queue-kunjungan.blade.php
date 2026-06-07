@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-antrian">
  <div class="page-header">
    <div>
      <div class="page-title">Antrian & Poli</div>
      <div class="page-sub">Validasi dan kirim kunjungan pasien ke dokter yang dituju</div>
    </div>
    <button class="btn btn-primary" id="btnKirimSemua"
      style="display:flex;align-items:center;justify-content:center;gap:8px;height:42px;min-width:180px;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="22" y1="2" x2="11" y2="13"/>
        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
      </svg>
      <span style="white-space:nowrap;font-size:13px;">Kirim Semua ke Dokter</span>
    </button>
  </div>

  {{-- KANBAN: ringkasan per poli --}}
  <div id="kanbanBoard" class="kanban-board"></div>

  {{-- TABLE: antrian belum dikirim --}}
  <div class="card antrian-card">
    <div class="antrian-card-header">
      <div class="card-title" style="margin-bottom:0">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          style="margin-right:8px;vertical-align:middle;color:#c0825a">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
        </svg>
        Kunjungan Belum Dikirim ke Dokter
      </div>
      <div id="antrian-count-badge" class="antrian-count-badge">0 kunjungan</div>
    </div>

    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>No. Kunjungan</th>
            <th>No. RM</th>
            <th>Nama Pasien</th>
            <th>Poli Tujuan</th>
            {{-- ↓ KOLOM BARU: dokter yang dituju --}}
            <th>Dokter</th>
            <th>Jenis</th>
            <th>Validasi</th>
            <th style="text-align:center">Aksi</th>
          </tr>
        </thead>
        <tbody id="tblAntrianBody"></tbody>
      </table>
    </div>
  </div>

  {{-- TABLE: sudah dikirim (log) --}}
  <div class="card antrian-card" style="margin-top:20px;">
    <div class="antrian-card-header">
      <div class="card-title" style="margin-bottom:0;color:#2e7d32">
        ✅ Kunjungan Sudah Dikirim
      </div>
      <div class="antrian-count-badge" style="background:#e8f5e9;color:#2e7d32">
        {{ $sudahDikirim->count() }} kunjungan
      </div>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>No. Kunjungan</th>
            <th>No. RM</th>
            <th>Nama Pasien</th>
            <th>Poli</th>
            <th>Dokter</th>
            <th>Jenis</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sudahDikirim as $k)
            <tr>
              <td><span class="antrian-rm">{{ $k->no_kunjungan }}</span></td>
              <td><span class="antrian-rm">{{ $k->pasien->no_rm ?? '-' }}</span></td>
              <td>
                <div class="antrian-nama-cell">
                  <div class="antrian-avatar">
                    {{ mb_strtoupper(mb_substr($k->pasien->nama ?? '?', 0, 1)) }}
                  </div>
                  <span class="antrian-nama-txt">{{ $k->pasien->nama ?? '-' }}</span>
                </div>
              </td>
              <td><span class="poli-chip">{{ $k->poli_tujuan }}</span></td>
              <td style="font-size:13px;color:#555">{{ $k->dokter->nama ? 'dr. '.$k->dokter->nama : '-' }}</td>
              <td>
                <span class="badge {{ ($k->pasien->jenis ?? '') == 'BPJS' ? 'b-bpjs' : 'b-mandiri' }}">
                  {{ $k->pasien->jenis ?? '-' }}
                </span>
              </td>
              <td><span class="badge b-kirim">✓ Terkirim</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:24px;color:#A8998A;font-size:13px;">
                Belum ada kunjungan yang dikirim hari ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<style>
.kanban-board {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 24px;
}
@media(max-width:900px) { .kanban-board { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px) { .kanban-board { grid-template-columns: 1fr; } }

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
.badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.b-bpjs    { background: #e3f2fd; color: #1565c0; }
.b-mandiri { background: #f3e5f5; color: #6a1b9a; }
.b-kirim   { background: #e8f5e9; color: #2e7d32; }
.b-belum   { background: #fff8e1; color: #e65100; }
.b-valid   { background: #e8f5e9; color: #2e7d32; }
.b-invalid { background: #ffebee; color: #c62828; }
.b-pending { background: #fff8e1; color: #e65100; }
.poli-chip { display: inline-block; padding: 3px 10px; border-radius: 6px; background: #f5f0ec; font-size: 12px; color: #6b5248; font-weight: 500; }
.dokter-chip { font-size: 12px; color: #555; }

/* Tombol aksi */
.aksi-group { display: flex; gap: 6px; align-items: center; justify-content: center; flex-wrap: wrap; }
.btn-kirim-dokter {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 12px; border-radius: 8px;
  background: #c0825a; color: #fff; border: none;
  font-size: 12px; font-weight: 600; cursor: pointer;
  transition: all .15s;
}
.btn-kirim-dokter:hover { background: #a86b45; transform: translateY(-1px); }
.btn-kirim-dokter:disabled { background: #e8f5e9; color: #2e7d32; cursor: default; transform: none; }
.btn-validasi {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 10px; border-radius: 7px;
  border: none; font-size: 11px; font-weight: 600; cursor: pointer;
  transition: all .15s;
}
.btn-valid-ok { background: #4caf50; color: #fff; }
.btn-valid-ok:hover { background: #388e3c; }
.btn-valid-no { background: #e53935; color: #fff; }
.btn-valid-no:hover { background: #c62828; }

/* Empty state */
.antrian-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 20px; text-align: center; }
.antrian-empty-icon { margin-bottom: 14px; opacity: .5; }
.antrian-empty-text { font-size: 15px; font-weight: 600; color: #A8998A; }
.antrian-empty-sub  { font-size: 12px; color: #C4B5A5; margin-top: 4px; }

/* Toast */
.vtoast {
  position: fixed; bottom: 24px; right: 24px;
  padding: 12px 20px; border-radius: 10px;
  font-size: 13px; font-weight: 500;
  color: #fff; z-index: 9999;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
</style>

<script>
// ── Data dari controller ──────────────────────────────────
let kunjunganData = @json($belumDikirimJson ?? []);
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── Render Kanban ─────────────────────────────────────────
function renderKanban() {
  const poliCount = {};
  kunjunganData.forEach(k => {
    poliCount[k.poli] = (poliCount[k.poli] || 0) + 1;
  });

  const board = document.getElementById('kanbanBoard');
  if (!Object.keys(poliCount).length) {
    board.innerHTML = `<div style="grid-column:1/-1;text-align:center;color:#C4B5A5;padding:20px;font-size:13px">
      Tidak ada antrian kunjungan
    </div>`;
    return;
  }

  board.innerHTML = Object.entries(poliCount).map(([poli, count]) => `
    <div style="background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 6px rgba(0,0,0,.06);border:1px solid #f0ebe6">
      <div style="font-size:12px;color:#A8998A;font-weight:600;margin-bottom:8px">${poli}</div>
      <div style="font-size:24px;font-weight:700;color:#c0825a">${count}</div>
      <div style="font-size:11px;color:#C4B5A5;margin-top:2px">kunjungan menunggu</div>
    </div>
  `).join('');
}

// ── Render Table ──────────────────────────────────────────
function renderTable() {
  const tbody = document.getElementById('tblAntrianBody');
  document.getElementById('antrian-count-badge').textContent = `${kunjunganData.length} kunjungan`;

  if (!kunjunganData.length) {
    tbody.innerHTML = `
      <tr><td colspan="8">
        <div class="antrian-empty">
          <div class="antrian-empty-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#C4B5A5" stroke-width="1.5">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
            </svg>
          </div>
          <div class="antrian-empty-text">Tidak ada kunjungan dalam antrian</div>
          <div class="antrian-empty-sub">Semua kunjungan sudah dikirim ke dokter</div>
        </div>
      </td></tr>`;
    return;
  }

  tbody.innerHTML = kunjunganData.map((k, i) => {
    const validasiClass = k.validasi === 'Valid' ? 'b-valid' : (k.validasi === 'Tidak Valid' ? 'b-invalid' : 'b-pending');
    const sudahValid    = k.validasi === 'Valid';

    return `
    <tr class="antrian-row" id="row-${k.id}">
      <td><span class="antrian-rm">${k.no_kunjungan}</span></td>
      <td><span class="antrian-rm">${k.rm}</span></td>
      <td>
        <div class="antrian-nama-cell">
          <div class="antrian-avatar">${k.nama.charAt(0).toUpperCase()}</div>
          <span class="antrian-nama-txt">${k.nama}</span>
        </div>
      </td>
      <td><span class="poli-chip">${k.poli}</span></td>
      {{-- Kolom dokter --}}
      <td class="dokter-chip">${k.dokter !== '-' ? 'dr. ' + k.dokter : '-'}</td>
      <td>
        <span class="badge ${k.jenis === 'BPJS' ? 'b-bpjs' : 'b-mandiri'}">${k.jenis}</span>
      </td>
      <td>
        <span class="badge ${validasiClass}" id="badge-validasi-${k.id}">${k.validasi}</span>
      </td>
      <td style="text-align:center">
        <div class="aksi-group">
          ${!sudahValid ? `
            <button class="btn-validasi btn-valid-ok" onclick="ubahValidasi(${k.id}, 'valid')" title="Validasi">✓ Valid</button>
            <button class="btn-validasi btn-valid-no" onclick="ubahValidasi(${k.id}, 'invalid')" title="Tolak">✕</button>
          ` : ''}
          <button class="btn-kirim-dokter" id="btn-kirim-${k.id}"
            onclick="kirimKunjungan(${k.id})"
            ${!sudahValid ? 'disabled title="Validasi dulu sebelum kirim"' : ''}>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="22" y1="2" x2="11" y2="13"/>
              <polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
            Kirim
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

// ── Ubah Validasi ─────────────────────────────────────────
// ← ENDPOINT BARU: /admin/kunjungan/{id}/validasi
function ubahValidasi(id, status) {
  fetch(`/admin/kunjungan/${id}/validasi`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
    body: JSON.stringify({ validasi: status })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const k = kunjunganData.find(x => x.id == id);
      if (k) {
        k.validasi = status === 'valid' ? 'Valid' : 'Tidak Valid';
      }
      renderTable();
      showToast(status === 'valid' ? '✅ Kunjungan divalidasi' : '❌ Kunjungan ditolak',
                status === 'valid' ? 'success' : 'danger');
    } else {
      showToast(data.message || 'Gagal memvalidasi', 'danger');
    }
  })
  .catch(() => showToast('Terjadi kesalahan koneksi', 'danger'));
}

// ── Kirim 1 Kunjungan ─────────────────────────────────────
// ← ENDPOINT BARU: /admin/kunjungan/{id}/kirim
function kirimKunjungan(id) {
  fetch(`/admin/kunjungan/${id}/kirim`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const idx = kunjunganData.findIndex(k => k.id == id);
      if (idx !== -1) kunjunganData.splice(idx, 1);
      renderTable();
      renderKanban();
      showToast(`${data.nama} (${data.no_kunjungan}) berhasil dikirim ke dokter`, 'success');
    } else {
      showToast(data.message || 'Gagal mengirim kunjungan', 'danger');
    }
  })
  .catch(() => showToast('Terjadi kesalahan koneksi', 'danger'));
}

// ── Kirim Semua ───────────────────────────────────────────
// ← ENDPOINT BARU: /admin/kunjungan/kirim-semua
document.getElementById('btnKirimSemua').addEventListener('click', function () {
  if (!kunjunganData.length) { showToast('Tidak ada kunjungan dalam antrian', 'info'); return; }

  fetch('/admin/kunjungan/kirim-semua', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      kunjunganData.length = 0;
      renderTable();
      renderKanban();
      showToast(data.message, 'success');
    } else {
      showToast(data.message || 'Gagal', 'danger');
    }
  })
  .catch(() => showToast('Terjadi kesalahan koneksi', 'danger'));
});

// ── Toast ─────────────────────────────────────────────────
function showToast(msg, type = 'info') {
  const colors = { success: '#4caf50', danger: '#e53935', info: '#1976d2' };
  const t = document.createElement('div');
  t.className = 'vtoast';
  t.style.background = colors[type] || colors.info;
  t.innerText = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3200);
}

// ── Init ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  renderTable();
  renderKanban();
});
</script>
@endpush
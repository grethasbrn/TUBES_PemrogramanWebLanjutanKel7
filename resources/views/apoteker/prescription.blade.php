@extends('layouts.apoteker')

@section('content')

<style>
/* ─── Page Header ─── */
.rx-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
}
.rx-header h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 26px;
    font-weight: 600;
    color: var(--text);
    margin: 0;
}
.rx-header h2 span { color: var(--purple); font-style: italic; }

/* ─── Toolbar ─── */
.rx-toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.rx-search { position: relative; }
.rx-search input {
    padding: 9px 12px 9px 36px;
    border-radius: 8px;
    border: 1px solid var(--cream3);
    background: var(--cream);
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    color: var(--text);
    outline: none;
    width: 220px;
}
.rx-search input:focus { border-color: var(--purple); }
.rx-search i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--text3); font-size: 14px; }
.rx-filter select {
    padding: 9px 12px;
    border-radius: 8px;
    border: 1px solid var(--cream3);
    background: var(--cream);
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    color: var(--text);
    outline: none;
    cursor: pointer;
}
.rx-filter select:focus { border-color: var(--purple); }

/* ─── Stats ─── */
.rx-stats { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.rx-stat {
    background: var(--cream);
    border: 1px solid var(--cream3);
    border-radius: 10px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 130px;
}
.rx-stat-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.rx-stat-icon.blue   { background: #e8f0fe; color: #1a73e8; }
.rx-stat-icon.orange { background: #fff3e0; color: #f57c00; }
.rx-stat-icon.green  { background: #e6f4ea; color: #34a853; }
.rx-stat-icon.purple { background: #f3e8ff; color: var(--purple); }
.rx-stat-icon.red    { background: #fce8e6; color: #d93025; }
.rx-stat-info label { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; color: var(--text3); font-weight: 500; }
.rx-stat-info strong { font-size: 20px; font-weight: 600; color: var(--text); line-height: 1.2; }

/* ─── Table ─── */
.rx-table-wrap { background: #fff; border: 1px solid var(--cream3); border-radius: 12px; overflow: hidden; }
.rx-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.rx-table thead th {
    background: var(--cream);
    padding: 12px 16px;
    text-align: left;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .06em;
    font-weight: 600;
    color: var(--text2);
    border-bottom: 1px solid var(--cream3);
    white-space: nowrap;
}
.rx-table tbody tr { border-bottom: 1px solid var(--cream3); transition: background .15s; cursor: pointer; }
.rx-table tbody tr:last-child { border-bottom: none; }
.rx-table tbody tr:hover { background: var(--cream); }
.rx-table td { padding: 13px 16px; color: var(--text); vertical-align: middle; }
.rx-table td.muted { color: var(--text3); font-size: 12px; }

.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
.badge-baru      { background: #e8f0fe; color: #1a73e8; }
.badge-validasi  { background: #fff3e0; color: #f57c00; }
.badge-siap      { background: #e6f4ea; color: #34a853; }
.badge-selesai   { background: #e6f4ea; color: #1e7e34; }
.badge-ditolak   { background: #fce8e6; color: #d93025; }
.badge-bpjs      { background: #f3e8ff; color: var(--purple); }
.badge-mandiri   { background: #fce8e6; color: #d93025; }

.action-btn {
    background: var(--purple);
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 6px 14px;
    font-size: 12px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: opacity .15s;
    white-space: nowrap;
}
.action-btn:hover { opacity: .85; }

.empty-state { text-align: center; padding: 60px 20px; color: var(--text3); }
.empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }
.empty-state p { font-size: 14px; margin: 0; }

/* ─── Modal ─── */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.modal-overlay.open { display: flex; }
.modal {
    background: #fff;
    border-radius: 16px;
    padding: 28px;
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 12px 32px rgba(0,0,0,0.18);
    position: relative;
}
.modal-close { position: absolute; top: 16px; right: 20px; background: none; border: none; font-size: 20px; cursor: pointer; color: var(--text3); line-height: 1; }
.modal h3 { font-family: 'Cormorant Garamond', serif; font-size: 21px; font-weight: 600; color: var(--text); margin: 0 0 20px; }
.modal-section { background: var(--cream); border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; }
.modal-section-title { font-size: 10px; text-transform: uppercase; letter-spacing: .07em; font-weight: 600; color: var(--text3); margin-bottom: 10px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 20px; }
.info-item label { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; color: var(--text3); font-weight: 500; margin-bottom: 2px; }
.info-item span { font-size: 13px; color: var(--text); font-weight: 500; }

/* Obat table di modal */
.obat-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
.obat-table th {
    background: var(--cream3);
    padding: 8px 10px;
    text-align: left;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .05em;
    font-weight: 600;
    color: var(--text2);
}
.obat-table td { padding: 8px 10px; border-bottom: 1px solid var(--cream3); }
.obat-table tbody tr:last-child td { border-bottom: none; }

/* Input obat apoteker */
.obat-input { width: 70px; padding: 5px 8px; border-radius: 6px; border: 1px solid var(--cream3); font-size: 12px; font-family: 'DM Sans', sans-serif; text-align: center; }
.obat-input:focus { border-color: var(--purple); outline: none; }

.stok-ok   { color: #34a853; font-size: 11px; }
.stok-warn { color: #f57c00; font-size: 11px; }
.stok-habis{ color: #d93025; font-size: 11px; }

.fr { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.fg { margin-bottom: 0; }
.fg label { display: block; font-size: 11px; color: var(--text2); margin-bottom: 5px; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }
.fg input, .fg select, .fg textarea {
    width: 100%;
    padding: 9px 12px;
    border-radius: 8px;
    border: 1px solid var(--cream3);
    background: var(--cream);
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    color: var(--text);
    outline: none;
    box-sizing: border-box;
}
.fg input:focus, .fg select:focus, .fg textarea:focus { border-color: var(--purple); }
.fg textarea { resize: vertical; min-height: 80px; }

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--cream3);
    flex-wrap: wrap;
}
.btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-family: 'DM Sans', sans-serif; cursor: pointer; border: 1px solid var(--cream3); background: var(--cream); color: var(--text); transition: all .15s; }
.btn:hover { background: var(--cream3); }
.btn-primary { background: var(--purple); color: #fff; border-color: var(--purple); }
.btn-primary:hover { opacity: .88; }
.btn-danger  { background: #fce8e6; color: #d93025; border-color: #f5c6cb; }
.btn-danger:hover { background: #f5c6cb; }
.btn-success { background: #e6f4ea; color: #1e7e34; border-color: #b7dfbb; }
.btn-success:hover { background: #b7dfbb; }

.no-resep-tag {
    font-size: 11px;
    color: var(--text3);
    background: var(--cream);
    border: 1px solid var(--cream3);
    padding: 2px 8px;
    border-radius: 6px;
    font-family: monospace;
}

.refresh-btn {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid var(--cream3);
    background: var(--cream);
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    color: var(--text2);
    display: flex;
    align-items: center;
    gap: 6px;
    transition: background .15s;
}
.refresh-btn:hover { background: var(--cream3); }
.refresh-btn.loading i { animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ─── Modal Tolak ─── */
.modal-tolak-body {
    font-size: 13px;
    color: var(--text3);
    margin-bottom: 16px;
    line-height: 1.6;
}
.alasan-error {
    font-size: 12px;
    color: #d93025;
    margin-top: 6px;
    display: none;
}
</style>

{{-- ─── Header ─── --}}
<div class="rx-header">
    <h2>Resep <span>Masuk</span></h2>
    <div class="rx-toolbar">
        <div class="rx-search">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama / No. RM / No. Resep…" oninput="filterTable()">
        </div>
        <div class="rx-filter">
            <select id="filterStatus" onchange="filterTable()">
                <option value="">Semua Status</option>
                <option value="baru">Baru</option>
                <option value="validasi">Validasi</option>
                <option value="siap">Siap</option>
                <option value="selesai">Selesai</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>
        <div class="rx-filter">
            <select id="filterBayar" onchange="filterTable()">
                <option value="">Semua Pembayaran</option>
                <option value="BPJS">BPJS</option>
                <option value="Mandiri">Mandiri</option>
            </select>
        </div>
        <button class="refresh-btn" id="refreshBtn" onclick="loadResep()">
            <i class="bi bi-arrow-clockwise" id="refreshIcon"></i> Refresh
        </button>
    </div>
</div>

{{-- ─── Stats ─── --}}
<div class="rx-stats">
    <div class="rx-stat">
        <div class="rx-stat-icon blue"><i class="bi bi-inbox"></i></div>
        <div class="rx-stat-info"><label>Total Resep</label><strong id="statTotal">0</strong></div>
    </div>
    <div class="rx-stat">
        <div class="rx-stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
        <div class="rx-stat-info"><label>Baru</label><strong id="statBaru">0</strong></div>
    </div>
    <div class="rx-stat">
        <div class="rx-stat-icon purple"><i class="bi bi-clipboard-check"></i></div>
        <div class="rx-stat-info"><label>Validasi</label><strong id="statValidasi">0</strong></div>
    </div>
    <div class="rx-stat">
        <div class="rx-stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div class="rx-stat-info"><label>Siap / Selesai</label><strong id="statSiap">0</strong></div>
    </div>
</div>

{{-- ─── Table ─── --}}
<div class="rx-table-wrap">
    <table class="rx-table">
        <thead>
            <tr>
                <th>No. Resep</th>
                <th>Nama Pasien</th>
                <th>No. RM</th>
                <th>Pembayaran</th>
                <th>Diagnosa</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="resepTableBody">
            <tr><td colspan="8">
                <div class="empty-state">
                    <i class="bi bi-arrow-clockwise"></i>
                    <p>Memuat data resep…</p>
                </div>
            </td></tr>
        </tbody>
    </table>
</div>

{{-- ─── Modal Detail Resep ─── --}}
<div class="modal-overlay" id="modalResep" onclick="closeModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal()">✕</button>
        <h3>💊 Detail Resep &nbsp;<span class="no-resep-tag" id="mNoResep">-</span></h3>

        {{-- Info Pasien --}}
        <div class="modal-section">
            <div class="modal-section-title">Data Pasien</div>
            <div class="info-grid">
                <div class="info-item"><label>Nama</label><span id="mPasienNama">-</span></div>
                <div class="info-item"><label>No. RM</label><span id="mPasienRM">-</span></div>
                <div class="info-item"><label>Pembayaran</label><span id="mPasienBayar">-</span></div>
                <div class="info-item"><label>Dokter</label><span id="mDokter">-</span></div>
            </div>
        </div>

        {{-- Info Resep --}}
        <div class="modal-section">
            <div class="modal-section-title">Informasi Resep</div>
            <div class="info-grid">
                <div class="info-item"><label>Diagnosa</label><span id="mDiagnosa">-</span></div>
                <div class="info-item"><label>Tanggal Resep</label><span id="mTanggal">-</span></div>
                <div class="info-item"><label>Catatan Dokter</label><span id="mCatatan">-</span></div>
                <div class="info-item"><label>Tanggal Kontrol</label><span id="mKontrol">-</span></div>
            </div>
        </div>

        {{-- Daftar Obat --}}
        <div class="modal-section">
            <div class="modal-section-title">Daftar Obat dari Dokter</div>
            <table class="obat-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Obat</th>
                        <th>Dosis</th>
                        <th>Jml Resep</th>
                        <th>Stok</th>
                        <th>Jml Diberikan</th>
                    </tr>
                </thead>
                <tbody id="mObatList"></tbody>
            </table>
        </div>

        {{-- Alasan Tolak (hanya muncul jika status ditolak) --}}
        <div class="modal-section" id="mSeksiAlasan" style="display:none; border-left: 3px solid #d93025;">
            <div class="modal-section-title" style="color:#d93025">Alasan Penolakan</div>
            <p id="mAlasanTolak" style="font-size:13px; color:var(--text); margin:0;"></p>
        </div>

        {{-- Status saat ini --}}
        <div style="margin-bottom:16px; display:flex; align-items:center; gap:10px;">
            <span style="font-size:12px; color:var(--text3)">Status saat ini:</span>
            <span class="badge" id="mStatusBadge">-</span>
        </div>

        <div class="modal-footer" id="mFooter"></div>
    </div>
</div>

{{-- ─── Modal Alasan Tolak ─── --}}
<div class="modal-overlay" id="modalTolak" onclick="closeTolakModal()">
    <div class="modal" style="max-width:420px" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeTolakModal()">✕</button>
        <h3>❌ Tolak Resep</h3>
        <p class="modal-tolak-body">
            Masukkan alasan penolakan resep ini. Pasien akan dikembalikan ke antrian dokter untuk mendapatkan resep baru.
        </p>
        <div class="fg">
            <label>Alasan Penolakan <span style="color:#d93025">*</span></label>
            <textarea id="inputAlasanTolak" rows="4"
                placeholder="Contoh: Dosis tidak sesuai, kombinasi obat berbahaya, obat tidak tersedia di sistem…"
                oninput="document.getElementById('alasanError').style.display='none'"></textarea>
            <span class="alasan-error" id="alasanError">Alasan penolakan tidak boleh kosong.</span>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeTolakModal()">Batal</button>
            <button class="btn btn-danger" onclick="konfirmasiTolak()">
                <i class="bi bi-x-circle"></i> Tolak Resep
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let allResep  = [];
let activeResep = null;

// ─── Load Data ────────────────────────────────────────────
async function loadResep() {
    const btn  = document.getElementById('refreshBtn');
    const icon = document.getElementById('refreshIcon');
    btn.classList.add('loading');

    try {
        const res  = await fetch('/apoteker/api/resep');
        const data = await res.json();
        const urutanStatus = { 'baru': 0, 'validasi': 1, 'siap': 2, 'selesai': 3, 'ditolak': 4 };
        allResep = data.sort((a, b) =>
            (urutanStatus[a.status] ?? 9) - (urutanStatus[b.status] ?? 9)
        );
        renderStats(allResep);
        filterTable();
    } catch(e) {
        console.error('Gagal load resep:', e);
        document.getElementById('resepTableBody').innerHTML = `
            <tr><td colspan="8">
                <div class="empty-state">
                    <i class="bi bi-exclamation-circle"></i>
                    <p>Gagal memuat data. <a href="#" onclick="loadResep()">Coba lagi</a></p>
                </div>
            </td></tr>`;
    } finally {
        btn.classList.remove('loading');
    }
}

// ─── Stats ────────────────────────────────────────────────
function renderStats(data) {
    document.getElementById('statTotal').textContent    = data.length;
    document.getElementById('statBaru').textContent     = data.filter(r => r.status === 'baru').length;
    document.getElementById('statValidasi').textContent = data.filter(r => r.status === 'validasi').length;
    document.getElementById('statSiap').textContent     = data.filter(r => r.status === 'siap' || r.status === 'selesai').length;
}

// ─── Table ────────────────────────────────────────────────
function renderTable(data) {
    const tbody = document.getElementById('resepTableBody');
    if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="8">
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Belum ada resep masuk dari dokter.</p>
            </div></td></tr>`;
        return;
    }

    tbody.innerHTML = data.map(r => `
        <tr onclick="openModal('${r.id}')">
            <td><span class="no-resep-tag">${r.no_resep}</span></td>
            <td><strong>${r.pasien}</strong></td>
            <td class="muted">${r.rm}</td>
            <td><span class="badge ${r.bayar === 'BPJS' ? 'badge-bpjs' : 'badge-mandiri'}">${r.bayar}</span></td>
            <td class="muted" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.diagnosa}</td>
            <td class="muted">${r.tanggal}</td>
            <td><span class="badge badge-${r.status}">${labelStatus(r.status)}</span></td>
            <td>
                <button class="action-btn" onclick="event.stopPropagation(); openModal('${r.id}')">
                    <i class="bi bi-eye"></i> Proses
                </button>
            </td>
        </tr>
    `).join('');
}

function labelStatus(s) {
    const map = { baru:'Baru', validasi:'Validasi', siap:'Siap', selesai:'Selesai', ditolak:'Ditolak' };
    return map[s] ?? s;
}

function filterTable() {
    const q      = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;
    const bayar  = document.getElementById('filterBayar').value;

    const filtered = allResep.filter(r => {
        const matchQ = !q || r.pasien.toLowerCase().includes(q)
                          || r.rm.toLowerCase().includes(q)
                          || r.no_resep.toLowerCase().includes(q);
        const matchS = !status || r.status === status;
        const matchB = !bayar  || r.bayar  === bayar;
        return matchQ && matchS && matchB;
    });

    renderTable(filtered);
}

// ─── Modal Detail ─────────────────────────────────────────
function openModal(id) {
    const r = allResep.find(x => x.id === id);
    if (!r) return;
    activeResep = r;

    document.getElementById('mNoResep').textContent     = r.no_resep;
    document.getElementById('mPasienNama').textContent  = r.pasien;
    document.getElementById('mPasienRM').textContent    = r.rm;
    document.getElementById('mPasienBayar').textContent = r.bayar;
    document.getElementById('mDokter').textContent      = r.dokter ?? '-';
    document.getElementById('mDiagnosa').textContent    = r.diagnosa;
    document.getElementById('mTanggal').textContent     = r.tanggal;
    document.getElementById('mCatatan').textContent     = r.catatan_dokter ?? '-';
    document.getElementById('mKontrol').textContent     = r.tanggal_kontrol ?? '-';

    // Tampilkan alasan tolak jika ada
    const seksiAlasan = document.getElementById('mSeksiAlasan');
    if (r.status === 'ditolak' && r.alasan_tolak) {
        document.getElementById('mAlasanTolak').textContent = r.alasan_tolak;
        seksiAlasan.style.display = 'block';
    } else {
        seksiAlasan.style.display = 'none';
    }

    // Status badge
    const sb = document.getElementById('mStatusBadge');
    sb.textContent = labelStatus(r.status);
    sb.className   = `badge badge-${r.status}`;

    // Obat list
    const tbody = document.getElementById('mObatList');
    if (!r.obat || !r.obat.length) {
        tbody.innerHTML = `<tr><td colspan="6" style="color:var(--text3);padding:8px 10px">Tidak ada data obat.</td></tr>`;
    } else {
        tbody.innerHTML = r.obat.map((o, i) => {
            const stok      = o.stok ?? 0;
            const stokClass = stok > 30 ? 'stok-ok' : stok > 0 ? 'stok-warn' : 'stok-habis';
            const stokLabel = stok > 30 ? `✓ ${stok}` : stok > 0 ? `⚠ ${stok}` : `✗ Habis`;
            return `
            <tr>
                <td class="muted">${i+1}</td>
                <td><strong>${o.nama}</strong></td>
                <td class="muted">${o.dosis ?? '-'}</td>
                <td>${o.jumlah ?? '-'}</td>
                <td><span class="${stokClass}">${stokLabel}</span></td>
                <td>
                    <input type="number" class="obat-input" id="jml-diberikan-${i}"
                        value="${o.jumlah ?? 1}" min="0" max="${stok}"
                        ${r.status === 'selesai' || r.status === 'ditolak' ? 'disabled' : ''}>
                </td>
            </tr>`;
        }).join('');
    }

    renderFooter(r.status);
    document.getElementById('modalResep').classList.add('open');
}

function renderFooter(status) {
    const footer = document.getElementById('mFooter');
    let html = `<button class="btn" onclick="closeModal()">Tutup</button>`;

    if (status === 'baru') {
        html += `
            <button class="btn btn-danger" onclick="bukaTolakModal()">
                <i class="bi bi-x-circle"></i> Tolak
            </button>
            <button class="btn btn-primary" onclick="prosesValidasi()">
                <i class="bi bi-clipboard-check"></i> Validasi & Input Obat
            </button>`;
    } else if (status === 'validasi') {
        html += `
            <button class="btn btn-danger" onclick="bukaTolakModal()">
                <i class="bi bi-x-circle"></i> Tolak
            </button>
            <button class="btn btn-success" onclick="simpanObatDanSiap()">
                <i class="bi bi-check2-circle"></i> Konfirmasi Siap
            </button>`;
    } else if (status === 'siap') {
        html += `
            <button class="btn btn-success" onclick="updateStatus('selesai')">
                <i class="bi bi-bag-check"></i> Tandai Selesai
            </button>`;
    }

    footer.innerHTML = html;
}

function closeModal() {
    document.getElementById('modalResep').classList.remove('open');
    activeResep = null;
}

// ─── Modal Tolak ─────────────────────────────────────────
function bukaTolakModal() {
    document.getElementById('inputAlasanTolak').value = '';
    document.getElementById('alasanError').style.display = 'none';
    document.getElementById('modalTolak').classList.add('open');
}

function closeTolakModal() {
    document.getElementById('modalTolak').classList.remove('open');
}

async function konfirmasiTolak() {
    const alasan = document.getElementById('inputAlasanTolak').value.trim();
    if (!alasan) {
        document.getElementById('alasanError').style.display = 'block';
        return;
    }

    closeTolakModal();
    if (!activeResep) return;

    try {
        const res = await fetch(`/apoteker/api/resep/${activeResep.id}/status`, {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status: 'ditolak', alasan_tolak: alasan }),
        });

        const data = await res.json();
        if (data.success) {
            // Update data lokal
            const idx = allResep.findIndex(r => r.id === activeResep.id);
            if (idx !== -1) {
                allResep[idx].status      = 'ditolak';
                allResep[idx].alasan_tolak = alasan;
            }
            activeResep.status       = 'ditolak';
            activeResep.alasan_tolak = alasan;

            renderStats(allResep);
            filterTable();
            showToast('✅ Resep ditolak. Pasien dikembalikan ke antrian dokter.');
            closeModal();
        } else {
            alert('Gagal menolak resep.');
        }
    } catch(e) {
        console.error(e);
        alert('Terjadi kesalahan. Coba lagi.');
    }
}

// ─── Aksi Lainnya ────────────────────────────────────────
async function updateStatus(status) {
    if (!activeResep) return;

    const label = { selesai: 'menyelesaikan', validasi: 'memvalidasi', siap: 'mengkonfirmasi siap' };
    if (!confirm(`Yakin ${label[status] ?? status} resep ini?`)) return;

    try {
        const res = await fetch(`/apoteker/api/resep/${activeResep.id}/status`, {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status }),
        });

        const data = await res.json();
        if (data.success) {
            const idx = allResep.findIndex(r => r.id === activeResep.id);
            if (idx !== -1) allResep[idx].status = status;
            activeResep.status = status;

            const sb = document.getElementById('mStatusBadge');
            sb.textContent = labelStatus(status);
            sb.className   = `badge badge-${status}`;
            renderFooter(status);

            renderStats(allResep);
            filterTable();
            showToast(`✅ Status resep berhasil diubah ke: ${labelStatus(status)}`);

            if (status === 'selesai') closeModal();
        } else {
            alert('Gagal mengubah status.');
        }
    } catch(e) {
        console.error(e);
        alert('Terjadi kesalahan. Coba lagi.');
    }
}

function prosesValidasi() {
    updateStatus('validasi');
}

async function simpanObatDanSiap() {
    if (!activeResep) return;

    const obatUpdated = (activeResep.obat ?? []).map((o, i) => {
        const input = document.getElementById(`jml-diberikan-${i}`);
        return { ...o, jumlah: parseInt(input?.value ?? o.jumlah) || 0 };
    });

    try {
        const resObat = await fetch(`/apoteker/api/resep/${activeResep.id}/update-obat`, {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ obat_list: obatUpdated }),
        });

        if (!resObat.ok) throw new Error('Gagal simpan obat');
        await updateStatus('siap');

    } catch(e) {
        console.error(e);
        alert('Terjadi kesalahan saat menyimpan. Coba lagi.');
    }
}

// ─── Toast ────────────────────────────────────────────────
function showToast(msg) {
    let t = document.getElementById('rx-toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'rx-toast';
        t.style.cssText = `position:fixed;bottom:24px;right:24px;background:#333;color:#fff;
            padding:12px 20px;border-radius:10px;font-size:13px;font-family:'DM Sans',sans-serif;
            z-index:99999;box-shadow:0 4px 16px rgba(0,0,0,.25);transition:opacity .3s;`;
        document.body.appendChild(t);
    }
    t.textContent   = msg;
    t.style.opacity = '1';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => { t.style.opacity = '0'; }, 3500);
}

// ─── Init ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    loadResep();
});
</script>
@endpush
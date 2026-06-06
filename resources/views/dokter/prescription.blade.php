@extends('layouts.dokter')

@section('content')

<script id="pasien-data" type="application/json">{!! json_encode($pasienJson->map(fn($p) => [
    'id'      => (string)($p['id'] ?? $p->id ?? ''),
    'nama'    => $p['nama'] ?? $p->nama ?? '',
    'rm'      => $p['rm'] ?? $p['no_rm'] ?? '',
    'usia'    => $p['usia'] ?? '',
    'jk'      => $p['jk'] ?? $p['jenis_kelamin'] ?? '-',
    'bayar'   => $p['bayar'] ?? $p['jenis'] ?? '',
    'poli'    => $p['poli'] ?? $p['poli_tujuan'] ?? '',
    'status'  => $p['status'] ?? '',
    'keluhan' => $p['keluhan'] ?? '-',
    'riwayat' => $p['riwayat'] ?? '-',
    'alergi'  => $p['alergi'] ?? '-',
    'bb'      => $p['bb'] ?? $p['berat_badan'] ?? '',
    'tb'      => $p['tb'] ?? $p['tinggi_badan'] ?? '',
    'td'      => $p['td'] ?? $p['tekanan_darah'] ?? '-',
])) !!}
</script>

<script id="obat-data" type="application/json">{!! json_encode($obatJson) !!}</script>

<style>
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
    min-width: 140px;
}
.rx-stat-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.rx-stat-icon.blue   { background: #e8f0fe; color: #1a73e8; }
.rx-stat-icon.orange { background: #fff3e0; color: #f57c00; }
.rx-stat-icon.green  { background: #e6f4ea; color: #34a853; }
.rx-stat-icon.purple { background: #f3e8ff; color: var(--purple); }
.rx-stat-info label { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; color: var(--text3); font-weight: 500; }
.rx-stat-info strong { font-size: 20px; font-weight: 600; color: var(--text); line-height: 1.2; }
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
.badge-menunggu  { background: #fff3e0; color: #f57c00; }
.badge-diperiksa { background: #e8f0fe; color: #1a73e8; }
.badge-selesai   { background: #e6f4ea; color: #34a853; }
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
    max-width: 620px;
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
.obat-list { display: flex; flex-direction: column; gap: 10px; }
.obat-row { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 8px; align-items: end; }
.obat-row .fg { margin-bottom: 0; }
.fg { margin-bottom: 12px; }
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
.fg textarea { resize: vertical; min-height: 70px; }
.fr { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.btn-remove-obat {
    background: #fce8e6;
    color: #d93025;
    border: none;
    border-radius: 7px;
    width: 32px;
    height: 32px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    align-self: flex-end;
}
.btn-add-obat {
    background: none;
    border: 1px dashed var(--purple);
    color: var(--purple);
    border-radius: 8px;
    padding: 8px;
    width: 100%;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: background .15s;
    margin-top: 4px;
}
.btn-add-obat:hover { background: #f3e8ff; }
.stok-info { font-size: 11px; color: var(--text3); margin-top: 4px; }
.stok-info.habis { color: #d93025; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--cream3); }
.btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-family: 'DM Sans', sans-serif; cursor: pointer; border: 1px solid var(--cream3); background: var(--cream); color: var(--text); transition: all .15s; }
.btn:hover { background: var(--cream3); }
.btn-primary { background: var(--purple); color: #fff; border-color: var(--purple); }
.btn-primary:hover { opacity: .88; }
.btn-draft { background: #fff3e0; color: #f57c00; border-color: #ffe0b2; }

/* ─── Banner Tolak ─── */
.banner-tolak {
    background: #fce8e6;
    border: 1px solid #f5c6cb;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.banner-tolak i { color: #d93025; font-size: 20px; flex-shrink: 0; }
.banner-tolak strong { color: #d93025; font-size: 13px; display: block; }
.banner-tolak p { margin: 3px 0 0; font-size: 12px; color: #c0392b; }

/* ─── Row ditolak highlight ─── */
.row-ditolak td { background: #fff8f8 !important; }
.badge-ditolak-resep { background: #fce8e6; color: #d93025; }
</style>

{{-- ─── Header ─── --}}
<div class="rx-header">
    <h2>Resep <span>Pasien</span></h2>
    <div class="rx-toolbar">
        <div class="rx-search">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama / No. RM…" oninput="filterTable()">
        </div>
        <div class="rx-filter">
            <select id="filterStatus" onchange="filterTable()">
                <option value="">Semua Status</option>
                <option value="Menunggu">Menunggu</option>
                <option value="Diperiksa">Diperiksa</option>
            </select>
        </div>
        <div class="rx-filter">
            <select id="filterBayar" onchange="filterTable()">
                <option value="">Semua Pembayaran</option>
                <option value="BPJS">BPJS</option>
                <option value="Mandiri">Mandiri</option>
            </select>
        </div>
    </div>
</div>

{{-- ─── Banner Resep Ditolak ─── --}}
<div class="banner-tolak" id="bannerTolak" style="display:none">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <div>
        <strong>Ada resep yang ditolak apoteker</strong>
        <p id="bannerTolakDetail"></p>
    </div>
</div>

{{-- ─── Stats ─── --}}
<div class="rx-stats">
    <div class="rx-stat">
        <div class="rx-stat-icon blue"><i class="bi bi-people"></i></div>
        <div class="rx-stat-info"><label>Total Pasien</label><strong id="statTotal">0</strong></div>
    </div>
    <div class="rx-stat">
        <div class="rx-stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
        <div class="rx-stat-info"><label>Menunggu</label><strong id="statMenunggu">0</strong></div>
    </div>
    <div class="rx-stat">
        <div class="rx-stat-icon purple"><i class="bi bi-activity"></i></div>
        <div class="rx-stat-info"><label>Diperiksa</label><strong id="statDiperiksa">0</strong></div>
    </div>
    <div class="rx-stat">
        <div class="rx-stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div class="rx-stat-info"><label>Selesai Hari Ini</label><strong id="statSelesai">0</strong></div>
    </div>
</div>

{{-- ─── Table ─── --}}
<div class="rx-table-wrap">
    <table class="rx-table">
        <thead>
            <tr>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Usia / JK</th>
                <th>Poli</th>
                <th>Pembayaran</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="pasienTableBody">
            <tr><td colspan="7">
                <div class="empty-state">
                    <i class="bi bi-arrow-clockwise"></i>
                    <p>Memuat data pasien…</p>
                </div>
            </td></tr>
        </tbody>
    </table>
</div>

{{-- ─── Modal Tulis Resep ─── --}}
<div class="modal-overlay" id="modalResep" onclick="closeResepModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeResepModal()">✕</button>
        <h3>✍️ Tulis Resep</h3>

        {{-- Notif resep sebelumnya ditolak --}}
        <div id="notifTolakModal" style="display:none; background:#fce8e6; border:1px solid #f5c6cb;
            border-radius:8px; padding:12px 14px; margin-bottom:16px;">
            <strong style="color:#d93025; font-size:13px;">
                <i class="bi bi-exclamation-circle"></i> Resep sebelumnya ditolak apoteker
            </strong>
            <p id="notifTolakAlasan" style="margin:4px 0 0; font-size:12px; color:#c0392b;"></p>
        </div>

        <div class="modal-section">
            <div class="modal-section-title">Data Pasien</div>
            <div class="info-grid">
                <div class="info-item"><label>Nama</label><span id="rPasienNama">-</span></div>
                <div class="info-item"><label>No. RM</label><span id="rPasienRM">-</span></div>
                <div class="info-item"><label>Usia</label><span id="rPasienUsia">-</span></div>
                <div class="info-item"><label>Jenis Kelamin</label><span id="rPasienJK">-</span></div>
                <div class="info-item"><label>Pembayaran</label><span id="rPasienBayar">-</span></div>
                <div class="info-item"><label>Poli</label><span id="rPasienPoli">-</span></div>
            </div>
        </div>

        <div class="modal-section">
            <div class="modal-section-title">Anamnesis</div>
            <div class="fr">
                <div class="info-item"><label>Keluhan</label><span id="rKeluhan">-</span></div>
                <div class="info-item"><label>Alergi</label><span id="rAlergi">-</span></div>
            </div>
            <div style="margin-top:8px" class="fr">
                <div class="info-item"><label>BB / TB</label><span id="rBBTB">-</span></div>
                <div class="info-item"><label>Tekanan Darah</label><span id="rTD">-</span></div>
            </div>
        </div>

        <div class="fg">
            <label>Diagnosa *</label>
            <input type="text" id="inputDiagnosa" placeholder="Contoh: Hipertensi Grade I">
        </div>

        <div class="fg">
            <label>Daftar Obat *</label>
            <div class="obat-list" id="obatList"></div>
            <button class="btn-add-obat" onclick="addObatRow()">
                <i class="bi bi-plus-circle"></i> Tambah Obat
            </button>
        </div>

        <div class="fr">
            <div class="fg">
                <label>Catatan Dokter</label>
                <textarea id="inputCatatan" placeholder="Instruksi tambahan untuk apoteker…"></textarea>
            </div>
            <div class="fg">
                <label>Tanggal Kontrol</label>
                <input type="date" id="inputKontrol">
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn" onclick="closeResepModal()">Batal</button>
            <button class="btn btn-draft" onclick="submitResep('draft')">💾 Simpan Draft</button>
            <button class="btn btn-primary" onclick="submitResep('baru')">📤 Kirim ke Apotek</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// ─── Data ────────────────────────────────────────────────
let allPasien = [];
try {
    allPasien = JSON.parse(document.getElementById('pasien-data').textContent) || [];
} catch(e) { allPasien = []; }

let allObat = [];
try {
    allObat = JSON.parse(document.getElementById('obat-data').textContent) || [];
} catch(e) { allObat = []; }

let activePasienId  = null;
// Map pasienId → alasan tolak dari resep terakhir yang ditolak
let resepDitolakMap = {};

// ─── Stats ───────────────────────────────────────────────
function renderStats(data) {
    document.getElementById('statTotal').textContent     = data.length;
    document.getElementById('statMenunggu').textContent  = data.filter(p => p.status === 'Menunggu').length;
    document.getElementById('statDiperiksa').textContent = data.filter(p => p.status === 'Diperiksa').length;
    document.getElementById('statSelesai').textContent   = data.filter(p => p.status === 'Selesai').length;
}

// ─── Table ───────────────────────────────────────────────
function renderTable(data) {
    const tbody = document.getElementById('pasienTableBody');
    if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="7">
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Tidak ada pasien yang perlu diresepkan.</p>
            </div></td></tr>`;
        return;
    }
    tbody.innerHTML = data.map(p => {
        const ditolak   = resepDitolakMap[p.id];
        const rowClass  = ditolak ? 'row-ditolak' : '';
        const tolakBadge = ditolak
            ? `<span class="badge badge-ditolak-resep" style="margin-left:6px">
                <i class="bi bi-exclamation-circle"></i> Resep Ditolak
               </span>`
            : '';
        return `
        <tr class="${rowClass}" onclick="openResepModal('${p.id}')">
            <td class="muted">${p.rm ?? '-'}</td>
            <td><strong>${p.nama}</strong>${tolakBadge}</td>
            <td class="muted">${p.usia ?? '-'} thn / ${p.jk ?? '-'}</td>
            <td>${p.poli ?? '-'}</td>
            <td><span class="badge ${p.bayar === 'BPJS' ? 'badge-bpjs' : 'badge-mandiri'}">${p.bayar ?? '-'}</span></td>
            <td><span class="badge ${badgeClass(p.status)}">${p.status}</span></td>
            <td>
                <button class="action-btn" onclick="event.stopPropagation(); openResepModal('${p.id}')">
                    <i class="bi bi-pencil-square"></i> Tulis Resep
                </button>
            </td>
        </tr>`;
    }).join('');
}

function badgeClass(s) {
    return { 'Menunggu': 'badge-menunggu', 'Diperiksa': 'badge-diperiksa', 'Selesai': 'badge-selesai' }[s] ?? 'badge-menunggu';
}

function filterTable() {
    const q      = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;
    const bayar  = document.getElementById('filterBayar').value;
    const filtered = allPasien.filter(p => {
        const matchQ      = !q      || p.nama.toLowerCase().includes(q) || (p.rm ?? '').toLowerCase().includes(q);
        const matchStatus = !status || p.status === status;
        const matchBayar  = !bayar  || p.bayar  === bayar;
        return matchQ && matchStatus && matchBayar;
    });
    renderTable(filtered);
}

// ─── Cek Resep Ditolak ───────────────────────────────────
async function cekResepDitolak() {
    try {
        const res  = await fetch('/dokter/api/resep');
        const data = await res.json();

        // Ambil hanya resep yang ditolak, group by pasienId (ambil yang terbaru)
        resepDitolakMap = {};
        data.filter(r => r.status === 'ditolak').forEach(r => {
            resepDitolakMap[r.pasienId] = r.alasan_tolak ?? 'Tidak ada keterangan.';
        });

        const jumlah = Object.keys(resepDitolakMap).length;
        const banner = document.getElementById('bannerTolak');
        if (jumlah > 0) {
            document.getElementById('bannerTolakDetail').textContent =
                `${jumlah} resep ditolak. Pasien sudah dikembalikan ke antrian — silakan tulis ulang resep.`;
            banner.style.display = 'flex';
        } else {
            banner.style.display = 'none';
        }

        // Re-render tabel supaya badge "Resep Ditolak" muncul
        filterTable();
    } catch(e) {
        console.error('Gagal cek resep ditolak:', e);
    }
}

// ─── Modal ───────────────────────────────────────────────
function openResepModal(id) {
    const p = allPasien.find(x => x.id == id);
    if (!p) return;
    activePasienId = id;

    document.getElementById('rPasienNama').textContent  = p.nama;
    document.getElementById('rPasienRM').textContent    = p.rm ?? '-';
    document.getElementById('rPasienUsia').textContent  = (p.usia ?? '-') + ' tahun';
    document.getElementById('rPasienJK').textContent    = p.jk ?? '-';
    document.getElementById('rPasienBayar').textContent = p.bayar ?? '-';
    document.getElementById('rPasienPoli').textContent  = p.poli ?? '-';
    document.getElementById('rKeluhan').textContent     = p.keluhan ?? '-';
    document.getElementById('rAlergi').textContent      = p.alergi ?? '-';
    document.getElementById('rBBTB').textContent        = `${p.bb ?? '-'} kg / ${p.tb ?? '-'} cm`;
    document.getElementById('rTD').textContent          = p.td ?? '-';

    // Tampilkan notif jika ada resep ditolak untuk pasien ini
    const alasanTolak = resepDitolakMap[id];
    const notif = document.getElementById('notifTolakModal');
    if (alasanTolak) {
        document.getElementById('notifTolakAlasan').textContent = 'Alasan: ' + alasanTolak;
        notif.style.display = 'block';
    } else {
        notif.style.display = 'none';
    }

    document.getElementById('inputDiagnosa').value = '';
    document.getElementById('inputCatatan').value  = '';
    document.getElementById('inputKontrol').value  = '';
    document.getElementById('obatList').innerHTML  = '';
    obatCounter = 0;
    addObatRow();

    document.getElementById('modalResep').classList.add('open');
}

function closeResepModal() {
    document.getElementById('modalResep').classList.remove('open');
    activePasienId = null;
}

// ─── Obat Rows ───────────────────────────────────────────
let obatCounter = 0;

function addObatRow() {
    const id  = obatCounter++;
    const row = document.createElement('div');
    row.className = 'obat-row';
    row.id = `obat-row-${id}`;

    const pasien     = allPasien.find(x => x.id === activePasienId);
    const jenisBayar = pasien?.bayar ?? 'Mandiri';
    const obatTersedia = jenisBayar === 'BPJS'
        ? allObat.filter(o => o.kategori === 'bpjs')
        : allObat;

    const options = obatTersedia.map(o =>
        `<option value="${o.nama}" data-stok="${o.stok}" data-harga="${o.harga}">
            ${o.nama} — ${o.tipe} (Stok: ${o.stok})
        </option>`
    ).join('');

    row.innerHTML = `
        <div class="fg">
            ${id === 0 ? '<label>Nama Obat</label>' : '<label>&nbsp;</label>'}
            <select id="obat-nama-${id}" onchange="onObatChange(${id})">
                <option value="">-- Pilih Obat --</option>
                ${options}
            </select>
            <div class="stok-info" id="obat-stok-info-${id}"></div>
        </div>
        <div class="fg">
            ${id === 0 ? '<label>Dosis</label>' : '<label>&nbsp;</label>'}
            <input type="text" placeholder="Contoh: 3x1" id="obat-dosis-${id}">
        </div>
        <div class="fg">
            ${id === 0 ? '<label>Jumlah</label>' : '<label>&nbsp;</label>'}
            <input type="number" placeholder="1" id="obat-jml-${id}" min="1">
        </div>
        <button class="btn-remove-obat" onclick="removeObatRow(${id})" title="Hapus">
            <i class="bi bi-trash3"></i>
        </button>
    `;
    document.getElementById('obatList').appendChild(row);
}

function onObatChange(id) {
    const sel      = document.getElementById(`obat-nama-${id}`);
    const opt      = sel.options[sel.selectedIndex];
    const stok     = parseInt(opt.dataset.stok ?? 0);
    const infoEl   = document.getElementById(`obat-stok-info-${id}`);
    const jmlInput = document.getElementById(`obat-jml-${id}`);

    if (!sel.value) { infoEl.textContent = ''; return; }

    jmlInput.max = stok;
    if (stok === 0) {
        infoEl.textContent = '⚠️ Stok habis!';
        infoEl.className   = 'stok-info habis';
    } else {
        infoEl.textContent = `Stok tersedia: ${stok}`;
        infoEl.className   = 'stok-info';
    }
}

function removeObatRow(id) {
    const row = document.getElementById(`obat-row-${id}`);
    if (row) row.remove();
}

function getObatList() {
    const rows = document.getElementById('obatList').querySelectorAll('.obat-row');
    const list = [];
    rows.forEach(row => {
        const id   = row.id.replace('obat-row-', '');
        const nama = document.getElementById(`obat-nama-${id}`)?.value?.trim();
        const dosis= document.getElementById(`obat-dosis-${id}`)?.value?.trim();
        const jml  = document.getElementById(`obat-jml-${id}`)?.value?.trim();
        if (nama) list.push({ nama, dosis: dosis || '-', jumlah: jml || 1 });
    });
    return list;
}

// ─── Submit ──────────────────────────────────────────────
async function submitResep(status) {
    const diagnosa = document.getElementById('inputDiagnosa').value.trim();
    const obatList = getObatList();

    if (!diagnosa)        { alert('Diagnosa wajib diisi.'); return; }
    if (!obatList.length) { alert('Pilih minimal satu obat.'); return; }

    const payload = {
        pasien_id      : activePasienId,
        diagnosa,
        obat_list      : obatList,
        catatan_dokter : document.getElementById('inputCatatan').value.trim(),
        tanggal_kontrol: document.getElementById('inputKontrol').value || null,
        status,
    };

    try {
        const res = await fetch('/dokter/api/resep/store', {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();
        if (data.success) {
            closeResepModal();

            if (status === 'baru') {
                // Update status pasien jadi Selesai (jangan dihapus dari list)
                const idx = allPasien.findIndex(p => p.id == activePasienId);
                if (idx !== -1) allPasien[idx].status = 'Selesai';

                // Hapus dari map ditolak karena sudah diresepkan ulang
                delete resepDitolakMap[activePasienId];
            }

            renderStats(allPasien);
            filterTable();
            cekResepDitolak();

            showToast(status === 'baru'
                ? `✅ Resep ${data.no_resep} berhasil dikirim ke apotek!`
                : `💾 Resep disimpan sebagai draft.`
            );
        } else {
            alert('Gagal menyimpan resep.');
        }
    } catch(err) {
        console.error(err);
        alert('Terjadi kesalahan. Coba lagi.');
    }
}

// ─── Toast ───────────────────────────────────────────────
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

// ─── Init ────────────────────────────────────────────────
renderStats(allPasien);
filterTable();
cekResepDitolak();
</script>
@endsection
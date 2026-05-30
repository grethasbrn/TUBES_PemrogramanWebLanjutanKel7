@extends('layouts.apoteker')
@section('content')

<style>
/* ── Layout ── */
.page-header{margin-bottom:20px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:600;color:var(--text)}
.page-title span{color:#A63D33;font-style:italic}
.page-sub{font-size:12px;color:var(--text3);margin-top:2px}

.grid2{display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:start}
@media(max-width:900px){.grid2{grid-template-columns:1fr}}

/* ── Stats ── */
.inv-stats{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap}
.inv-stat{flex:1;min-width:110px;background:var(--white);border:1px solid var(--cream3);border-radius:10px;padding:12px 14px;display:flex;align-items:center;gap:10px}
.inv-stat-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}
.si-red    {background:#fce8e6;color:#A63D33}
.si-orange {background:#fff3e0;color:#f57c00}
.si-blue   {background:#e8f0fe;color:#1a73e8}
.si-green  {background:#e6f4ea;color:#34a853}
.inv-stat-info label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.05em;color:var(--text3);font-weight:500}
.inv-stat-info strong{font-size:18px;font-weight:600;color:var(--text)}

/* ── Tabs ── */
.rx-tabs{display:flex;border-bottom:2px solid var(--cream3);margin-bottom:16px}
.rx-tab{padding:9px 18px;font-size:13px;font-family:'DM Sans',sans-serif;font-weight:500;color:var(--text3);cursor:pointer;border:none;background:none;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s}
.rx-tab:hover{color:var(--text)}
.rx-tab.active{color:#A63D33;border-bottom-color:#A63D33}
.rx-tab-count{display:inline-block;background:var(--cream3);color:var(--text3);border-radius:10px;padding:1px 7px;font-size:10px;margin-left:5px}
.rx-tab.active .rx-tab-count{background:#A63D33;color:white}

/* ── Search row ── */
.search-row{display:flex;gap:8px;margin-bottom:12px}
.search-input{flex:1;padding:9px 12px;border:1px solid var(--cream3);border-radius:8px;background:var(--white);font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none}
.search-input:focus{border-color:#A63D33}
.filter-sel{padding:9px 12px;border:1px solid var(--cream3);border-radius:8px;background:var(--white);font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;cursor:pointer}
.filter-sel:focus{border-color:#A63D33}

/* ── Badge ── */
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500}
.b-baru    {background:#fff3e0;color:#f57c00}
.b-masuk   {background:#fff3e0;color:#f57c00}
.b-diproses{background:#e8f0fe;color:#1a73e8}
.b-lunas   {background:#e6f4ea;color:#34a853}
.b-bpjs    {background:#f3e8ff;color:#7c3aed}
.b-mandiri {background:#fce8e6;color:#d93025}

/* ── Obat table ── */
.obat-table-wrap{border:1px solid var(--cream3);border-radius:8px;overflow:hidden;margin-bottom:10px}
.obat-table{width:100%;border-collapse:collapse;font-size:13px}
.obat-table thead th{background:var(--cream);padding:9px 12px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.06em;font-weight:600;color:var(--text2);border-bottom:1px solid var(--cream3)}
.obat-table tbody td{padding:10px 12px;border-bottom:1px solid var(--cream3);vertical-align:middle}
.obat-table tbody tr:last-child td{border-bottom:none}
.obat-table tbody tr:hover td{background:var(--cream)}

.obat-input{width:100%;padding:6px 8px;border:1px solid var(--cream3);border-radius:6px;background:var(--white);font-size:12px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;box-sizing:border-box}
.obat-input:focus{border-color:#A63D33}
.stok-badge{font-size:10px;padding:2px 6px;border-radius:4px;margin-top:3px;display:inline-block}
.stok-ok   {background:#e6f4ea;color:#34a853}
.stok-warn {background:#fff3e0;color:#f57c00}
.stok-habis{background:#fce8e6;color:#d93025}

.btn-del-obat{background:#fce8e6;color:#d93025;border:none;border-radius:5px;width:26px;height:26px;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center}
.btn-del-obat:hover{background:#f44336;color:white}
.btn-add-obat{background:none;border:1px dashed #A63D33;color:#A63D33;border-radius:7px;padding:7px;width:100%;font-size:12px;font-family:'DM Sans',sans-serif;cursor:pointer;transition:background .15s;margin-top:6px}
.btn-add-obat:hover{background:var(--red-light)}

/* ── Buttons ── */
.btn{padding:10px 16px;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;border:1px solid var(--cream3);background:var(--cream);color:var(--text);transition:all .15s;font-weight:500}
.btn:hover{background:var(--cream3)}
.btn-primary{background:#A63D33;color:white;border-color:#A63D33;width:100%;margin-top:4px;text-align:center}
.btn-primary:hover{opacity:.88}

/* ── Alert ── */
.alert-banner{padding:9px 13px;border-radius:8px;font-size:12px;display:flex;align-items:center;gap:8px;margin-bottom:14px}
.alert-bpjs   {background:#e0f5f1;border:1px solid #a0d9d2;color:#0f6e56}
.alert-mandiri{background:#fff8f0;border:1px solid #ffe0b2;color:#e65100}
.alert-success{background:#e6f4ea;border:1px solid #a8d5b5;color:#2e7d32}
.alert-info   {background:#e8f0fe;border:1px solid #c5d8f8;color:#1a73e8}

/* ── Info grid ── */
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px 20px;margin-bottom:14px}
.info-item label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.05em;color:var(--text3);font-weight:500;margin-bottom:2px}
.info-item span{font-size:13px;color:var(--text);font-weight:500}

/* ── Empty ── */
.empty-state{text-align:center;padding:50px 20px;color:var(--text3)}
.empty-state i{font-size:36px;display:block;margin-bottom:10px}
.empty-state p{font-size:13px;margin:0}

/* ── Toast ── */
.toast-container{position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:8px}
.toast{padding:11px 18px;border-radius:9px;font-size:13px;font-family:'DM Sans',sans-serif;color:white;box-shadow:0 4px 16px rgba(0,0,0,.2);animation:slideIn .25s ease}
@keyframes slideIn{from{transform:translateY(10px);opacity:0}to{transform:translateY(0);opacity:1}}
.toast.success{background:#2e7d32}
.toast.error  {background:#c62828}
.toast.info   {background:#1565c0}
@keyframes spin{to{transform:rotate(360deg)}}
.spin{display:inline-block;animation:spin 1s linear infinite}
</style>

{{-- Header --}}
<div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px">
        <div>
            <div class="page-title">Invoice <span>Apotek</span></div>
            <div class="page-sub">Terima resep dokter, periksa obat, kirim invoice ke admin</div>
        </div>
        <div style="font-size:12px;color:var(--text3)" id="lastUpdate">-</div>
    </div>
</div>

{{-- Stats --}}
<div class="inv-stats">
    <div class="inv-stat">
        <div class="inv-stat-icon si-red"><i class="bi bi-inbox-fill"></i></div>
        <div class="inv-stat-info"><label>Resep Masuk</label><strong id="statResep">0</strong></div>
    </div>
    <div class="inv-stat">
        <div class="inv-stat-icon si-orange"><i class="bi bi-receipt"></i></div>
        <div class="inv-stat-info"><label>Total Invoice</label><strong id="statTotal">0</strong></div>
    </div>
    <div class="inv-stat">
        <div class="inv-stat-icon si-blue"><i class="bi bi-arrow-repeat"></i></div>
        <div class="inv-stat-info"><label>Diproses</label><strong id="statDiproses">0</strong></div>
    </div>
    <div class="inv-stat">
        <div class="inv-stat-icon si-green"><i class="bi bi-check2-circle"></i></div>
        <div class="inv-stat-info"><label>Lunas</label><strong id="statLunas">0</strong></div>
    </div>
</div>

{{-- Tabs --}}
<div class="rx-tabs">
    <button class="rx-tab active" id="tab-resep" onclick="switchTab('resep')">
        Resep Masuk <span class="rx-tab-count" id="tabCountResep">0</span>
    </button>
    <button class="rx-tab" id="tab-invoice" onclick="switchTab('invoice')">
        Invoice Terkirim <span class="rx-tab-count" id="tabCountInvoice">0</span>
    </button>
</div>

{{-- Grid --}}
<div class="grid2">

    {{-- Kiri: List --}}
    <div>
        <div class="search-row">
            <input type="text" class="search-input" id="srchInput" placeholder="Cari nama / No. RM…" oninput="renderList()">
            <select class="filter-sel" id="fltrSelect" onchange="renderList()">
                <option value="">Semua</option>
            </select>
        </div>
        <div id="leftList">
            <div class="empty-state">
                <i class="bi bi-arrow-clockwise spin"></i>
                <p>Memuat data…</p>
            </div>
        </div>
    </div>

    {{-- Kanan: Detail --}}
    <div id="detailPanel">
        <div class="invoice-card">
            <div class="empty-state" style="padding:50px 0">
                <i class="bi bi-file-earmark-medical"></i>
                <p>Pilih resep untuk memeriksa & mengirim invoice</p>
            </div>
        </div>
    </div>

</div>

<div class="toast-container" id="toastWrap"></div>

@endsection

@push('scripts')
<script>
let allReseps   = [];
let allInvoices = [];
let stockData   = [];
let activeTab   = 'resep';
let activeId    = null;
let editObat    = [];

document.addEventListener('DOMContentLoaded', async () => {
    await Promise.all([loadStock(), loadReseps(), loadInvoices()]);
});

async function loadStock() {
    try {
        const res = await fetch('/apoteker/api/stok');
        if (res.ok) stockData = await res.json();
    } catch(e) {}
}

async function loadReseps() {
    try {
        const res  = await fetch('/apoteker/api/resep');
        const all  = await res.json();
        allReseps  = all.filter(r => ['baru','validasi'].includes(r.status));
        updateStats();
        if (activeTab === 'resep') renderList();
    } catch(e) { console.error(e); }
}

async function loadInvoices() {
    try {
        const res   = await fetch('/apoteker/api/invoice');
        allInvoices = await res.json();
        updateStats();
        if (activeTab === 'invoice') renderList();
    } catch(e) { console.error(e); }
}

function updateStats() {
    document.getElementById('statResep').textContent    = allReseps.length;
    document.getElementById('statTotal').textContent    = allInvoices.length;
    document.getElementById('statDiproses').textContent = allInvoices.filter(i => i.status === 'diproses').length;
    document.getElementById('statLunas').textContent    = allInvoices.filter(i => i.status === 'lunas').length;
    document.getElementById('tabCountResep').textContent   = allReseps.length;
    document.getElementById('tabCountInvoice').textContent = allInvoices.length;
}

function switchTab(tab) {
    activeTab = tab;
    activeId  = null;
    document.getElementById('tab-resep').classList.toggle('active', tab === 'resep');
    document.getElementById('tab-invoice').classList.toggle('active', tab === 'invoice');

    const fltr = document.getElementById('fltrSelect');
    fltr.innerHTML = tab === 'resep'
        ? `<option value="">Semua</option><option value="baru">Baru</option><option value="validasi">Validasi</option>`
        : `<option value="">Semua</option><option value="masuk">Masuk</option><option value="diproses">Diproses</option><option value="lunas">Lunas</option>`;

    document.getElementById('detailPanel').innerHTML = `
        <div class="invoice-card">
            <div class="empty-state" style="padding:50px 0">
                <i class="bi bi-file-earmark-medical"></i>
                <p>Pilih ${tab === 'resep' ? 'resep' : 'invoice'} untuk melihat detail</p>
            </div>
        </div>`;
    renderList();
}

function renderList() {
    const q      = document.getElementById('srchInput').value.toLowerCase();
    const filter = document.getElementById('fltrSelect').value;
    const container = document.getElementById('leftList');

    if (activeTab === 'resep') {
        let list = allReseps;
        if (q)      list = list.filter(r => (r.pasien||'').toLowerCase().includes(q) || (r.rm||'').toLowerCase().includes(q));
        if (filter) list = list.filter(r => r.status === filter);

        if (!list.length) {
            container.innerHTML = `<div class="empty-state"><i class="bi bi-inbox"></i><p>Tidak ada resep masuk.</p></div>`;
            return;
        }

        container.innerHTML = list.map(r => `
            <div class="inv-list-item card ${activeId == r.id ? 'active' : ''}" id="item-${r.id}" onclick="openResep('${r.id}')">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px">
                    <div>
                        <div style="font-size:11px;font-weight:600;color:#A63D33;font-family:'DM Mono',monospace">${r.no_resep||'-'}</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);margin:2px 0">${r.pasien||'-'}</div>
                        <div style="font-size:11px;color:var(--text3)">${r.rm||'-'} · ${r.dokter||'-'}</div>
                    </div>
                    <span class="badge b-baru">${r.status}</span>
                </div>
                <div style="display:flex;gap:6px;margin-top:8px;align-items:center">
                    <span class="badge ${r.bayar==='BPJS'?'b-bpjs':'b-mandiri'}">${r.bayar||'Mandiri'}</span>
                    <span style="font-size:11px;color:var(--text3);margin-left:auto">${(r.obat||[]).length} obat · ${r.tanggal||''}</span>
                </div>
            </div>`).join('');

    } else {
        let list = allInvoices;
        if (q)      list = list.filter(i => (i.nama||'').toLowerCase().includes(q) || (i.no_invoice||'').toLowerCase().includes(q));
        if (filter) list = list.filter(i => i.status === filter);

        if (!list.length) {
            container.innerHTML = `<div class="empty-state"><i class="bi bi-inbox"></i><p>Tidak ada invoice.</p></div>`;
            return;
        }

        const badgeMap = { masuk:'b-masuk', diproses:'b-diproses', lunas:'b-lunas' };
        const labelMap = { masuk:'Masuk', diproses:'Diproses', lunas:'Lunas' };

        container.innerHTML = list.map(inv => `
            <div class="inv-list-item card ${activeId == inv.id ? 'active' : ''}" id="item-${inv.id}" onclick="openInvoice(${inv.id})">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px">
                    <div>
                        <div style="font-size:11px;font-weight:600;color:#A63D33;font-family:'DM Mono',monospace">${inv.no_invoice}</div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);margin:2px 0">${inv.nama}</div>
                        <div style="font-size:11px;color:var(--text3)">${inv.no_rm}</div>
                    </div>
                    <div class="inv-price">${formatRp(inv.total)}</div>
                </div>
                <div style="display:flex;gap:6px;margin-top:8px;align-items:center">
                    <span class="badge ${inv.jenis==='BPJS'?'b-bpjs':'b-mandiri'}">${inv.jenis}</span>
                    <span class="badge ${badgeMap[inv.status]||''}">${labelMap[inv.status]||inv.status}</span>
                    <span style="font-size:11px;color:var(--text3);margin-left:auto">${inv.tanggal}</span>
                </div>
            </div>`).join('');
    }
}

// ── Buka Resep ───────────────────────────────────────────
function openResep(id) {
    const r = allReseps.find(x => x.id == id);
    if (!r) return;
    activeId = id;

    document.querySelectorAll('.inv-list-item').forEach(el => el.classList.remove('active'));
    document.getElementById(`item-${id}`)?.classList.add('active');

    editObat = (r.obat||[]).map(o => {
        const match = stockData.find(s =>
            s.nama_obat.toLowerCase().includes((o.nama||'').toLowerCase()) ||
            (o.nama||'').toLowerCase().includes(s.nama_obat.toLowerCase())
        );
        return {
            nama  : o.nama  || '',
            dosis : o.dosis || '-',
            jumlah: parseInt(o.jumlah) || 1,
            harga : match ? parseFloat(match.harga) || 0 : 0,
            stok  : match ? parseInt(match.jumlah)  || 0 : 0,
        };
    });

    renderResepDetail(r);
}

function renderResepDetail(r) {
    const isBPJS   = r.bayar === 'BPJS';
    const subtotal = editObat.reduce((s,o) => s + (isBPJS ? 0 : o.harga * o.jumlah), 0);
    const ppn      = isBPJS ? 0 : Math.round(subtotal * 0.11);
    const total    = subtotal + ppn;

    const obatRows = editObat.length === 0
        ? `<tr><td colspan="6" style="text-align:center;padding:16px;color:var(--text3);font-size:12px">Belum ada obat</td></tr>`
        : editObat.map((o,i) => {
            const stokBadge = o.stok > 10
                ? `<span class="stok-badge stok-ok">Stok: ${o.stok}</span>`
                : o.stok > 0
                ? `<span class="stok-badge stok-warn">Stok: ${o.stok}</span>`
                : `<span class="stok-badge stok-habis">Stok habis</span>`;
            return `
            <tr>
                <td style="min-width:150px">
                    <select class="obat-input" onchange="onObatChange(${i},this)">
                        <option value="">-- Pilih --</option>
                        ${stockData.map(s=>`<option value="${s.nama_obat}" data-harga="${s.harga}" data-stok="${s.jumlah}" ${o.nama===s.nama_obat?'selected':''}>${s.nama_obat}</option>`).join('')}
                    </select>
                    ${stokBadge}
                </td>
                <td><input class="obat-input" type="text" value="${o.dosis}" oninput="editObat[${i}].dosis=this.value" style="width:65px"></td>
                <td><input class="obat-input" type="number" value="${o.jumlah}" min="1" oninput="onJmlChange(${i},this)" style="width:55px"></td>
                <td style="white-space:nowrap;font-size:12px">${isBPJS?'<span style="color:var(--text3)">BPJS</span>':formatRp(o.harga)}</td>
                <td style="white-space:nowrap;font-size:12px;font-weight:500">${isBPJS?'-':formatRp(o.harga*o.jumlah)}</td>
                <td><button class="btn-del-obat" onclick="removeObat(${i})"><i class="bi bi-trash3"></i></button></td>
            </tr>`;
        }).join('');

    document.getElementById('detailPanel').innerHTML = `
        <div class="invoice-card">
            <div class="inv-header">
                <div>
                    <div class="inv-title">${r.pasien||'-'}</div>
                    <div class="inv-no">${r.no_resep||'-'} · ${r.tanggal||'-'}</div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:5px">
                    <span class="badge ${isBPJS?'b-bpjs':'b-mandiri'}">${r.bayar||'Mandiri'}</span>
                    <span class="badge b-baru">${r.status}</span>
                </div>
            </div>

            <div class="alert-banner ${isBPJS?'alert-bpjs':'alert-mandiri'}">
                <span>${isBPJS?'🏥 Pasien BPJS — total tagihan Rp 0':'💳 Pasien Mandiri — harga dari stok apotek'}</span>
            </div>

            <div class="info-grid">
                <div class="info-item"><label>Dokter</label><span>${r.dokter||'-'}</span></div>
                <div class="info-item"><label>No. RM</label><span>${r.rm||'-'}</span></div>
                <div class="info-item"><label>Diagnosa</label><span>${r.diagnosa||'-'}</span></div>
                <div class="info-item"><label>Poli</label><span>${r.poli||'-'}</span></div>
            </div>

            <div class="inv-section-title">
                Daftar Obat
                <span style="color:#A63D33;margin-left:6px;text-transform:none;font-size:10px">(dapat diedit)</span>
            </div>
            <div class="obat-table-wrap">
                <table class="obat-table">
                    <thead>
                        <tr><th>Nama Obat</th><th>Dosis</th><th>Jml</th><th>Harga</th><th>Subtotal</th><th></th></tr>
                    </thead>
                    <tbody id="obatTbody">${obatRows}</tbody>
                </table>
            </div>
            <button class="btn-add-obat" onclick="addObatRow()">
                <i class="bi bi-plus-circle"></i> Tambah Obat
            </button>

            <div class="inv-section-title" style="margin-top:14px">Ringkasan</div>
            <div class="inv-row"><span>Subtotal</span><span id="sumSubtotal">${formatRp(subtotal)}</span></div>
            ${!isBPJS
                ? `<div class="inv-row"><span>PPN 11%</span><span id="sumPpn">${formatRp(ppn)}</span></div>`
                : `<div class="inv-row" style="color:#0f9d82"><span>Ditanggung BPJS</span><span>- ${formatRp(subtotal)}</span></div>`
            }
            <div class="inv-total-row">
                <span>Total Tagihan</span>
                <span id="sumTotal" style="color:#A63D33">${formatRp(total)}</span>
            </div>

            <button class="btn btn-primary" onclick="kirimInvoice('${r.id}')">
                <i class="bi bi-send-check"></i> Kirim Invoice ke Admin
            </button>
        </div>`;
}

// ── Buka Invoice (view only) ─────────────────────────────
function openInvoice(id) {
    const inv = allInvoices.find(i => i.id == id);
    if (!inv) return;
    activeId = id;

    document.querySelectorAll('.inv-list-item').forEach(el => el.classList.remove('active'));
    document.getElementById(`item-${id}`)?.classList.add('active');

    const isBPJS = inv.jenis === 'BPJS';

    const obatRows = (inv.obat||[]).length === 0
        ? `<tr><td colspan="5" style="text-align:center;padding:16px;color:var(--text3);font-size:12px">Tidak ada data obat</td></tr>`
        : (inv.obat||[]).map(o => `
            <tr>
                <td>${o.nama||'-'}</td>
                <td style="color:var(--text3)">${o.dosis||'-'}</td>
                <td style="text-align:center">${o.jumlah||1}</td>
                <td style="text-align:right">${isBPJS?'<span style="color:var(--text3);font-size:11px">BPJS</span>':formatRp(o.harga||0)}</td>
                <td style="text-align:right;font-weight:500">${isBPJS?'-':formatRp((o.harga||0)*(o.jumlah||1))}</td>
            </tr>`).join('');

    const statusBadgeClass = { masuk:'b-masuk', diproses:'b-diproses', lunas:'b-lunas' };
    const statusBanner = inv.status === 'lunas'
        ? `<div class="alert-banner alert-success"><i class="bi bi-check-circle-fill"></i> Invoice sudah lunas</div>`
        : `<div class="alert-banner alert-info"><i class="bi bi-info-circle"></i> ${inv.status==='masuk'?'Menunggu diproses admin':'Sedang diproses admin'}</div>`;

    document.getElementById('detailPanel').innerHTML = `
        <div class="invoice-card">
            <div class="inv-header">
                <div>
                    <div class="inv-title">${inv.nama}</div>
                    <div class="inv-no">${inv.no_invoice} · ${inv.no_rm}</div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:5px">
                    <span class="badge ${isBPJS?'b-bpjs':'b-mandiri'}">${inv.jenis}</span>
                    <span class="badge ${statusBadgeClass[inv.status]||''}">${inv.status}</span>
                </div>
            </div>

            ${statusBanner}

            <div class="info-grid">
                <div class="info-item"><label>No. Invoice</label><span>${inv.no_invoice}</span></div>
                <div class="info-item"><label>Tanggal</label><span>${inv.tanggal}</span></div>
                <div class="info-item"><label>Diagnosa</label><span>${inv.diagnosa||'-'}</span></div>
                <div class="info-item"><label>No. RM</label><span>${inv.no_rm}</span></div>
            </div>

            <div class="inv-section-title">Rincian Obat</div>
            <div class="obat-table-wrap">
                <table class="obat-table">
                    <thead><tr><th>Nama Obat</th><th>Dosis</th><th style="text-align:center">Jml</th><th style="text-align:right">Harga</th><th style="text-align:right">Subtotal</th></tr></thead>
                    <tbody>${obatRows}</tbody>
                </table>
            </div>

            <div class="inv-section-title">Ringkasan</div>
            <div class="inv-row"><span>Subtotal</span><span>${formatRp(inv.subtotal)}</span></div>
            ${!isBPJS
                ? `<div class="inv-row"><span>PPN 11%</span><span>${formatRp(inv.ppn)}</span></div>`
                : `<div class="inv-row" style="color:#0f9d82"><span>Ditanggung BPJS</span><span>- ${formatRp(inv.subtotal)}</span></div>`
            }
            <div class="inv-total-row">
                <span>Total Tagihan</span>
                <span style="color:#A63D33">${formatRp(inv.total)}</span>
            </div>
        </div>`;
}

// ── Edit Obat ────────────────────────────────────────────
function onObatChange(i, sel) {
    const opt = sel.options[sel.selectedIndex];
    editObat[i].nama  = sel.value;
    editObat[i].harga = parseFloat(opt.dataset.harga) || 0;
    editObat[i].stok  = parseInt(opt.dataset.stok)   || 0;
    const r = allReseps.find(x => x.id == activeId);
    if (r) renderResepDetail(r);
}

function onJmlChange(i, input) {
    editObat[i].jumlah = parseInt(input.value) || 1;
    updateSummary();
}

function removeObat(i) {
    editObat.splice(i, 1);
    const r = allReseps.find(x => x.id == activeId);
    if (r) renderResepDetail(r);
}

function addObatRow() {
    editObat.push({ nama:'', dosis:'1x1', jumlah:1, harga:0, stok:0 });
    const r = allReseps.find(x => x.id == activeId);
    if (r) renderResepDetail(r);
}

function updateSummary() {
    const r = allReseps.find(x => x.id == activeId);
    if (!r) return;
    const isBPJS   = r.bayar === 'BPJS';
    const subtotal = editObat.reduce((s,o) => s + (isBPJS ? 0 : o.harga * o.jumlah), 0);
    const ppn      = isBPJS ? 0 : Math.round(subtotal * 0.11);
    const s = document.getElementById('sumSubtotal');
    const p = document.getElementById('sumPpn');
    const t = document.getElementById('sumTotal');
    if (s) s.textContent = formatRp(subtotal);
    if (p) p.textContent = formatRp(ppn);
    if (t) t.textContent = formatRp(subtotal + ppn);
}

// ── Kirim Invoice ────────────────────────────────────────
async function kirimInvoice(id) {
    const r = allReseps.find(x => x.id == id);
    if (!r) return;

    if (editObat.some(o => !o.nama.trim())) {
        showToast('Ada obat yang belum dipilih', 'error'); return;
    }

    const habis = editObat.filter(o => o.stok <= 0 && r.bayar !== 'BPJS');
    if (habis.length && !confirm(`Stok habis: ${habis.map(o=>o.nama).join(', ')}\nTetap kirim?`)) return;

    try {
        // 1. Simpan obat beserta harga
        const r1 = await fetch(`/apoteker/api/resep/${id}/update-obat`, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf() },
            body   : JSON.stringify({ obat_list: editObat }),
        });
        if (!r1.ok) { showToast('Gagal simpan obat', 'error'); return; }

        // 2. Update status siap → auto buat invoice di ResepController
        const r2   = await fetch(`/apoteker/api/resep/${id}/status`, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf() },
            body   : JSON.stringify({ status: 'siap' }),
        });
        const data = await r2.json();
        if (!data.success) { showToast('Gagal kirim invoice', 'error'); return; }

        showToast('✅ Invoice berhasil dikirim ke admin!', 'success');
        await Promise.all([loadReseps(), loadInvoices()]);
        switchTab('invoice');

    } catch(e) {
        console.error(e);
        showToast('Terjadi kesalahan', 'error');
    }
}

function formatRp(n) { return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }
function csrf()      { return document.querySelector('meta[name="csrf-token"]').content; }

function showToast(msg, type='success') {
    const wrap = document.getElementById('toastWrap');
    const t    = document.createElement('div');
    t.className = `toast ${type}`;
    t.textContent = msg;
    wrap.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}
</script>
@endpush
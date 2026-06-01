@extends('layouts.apoteker')
@section('content')

<style>
/* ── Layout ── */
.page-header{margin-bottom:20px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:600;color:var(--text)}
.page-title span{color:#A63D33;font-style:italic}
.page-sub{font-size:12px;color:var(--text3);margin-top:2px}

.grid2{display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start}
@media(max-width:900px){.grid2{grid-template-columns:1fr}}

/* ── Stats ── */
.inv-stats{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap}
.inv-stat{flex:1;min-width:110px;background:var(--white);border:1px solid var(--cream3);border-radius:10px;padding:12px 14px;display:flex;align-items:center;gap:10px}
.inv-stat-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}
.si-red    {background:#fce8e6;color:#A63D33}
.si-orange {background:#fff3e0;color:#f57c00}
.si-blue   {background:#e8f0fe;color:#1a73e8}
.si-green  {background:#e6f4ea;color:#34a853}
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

/* ── Obat table ── */
/* PENTING: Ubah overflow menjadi visible agar dropdown absolute tidak terpotong */
.obat-table-wrap{border:1px solid var(--cream3);border-radius:8px;overflow:visible;margin-bottom:10px}
.obat-table{width:100%;border-collapse:collapse;font-size:13px}
.obat-table thead th{background:var(--cream);padding:9px 12px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.06em;font-weight:600;color:var(--text2);border-bottom:1px solid var(--cream3)}
.obat-table tbody td{padding:10px 12px;border-bottom:1px solid var(--cream3);vertical-align:middle;position:relative;} /* Ditambahkan posisi relative */
.obat-table tbody tr:last-child td{border-bottom:none}
.obat-table tbody tr:hover td{background:var(--cream)}

.obat-input{width:100%;padding:6px 8px;border:1px solid var(--cream3);border-radius:6px;background:var(--white);font-size:12px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;box-sizing:border-box}
.obat-input:focus{border-color:#A63D33}

/* ── Buttons ── */
.btn{padding:10px 16px;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;border:1px solid var(--cream3);background:var(--cream);color:var(--text);transition:all .15s;font-weight:500}
.btn:hover{background:var(--cream3)}
.btn-primary{background:#A63D33;color:white;border-color:#A63D33;width:100%;margin-top:4px;text-align:center}
.btn-primary:hover{opacity:.88}

/* ── Info grid ── */
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px 20px;margin-bottom:14px}
.info-item label{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.05em;color:var(--text3);font-weight:500;margin-bottom:2px}
.info-item span{font-size:13px;color:var(--text);font-weight:500}

/* ── Empty ── */
.empty-state{text-align:center;padding:50px 20px;color:var(--text3)}
.empty-state i{font-size:36px;display:block;margin-bottom:10px}
.empty-state p{font-size:13px;margin:0}

/* Dropdown styling */
.dropdown-obat {position: fixed;background: white;border: 1px solid #ddd;border-radius: 6px;max-height: 120px;overflow-y: auto;z-index: 9999;box-shadow: 0 4px 12px rgba(0,0,0,0.15);display: none;}
.dropdown-item{padding:8px 10px;cursor:pointer;font-size:12px;border-bottom: 1px solid #eee;}
.dropdown-item:hover{background:#f5f5f5;}
.obat-table {overflow: visible !important;}
.obat-table tbody {overflow: visible !important;}
.invoice-card {overflow: visible !important;}
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
        <div class="inv-stat-info"><label>Resep Masuk</label><strong id="statResep">{{ $reseps->where('status', 'baru')->count() }}</strong></div>
    </div>
    <div class="inv-stat">
        <div class="inv-stat-icon si-orange"><i class="bi bi-receipt"></i></div>
        <div class="inv-stat-info"><label>Total Invoice</label><strong id="statTotal">{{ $reseps->count() }}</strong></div>
    </div>
</div>

{{-- Tabs --}}
<div class="rx-tabs">
    <button class="rx-tab active" id="tab-resep" onclick="switchTab('resep')">
        Resep Masuk <span class="rx-tab-count" id="tabCountResep">{{ $reseps->where('status', 'baru')->count() }}</span>
    </button>
    <button class="rx-tab" id="tab-invoice" onclick="switchTab('invoice')">
        Invoice Terkirim <span class="rx-tab-count" id="tabCountInvoice">{{ $reseps->where('status', '!=', 'baru')->count() }}</span>
    </button>
</div>

{{-- Grid --}}
<div class="grid2">
    {{-- LIST RESEP --}}
    <div id="list-container">
        @forelse($reseps as $r)
            <a href="{{ route('apoteker.index', ['resep' => $r->id]) }}"
               class="inv-list-item card {{ request('resep') == $r->id ? 'active' : '' }} resep-item"
               data-status="{{ $r->status == 'baru' ? 'resep' : 'invoice' }}">
                <div>
                    <div style="font-size:11px;color:#A63D33">{{ $r->no_resep }}</div>
                    <div style="font-weight:600">{{ $r->pasien->nama }}</div>
                    <div style="font-size:12px;color:gray">{{ $r->pasien->no_rm }}</div>
                </div>
                <span class="badge 
                    @if($r->status == 'baru') b-warn
                    @elseif($r->status == 'validasi') b-validasi
                    @elseif($r->status == 'siap') b-siap
                    @elseif($r->status == 'selesai') b-selesai
                    @elseif($r->status == 'ditolak') b-ditolak
                    @endif">
                    {{ $r->status }}
                </span>
            </a>
        @empty
            <div class="empty-state">Tidak ada resep</div>
        @endforelse
    </div>

    {{-- DETAIL --}}
    <div>
        @if($selectedResep)
            <div class="invoice-card">
                <div class="inv-header">
                    <div>
                        <div class="inv-title">{{ $selectedResep->pasien->nama }}</div>
                        <div class="inv-no">{{ $selectedResep->no_resep }}</div>
                    </div>
                    <span class="badge 
                    @if($selectedResep->status == 'baru') b-warn
                    @elseif($selectedResep->status == 'validasi') b-validasi
                    @elseif($selectedResep->status == 'siap') b-siap
                    @elseif($selectedResep->status == 'selesai') b-selesai
                    @elseif($selectedResep->status == 'ditolak') b-ditolak
                    @endif">
                    {{ $selectedResep->status }}
                </span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>No RM</label>
                        <span>{{ $selectedResep->pasien->no_rm }}</span>
                    </div>
                    <div class="info-item">
                        <label>Dokter</label>
                        <span>{{ $selectedResep->pasien->dokter }}</span>
                    </div>
                    <div class="info-item">
                        <label>Jenis Pasien</label>
                        <span>
                            @if($selectedResep->pasien->jenis == 'BPJS')
                                <span class="badge b-bpjs">BPJS</span>
                            @else
                                <span class="badge b-mandiri">Mandiri</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Diagnosa</label>
                        <span>{{ $selectedResep->diagnosa }}</span>
                    </div>
                </div>

                <div class="inv-section-title">Daftar Obat</div>

                <form action="{{ route('resep.kirim', $selectedResep->id) }}" method="POST">
                @csrf
                <div class="obat-table-wrap">
                    <table class="obat-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Dosis</th>
                                <th>Jumlah</th>
                                <th></th> 
                            </tr>
                        </thead>
                        <tbody id="obat-tbody">
                            @foreach($selectedResep->obat_list as $i => $o)
                            <tr>
                                <td style="position:relative;overflow:visible !important;">
                                    <input type="text"
                                        name="obat[{{ $i }}][nama]"
                                        value="{{ $o['nama'] }}"
                                        class="obat-input nama-obat"
                                        autocomplete="off">
                                    <div class="dropdown-obat"></div>
                                </td>
                                <td>
                                    <input type="text" name="obat[{{ $i }}][dosis]"
                                        value="{{ $o['dosis'] }}"
                                        class="obat-input">
                                </td>
                                <td>
                                    <input type="number" name="obat[{{ $i }}][jumlah]"
                                        value="{{ $o['jumlah'] }}"
                                        class="obat-input jumlah-obat">
                                    <div class="stok-info" style="font-size:11px;margin-top:3px;color:gray;"></div>
                                </td>
                                <td style="width:36px;text-align:center;">
                                    <button type="button" onclick="hapusObat(this)"
                                        style="background:none;border:none;cursor:pointer;color:#A63D33;font-size:16px;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display:flex;gap:8px;margin-top:15px; margin-bottom:8px">
                    <button type="button" onclick="tambahObat()"
                        class="btn"
                        style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <i class="bi bi-plus-circle"></i> Tambah Obat
                    </button>
                    <button type="submit" class="btn btn-primary" style="flex:2;">
                        Kirim Invoice
                    </button>
                </div>
                </form>
            </div>
        @else
            <div class="empty-state">Pilih resep</div>
        @endif
    </div>
</div>

<script>
// Buat satu dropdown global di body
const globalDropdown = document.createElement('div');
globalDropdown.className = 'dropdown-obat';
document.body.appendChild(globalDropdown);

let activeInput = null;
let activeRow = null;

function posisiDropdown() {
    if (!activeInput) return;
    let rect = activeInput.getBoundingClientRect();
    globalDropdown.style.top   = (rect.bottom + window.scrollY) + 'px';
    globalDropdown.style.left  = rect.left + 'px';
    globalDropdown.style.width = rect.width + 'px';
}

// Update posisi saat scroll
window.addEventListener('scroll', posisiDropdown, true);

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('nama-obat')) {
        activeInput = e.target;
        activeRow   = e.target.closest('tr');
        let keyword = activeInput.value;

        if (keyword.length < 2) {
            globalDropdown.innerHTML = '';
            globalDropdown.style.display = 'none';
            return;
        }

        // Pakai absolute bukan fixed, karena dropdown ada di body
        globalDropdown.style.position = 'absolute';
        posisiDropdown();
        globalDropdown.style.display = 'block';

        fetch(`{{ route('apoteker.obat.search') }}?q=${encodeURIComponent(keyword)}`)
        .then(res => res.json())
        .then(data => {
            globalDropdown.innerHTML = '';
            if (data.length === 0) {
                globalDropdown.innerHTML = '<div class="dropdown-item" style="color:gray">Tidak ditemukan</div>';
                return;
            }
            data.forEach(item => {
                let div = document.createElement('div');
                div.classList.add('dropdown-item');
                div.innerHTML = `<b>${item.nama}</b><br><small style="color:#666">Stok: ${item.stok} | Harga: Rp ${Number(item.harga).toLocaleString('id-ID')}</small>`;
                div.addEventListener('mousedown', function(ev) {
                    ev.preventDefault(); // cegah blur dulu
                    activeInput.value = item.nama;
                    let stokInfo    = activeRow.querySelector('.stok-info');
                    let jumlahInput = activeRow.querySelector('.jumlah-obat');
                    stokInfo.innerHTML = `Stok: <b>${item.stok}</b> | Harga: Rp ${Number(item.harga).toLocaleString('id-ID')}`;
                    jumlahInput.max = item.stok;
                    jumlahInput.setAttribute('data-harga', item.harga);
                    globalDropdown.innerHTML = '';
                    globalDropdown.style.display = 'none';
                });
                globalDropdown.appendChild(div);
            });
        }).catch(err => console.error("Gagal:", err));
    }
});

document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('nama-obat')) {
        globalDropdown.innerHTML = '';
        globalDropdown.style.display = 'none';
        activeInput = null;
    }
});

function switchTab(type) {
    document.querySelectorAll('.rx-tab').forEach(tab => tab.classList.remove('active'));
    document.getElementById(type === 'resep' ? 'tab-resep' : 'tab-invoice').classList.add('active');
    document.querySelectorAll('.resep-item').forEach(item => {
        item.style.display = item.getAttribute('data-status') === type ? 'block' : 'none';
    });
}
switchTab('resep');

document.addEventListener('input', function (e) {
    if (e.target.classList.contains('jumlah-obat')) {
        let total = 0;
        document.querySelectorAll('.jumlah-obat').forEach(input => {
            let harga = parseFloat(input.getAttribute('data-harga') || 0);
            let qty   = parseFloat(input.value || 0);
            total += (harga * qty);
        });
        console.log("Total: Rp " + total);
    }
});

let obatIndex = {{ $selectedResep ? count($selectedResep->obat_list) : 0 }};
function tambahObat() {
    let tbody = document.getElementById('obat-tbody');
    let i = obatIndex++;
    let tr = document.createElement('tr');
    tr.innerHTML = `
        <td style="position:relative;overflow:visible !important;">
            <input type="text" name="obat[${i}][nama]" value=""
                class="obat-input nama-obat" autocomplete="off" placeholder="Cari nama obat...">
        </td>
        <td>
            <input type="text" name="obat[${i}][dosis]" value=""
                class="obat-input" placeholder="Misal: 3x1">
        </td>
        <td>
            <input type="number" name="obat[${i}][jumlah]" value=""
                class="obat-input jumlah-obat" placeholder="0">
            <div class="stok-info" style="font-size:11px;margin-top:3px;color:gray;"></div>
        </td>
        <td style="width:36px;text-align:center;">
            <button type="button" onclick="hapusObat(this)"
                style="background:none;border:none;cursor:pointer;color:#A63D33;font-size:16px;">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

function hapusObat(btn) {
    let row = btn.closest('tr');
    let tbody = document.getElementById('obat-tbody');
    if (tbody.querySelectorAll('tr').length <= 1) {
        alert('Minimal harus ada 1 obat');
        return;
    }
    row.remove();
}
</script>
@endsection
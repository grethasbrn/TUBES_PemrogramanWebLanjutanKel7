@extends('layouts.apoteker')
@section('content')

<style>
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css");
/* ── Layout ── */
.page-header{margin-bottom:20px}
.page-title{font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:600;color:var(--text)}
.page-title span{color:#A63D33;font-style:italic}
.page-sub{font-size:12px;color:var(--text3);margin-top:2px}

/* Ditambahkan modifier class .full-width untuk menyembunyikan detail saat melihat invoice */
.grid2{display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start}
.grid2.full-width{grid-template-columns:1fr !important;}
.grid2.full-width #detail-container{display:none !important;}

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

/* ── Obat table ── */
.obat-table-wrap{border:1px solid var(--cream3);border-radius:8px;overflow:visible;margin-bottom:10px}
.obat-table{width:100%;border-collapse:collapse;font-size:13px}
.obat-table thead th{background:var(--cream);padding:9px 12px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.06em;font-weight:600;color:var(--text2);border-bottom:1px solid var(--cream3)}
.obat-table tbody td{padding:10px 12px;border-bottom:1px solid var(--cream3);vertical-align:middle;position:relative;}
.obat-table tbody tr:last-child td{border-bottom:none}
.obat-table tbody tr:hover td{background:var(--cream)}

.obat-input {padding: 6px 8px;border: 1px solid var(--cream3);border-radius: 6px;background: var(--white);
font-size: 12px;font-family: 'DM Sans', sans-serif;color: var(--text); outline: none;box-sizing: border-box;}
.obat-input:focus {border-color: #A63D33;}
.obat-input.nama-obat {width: 100%; }
.obat-input.dosis-obat, 
.obat-input.jumlah-obat {width: 75px;}

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
<div class="grid2" id="main-grid">
    {{-- LIST RESEP / INVOICE (BAGIAN KIRI) --}}
    <div id="list-container">
        @forelse($reseps as $r)
            @php
                $isSelected = request('resep') == $r->id;
                $payBadge = $r->pasien->jenis === 'BPJS' ? 'b-bpjs' : 'b-mandiri';
                $statusBadge = [
                    'baru' => 'b-warn',
                    'validasi' => 'b-validasi',
                    'siap' => 'b-siap',
                    'selesai' => 'b-selesai',
                    'ditolak' => 'b-ditolak'
                ][$r->status] ?? 'b-warn';

                // Hitung total harga item di list samping jika statusnya bukan resep baru
                $listTotal = collect($r->obat_list)->sum(function($o) use ($r) {
                    if ($r->pasien->jenis === 'BPJS') return 0;
                    $b = \App\Models\Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%'.strtolower($o['nama'] ?? '').'%'])->where('jumlah','>',0)->first();
                    return ($o['harga'] ?? $b?->harga ?? 0) * ($o['jumlah'] ?? 0);
                });
                $listPpn = $r->pasien->jenis === 'BPJS' ? 0 : round($listTotal * 0.11);
                $listGrandTotal = $listTotal + $listPpn;
            @endphp

            {{-- Jika statusnya 'baru' link aktif mengarah ke detail, jika sudah terkirim (invoice) link dinonaktifkan (pointer-events: none) --}}
            <a href="{{ $r->status == 'baru' ? route('apoteker.invoice', ['resep' => $r->id]) : '#' }}"
               class="card resep-item"
               data-status="{{ $r->status == 'baru' ? 'resep' : 'invoice' }}"
               style="display:block; text-decoration:none; margin-bottom:12px; border-width: 1px; border-style: solid; border-color:{{ ($isSelected && $r->status == 'baru') ? '#A63D33' : 'var(--cream3)' }}; background:{{ ($isSelected && $r->status == 'baru') ? 'var(--red-light)' : 'var(--white)' }}; padding: 14px; border-radius: 8px; {{ $r->status != 'baru' ? 'pointer-events: none;' : '' }}">
                
                <div style="display:flex; justify-content:space-between; align-items:flex-start">
                    <div>
                        <div style="font-size:11px; color:#A63D33; font-weight: 500;">{{ $r->no_resep }}</div>
                        <div style="font-weight:600; font-size:14px; color:var(--text); margin-top:2px;">{{ $r->pasien->nama }}</div>
                        <div style="font-size:12px; color:var(--text2); margin-top:2px">{{ $r->pasien->no_rm }} ·  {{ $r->pasien->dokter }}</div>
                        
                        <div style="margin-top:8px; display:flex; gap:6px">
                            <span class="badge {{ $payBadge }}">{{ $r->pasien->jenis }}</span>
                            <span class="badge {{ $statusBadge }}">{{ $r->status }}</span>
                        </div>
                    </div>

                    <div style="text-align:right">
                        @if($r->pasien->jenis === 'BPJS')
                            <div style="font-family:'Cormorant Garamond',serif; font-size:15px; font-weight:600; color:#34a853">Gratis</div>
                        @else
                            <div style="font-family:'Cormorant Garamond',serif; font-size:15px; font-weight:600; color:var(--text)">Rp {{ number_format($listGrandTotal, 0, ',', '.') }}</div>
                        @endif
                        <div style="font-size:11px; color:var(--text3); margin-top:4px">{{ $r->created_at ? $r->created_at->format('d M Y') : '' }}</div>
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state">Tidak ada resep</div>
        @endforelse
    </div>

    {{-- DETAIL (BAGIAN KANAN) --}}
    <div id="detail-container">
        @if($selectedResep && $selectedResep->status == 'baru')
            <div class="invoice-card">
                <div class="inv-header">
                    <div>
                        <div class="inv-title">{{ $selectedResep->pasien->nama }}</div>
                        <div class="inv-no">{{ $selectedResep->no_resep }}</div>
                    </div>
                    <span class="badge b-warn">{{ $selectedResep->status }}</span>
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
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                                <th></th> 
                            </tr>
                        </thead>
                        <tbody id="obat-tbody">
                            @foreach($selectedResep->obat_list as $i => $o)
                            @php
                                $batch = \App\Models\Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%'.strtolower($o['nama'] ?? '').'%'])
                                    ->where('jumlah', '>', 0)->first();
                                $harga = ($selectedResep->pasien->jenis === 'BPJS') ? 0 : ($o['harga'] ?? ($batch?->harga ?? 0));
                                $subtotal = $harga * ($o['jumlah'] ?? 0);
                            @endphp
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
                                        class="obat-input dosis-obat">
                                </td>
                                <td>
                                    <input type="number" name="obat[{{ $i }}][jumlah]"
                                        value="{{ $o['jumlah'] }}"
                                        class="obat-input jumlah-obat"
                                        data-harga="{{ $harga }}"
                                        oninput="hitungTotal()">
                                    <div class="stok-info" style="font-size:11px;margin-top:3px;color:gray;"></div>
                                </td>
                                <td style="font-size:13px;white-space:nowrap">
                                    Rp {{ number_format($harga, 0, ',', '.') }}
                                    <input type="hidden" name="obat[{{ $i }}][harga]" value="{{ $harga }}">
                                </td>
                                <td class="subtotal-cell" style="font-size:13px;font-weight:600;white-space:nowrap">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
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

                        <tfoot>
                            <tr style="background:var(--cream)">
                                <td colspan="4" style="padding:10px 12px;font-weight:600;text-align:right">Subtotal</td>
                                <td id="totalSubtotal" style="padding:10px 12px;font-weight:600;font-size:14px">
                                    @php
                                        $total = collect($selectedResep->obat_list)->sum(function($o) use ($selectedResep) {
                                            if ($selectedResep->pasien->jenis === 'BPJS') return 0;
                                            $b = \App\Models\Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%'.strtolower($o['nama'] ?? '').'%'])->where('jumlah','>',0)->first();
                                            return ($o['harga'] ?? $b?->harga ?? 0) * ($o['jumlah'] ?? 0);
                                        });
                                        $ppn = $selectedResep->pasien->jenis === 'BPJS' ? 0 : round($total * 0.11);
                                    @endphp
                                Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                            <tr style="background:var(--cream)">
                                <td colspan="4" style="padding:4px 12px;text-align:right;color:var(--text3);font-size:12px">PPN 11%</td>
                                <td style="padding:4px 12px;color:var(--text3);font-size:12px">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                            <tr style="background:#fce8e6">
                                <td colspan="4" style="padding:10px 12px;font-weight:700;text-align:right;color:#A63D33">TOTAL TAGIHAN</td>
                                <td id="grandTotal" style="padding:10px 12px;font-weight:700;font-size:15px;color:#A63D33">
                                    @if($selectedResep->pasien->jenis === 'BPJS')
                                        <span style="color:#34a853">GRATIS (BPJS)</span>
                                    @else
                                        Rp {{ number_format($total + $ppn, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>

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
                    ev.preventDefault();
                    activeInput.value = item.nama;
                    let stokInfo    = activeRow.querySelector('.stok-info');
                    let jumlahInput = activeRow.querySelector('.jumlah-obat');
                    
                    stokInfo.innerHTML = `Stok: <b>${item.stok}</b> | Harga: Rp ${Number(item.harga).toLocaleString('id-ID')}`;
                    jumlahInput.max = item.stok;
                    jumlahInput.setAttribute('data-harga', item.harga);
                    
                    // ── TAMBAHKAN KODE DI BAWAH INI ──
                    // Berfungsi memperbarui teks Harga Satuan (kolom ke-4) dan input hidden harganya
                    let hargaCell = activeRow.cells[3]; 
                    if (hargaCell) {
                        hargaCell.innerHTML = `Rp ${Number(item.harga).toLocaleString('id-ID')} <input type="hidden" name="${jumlahInput.name.replace('[jumlah]', '[harga]')}" value="${item.harga}">`;
                    }
                    
                    globalDropdown.innerHTML = '';
                    globalDropdown.style.display = 'none';
                    
                    // Pemicu hitung ulang subtotal & grand total secara real-time
                    hitungTotal(); 
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
    
    const mainGrid = document.getElementById('main-grid');
    
    if(type === 'invoice') {
        // Sembunyikan detail & buat list kiri memenuhi lebar layar (1 column full)
        mainGrid.classList.add('full-width');
    } else {
        // Kembalikan ke format split grid 2 column saat tab resep masuk dibuka
        mainGrid.classList.remove('full-width');
    }

    document.querySelectorAll('.resep-item').forEach(item => {
        item.style.display = item.getAttribute('data-status') === type ? 'block' : 'none';
    });
}
// Default startup
switchTab('resep');

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
                class="obat-input dosis-obat" placeholder="Misal: 3x1"> </td>
        <td>
            <input type="number" name="obat[${i}][jumlah]" value=""
                class="obat-input jumlah-obat" placeholder="0">
            <div class="stok-info" style="font-size:11px;margin-top:3px;color:gray;"></div>
        </td>
        <td style="font-size:13px;white-space:nowrap">
            Rp 0
            <input type="hidden" name="obat[${i}][harga]" value="0">
        </td>
        <td class="subtotal-cell" style="font-size:13px;font-weight:600;white-space:nowrap">
            Rp 0
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

function hitungTotal() {
    let subtotal = 0;
    document.querySelectorAll('.jumlah-obat').forEach(input => {
        const harga = parseFloat(input.getAttribute('data-harga') || 0);
        const qty   = parseFloat(input.value || 0);
        subtotal += harga * qty;
        const row = input.closest('tr');
        const cell = row.querySelector('.subtotal-cell');
        if (cell) cell.textContent = 'Rp ' + (harga * qty).toLocaleString('id-ID');
    });
    const ppn = subtotal * 0.11;
    const grand = subtotal + ppn;
    const el = document.getElementById('totalSubtotal');
    const gel = document.getElementById('grandTotal');
    if (el) el.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    if (gel) gel.textContent = 'Rp ' + grand.toLocaleString('id-ID');
}
</script>
@endsection
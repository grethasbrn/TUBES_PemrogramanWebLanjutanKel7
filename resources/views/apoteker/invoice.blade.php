@extends('layouts.apoteker')
@section('content')

<div class="page-section active" id="sec-invoice">
  <div class="page-header">
    <div>
      <div class="page-title">Invoice</div>
      <div class="page-sub">Daftar tagihan yang dikirim ke admin</div>
    </div>
  </div>

  <div class="rx-layout">

    {{-- KIRI: List Invoice --}}
    <div>
      <div class="search-row" style="margin-bottom:12px">
        <input type="text" class="search-input" placeholder="Cari no. invoice atau pasien..."
               id="invSearch" oninput="renderInvoiceList()">
        <select class="filter-sel" id="invFilterStatus" onchange="renderInvoiceList()">
          <option value="">Semua</option>
          <option value="masuk">Masuk</option>
          <option value="diproses">Diproses</option>
          <option value="lunas">Lunas</option>
        </select>
      </div>
      <div id="invoiceList">
        <div class="card" style="text-align:center;padding:30px;color:var(--text3)">
          Memuat data invoice...
        </div>
      </div>
    </div>

    {{-- KANAN: Detail Invoice --}}
    <div id="invoicePreview">
      <div class="card" style="text-align:center;padding:40px 20px;color:var(--text3)">
        <div style="font-size:32px;margin-bottom:10px">🧾</div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:16px">Pilih invoice untuk preview</div>
      </div>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
let allInvoices = [];
let selectedInv = null;

// ── Load dari API ──
async function loadInvoices() {
    try {
        const res = await fetch('/apoteker/api/resep');
        const reseps = await res.json();

        // Konversi resep → invoice (simulasi)
        allInvoices = reseps.map((r, i) => {
            const obat = r.obat || [];
            const subtotal = obat.reduce((sum, o) => sum + ((o.harga || 0) * (o.qty || 1)), 0);
            const ppn = Math.round(subtotal * 0.11);
            return {
                id: r.id,
                no_invoice: `INV-2026-${String(i+1).padStart(3,'0')}`,
                pasien: r.pasien || '-',
                rm: r.rm || '-',
                resep: r.no_resep || '-',
                bayar: r.bayar || 'Mandiri',
                status: r.status === 'selesai' ? 'lunas' : (r.status === 'validasi' ? 'diproses' : 'masuk'),
                tanggal: r.tanggal || '-',
                obat: obat,
                subtotal: subtotal,
                ppn: ppn,
                total: subtotal + ppn,
                diagnosa: r.diagnosa || '-',
            };
        });

        renderInvoiceList();
    } catch (err) {
        document.getElementById('invoiceList').innerHTML = `
            <div class="card" style="text-align:center;padding:30px;color:var(--text3)">
                Gagal memuat data invoice
            </div>`;
    }
}

function renderInvoiceList() {
    const q      = document.getElementById('invSearch').value.toLowerCase();
    const status = document.getElementById('invFilterStatus').value;
    const container = document.getElementById('invoiceList');

    let list = allInvoices;
    if (q)      list = list.filter(i => i.no_invoice.toLowerCase().includes(q) || i.pasien.toLowerCase().includes(q));
    if (status) list = list.filter(i => i.status === status);

    if (!list.length) {
        container.innerHTML = `
            <div class="card" style="text-align:center;padding:30px;color:var(--text3)">
                Tidak ada invoice ditemukan
            </div>`;
        return;
    }

    const badgeMap  = { masuk: 'b-baru', diproses: 'b-validasi', lunas: 'b-selesai' };
    const labelMap  = { masuk: 'Masuk', diproses: 'Diproses', lunas: 'Lunas' };

    container.innerHTML = list.map(inv => `
        <div class="rx-item inv-item" id="inv-${inv.id}" onclick="showInvoice('${inv.id}')">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px">
                <span class="rx-no">${inv.no_invoice}</span>
                <span style="font-family:'Cormorant Garamond',serif;font-size:15px;font-weight:600;color:var(--text)">
                    ${formatRp(inv.total)}
                </span>
            </div>
            <div class="rx-meta">${inv.pasien} · ${inv.rm}</div>
            <div style="display:flex;gap:6px;margin-top:6px;align-items:center">
                <span class="badge ${inv.bayar === 'BPJS' ? 'b-siap' : 'b-mandiri'}">${inv.bayar}</span>
                <span class="badge ${badgeMap[inv.status] || ''}">${labelMap[inv.status] || inv.status}</span>
                <span style="font-size:11px;color:var(--text3);margin-left:auto">${inv.tanggal}</span>
            </div>
        </div>
    `).join('');
}

function showInvoice(id) {
    document.querySelectorAll('.inv-item').forEach(el => el.classList.remove('selected'));
    const el = document.getElementById(`inv-${id}`);
    if (el) el.classList.add('selected');

    const inv = allInvoices.find(i => i.id == id);
    if (!inv) return;
    selectedInv = inv;

    const obatHtml = (inv.obat || []).length === 0
        ? '<div style="font-size:12px;color:var(--text3);padding:8px 0">Tidak ada data obat</div>'
        : (inv.obat || []).map(o => `
            <div style="display:flex;justify-content:space-between;align-items:center;
                        padding:10px 0;border-bottom:1px solid var(--cream3);font-size:13px">
                <div>
                    <div style="font-weight:500;color:var(--text)">${o.nama}</div>
                    <div style="font-size:11px;color:var(--text3);margin-top:2px">
                        ${o.qty || 1} × ${formatRp(o.harga || 0)}
                    </div>
                </div>
                <div style="font-weight:500;color:var(--text)">${formatRp((o.harga||0)*(o.qty||1))}</div>
            </div>
        `).join('');

    const bayarInfo = inv.bayar === 'BPJS'
        ? `<div style="background:var(--teal-light);border:1px solid #a0d9d2;border-radius:8px;
                       padding:9px 13px;font-size:12px;color:var(--teal);display:flex;align-items:center;gap:8px;margin-bottom:16px">
                <span>🏥</span> Pasien BPJS — biaya ditanggung BPJS
           </div>`
        : `<div style="background:var(--purple-light);border:1px solid #c5bef0;border-radius:8px;
                       padding:9px 13px;font-size:12px;color:var(--purple2);display:flex;align-items:center;gap:8px;margin-bottom:16px">
                <span>💳</span> Pasien Mandiri — biaya dibayar penuh
           </div>`;

    const btnKirim = inv.status === 'masuk'
        ? `<button class="btn btn-danger" style="flex:1" onclick="kirimInvoice('${inv.id}')">
               🖨 Proses Pembayaran
           </button>
           <button class="btn btn-teal" style="flex:1" onclick="tandaiDiproses('${inv.id}')">
               ✅ Tandai Diproses
           </button>`
        : inv.status === 'diproses'
        ? `<button class="btn btn-teal" style="flex:1" onclick="tandaiLunas('${inv.id}')">
               ✅ Tandai Lunas
           </button>`
        : `<div style="text-align:center;font-size:13px;color:var(--green);font-weight:500;width:100%">
               ✅ Invoice sudah lunas
           </div>`;

    document.getElementById('invoicePreview').innerHTML = `
        <div class="card" style="padding:0;overflow:hidden">

            {{-- Header --}}
            <div style="background:var(--orange);padding:16px 18px;display:flex;justify-content:space-between;align-items:flex-start">
                <div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:600;color:white">
                        ${inv.no_invoice}
                    </div>
                    <div style="font-size:12px;color:rgba(255,255,255,.8);margin-top:2px">
                        ${inv.pasien} · ${inv.rm}
                    </div>
                </div>
                <span class="badge" style="background:rgba(255,255,255,.25);color:white">
                    ${inv.status === 'masuk' ? 'Masuk' : inv.status === 'diproses' ? 'Diproses' : 'Lunas'}
                </span>
            </div>

            <div style="padding:16px 18px">
                ${bayarInfo}

                {{-- Rincian Obat --}}
                <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">
                    Rincian Obat
                </div>
                ${obatHtml}

                {{-- Total --}}
                <div style="margin-top:12px;padding-top:4px">
                    <div style="display:flex;justify-content:space-between;font-size:13px;
                                color:var(--text2);padding:6px 0;border-bottom:1px solid var(--cream3)">
                        <span>Subtotal</span>
                        <span>${formatRp(inv.subtotal)}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;
                                color:var(--text2);padding:6px 0;border-bottom:1px solid var(--cream3)">
                        <span>PPN 11%</span>
                        <span>${formatRp(inv.ppn)}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:16px;
                                font-family:'Cormorant Garamond',serif;font-weight:600;
                                padding:10px 0;color:var(--orange)">
                        <span>Total Tagihan</span>
                        <span>${formatRp(inv.total)}</span>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div style="display:flex;gap:8px;margin-top:4px">
                    ${btnKirim}
                </div>
            </div>
        </div>`;
}

// ── Aksi ──
function kirimInvoice(id) {
    const inv = allInvoices.find(i => i.id == id);
    if (inv) { inv.status = 'diproses'; renderInvoiceList(); showInvoice(id); }
    showToast('Invoice diproses!', 'success');
}
function tandaiDiproses(id) {
    const inv = allInvoices.find(i => i.id == id);
    if (inv) { inv.status = 'diproses'; renderInvoiceList(); showInvoice(id); }
    showToast('Ditandai diproses', 'success');
}
function tandaiLunas(id) {
    const inv = allInvoices.find(i => i.id == id);
    if (inv) { inv.status = 'lunas'; renderInvoiceList(); showInvoice(id); }
    showToast('Invoice lunas!', 'success');
}

// ── Helpers ──
function formatRp(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

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

document.addEventListener('DOMContentLoaded', loadInvoices);
</script>
@endpush
@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-invoice">
  <div class="page-header">
    <div>
      <div class="page-title">Invoice Masuk</div>
      <div class="page-sub">Invoice dari apoteker untuk diproses pembayaran</div>
    </div>
  </div>

  <div class="grid2" style="align-items:start">
    <div>
      <div class="search-row">
        <input type="text" class="search-input" placeholder="Cari no. invoice atau pasien..." id="srchInvoice" oninput="renderInvoiceList()">
        <select class="filter-sel" id="fltrInvoiceStatus" onchange="renderInvoiceList()">
          <option value="">Semua</option>
          <option value="Masuk">Masuk</option>
          <option value="Diproses">Diproses</option>
          <option value="Lunas">Lunas</option>
        </select>
      </div>
      <div id="invoiceList"></div>
    </div>

    <div id="invoiceDetail">
      <div class="empty-state card"><div class="icon">🧾</div><div class="label">Pilih invoice untuk detail</div></div>
    </div>
  </div>
</div>

<script>
let invoiceData = [
  {
    id: 'INV-2026-001',
    pasien: 'Sari Dewi',
    rm: 'RM-2026-0042',
    tanggal: '24 Apr 2026',
    status: 'Masuk',
    bayar: 'Mandiri',
    items: [
      { nama: 'Levofloxacin 500mg', qty: 5, harga: 45000 },
      { nama: 'Fexofenadine 120mg', qty: 10, harga: 12000 },
      { nama: 'Paracetamol 500mg', qty: 15, harga: 3500 }
    ]
  },
  {
    id: 'INV-2026-002',
    pasien: 'Ahmad Ridwan',
    rm: 'RM-2026-0078',
    tanggal: '24 Apr 2026',
    status: 'Diproses',
    bayar: 'BPJS',
    items: [
      { nama: 'Amoxicillin', qty: 10, harga: 5000 }
    ]
  },
  {
    id: 'INV-2026-003',
    pasien: 'Nur Halimah',
    rm: 'RM-2026-0091',
    tanggal: '23 Apr 2026',
    status: 'Lunas',
    bayar: 'Mandiri',
    items: [
      { nama: 'Vitamin C', qty: 1, harga: 52500 }
    ]
  }
];

let currentInvoice = null;

console.log(invoiceData);

function fRp(n){
  return 'Rp ' + n.toLocaleString();
}

function renderInvoiceList() {
  const q = (document.getElementById('srchInvoice').value || '').toLowerCase();
  const fs = document.getElementById('fltrInvoiceStatus').value;

  let data = invoiceData.filter(i => {
    if (q && !i.id.toLowerCase().includes(q) && !i.pasien.toLowerCase().includes(q)) return false;
    if (fs && i.status !== fs) return false;
    return true;
  });

  // Pemetaan badge sesuai sistem CSS kamu
  const sBadge = { 
    'Masuk': 'b-danger', 
    'Diproses': 'b-warn', 
    'Lunas': 'b-selesai' 
  };

  document.getElementById('invoiceList').innerHTML = data.map(i => {
    const total = i.items.reduce((s, x) => s + x.qty * x.harga, 0);
    const isSelected = currentInvoice && currentInvoice.id === i.id;
    
    // Gunakan class b-bpjs atau b-mandiri sesuai data
    const payBadge = i.bayar === 'BPJS' ? 'b-bpjs' : 'b-mandiri';

    return `
      <div class="card ${isSelected ? 'active' : ''}" 
           onclick="selectInvoice('${i.id}')" 
           style="margin-bottom:12px; cursor:pointer; border-color:${isSelected ? '#A63D33' : 'var(--cream3)'}; background:${isSelected ? 'var(--red-light)' : 'var(--white)'}">
        
        <div style="display:flex; justify-content:space-between; align-items:flex-start">
          <div>
            <div style="font-family:'Cormorant Garamond',serif; font-size:15px; font-weight:600; color:var(--text)">${i.id}</div>
            <div style="font-size:12px; color:var(--text2); margin-top:2px">${i.pasien} · ${i.rm}</div>
            
            <div style="margin-top:8px; display:flex; gap:6px">
              <span class="badge ${payBadge}">${i.bayar}</span>
              <span class="badge ${sBadge[i.status] || 'b-warn'}">${i.status}</span>
            </div>
          </div>

          <div style="text-align:right">
            <div style="font-family:'Cormorant Garamond',serif; font-size:17px; font-weight:600; color:var(--text)">${fRp(total)}</div>
            <div style="font-size:11px; color:var(--text3); margin-top:4px">${i.tgl || '24 Apr 2026'}</div>
          </div>
        </div>

      </div>`;
  }).join('');

  if (!data.length) {
    document.getElementById('invoiceList').innerHTML = `
      <div style="color:var(--text3); font-size:13px; padding:24px; text-align:center">
        Tidak ada invoice
      </div>`;
  }
}

function selectInvoice(id) {
  currentInvoice = invoiceData.find(i => i.id === id);
  renderInvoiceList();
  renderInvoiceDetail();
}

function renderInvoiceDetail() {
  const inv = currentInvoice;
  const detailContainer = document.getElementById('invoiceDetail');

  if (!inv) {
    detailContainer.innerHTML = `
      <div class="card" style="text-align:center; padding: 50px 20px; color: var(--text3);">
        <div style="font-size: 40px; margin-bottom: 10px;">🧾</div>
        <div class="label">Pilih invoice untuk melihat detail</div>
      </div>`;
    return;
  }

  const subtotal = inv.items.reduce((s, i) => s + i.qty * i.harga, 0);
  const isBPJS = inv.bayar === 'BPJS';
  const ppn = isBPJS ? 0 : Math.round(subtotal * 0.11);
  const totalAkhir = isBPJS ? 0 : subtotal + ppn;
  
  // Mapping badge status sesuai CSS sistem kamu
  const sBadge = { Masuk: 'b-danger', Diproses: 'b-warn', Lunas: 'b-selesai' };

  detailContainer.innerHTML = `
    <div class="invoice-card">
      <div class="inv-header" style="background: #A63D33; margin: -22px -22px 18px -22px; padding: 22px; border-radius: 12px 12px 0 0; color: white; border-bottom: none; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
          <div class="inv-title" style="color: white; font-size: 18px;">${inv.id}</div>
          <div class="inv-no" style="color: rgba(255,255,255,0.8); font-size: 13px;">${inv.pasien} · ${inv.rm}</div>
        </div>
        <span class="badge" style="background: rgba(255,255,255,0.2); color: white; border: none;">${inv.status}</span>
      </div>

      <div class="detail-body">
        <div style="display:flex; gap:6px; margin-bottom:12px">
          ${badgeBayar(inv.bayar)}
        </div>

        ${isBPJS ? 
          `<div class="alert-banner info" style="background: var(--teal-light); color: #0F6E56; border: none; font-size: 12px;">
            <span>🏥 Pasien BPJS — biaya obat standar ditanggung pemerintah</span>
          </div>` : 
          `<div class="alert-banner info" style="font-size: 12px;">
            <span>💳 Pasien Mandiri — biaya dibayar penuh</span>
          </div>`
        }

        <div class="inv-section-title">RINCIAN OBAT</div>

        <div style="border: 1px solid var(--cream3); border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
          ${inv.items.map(i => `
            <div class="inv-row" style="padding: 10px 12px; background: var(--white);">
              <div>
                <div style="font-weight: 500; color: var(--text);">${i.nama}</div>
                <div style="font-size: 11px; color: var(--text3);">${i.qty} × ${fRp(i.harga)}</div>
              </div>
              <div style="font-weight: 600;">${fRp(i.qty * i.harga)}</div>
            </div>
          `).join('')}

          <div style="padding: 12px; background: var(--cream2); border-top: 1px solid var(--cream3);">
            <div class="inv-row" style="border: none; padding: 2px 0;">
              <span style="color: var(--text2);">Subtotal</span>
              <span>${fRp(subtotal)}</span>
            </div>
            ${isBPJS ? 
              `<div class="inv-row" style="border: none; padding: 2px 0; color: var(--teal);">
                <span>Ditanggung BPJS</span>
                <span>- ${fRp(subtotal)}</span>
              </div>` : 
              `<div class="inv-row" style="border: none; padding: 2px 0;">
                <span>PPN 11%</span>
                <span>${fRp(ppn)}</span>
              </div>`
            }
          </div>

          <div class="inv-total-row" style="padding: 12px; background: var(--cream); border-top: 1px solid var(--cream3); color: #A63D33;">
            <span>Total Tagihan</span>
            <span style="font-size: 19px;">${isBPJS ? 'Rp 0' : fRp(totalAkhir)}</span>
          </div>
        </div>

        ${inv.status !== 'Lunas' ? `
          <div style="display: flex; gap: 8px; margin-top: 20px;">
            <button class="btn btn-danger" style="flex: 1; padding: 12px; font-weight: 600;" onclick="prosesInvoice('${inv.id}')">💳 Proses Pembayaran</button>
            ${inv.status === 'Masuk' ? 
              `<button class="btn btn-teal btn-sm" style="padding: 0 15px;" onclick="ubahStatusInv('${inv.id}','Diproses')">🔄 Tandai Diproses</button>` : 
              ''
            }
          </div>` : 
          `<div class="alert-banner info" style="background: var(--green-light); color: #1F6B43; border: 1px solid var(--green); justify-content: center;">
            <span style="font-weight: 600;">✅ Invoice ini sudah lunas</span>
          </div>`
        }
      </div>
    </div>
  `;
}

// Fungsi untuk menampilkan badge pembayaran
function badgeBayar(metode) {
    const cls = metode === 'BPJS' ? 'b-bpjs' : 'b-mandiri';
    return `<span class="badge ${cls}">${metode}</span>`;
}

// Fungsi untuk memproses pembayaran (Tombol Merah)
function prosesInvoice(id) {
    const inv = invoiceData.find(i => i.id === id);
    if (inv) {
        inv.status = 'Lunas';
        alert('Pembayaran ' + id + ' berhasil diproses!');
        renderInvoiceList();
        renderInvoiceDetail();
    }
}

function ubahStatusInv(id, statusBaru) {
    const inv = invoiceData.find(i => i.id === id);
    if (inv) {
        inv.status = statusBaru;
        renderInvoiceList();
        renderInvoiceDetail();
    }
}

function fRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function bayar(){
  currentInvoice.status = 'Lunas';
  alert('Berhasil bayar');
  renderInvoiceList();
  renderInvoiceDetail();
}

</script>
<script>
document.addEventListener("DOMContentLoaded", function(){
  renderInvoiceList();
});
</script>
@endsection
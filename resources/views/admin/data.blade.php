@extends('layouts.admin')

@section('content')

{{-- ===================== DATA PASIEN ===================== --}}
<div class="page-section active" id="sec-pasien">
  <div class="page-header">
    <div>
      <div class="page-title">Data Pasien</div>
      <div class="page-sub">Input dan kelola data pasien yang mendaftar</div>
    </div>
    <a href="{{ route('pasien.create') }}" class="btn btn-primary">+ Daftar Pasien Baru</a>
  </div>

  <div class="search-row">
    <input type="text" class="search-input" placeholder="Cari nama, NIK, atau No. RM...">
    <select class="filter-sel">
      <option value="">Semua Jenis</option>
      <option value="BPJS">BPJS</option>
      <option value="Mandiri">Mandiri</option>
    </select>
    <select class="filter-sel">
      <option value="">Semua Status</option>
      <option value="Menunggu">Menunggu</option>
      <option value="Diperiksa">Diperiksa</option>
      <option value="Selesai">Selesai</option>
    </select>
    <select class="filter-sel">
      <option value="">Semua Poli</option>
      <option value="Umum">Umum</option>
      <option value="Anak">Anak</option>
      <option value="Penyakit Dalam">Penyakit Dalam</option>
      <option value="Bedah">Bedah</option>
      <option value="Gigi">Gigi</option>
      <option value="Kebidanan">Kebidanan</option>
      <option value="Mata">Mata</option>
      <option value="UGD">UGD</option>
    </select>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>NIK</th>
          <th>Tgl Lahir</th>
          <th>Jenis</th>
          <th>Poli Tujuan</th>
          <th>Status</th>
          <th>Validasi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pasiens as $p)
          <tr>
            <td>{{ $p->no_rm }}</td>
            <td>{{ $p->nama }}</td>
            <td>{{ $p->nik }}</td>
            <td>{{ \Carbon\Carbon::parse($p->tgl_lahir)->format('d/m/Y') }}</td>
            <td>
              <span class="badge {{ $p->jenis == 'BPJS' ? 'b-bpjs' : 'b-mandiri' }}">
                {{ $p->jenis }}
              </span>
            </td>
            <td>{{ $p->poli_tujuan }}</td>
            <td>
              <span class="badge {{ $p->status == 'Selesai' ? 'b-selesai' : ($p->status == 'Diperiksa' ? 'b-siap' : 'b-warn') }}">
                {{ $p->status }}
              </span>
            </td>
            <td>
              <span class="badge {{ $p->validasi == 'Valid' ? 'b-valid' : ($p->validasi == 'Tidak Valid' ? 'b-invalid' : 'b-pending') }}">
                {{ $p->validasi }}
              </span>
            </td>
            <td>
              <button class="btn btn-sm">Detail</button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" style="text-align:center; color:#A8998A; padding:2rem;">
              Belum ada data pasien.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ===================== VALIDASI PASIEN ===================== --}}
<div class="page-section" id="sec-validasi">
  <div class="page-header">
    <div>
      <div class="page-title">Validasi Status Pasien</div>
      <div class="page-sub">Konfirmasi status BPJS & Mandiri pasien</div>
    </div>
  </div>
  <div class="validasi-wrap">
    <div class="validasi-left">
      <div class="validasi-tabs">
        <button class="vtab active" onclick="filterValidasi('semua',this)">Semua</button>
        <button class="vtab" onclick="filterValidasi('pending',this)">Pending</button>
        <button class="vtab" onclick="filterValidasi('valid',this)">Valid</button>
        <button class="vtab" onclick="filterValidasi('invalid',this)">Tidak Valid</button>
      </div>
      <div id="validasiList" class="validasi-list-wrap"></div>
    </div>
    <div id="validasiDetail" class="validasi-right">
      <div class="vempty-state">
        <div class="vempty-icon">✅</div>
        <div class="vempty-label">Pilih pasien untuk validasi</div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<style>
/* ===== LAYOUT VALIDASI ===== */
.validasi-wrap {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  align-items: start;
}
@media(max-width:768px){ .validasi-wrap { grid-template-columns: 1fr; } }

/* ===== TABS ===== */
.validasi-tabs {
  display: flex;
  gap: 0;
  margin-bottom: 16px;
  border-bottom: 2px solid #f0ebe6;
}
.vtab {
  padding: 8px 20px;
  border: none;
  background: none;
  font-size: 13px;
  font-weight: 500;
  color: #A8998A;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  transition: all .15s;
}
.vtab.active {
  color: #c0825a;
  border-bottom-color: #c0825a;
  font-weight: 600;
}
.vtab:hover { color: #c0825a; }

/* ===== LIST PASIEN ===== */
.validasi-list-wrap {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #f0ebe6;
  overflow: hidden;
}
.validasi-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-bottom: 1px solid #f7f3f0;
  gap: 10px;
  cursor: pointer;
  transition: background .15s;
}
.validasi-row:last-child { border-bottom: none; }
.validasi-row:hover { background: #fdf9f7; }
.validasi-row.selected { background: #fdf3ec; }

.v-avatar {
  width: 38px; height: 38px;
  border-radius: 50%;
  background: #fde8d8;
  display: flex; align-items: center; justify-content: center;
  font-size: 15px; font-weight: 700;
  color: #c0825a;
  flex-shrink: 0;
}
.v-name { font-weight: 600; font-size: 14px; color: #2d2016; }
.v-sub  { font-size: 12px; color: #A8998A; margin-top: 2px; display: flex; align-items: center; gap: 6px; }

/* ===== BADGES ===== */
.badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.b-valid    { background: #e8f5e9; color: #2e7d32; }
.b-invalid  { background: #ffebee; color: #c62828; }
.b-pending  { background: #fff8e1; color: #e65100; }
.b-bpjs     { background: #e3f2fd; color: #1565c0; }
.b-mandiri  { background: #f3e5f5; color: #6a1b9a; }
.b-warn     { background: #fff8e1; color: #e65100; }
.b-siap     { background: #e3f2fd; color: #1565c0; }
.b-selesai  { background: #e8f5e9; color: #2e7d32; }

/* ===== ACTION BUTTONS ===== */
.btn-valid-ok {
  width: 32px; height: 32px; border-radius: 50%;
  background: #4caf50; color: #fff; border: none;
  font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: opacity .15s;
}
.btn-valid-ok:hover { opacity: .85; }
.btn-valid-no {
  width: 32px; height: 32px; border-radius: 50%;
  background: #e53935; color: #fff; border: none;
  font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: opacity .15s;
}
.btn-valid-no:hover { opacity: .85; }

/* ===== DETAIL PANEL ===== */
.validasi-right {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #f0ebe6;
  overflow: hidden;
  min-height: 300px;
}
.vempty-state {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  height: 300px; color: #C4B5A5;
}
.vempty-icon  { font-size: 40px; margin-bottom: 12px; }
.vempty-label { font-size: 14px; }

.vdetail-header {
  padding: 18px 20px;
  border-bottom: 1px solid #f0ebe6;
  background: #fdf9f7;
  display: flex; justify-content: space-between; align-items: flex-start;
}
.vdetail-title { font-size: 16px; font-weight: 700; color: #2d2016; }
.vdetail-sub   { font-size: 12px; color: #A8998A; margin-top: 3px; }
.vdetail-body  { padding: 18px 20px; }

.info-grid {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 14px; margin-bottom: 16px;
}
.info-item label {
  display: block; font-size: 10px; color: #C4B5A5;
  text-transform: uppercase; letter-spacing: .07em; margin-bottom: 3px;
}
.info-item span { font-size: 13px; font-weight: 600; color: #2d2016; }

.vcheck-box {
  background: #fdf9f7; border-radius: 10px;
  padding: 14px 16px; margin-bottom: 14px;
}
.vcheck-title {
  font-size: 10px; color: #C4B5A5;
  text-transform: uppercase; letter-spacing: .07em; margin-bottom: 10px;
}
.vcheck-row {
  display: flex; justify-content: space-between;
  font-size: 13px; padding: 4px 0;
}

.vaction-row { display: flex; gap: 10px; margin-top: 16px; }
.vbtn-ok {
  flex: 1; padding: 10px; border-radius: 8px;
  background: #4caf50; color: #fff; border: none;
  font-size: 13px; font-weight: 600; cursor: pointer;
}
.vbtn-no {
  flex: 1; padding: 10px; border-radius: 8px;
  background: #e53935; color: #fff; border: none;
  font-size: 13px; font-weight: 600; cursor: pointer;
}
.vbtn-amber {
  padding: 8px 14px; border-radius: 8px;
  background: #ff9800; color: #fff; border: none;
  font-size: 12px; font-weight: 600; cursor: pointer;
}
.vbtn-outline {
  padding: 8px 14px; border-radius: 8px;
  background: #fff; color: #c0825a;
  border: 1px solid #c0825a;
  font-size: 12px; font-weight: 600; cursor: pointer;
}
.valert-invalid {
  background: #ffebee; border: 1px solid #f7c1c1;
  border-radius: 8px; padding: 10px 12px;
  font-size: 12px; color: #c62828; margin-bottom: 14px;
}
.valert-mandiri {
  background: #ede7f6; border: 1px solid #c5bde8;
  border-radius: 8px; padding: 10px 12px;
  font-size: 12px; color: #4527a0; margin-bottom: 14px;
}

/* ===== TOAST ===== */
.vtoast {
  position: fixed; bottom: 24px; right: 24px;
  padding: 12px 20px; border-radius: 10px;
  font-size: 13px; font-weight: 500;
  color: #fff; z-index: 9999;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
  animation: fadeInUp .25s ease;
}
@keyframes fadeInUp {
  from { opacity:0; transform:translateY(10px); }
  to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
let validasiFilter = 'semua';
let currentValidasiId = null;
let pasienData = @json($pasienJson);

/* ===== BADGE HELPERS ===== */
function badgeValidasi(v){
  if(v==='valid')   return `<span class="badge b-valid">✓ Valid</span>`;
  if(v==='invalid') return `<span class="badge b-invalid">✕ Tidak Valid</span>`;
  return `<span class="badge b-pending">Pending</span>`;
}
function badgeBayar(j){
  return j==='BPJS'
    ? `<span class="badge b-bpjs">BPJS</span>`
    : `<span class="badge b-mandiri">Mandiri</span>`;
}
function fDate(d){ return new Date(d).toLocaleDateString('id-ID'); }
function hitungUsia(t){
  return Math.floor((Date.now()-new Date(t))/(1000*60*60*24*365))+' thn';
}

/* ===== TOAST ===== */
function showToast(msg, type){
  const c = {'success':'#4caf50','danger':'#e53935','info':'#1976d2'};
  const t = document.createElement('div');
  t.className = 'vtoast';
  t.style.background = c[type]||c.info;
  t.innerText = msg;
  document.body.appendChild(t);
  setTimeout(()=>t.remove(), 3000);
}

/* ===== FILTER ===== */
function filterValidasi(f, el){
  validasiFilter = f;
  document.querySelectorAll('.vtab').forEach(b=>b.classList.remove('active'));
  el.classList.add('active');
  renderValidasiList(f);
}

/* ===== RENDER LIST ===== */
function renderValidasiList(f){
  let data = [...pasienData];
  if(f==='pending') data = data.filter(p=>p.validasiBPJS==='pending');
  else if(f==='valid')   data = data.filter(p=>p.validasiBPJS==='valid');
  else if(f==='invalid') data = data.filter(p=>p.validasiBPJS==='invalid');

  const wrap = document.getElementById('validasiList');

  if(!data.length){
    wrap.innerHTML = `<div style="padding:30px;text-align:center;color:#C4B5A5;font-size:13px">Tidak ada data</div>`;
    return;
  }

  wrap.innerHTML = data.map(p=>`
    <div class="validasi-row ${currentValidasiId===p.id?'selected':''}" id="vrow-${p.id}">
      <div style="display:flex;align-items:center;gap:12px;flex:1" onclick="selectValidasi('${p.id}')">
        <div class="v-avatar">${p.nama.charAt(0)}</div>
        <div>
          <div class="v-name">${p.nama}</div>
          <div class="v-sub">${p.rm} · ${badgeBayar(p.jenisBayar)}</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        ${badgeValidasi(p.validasiBPJS)}
        ${p.validasiBPJS==='pending'?`
          <button class="btn-valid-ok" title="Valid" onclick="ubahValidasi('${p.id}','valid')">✓</button>
          <button class="btn-valid-no" title="Tidak Valid" onclick="ubahValidasi('${p.id}','invalid')">✕</button>
        `:''}
      </div>
    </div>`).join('');
}

/* ===== SELECT DETAIL ===== */
function selectValidasi(id){
  currentValidasiId = id;
  const p = pasienData.find(x=>x.id===id);
  if(!p) return;

  // highlight row
  document.querySelectorAll('.validasi-row').forEach(r=>r.classList.remove('selected'));
  const row = document.getElementById('vrow-'+id);
  if(row) row.classList.add('selected');

  const detail = document.getElementById('validasiDetail');
  detail.innerHTML = `
    <div class="vdetail-header">
      <div>
        <div class="vdetail-title">${p.nama}</div>
        <div class="vdetail-sub">${p.rm} · Verifikasi Status</div>
      </div>
      ${badgeValidasi(p.validasiBPJS)}
    </div>
    <div class="vdetail-body">
      <div class="info-grid">
        <div class="info-item"><label>NIK</label><span style="font-family:monospace">${p.nik}</span></div>
        <div class="info-item"><label>Tgl Lahir</label><span>${fDate(p.tglLahir)} (${hitungUsia(p.tglLahir)})</span></div>
        <div class="info-item"><label>Jenis Kelamin</label><span>${p.jk==='L'?'Laki-laki':p.jk==='P'?'Perempuan':'-'}</span></div>
        <div class="info-item"><label>Jenis Pasien</label><span>${badgeBayar(p.jenisBayar)}</span></div>
        ${p.jenisBayar==='BPJS'?`<div class="info-item" style="grid-column:1/-1"><label>Nomor BPJS</label><span style="font-family:monospace">${p.noBPJS||'-'}</span></div>`:''}
      </div>

      ${p.jenisBayar==='BPJS'?`
        <div class="vcheck-box">
          <div class="vcheck-title">Pengecekan BPJS</div>
          <div class="vcheck-row"><span>Nomor BPJS</span><span style="font-family:monospace;font-weight:600">${p.noBPJS||'-'}</span></div>
          <div class="vcheck-row">
            <span>Kesesuaian NIK</span>
            <span style="color:${p.validasiBPJS!=='invalid'?'#2e7d32':'#c62828'};font-weight:600">
              ${p.validasiBPJS!=='invalid'?'✓ Sesuai':'✕ Tidak Sesuai'}
            </span>
          </div>
          <div class="vcheck-row">
            <span>Status Kepesertaan</span>
            <span style="color:${p.validasiBPJS==='valid'?'#2e7d32':p.validasiBPJS==='invalid'?'#c62828':'#e65100'};font-weight:600">
              ${p.validasiBPJS==='valid'?'Aktif':p.validasiBPJS==='invalid'?'Tidak Aktif':'Perlu Dicek'}
            </span>
          </div>
        </div>
        ${p.validasiBPJS==='invalid'?`<div class="valert-invalid">⚠️ Status BPJS tidak valid. Pasien perlu konfirmasi ke loket BPJS atau beralih ke pembayaran mandiri.</div>`:''}
      `:`<div class="valert-mandiri">💳 Pasien Mandiri: tidak memerlukan validasi BPJS. Status otomatis valid.</div>`}

      ${p.validasiBPJS==='pending'?`
        <div style="font-size:12px;color:#A8998A;margin-bottom:12px">Konfirmasi status kepesertaan pasien:</div>
        <div class="vaction-row">
          <button class="vbtn-ok" onclick="ubahValidasi('${p.id}','valid')">✓ Konfirmasi Valid</button>
          <button class="vbtn-no" onclick="ubahValidasi('${p.id}','invalid')">✕ Tidak Valid</button>
        </div>`:''}

      ${p.validasiBPJS==='invalid'?`
        <div class="vaction-row">
          <button class="vbtn-amber" onclick="ubahValidasi('${p.id}','valid')">🔄 Ubah ke Valid</button>
          <button class="vbtn-outline" onclick="ubahJenisBayar('${p.id}')">Beralih ke Mandiri</button>
        </div>`:''}
    </div>`;
}

/* ===== UBAH VALIDASI (ke server) ===== */
function ubahValidasi(id, status){
  const p = pasienData.find(x=>x.id===id);
  if(!p) return;
  p.validasiBPJS = status;

  fetch(`/admin/pasien/${id}/validasi`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({ validasi: status })
  })
  .then(r=>r.json())
  .then(()=>{
    renderValidasiList(validasiFilter);
    if(currentValidasiId===id) selectValidasi(id);
    showToast(`${p.nama}: ${status==='valid'?'✅ Valid':'❌ Tidak Valid'}`, status==='valid'?'success':'danger');
  })
  .catch(()=>showToast('Gagal menyimpan validasi','danger'));
}

/* ===== UBAH JENIS BAYAR ===== */
function ubahJenisBayar(id){
  const p = pasienData.find(x=>x.id===id);
  if(!p) return;
  p.jenisBayar = 'Mandiri';
  p.noBPJS = '';
  p.validasiBPJS = 'valid';

  fetch(`/admin/pasien/${id}/validasi`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({ validasi: 'valid' })
  })
  .then(r=>r.json())
  .then(()=>{
    renderValidasiList(validasiFilter);
    if(currentValidasiId===id) selectValidasi(id);
    showToast(`${p.nama} beralih ke Mandiri`, 'info');
  })
  .catch(()=>showToast('Gagal menyimpan','danger'));
}

/* ===== SHOW SECTION (dipanggil dari sidebar) ===== */
function showSection(id){
  document.querySelectorAll('.page-section').forEach(s=>s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
    renderValidasiList('semua');

    // Hash navigation
    const hash = window.location.hash.replace('#', '');
    if (hash) showSection(hash);

    // ===== FILTER & SEARCH =====
    const searchInput = document.querySelector('.search-input');
    const filterSels  = document.querySelectorAll('.filter-sel');
    const rows        = document.querySelectorAll('tbody tr');

    function applyFilter() {
        const keyword    = searchInput.value.toLowerCase();
        const jenis      = filterSels[0].value.toLowerCase();
        const status     = filterSels[1].value.toLowerCase();
        const poli       = filterSels[2].value.toLowerCase();

        rows.forEach(row => {
            // Ambil teks per kolom (sesuai urutan kolom tabel)
            const cells   = row.querySelectorAll('td');
            if (!cells.length) return; // skip baris kosong

            const nama    = (cells[1]?.innerText || '').toLowerCase();
            const nik     = (cells[2]?.innerText || '').toLowerCase();
            const noRm    = (cells[0]?.innerText || '').toLowerCase();
            const jenisEl = (cells[4]?.innerText || '').toLowerCase();
            const poliEl  = (cells[5]?.innerText || '').toLowerCase();
            const statEl  = (cells[6]?.innerText || '').toLowerCase();

            const matchSearch = !keyword || nama.includes(keyword) || nik.includes(keyword) || noRm.includes(keyword);
            const matchJenis  = !jenis  || jenisEl.includes(jenis);
            const matchStatus = !status || statEl.includes(status);
            const matchPoli   = !poli   || poliEl.includes(poli);

            row.style.display = (matchSearch && matchJenis && matchStatus && matchPoli) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilter);
    filterSels.forEach(sel => sel.addEventListener('change', applyFilter));
});
</script>
@endpush
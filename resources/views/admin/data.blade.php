@extends('layouts.admin')

@section('content')

{{-- ===================== DATA PASIEN ===================== --}}
<div class="page-section active" id="sec-pasien">
  <div class="page-header">
    <div>
      <div class="page-title">Data Pasien</div>
      <div class="page-sub">Input dan kelola data pasien yang mendaftar</div>
    </div>
    <a href="{{ route('pasien.create') }}" class="btn btn-primary" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 8px; height: 42px; text-decoration: none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display: inline-block !important; flex-shrink: 0; margin: 0;">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      <span style="white-space: nowrap !important; display: inline-block !important; line-height: 1;">Daftar Pasien Baru</span>
    </a>
  </div>

  {{-- SUMMARY STATS --}}
  <div class="pasien-stats-row">
    <div class="pstat-card">
      <div class="pstat-icon" style="background:#fde8d8;color:#c0825a">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div>
        <div class="pstat-num">{{ $pasiens->count() }}</div>
        <div class="pstat-label">Total Pasien</div>
      </div>
    </div>
    <div class="pstat-card">
      <div class="pstat-icon" style="background:#fff8e1;color:#e65100">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div>
        <div class="pstat-num" style="color:#e65100">{{ $pasiens->where('status','Menunggu')->count() }}</div>
        <div class="pstat-label">Menunggu</div>
      </div>
    </div>
    <div class="pstat-card">
      <div class="pstat-icon" style="background:#e3f2fd;color:#1565c0">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div>
        <div class="pstat-num" style="color:#1565c0">{{ $pasiens->where('status','Diperiksa')->count() }}</div>
        <div class="pstat-label">Diperiksa</div>
      </div>
    </div>
    <div class="pstat-card">
      <div class="pstat-icon" style="background:#e8f5e9;color:#2e7d32">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      </div>
      <div>
        <div class="pstat-num" style="color:#2e7d32">{{ $pasiens->where('status','Selesai')->count() }}</div>
        <div class="pstat-label">Selesai</div>
      </div>
    </div>
  </div>

  {{-- SEARCH & FILTER --}}
  <div class="filter-card">
    <div class="filter-search-wrap">
      <svg class="filter-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" class="search-input" placeholder="Cari nama, NIK, atau No. RM...">
    </div>
    <div class="filter-selects">
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
  </div>

  @if(session('success'))
    <div class="alert alert-success">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      {{ session('success') }}
    </div>
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
          <th style="text-align:center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pasiens as $p)
          <tr class="tbl-row">
            <td><span class="rm-code">{{ $p->no_rm }}</span></td>
            <td>
              <div class="pasien-nama-cell">
                <div class="pasien-avatar">{{ mb_strtoupper(mb_substr($p->nama, 0, 1)) }}</div>
                <span class="pasien-nama-text">{{ $p->nama }}</span>
              </div>
            </td>
            <td><span style="font-family:monospace;font-size:12px;color:#6b5e52">{{ $p->nik }}</span></td>
            <td style="font-size:13px;color:#5c4f45">{{ \Carbon\Carbon::parse($p->tgl_lahir)->format('d/m/Y') }}</td>
            <td>
              <span class="badge {{ $p->jenis == 'BPJS' ? 'b-bpjs' : 'b-mandiri' }}">
                {{ $p->jenis }}
              </span>
            </td>
            <td>
              <span class="poli-chip">{{ $p->poli_tujuan }}</span>
            </td>
            <td>
              <span class="badge status-badge {{ $p->status == 'Selesai' ? 'b-selesai' : ($p->status == 'Diperiksa' ? 'b-siap' : 'b-warn') }}">
                @if($p->status == 'Menunggu') ⏳
                @elseif($p->status == 'Diperiksa') 🔵
                @else ✅
                @endif
                {{ $p->status }}
              </span>
            </td>
            <td>
              <span class="badge {{ $p->validasi == 'Valid' ? 'b-valid' : ($p->validasi == 'Tidak Valid' ? 'b-invalid' : 'b-pending') }}">
                {{ $p->validasi }}
              </span>
            </td>
            <td>
              <div class="aksi-group">
                <a href="{{ route('pasien.edit', $p->id) }}" class="btn-aksi btn-edit" title="Edit">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Edit
                </a>
                <form action="{{ route('pasien.destroy', $p->id) }}" method="POST"
                      onsubmit="return confirm('Yakin hapus pasien ini?')" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-aksi btn-hapus" title="Hapus">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    Hapus
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9">
              <div class="empty-state">
                <div class="empty-icon">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C4B5A5" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="empty-text">Belum ada data pasien</div>
                <div class="empty-sub">Mulai dengan mendaftarkan pasien baru</div>
              </div>
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
/* ===== SUMMARY STATS ===== */
.pasien-stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 20px;
}
@media(max-width:768px){ .pasien-stats-row { grid-template-columns: repeat(2, 1fr); } }
.pstat-card {
  background: #fff;
  border: 1px solid #f0ebe6;
  border-radius: 12px;
  padding: 16px 18px;
  display: flex;
  align-items: center;
  gap: 14px;
  transition: box-shadow .15s;
}
.pstat-card:hover { box-shadow: 0 2px 12px rgba(192,130,90,.1); }
.pstat-icon {
  width: 42px; height: 42px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.pstat-num  { font-size: 22px; font-weight: 700; color: #c0825a; line-height: 1; }
.pstat-label{ font-size: 12px; color: #A8998A; margin-top: 3px; }

/* ===== FILTER CARD ===== */
.filter-card {
  background: #fff;
  border: 1px solid #f0ebe6;
  border-radius: 12px;
  padding: 14px 18px;
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 18px;
}
.filter-search-wrap {
  position: relative;
  flex: 1;
  min-width: 200px;
}
.filter-search-icon {
  position: absolute;
  left: 12px; top: 50%;
  transform: translateY(-50%);
  color: #A8998A;
  pointer-events: none;
}
.search-input {
  width: 100%;
  padding: 9px 12px 9px 36px;
  border: 1px solid #ece6e0;
  border-radius: 8px;
  font-size: 13px;
  color: #2d2016;
  background: #fdfaf8;
  transition: border-color .15s;
  box-sizing: border-box;
}
.search-input:focus { outline: none; border-color: #c0825a; background: #fff; }
.filter-selects { display: flex; gap: 10px; flex-wrap: wrap; }
.filter-sel {
  padding: 9px 12px;
  border: 1px solid #ece6e0;
  border-radius: 8px;
  font-size: 13px;
  color: #2d2016;
  background: #fdfaf8;
  cursor: pointer;
  transition: border-color .15s;
}
.filter-sel:focus { outline: none; border-color: #c0825a; }

/* ===== TABLE ENHANCEMENTS ===== */
.tbl-row { transition: background .12s; }
.tbl-row:hover { background: #fdf9f7; }
.rm-code {
  font-family: monospace;
  font-size: 12px;
  background: #f5f0ec;
  color: #7a5c46;
  padding: 3px 8px;
  border-radius: 5px;
  font-weight: 600;
}
.pasien-nama-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}
.pasien-avatar {
  width: 32px; height: 32px;
  border-radius: 50%;
  background: #fde8d8;
  color: #c0825a;
  font-size: 13px;
  font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.pasien-nama-text { font-size: 13px; font-weight: 600; color: #2d2016; }
.poli-chip {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  background: #f5f0ec;
  font-size: 12px;
  color: #6b5248;
  font-weight: 500;
}
.status-badge { gap: 4px; }

/* ===== TOMBOL AKSI ===== */
.aksi-group { display: flex; gap: 6px; align-items: center; justify-content: center; }
.btn-aksi {
  height: 30px;
  padding: 0 10px;
  border-radius: 7px;
  border: none; cursor: pointer;
  display: inline-flex; align-items: center; justify-content: center; gap: 5px;
  font-size: 12px; font-weight: 500; text-decoration: none;
  transition: all .15s;
}
.btn-aksi:hover { opacity: .82; transform: translateY(-1px); }
.btn-edit  { background: #fff3e0; color: #c0825a; }
.btn-hapus { background: #ffebee; color: #c62828; }

/* ===== EMPTY STATE ===== */
.empty-state {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 40px 20px; text-align: center;
}
.empty-icon { margin-bottom: 12px; opacity: .6; }
.empty-text { font-size: 15px; font-weight: 600; color: #A8998A; }
.empty-sub  { font-size: 12px; color: #C4B5A5; margin-top: 4px; }

/* ===== ALERT ===== */
.alert-success {
  display: flex; align-items: center;
  padding: 12px 16px; border-radius: 10px;
  background: #e8f5e9; border: 1px solid #c8e6c9;
  color: #2e7d32; font-size: 13px; font-weight: 500;
  margin-bottom: 16px;
}

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

// Memastikan data pasien ter-render dengan aman sebagai array object JavaScript
let pasienData = @json($pasienJson ?? []);

/* ===== BADGE HELPERS ===== */
function badgeValidasi(v){
  if(!v) return `<span class="badge b-pending">Pending</span>`;
  let normStatus = v.toLowerCase();
  if(normStatus === 'valid')     return `<span class="badge b-valid">✓ Valid</span>`;
  if(normStatus === 'invalid' || normStatus === 'tidak valid')   return `<span class="badge b-invalid">✕ Tidak Valid</span>`;
  return `<span class="badge b-pending">Pending</span>`;
}

function badgeBayar(j){
  return j === 'BPJS'
    ? `<span class="badge b-bpjs">BPJS</span>`
    : `<span class="badge b-mandiri">Mandiri</span>`;
}

function fDate(d){ 
  if(!d) return '-';
  return new Date(d).toLocaleDateString('id-ID'); 
}

function hitungUsia(t){
  if(!t) return '-';
  return Math.floor((Date.now() - new Date(t)) / (1000 * 60 * 60 * 24 * 365)) + ' thn';
}

/* ===== TOAST ===== */
function showToast(msg, type){
  const c = {'success':'#4caf50','danger':'#e53935','info':'#1976d2'};
  const t = document.createElement('div');
  t.className = 'vtoast';
  t.style.background = c[type] || c.info;
  t.innerText = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3000);
}

/* ===== FILTER ===== */
function filterValidasi(f, el){
  validasiFilter = f;
  document.querySelectorAll('.vtab').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
  renderValidasiList(f);
}

/* ===== RENDER LIST ===== */
function renderValidasiList(f){
  let data = [...pasienData];
  
  if(f !== 'semua') {
    data = data.filter(p => {
      let status = (p.validasiBPJS || 'pending').toLowerCase();
      if(f === 'invalid') return status === 'invalid' || status === 'tidak valid';
      return status === f;
    });
  }

  const wrap = document.getElementById('validasiList');
  if(!data.length){
    wrap.innerHTML = `<div style="padding:30px;text-align:center;color:#C4B5A5;font-size:13px">Tidak ada data pasien</div>`;
    return;
  }

  wrap.innerHTML = data.map(p => {
    let currentStatus = (p.validasiBPJS || 'pending').toLowerCase();
    return `
    <div class="validasi-row ${Number(currentValidasiId) === Number(p.id) ? 'selected' : ''}" id="vrow-${p.id}">
      <div style="display:flex;align-items:center;gap:12px;flex:1" onclick="selectValidasi(${p.id})">
        <div class="v-avatar">${p.nama ? p.nama.charAt(0).toUpperCase() : '?'}</div>
        <div>
          <div class="v-name">${p.nama}</div>
          <div class="v-sub">${p.rm || '-'} · ${badgeBayar(p.jenisBayar)}</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        ${badgeValidasi(p.validasiBPJS)}
        ${currentStatus === 'pending' ? `
          <button class="btn-valid-ok" title="Valid" onclick="ubahValidasi(${p.id}, 'valid')">✓</button>
          <button class="btn-valid-no" title="Tidak Valid" onclick="ubahValidasi(${p.id}, 'invalid')">✕</button>
        ` : ''}
      </div>
    </div>`;
  }).join('');
}

/* ===== SELECT DETAIL ===== */
function selectValidasi(id){
  currentValidasiId = id;
  // Menggunakan '==' (bukan ===) agar toleran terhadap perbedaan tipe data string/number
  const p = pasienData.find(x => x.id == id); 
  if(!p) return;

  document.querySelectorAll('.validasi-row').forEach(r => r.classList.remove('selected'));
  const row = document.getElementById('vrow-' + id);
  if(row) row.classList.add('selected');

  let currentStatus = (p.validasiBPJS || 'pending').toLowerCase();
  const detail = document.getElementById('validasiDetail');
  
  detail.innerHTML = `
    <div class="vdetail-header">
      <div>
        <div class="vdetail-title">${p.nama}</div>
        <div class="vdetail-sub">${p.rm || '-'} · Verifikasi Status</div>
      </div>
      ${badgeValidasi(p.validasiBPJS)}
    </div>
    <div class="vdetail-body">
      <div class="info-grid">
        <div class="info-item"><label>NIK</label><span style="font-family:monospace">${p.nik || '-'}</span></div>
        <div class="info-item"><label>Tgl Lahir</label><span>${fDate(p.tglLahir)} (${hitungUsia(p.tglLahir)})</span></div>
        <div class="info-item"><label>Jenis Kelamin</label><span>${p.jk === 'L' ? 'Laki-laki' : p.jk === 'P' ? 'Perempuan' : '-'}</span></div>
        <div class="info-item"><label>Jenis Pasien</label><span>${badgeBayar(p.jenisBayar)}</span></div>
        ${p.jenisBayar === 'BPJS' ? `<div class="info-item" style="grid-column:1/-1"><label>Nomor BPJS</label><span style="font-family:monospace">${p.noBPJS || '-'}</span></div>` : ''}
      </div>

      ${p.jenisBayar === 'BPJS' ? `
        <div class="vcheck-box">
          <div class="vcheck-title">Pengecekan BPJS</div>
          <div class="vcheck-row"><span>Nomor BPJS</span><span style="font-family:monospace;font-weight:600">${p.noBPJS || '-'}</span></div>
          <div class="vcheck-row">
            <span>Kesesuaian NIK</span>
            <span style="color:${currentStatus !== 'invalid' ? '#2e7d32' : '#c62828'};font-weight:600">
              ${currentStatus !== 'invalid' ? '✓ Sesuai' : '✕ Tidak Sesuai'}
            </span>
          </div>
          <div class="vcheck-row">
            <span>Status Kepesertaan</span>
            <span style="color:${currentStatus === 'valid' ? '#2e7d32' : (currentStatus === 'invalid' ? '#c62828' : '#e65100')};font-weight:600">
              ${currentStatus === 'valid' ? 'Aktif' : (currentStatus === 'invalid' ? 'Tidak Aktif' : 'Perlu Dicek')}
            </span>
          </div>
        </div>
        ${currentStatus === 'invalid' ? `<div class="valert-invalid">⚠️ Status BPJS tidak valid. Pasien perlu konfirmasi ke loket BPJS atau beralih ke pembayaran mandiri.</div>` : ''}
      ` : `<div class="valert-mandiri">💳 Pasien Mandiri: tidak memerlukan validasi BPJS. Status otomatis valid.</div>`}

      ${currentStatus === 'pending' ? `
        <div style="font-size:12px;color:#A8998A;margin-bottom:12px">Konfirmasi status kepesertaan pasien:</div>
        <div class="vaction-row">
          <button class="vbtn-ok" onclick="ubahValidasi(${p.id}, 'valid')">✓ Konfirmasi Valid</button>
          <button class="vbtn-no" onclick="ubahValidasi(${p.id}, 'invalid')">✕ Tidak Valid</button>
        </div>` : ''}

      ${currentStatus === 'invalid' ? `
        <div class="vaction-row">
          <button class="vbtn-amber" onclick="ubahValidasi(${p.id}, 'valid')">🔄 Ubah ke Valid</button>
          <button class="vbtn-outline" onclick="ubahJenisBayar(${p.id})">Beralih ke Mandiri</button>
        </div>` : ''}
    </div>`;
}

/* ===== UBAH VALIDASI (ke server) ===== */
function ubahValidasi(id, status){
  const p = pasienData.find(x => x.id == id);
  if(!p) return;

  // Menggunakan fallback jika tag meta CSRF di layout utama Anda tidak ditemukan
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

  fetch(`/admin/pasien/${id}/validasi`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ validasi: status })
  })
  .then(res => {
    if(!res.ok) throw new Error("HTTP error " + res.status);
    return res.json();
  })
  .then(() => {
    p.validasiBPJS = status;
    renderValidasiList(validasiFilter);
    if(Number(currentValidasiId) === Number(id)) selectValidasi(id);
    showToast(`${p.nama}: ${status === 'valid' ? '✅ Valid' : '❌ Tidak Valid'}`, status === 'valid' ? 'success' : 'danger');
  })
  .catch((err) => {
    console.error(err);
    showToast('Gagal menyimpan validasi. Periksa Route/Controller Anda.', 'danger');
  });
}

/* ===== UBAH JENIS BAYAR ===== */
function ubahJenisBayar(id){
  const p = pasienData.find(x => x.id == id);
  if(!p) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

  fetch(`/admin/pasien/${id}/validasi`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ validasi: 'valid', jenisBayar: 'Mandiri' })
  })
  .then(res => {
    if(!res.ok) throw new Error("HTTP error " + res.status);
    return res.json();
  })
  .then(() => {
    p.jenisBayar = 'Mandiri';
    p.noBPJS = '';
    p.validasiBPJS = 'valid';
    renderValidasiList(validasiFilter);
    if(Number(currentValidasiId) === Number(id)) selectValidasi(id);
    showToast(`${p.nama} beralih ke Mandiri`, 'info');
  })
  .catch((err) => {
    console.error(err);
    showToast('Gagal memproses perubahan pembayaran', 'danger');
  });
}

/* ===== SHOW SECTION ===== */
function showSection(id){
  document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
  const target = document.getElementById(id);
  if(target) target.classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
    renderValidasiList('semua');

    const hash = window.location.hash.replace('#', '');
    if (hash) showSection(hash);

    const searchInput = document.querySelector('.search-input');
    const filterSels  = document.querySelectorAll('.filter-sel');
    const rows        = document.querySelectorAll('tbody tr');

    function applyFilter() {
        if(!searchInput) return;
        const keyword    = searchInput.value.toLowerCase();
        const jenis      = filterSels[0]?.value.toLowerCase() || '';
        const status     = filterSels[1]?.value.toLowerCase() || '';
        const poli       = filterSels[2]?.value.toLowerCase() || '';

        rows.forEach(row => {
            const cells   = row.querySelectorAll('td');
            if (!cells.length) return;

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

    if(searchInput) {
      searchInput.addEventListener('input', applyFilter);
    }
    filterSels.forEach(sel => sel.addEventListener('change', applyFilter));
});
</script>
@endpush
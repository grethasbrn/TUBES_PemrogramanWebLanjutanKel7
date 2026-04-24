@extends('layouts.apoteker')

@section('content')

<div class="page-section active" id="sec-resep">
  <div class="page-header">
    <div>
      <div class="page-title">Validasi Resep</div>
      <div class="page-sub">Verifikasi dan proses resep dokter</div>
    </div>
  </div>

  <div class="rx-layout">
    <div>
      <div class="tabs">
        <button class="tab-btn active" onclick="filterResep('semua',this)">Semua</button>
        <button class="tab-btn" onclick="filterResep('baru',this)">Baru</button>
        <button class="tab-btn" onclick="filterResep('validasi',this)">Divalidasi</button>
        <button class="tab-btn" onclick="filterResep('siap',this)">Siap Ambil</button>
        <button class="tab-btn" onclick="filterResep('selesai',this)">Selesai</button>
      </div>
      <div id="resepList"></div>
    </div>
    <div id="resepDetail">
      <div class="card" style="text-align:center;padding:40px 20px;color:var(--text3)">
        <div style="font-size:32px;margin-bottom:10px">📋</div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:16px">Pilih resep untuk detail</div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Substitusi -->
<div class="modal-overlay" id="modalSubstitusi">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalSubstitusi')">✕</button>
    <h3>Substitusi Obat</h3>
    <div id="subsInfo" style="margin-bottom:14px;font-size:13px;color:var(--text2)"></div>
    <div class="fg"><label>Obat Pengganti</label>
      <select id="subsObat"></select>
    </div>
    <div class="fg"><label>Alasan Substitusi</label>
      <select id="subsAlasan">
        <option>Stok habis</option>
        <option>Obat tidak tersedia di formularium</option>
        <option>Harga terlalu tinggi (BPJS)</option>
        <option>Obat mendekati expired</option>
        <option>Permintaan pasien</option>
      </select>
    </div>
    <div class="fg"><label>Catatan Tambahan</label>
      <textarea id="subsCatatan" rows="2" placeholder="Keterangan penggantian obat..."></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeModal('modalSubstitusi')">Batal</button>
      <button class="btn btn-amber" onclick="simpanSubstitusi()">🔄 Konfirmasi Substitusi</button>
    </div>
  </div>
</div>

@endsection
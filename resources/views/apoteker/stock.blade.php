@extends('layouts.apoteker')

@section('content')

<div class="page-section active" id="sec-stock">
  <div class="page-header">
    <div>
      <div class="page-title">Stok Obat</div>
      <div class="page-sub">Manajemen inventaris per batch</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalTambahBatch')">+ Input Batch Baru</button>
  </div>

  <div class="search-row">
    <input type="text" class="search-input" placeholder="Cari nama obat, batch..." id="stockSearch" oninput="renderStockTable()">
    <select class="filter-sel" id="stockFilterTipe" onchange="renderStockTable()">
      <option value="">Semua Tipe</option>
      <option value="Tablet">Tablet</option>
      <option value="Sirup">Sirup</option>
      <option value="Kapsul">Kapsul</option>
      <option value="Injeksi">Injeksi</option>
      <option value="Salep">Salep</option>
    </select>
    <select class="filter-sel" id="stockFilterStatus" onchange="renderStockTable()">
      <option value="">Semua Status</option>
      <option value="aman">Aman</option>
      <option value="kritis">Kritis (&lt;10)</option>
      <option value="expired">Expired</option>
      <option value="exp-soon">Exp &lt; 90 hari</option>
    </select>
  </div>

  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Nama Obat</th>
          <th>Tipe</th>
          <th>No. Batch</th>
          <th>Jumlah</th>
          <th>Harga Satuan</th>
          <th>Tgl Expired</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="stockTableBody"></tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Batch -->
<div class="modal-overlay" id="modalTambahBatch">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalTambahBatch')">✕</button>
    <h3>Input Batch Obat Baru</h3>
    <div class="fg">
      <label>Nama Obat</label>
      <input type="text" id="bNama" placeholder="Contoh: Paracetamol 500mg">
    </div>
    <div class="fr">
      <div class="fg">
        <label>Tipe Obat</label>
        <select id="bTipe">
          <option>Tablet</option><option>Sirup</option><option>Kapsul</option><option>Injeksi</option><option>Salep</option>
        </select>
      </div>
      <div class="fg">
        <label>No. Batch</label>
        <input type="text" id="bBatch" placeholder="Contoh: BT-2026-001">
      </div>
    </div>
    <div class="fr">
      <div class="fg">
        <label>Jumlah (unit)</label>
        <input type="number" id="bJumlah" placeholder="Contoh: 100">
      </div>
      <div class="fg">
        <label>Harga Satuan (Rp)</label>
        <input type="number" id="bHarga" placeholder="Contoh: 2500">
      </div>
    </div>
    <div class="fr">
      <div class="fg">
        <label>Tanggal Kadaluarsa</label>
        <input type="date" id="bExp">
      </div>
      <div class="fg">
        <label>Tanggal Masuk</label>
        <input type="date" id="bMasuk">
      </div>
    </div>
    <div class="fg">
      <label>Supplier / Keterangan</label>
      <input type="text" id="bSupplier" placeholder="Nama supplier atau keterangan">
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeModal('modalTambahBatch')">Batal</button>
      <button class="btn btn-primary" onclick="tambahBatch()">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>
         Simpan Batch</button>
    </div>
  </div>
</div>

<!-- Modal Edit Batch -->
<div class="modal-overlay" id="modalEditBatch">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalEditBatch')">✕</button>
    <h3>Edit Data Batch</h3>
    <input type="hidden" id="editIdx">
    <div class="fg"><label>Nama Obat</label><input type="text" id="editNama"></div>
    <div class="fr">
      <div class="fg"><label>Tipe</label>
        <select id="editTipe"><option>Tablet</option><option>Sirup</option><option>Kapsul</option><option>Injeksi</option><option>Salep</option></select>
      </div>
      <div class="fg"><label>No. Batch</label><input type="text" id="editBatch"></div>
    </div>
    <div class="fr">
      <div class="fg"><label>Jumlah</label><input type="number" id="editJumlah"></div>
      <div class="fg"><label>Harga Satuan (Rp)</label><input type="number" id="editHarga"></div>
    </div>
    <div class="fg"><label>Tanggal Expired</label><input type="date" id="editExp"></div>
    <div class="modal-footer">
      <button class="btn" onclick="closeModal('modalEditBatch')">Batal</button>
      <button class="btn btn-primary" onclick="simpanEdit()">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z"/></svg>
         Simpan</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  renderStockTable();
});

<script>
function renderStockTable() {
  fetch('/batch')
    .then(res => res.json())
    .then(data => {
      let html = '';

      data.forEach(item => {
        html += `
          <tr>
            <td>${item.nama_obat}</td>
            <td>${item.tipe}</td>
            <td>${item.no_batch}</td>
            <td>${item.jumlah}</td>
            <td>Rp ${item.harga}</td>
            <td>${item.tgl_expired}</td>
            <td>-</td>
            <td>-</td>
          </tr>
        `;
      });

      document.getElementById('stockTableBody').innerHTML = html;
    });
}
</script>
</script>
@endsection
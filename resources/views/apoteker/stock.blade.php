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


@include('apoteker.Batch.index')

<script>
    // Fungsi untuk membuka modal apapun berdasarkan ID
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('open');
        }
    }

    // Fungsi untuk menutup modal
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('open');
        }
    }

    // LOGIC AUTO-OPEN (Jika klik dari Dashboard)
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('add') === 'true') {
            openModal('modalTambahBatch');
            
            // Bersihkan URL agar rapi kembali
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
@endsection
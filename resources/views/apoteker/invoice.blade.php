@extends('layouts.apoteker')

@section('content')

<div class="page-section active" id="sec-invoice">
  <div class="page-header">
    <div>
      <div class="page-title">Invoice</div>
      <div class="page-sub">Daftar tagihan yang dikirim ke admin</div>
    </div>
  </div>

  <div class="grid2" style="align-items:start">
    <div>
      <div class="search-row">
        <input type="text" class="search-input" placeholder="Cari no. invoice atau pasien..." id="invSearch" oninput="renderInvoiceTable()">
        <select class="filter-sel" id="invFilterStatus" onchange="renderInvoiceTable()">
          <option value="">Semua Status</option>
          <option value="Terkirim">Terkirim</option>
          <option value="Lunas">Lunas</option>
          <option value="Draft">Draft</option>
        </select>
      </div>
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>No. Invoice</th>
              <th>Pasien</th>
              <th>Resep</th>
              <th>Total</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="invoiceTableBody"></tbody>
        </table>
      </div>
    </div>
    <div id="invoicePreview">
      <div class="card" style="text-align:center;padding:40px 20px;color:var(--text3)">
        <div style="font-size:32px;margin-bottom:10px">🧾</div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:16px">Pilih invoice untuk preview</div>
      </div>
    </div>
  </div>
</div>

@endsection
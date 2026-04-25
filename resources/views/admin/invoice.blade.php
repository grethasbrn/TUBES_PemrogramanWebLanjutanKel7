@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-laporan">
  <div class="page-header">
    <div>
      <div class="page-title">Laporan Bulanan</div>
      <div class="page-sub">Ringkasan aktivitas, pasien, dan keuangan</div>
    </div>
    <div class="no-print" style="display:flex;gap:8px">
      <select class="filter-sel" id="laporanBulan">
        <option value="4">April 2026</option><option value="3">Maret 2026</option>
        <option value="2">Februari 2026</option><option value="1">Januari 2026</option>
      </select>
      <button class="btn btn-teal" onclick="window.print()">🖨 Cetak</button>
      <button class="btn btn-primary" onclick="exportLaporan()">⬇ Export</button>
    </div>
  </div>

  <!-- Stats row -->
  <div class="grid3" id="laporanStats"></div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Tren Kunjungan Pasien</div>
      <canvas id="chartKunjungan" height="200"></canvas>
    </div>
    <div class="card">
      <div class="card-title">Distribusi Jenis Pasien</div>
      <canvas id="chartJenisPasien" height="200"></canvas>
    </div>
  </div>

  <div class="grid22">
    <div class="card">
      <div class="card-title">Pasien per Poli</div>
      <div id="laporanPerPoli"></div>
    </div>
    <div class="card">
      <div class="card-title">Ringkasan Keuangan</div>
      <div id="laporanKeuangan"></div>
    </div>
  </div>

  <!-- Detail Table -->
  <div class="card">
    <div class="card-title">Detail Transaksi Bulan Ini</div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>No. Transaksi</th><th>Pasien</th><th>Poli</th><th>Jenis</th><th>Total</th><th>Metode</th><th>Status</th><th>Tanggal</th></tr></thead>
        <tbody id="tblLaporanDetail"></tbody>
      </table>
    </div>
  </div>
</div>

@endsection
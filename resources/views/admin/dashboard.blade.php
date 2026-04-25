@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-dashboard">
  <div class="page-header">
    <div>
      <div class="page-title">Dashboard</div>
      <div class="page-sub">Ringkasan operasional rumah sakit hari ini</div>
    </div>
    <button class="btn btn-primary" onclick="showSection('pasien');setTimeout(()=>openModal('modalTambahPasien'),100)">+ Daftar Pasien Baru</button>
  </div>

  <div class="metrics">
    <div class="metric">
      <div class="metric-label">Total Pasien Hari Ini</div>
      <div class="metric-val" id="m-total-pasien">0</div>
      <div class="metric-sub up" id="m-total-sub">Loading...</div>
    </div>
    <div class="metric">
      <div class="metric-label">Menunggu Validasi</div>
      <div class="metric-val warn" id="m-validasi">0</div>
      <div class="metric-sub warn">Perlu dikonfirmasi</div>
    </div>
    <div class="metric">
      <div class="metric-label">Invoice Masuk</div>
      <div class="metric-val" id="m-invoice" style="color:var(--blue)">0</div>
      <div class="metric-sub info">Dari apoteker</div>
    </div>
    <div class="metric">
      <div class="metric-label">Pemasukan Hari Ini</div>
      <div class="metric-val" id="m-pemasukan" style="font-size:20px">Rp 0</div>
      <div class="metric-sub up" id="m-pemasukan-sub">0 transaksi lunas</div>
    </div>
  </div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Antrian Pasien per Poli</div>
      <div id="dashAntrianPoli"></div>
    </div>
    <div class="card">
      <div class="card-title">Aktivitas Terbaru</div>
      <div id="dashAktivitas"></div>
    </div>
  </div>

  <div class="grid22">
    <div class="card">
      <div class="card-title">Status Pasien Hari Ini</div>
      <canvas id="chartStatusPasien" height="210"></canvas>
    </div>
    <div class="card">
      <div class="card-title">Pemasukan 7 Hari Terakhir</div>
      <canvas id="chartPemasukan" height="210"></canvas>
    </div>
  </div>
</div>

@endsection
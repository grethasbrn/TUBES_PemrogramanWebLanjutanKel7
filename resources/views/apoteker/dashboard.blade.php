@extends('layouts.apoteker')

@section('content')
    
  <div class="page-section active" id="sec-dashboard">

    <div class="page-header">
      <div>
        <div class="page-title">Dashboard</div>
        <div class="page-sub">Ringkasan aktivitas farmasi hari ini</div>
      </div>
      <button class="btn btn-primary" onclick="showSection('stock'); openModal('modalTambahBatch')">+ Input Batch Obat</button>
    </div>

    <!-- info stok -->
    <div class="metrics">
      <div class="metric">
        <div class="metric-label">Total Jenis Obat</div>
        <div class="metric-val" id="m-total">248</div>
        <div class="metric-sub up">+4 bulan ini</div>
      </div>
      <div class="metric">
        <div class="metric-label">Resep Hari Ini</div>
        <div class="metric-val" id="m-resep">37</div>
        <div class="metric-sub up">+12 vs kemarin</div>
      </div>
      <div class="metric">
        <div class="metric-label">Stok Kritis</div>
        <div class="metric-val dn" id="m-kritis">—</div>
        <div class="metric-sub dn" id="m-kritis-sub">Perlu restock</div>
      </div>
      <div class="metric">
        <div class="metric-label">Mendekati Expired</div>
        <div class="metric-val warn" id="m-exp">—</div>
        <div class="metric-sub warn" id="m-exp-sub">Dalam 90 hari</div>
      </div>
    </div>

    <!-- alert -->
    <div id="alertBanner" class="alert-banner danger">
      <span>⚠</span>
      <div class="alert-marquee">
        <div class="alert-track" id="alertTrack"></div>
      </div>
    </div>

    <div class="grid2">
      <div class="card">
        <div class="card-title">Resep masuk — 7 hari terakhir</div>
        <canvas id="chartResep" height="180"></canvas>
      </div>
      <div class="card">
        <div class="card-title">Alert aktif</div>
        <div id="alertList"></div>
      </div>
    </div>

    <div class="grid22">
      <div class="card">
        <div class="card-title">Aktivitas Terbaru</div>
        <div id="activityList"></div>
      </div>
      <div class="card">
        <div class="card-title">Distribusi Tipe Obat</div>
        <canvas id="chartTipe" height="200"></canvas>
      </div>
    </div>

  </div>


@endsection
@extends('layouts.dokter')

@section('content')

<div class="page-section active" id="sec-dashboard">
  <div class="page-header">
    <div>
      <div class="page-title">Dashboard</div>
      <div class="page-sub">Selamat pagi, Dr. Tirta — Kamis, 24 April 2026</div>
    </div>
    <button class="btn btn-primary" onclick="showSection('buat-resep')">✍️ Buat Resep Baru</button>
  </div>

  <div class="metrics">
    <div class="metric">
      <div class="metric-label">Pasien Hari Ini</div>
      <div class="metric-val" id="m-pasien-hari">12</div>
      <div class="metric-sub up">+3 vs kemarin</div>
    </div>
    <div class="metric">
      <div class="metric-label">Resep Dibuat</div>
      <div class="metric-val" id="m-resep-dibuat">8</div>
      <div class="metric-sub info">Hari ini</div>
    </div>
    <div class="metric">
      <div class="metric-label">Divalidasi Apoteker</div>
      <div class="metric-val" id="m-divalidasi" style="color:var(--teal)">5</div>
      <div class="metric-sub up">Siap diambil</div>
    </div>
    <div class="metric">
      <div class="metric-label">Substitusi Obat</div>
      <div class="metric-val warn" id="m-substitusi" style="color:var(--amber)">2</div>
      <div class="metric-sub warn">Perlu diketahui</div>
    </div>
  </div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Antrian Pasien Hari Ini</div>
      <div id="dashAntrianList"></div>
    </div>
    <div class="card">
      <div class="card-title">Status Resep Terbaru</div>
      <div id="dashResepList"></div>
    </div>
  </div>

  <div class="grid22">
    <div class="card">
      <div class="card-title">Aktivitas Terbaru</div>
      <div id="activityList"></div>
    </div>
    <div class="card">
      <div class="card-title">Distribusi Pasien Bulan Ini</div>
      <canvas id="chartPasien" height="200"></canvas>
    </div>
  </div>
</div>

@endsection
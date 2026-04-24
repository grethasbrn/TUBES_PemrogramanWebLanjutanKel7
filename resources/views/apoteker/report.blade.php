@extends('layouts.apoteker')

@section('content')

<div class="page-section" id="sec-report">
  <div class="page-header">
    <div>
      <div class="page-title">Report</div>
      <div class="page-sub">Laporan bulanan aktivitas farmasi</div>
    </div>
    <div style="display:flex;gap:8px">
      <select class="filter-sel" id="reportMonth">
        <option>April 2026</option><option>Maret 2026</option><option>Februari 2026</option>
      </select>
      <button class="btn btn-teal">⬇ Export PDF</button>
    </div>
  </div>

  <div class="metrics" style="margin-bottom:14px">
    <div class="metric"><div class="metric-label">Total Resep</div><div class="metric-val">148</div><div class="metric-sub up">+22% bulan ini</div></div>
    <div class="metric"><div class="metric-label">Resep Selesai</div><div class="metric-val">132</div><div class="metric-sub up">89.2% completion</div></div>
    <div class="metric"><div class="metric-label">Total Pendapatan</div><div class="metric-val" style="font-size:20px">Rp 24.8jt</div><div class="metric-sub up">+15% vs bulan lalu</div></div>
    <div class="metric"><div class="metric-label">Obat Disubstitusi</div><div class="metric-val warn">14</div><div class="metric-sub warn">9.4% dari resep</div></div>
  </div>

  <div class="grid22">
    <div class="card">
      <div class="card-title">10 Obat Terlaris</div>
      <div id="topDrugsChart"></div>
    </div>
    <div class="card">
      <div class="card-title">Pendapatan per Minggu</div>
      <canvas id="chartPendapatan" height="200"></canvas>
    </div>
  </div>
</div>

@endsection
@extends('layouts.apoteker')

@section('content')
    
  <div class="page-section active" id="sec-dashboard">

    <div class="page-header">
      <div>
        <div class="page-title">Dashboard</div>
        <div class="page-sub">Ringkasan aktivitas farmasi hari ini</div>
      </div>
      <a href="{{ route('apoteker.stock', ['add' => 'true']) }}" class="btn btn-primary">+ Input Batch Obat</a>
    </div>

    <!-- info stok -->
    <div class="metrics">
      <div class="metric">
        <div class="metric-label">Total Jenis Obat</div>
        <div class="metric-val" id="m-total">{{ $totalJenisObat }}</div>
        <div class="metric-sub {{ $tambahBulanIni >= 0 ? 'up' : 'dn' }}">
          {{ $tambahBulanIni >= 0 ? '+' : '' }}{{ $tambahBulanIni }} bulan ini
        </div>
      </div>
      <div class="metric">
        <div class="metric-label">Resep Hari Ini</div>
        <div class="metric-val" id="m-resep">{{ $resepHariIni }}</div>
        <div class="metric-sub {{ $selisihKemarin >= 0 ? 'up' : 'dn' }}">
        {{ $selisihKemarin >= 0 ? '+' : '' }}{{ $selisihKemarin }} vs kemarin
        </div>
      </div>
      <div class="metric">
        <div class="metric-label">Stok Kritis</div>
        <div class="metric-val dn" id="m-kritis">{{ $stokKritis > 0 ? $stokKritis : '—' }}</div>
        <div class="metric-sub dn" id="m-kritis-sub">Perlu restock</div>
      </div>
      <div class="metric">
        <div class="metric-label">Mendekati Expired</div>
        <div class="metric-val warn" id="m-exp">{{ $mendekatiExpired > 0 ? $mendekatiExpired : '—' }}</div>
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
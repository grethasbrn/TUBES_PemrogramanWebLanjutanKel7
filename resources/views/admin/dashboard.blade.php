@extends('layouts.admin')

@section('content')

<!-- ════ DASHBOARD ════ -->
<div class="page active" id="pg-dashboard">
  <div class="page-header">
    <div>
      <div class="page-title">Dashboard Kasir</div>
      <div class="page-sub">Selamat pagi — ringkasan transaksi hari ini</div>
    </div>
    <button class="btn btn-gold" onclick="showPage('daftar',document.getElementById('ni-daftar'))">+ Daftarkan Pasien</button>
  </div>
  <div class="page-body">
    <div class="stat-row" id="dashStats"></div>
    <div id="dashAlertWrap"></div>
    <div class="grid2">
      <div class="card">
        <div class="card-header"><div class="card-title">Resep siap bayar</div><span class="badge b-siap" id="dashSiapCount">0 resep</span></div>
        <div class="card-body"><div id="dashSiapList"></div></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Aktivitas terbaru</div></div>
        <div class="card-body"><div id="dashActivity"></div></div>
      </div>
    </div>
    <div class="grid22">
      <div class="card">
        <div class="card-header"><div class="card-title">Pasien terdaftar hari ini</div></div>
        <div class="card-body"><div id="dashPasienToday"></div></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Ringkasan metode bayar</div></div>
        <div class="card-body"><div id="dashMetodeSummary"></div></div>
      </div>
    </div>
  </div>
</div>

@endsection
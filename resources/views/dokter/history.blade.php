@extends('layouts.dokter')

@section('content')

<div class="page-section active" id="sec-riwayat">
  <div class="page-header">
    <div>
      <div class="page-title">Riwayat Pasien</div>
      <div class="page-sub">Rekam medis dan riwayat resep pasien</div>
    </div>
  </div>

  <div class="pasien-layout">
    <div>
      <div class="search-row">
        <input type="text" class="search-input" placeholder="Cari pasien..." id="riwayatSearch" oninput="renderRiwayatList()">
      </div>
      <div id="riwayatPasienList"></div>
    </div>
    <div id="riwayatDetail">
      <div class="empty-state card">
        <div class="icon">📅</div>
        <div class="label">Pilih pasien untuk lihat riwayat</div>
      </div>
    </div>
  </div>
</div>

@endsection
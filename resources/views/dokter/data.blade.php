@extends('layouts.dokter')

@section('content')

<div class="page-section active" id="sec-pasien">
  <div class="page-header">
    <div>
      <div class="page-title">Data Pasien</div>
      <div class="page-sub">Daftar pasien yang dikirim oleh admin</div>
    </div>
  </div>

  <div class="pasien-layout">
    <!-- List -->
    <div>
      <div class="search-row">
        <input type="text" class="search-input" placeholder="Cari nama atau No. RM..." id="pasienSearch" oninput="renderPasienList()">
        <select class="filter-sel" id="pasienFilterBayar" onchange="renderPasienList()">
          <option value="">Semua</option>
          <option value="BPJS">BPJS</option>
          <option value="Mandiri">Mandiri</option>
        </select>
      </div>
      <div id="pasienList"></div>
    </div>

    <!-- Detail -->
    <div id="pasienDetail">
      <div class="empty-state card">
        <div class="icon">👤</div>
        <div class="label">Pilih pasien untuk detail</div>
      </div>
    </div>
  </div>
</div>


@endsection
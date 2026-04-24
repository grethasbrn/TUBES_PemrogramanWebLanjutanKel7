@extends('layouts.dokter')

@section('content')

<div class="page-section active" id="sec-resep-status">
  <div class="page-header">
    <div>
      <div class="page-title">Status Resep</div>
      <div class="page-sub">Pantau validasi dan proses resep oleh apoteker</div>
    </div>
  </div>

  <div class="rx-layout">
    <div>
      <div class="tabs">
        <button class="tab-btn active" onclick="filterStatus('semua',this)">Semua</button>
        <button class="tab-btn" onclick="filterStatus('baru',this)">Baru</button>
        <button class="tab-btn" onclick="filterStatus('validasi',this)">Divalidasi</button>
        <button class="tab-btn" onclick="filterStatus('siap',this)">Siap Ambil</button>
        <button class="tab-btn" onclick="filterStatus('selesai',this)">Selesai</button>
      </div>
      <div id="statusResepList"></div>
    </div>
    <div id="statusResepDetail">
      <div class="empty-state card">
        <div class="icon">📋</div>
        <div class="label">Pilih resep untuk detail</div>
      </div>
    </div>
  </div>
</div>

@endsection
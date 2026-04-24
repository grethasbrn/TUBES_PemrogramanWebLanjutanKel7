@extends('layouts.admin')

@section('content')

<!-- ════ PEMBAYARAN ════ -->
<div class="page" id="pg-pembayaran">
  <div class="page-header">
    <div>
      <div class="page-title">Proses Pembayaran</div>
      <div class="page-sub">Pilih resep yang sudah divalidasi apoteker</div>
    </div>
  </div>
  <div class="page-body">
    <div style="display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,1fr);gap:18px;align-items:start">
      <!-- LEFT: Resep List -->
      <div style="display:flex;flex-direction:column;gap:14px">
        <div class="search-row">
          <input class="search-input" type="text" placeholder="Cari nama pasien / no. resep..." id="paySearch" oninput="renderPayList()">
          <select class="filter-sel" id="payFilter" onchange="renderPayList()">
            <option value="siap">Siap Bayar</option>
            <option value="">Semua</option>
          </select>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title">Antrian Pembayaran</div><span class="badge b-siap" id="payQueueCount">0</span></div>
          <div class="card-body"><div id="payRxList"></div></div>
        </div>
      </div>
      <!-- RIGHT: Payment Form -->
      <div id="payFormWrap">
        <div style="background:var(--white);border:1.5px solid var(--paper3);border-radius:14px;overflow:hidden">
          <div style="padding:20px;text-align:center;color:var(--ink3)">
            <div style="font-size:32px;margin-bottom:10px;opacity:.3">💳</div>
            <div style="font-size:14px;font-weight:500">Pilih resep di sebelah kiri</div>
            <div style="font-size:12px;margin-top:4px;color:var(--ink4)">untuk memulai proses pembayaran</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
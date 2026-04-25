@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-antrian">
  <div class="page-header">
    <div>
      <div class="page-title">Antrian & Poli</div>
      <div class="page-sub">Kirim pasien ke dokter sesuai poli tujuan</div>
    </div>
    <button class="btn btn-primary" onclick="kirimSemuaPoli()">📤 Kirim Semua ke Dokter</button>
  </div>

  <div id="kanbanBoard" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px"></div>

  <div class="card">
    <div class="card-title">Pasien Belum Dikirim ke Dokter</div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr><th>No. RM</th><th>Nama</th><th>Poli</th><th>Jenis</th><th>Antrian</th><th>Status Kirim</th><th>Aksi</th></tr>
        </thead>
        <tbody id="tblAntrianBody"></tbody>
      </table>
    </div>
  </div>
</div>

@endsection
@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-pasien">
  <div class="page-header">
    <div>
      <div class="page-title">Data Pasien</div>
      <div class="page-sub">Input dan kelola data pasien yang mendaftar</div>
    </div>
    <button class="btn btn-primary">+ Daftar Pasien Baru</button>
  </div>

  <div class="search-row">
    <input type="text" class="search-input" placeholder="Cari nama, NIK, atau No. RM...">
    <select class="filter-sel">
      <option value="">Semua Jenis</option>
      <option value="BPJS">BPJS</option>
      <option value="Mandiri">Mandiri</option>
    </select>
    <select class="filter-sel">
      <option value="">Semua Status</option>
      <option value="Menunggu">Menunggu</option>
      <option value="Diperiksa">Diperiksa</option>
      <option value="Selesai">Selesai</option>
    </select>
    <select class="filter-sel">
      <option value="">Semua Poli</option>
      <option value="Umum">Umum</option>
      <option value="Anak">Anak</option>
      <option value="Penyakit Dalam">Penyakit Dalam</option>
      <option value="Bedah">Bedah</option>
      <option value="Gigi">Gigi</option>
      <option value="Kebidanan">Kebidanan</option>
      <option value="Mata">Mata</option>
      <option value="UGD">UGD</option>
    </select>
  </div>

  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>NIK</th>
          <th>Tgl Lahir</th>
          <th>Jenis</th>
          <th>Poli Tujuan</th>
          <th>Status</th>
          <th>Validasi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tblPasienBody"></tbody>
    </table>
  </div>
</div>


@endsection
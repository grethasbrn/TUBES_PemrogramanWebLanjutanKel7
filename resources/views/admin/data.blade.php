@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-pasien">
  <div class="page-header">
    <div>
      <div class="page-title">Data Pasien</div>
      <div class="page-sub">Input dan kelola data pasien yang mendaftar</div>
    </div>

    {{-- ✅ Tombol sekarang terhubung ke route pasien.create --}}
    <a href="{{ route('pasien.create') }}" class="btn btn-primary">+ Daftar Pasien Baru</a>
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

  {{-- ✅ Flash message sukses --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

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
      <tbody>
        @forelse($pasiens as $p)
          <tr>
            <td>{{ $p->no_rm }}</td>
            <td>{{ $p->nama }}</td>
            <td>{{ $p->nik }}</td>
            <td>{{ \Carbon\Carbon::parse($p->tgl_lahir)->format('d/m/Y') }}</td>
            <td>
              <span class="badge {{ $p->jenis == 'BPJS' ? 'b-bpjs' : 'b-mandiri' }}">
                {{ $p->jenis }}
              </span>
            </td>
            <td>{{ $p->poli_tujuan }}</td>
            <td>
              <span class="badge {{ $p->status == 'Selesai' ? 'b-selesai' : ($p->status == 'Diperiksa' ? 'b-siap' : 'b-warn') }}">
                {{ $p->status }}
              </span>
            </td>
            <td>
              <span class="badge b-validasi">{{ $p->validasi }}</span>
            </td>
            <td>
              <button class="btn btn-sm">Detail</button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" style="text-align:center; color:#A8998A; padding:2rem;">
              Belum ada data pasien.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
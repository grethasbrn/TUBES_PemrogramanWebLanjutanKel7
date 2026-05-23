@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-laporan">
  <div class="page-header">
    <div>
      <div class="page-title">Laporan Bulanan</div>
      <div class="page-sub">Ringkasan aktivitas, pasien, dan keuangan</div>
    </div>
    <div class="no-print" style="display:flex;gap:8px">
      <select class="filter-sel" id="laporanBulan">
        <option value="4">April 2026</option><option value="3">Maret 2026</option>
        <option value="2">Februari 2026</option><option value="1">Januari 2026</option>
      </select>
      <button class="btn btn-teal">Cetak</button>
      <button class="btn btn-primary">Export</button>
    </div>
  </div>

  <!-- Stats row -->
  <div class="grid3">

      <div class="card">
          <div>Total Pasien</div>
          <h2>{{ $totalPasien }}</h2>
      </div>

      <div class="card">
          <div>Total Pendapatan</div>
          <h2>Rp {{ number_format($totalPendapatan,0,',','.') }}</h2>
      </div>

  </div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Tren Kunjungan Pasien</div>
      <canvas id="chartKunjungan" height="200"></canvas>
    </div>
    <div class="card">
      <div class="card-title">Distribusi Jenis Pasien</div>
      <canvas id="chartJenisPasien" height="200"></canvas>
    </div>
  </div>

  <div class="grid22">
    <div class="card">
      <div class="card-title">Pasien per Poli</div>
      <div id="laporanPerPoli"></div>
    </div>
    <div class="card">
      <div class="card-title">Ringkasan Keuangan</div>
      <div id="laporanKeuangan"></div>
    </div>
  </div>

  <!-- Detail Table -->
  <div class="card">
    <div class="card-title">Detail Transaksi Bulan Ini</div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>No. Transaksi</th><th>Pasien</th><th>Poli</th><th>Jenis</th><th>Total</th><th>Metode</th><th>Status</th><th>Tanggal</th></tr></thead>
        <tbody>
        @foreach($transaksi as $t)
        <tr>
            <td>{{ $t->no_invoice }}</td>
            <td>{{ $t->nama }}</td>
            <td>-</td>
            <td>{{ $t->jenis }}</td>

            <td>
                Rp {{ number_format($t->total_tagihan,0,',','.') }}
            </td>

            <td>{{ $t->jenis }}</td>

            <td>{{ $t->status }}</td>

            <td>{{ $t->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
@extends('layouts.dokter')

@section('content')
<div class="page-section active" id="sec-dashboard">
  <div class="page-header">
    <div>
      <div class="page-title">Dashboard</div>
      {{-- ✅ PERBAIKAN: pakai format biasa, tidak perlu translatedFormat --}}
      <div class="page-sub">{{ now()->format('l, d F Y') }}</div>
    </div>
    <button class="btn btn-primary" onclick="showSection('buat-resep')">✍️ Buat Resep Baru</button>
  </div>

  {{-- ✅ PERBAIKAN: semua 4 metric card diisi, tidak lagi ada komentar placeholder --}}
  <div class="metrics">
    <div class="metric">
      <div class="metric-label">Pasien Hari Ini</div>
      <div class="metric-val" id="m-pasien-hari">{{ $pasienHariIni }}</div>
      <div class="metric-sub info">Menunggu & diperiksa</div>
    </div>
    <div class="metric">
      <div class="metric-label">Resep Dibuat</div>
      <div class="metric-val" id="m-resep-dibuat">{{ $resepTerbaru->count() }}</div>
      <div class="metric-sub info">Hari ini</div>
    </div>
    <div class="metric">
      <div class="metric-label">Selesai Diperiksa</div>
      <div class="metric-val" style="color:var(--teal)" id="m-selesai">
        {{ $antrian->where('status', 'Selesai')->count() }}
      </div>
      <div class="metric-sub up">Sudah ditangani</div>
    </div>
    <div class="metric">
      <div class="metric-label">Menunggu Antrian</div>
      <div class="metric-val warn" style="color:var(--amber)" id="m-antri">
        {{ $antrian->where('status', 'Menunggu')->count() }}
      </div>
      <div class="metric-sub warn">Belum diperiksa</div>
    </div>
  </div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Antrian Pasien Hari Ini</div>
      <div id="dashAntrianList">
        @forelse($antrian as $p)
          <div class="antrian-item" onclick="selectPasien('{{ $p->id }}')">
            <div class="antrian-nama">{{ $p->nama }}</div>
            <div class="antrian-rm">{{ $p->no_rm }}</div>
            <span class="badge {{ $p->status === 'Menunggu' ? 'b-baru' : 'b-validasi' }}">
              {{ $p->status }}
            </span>
          </div>
        @empty
          <p style="color:#aaa;text-align:center;padding:20px">Belum ada pasien hari ini</p>
        @endforelse
      </div>
    </div>

    <div class="card">
      <div class="card-title">Resep Terbaru</div>
      <div id="dashResepList">
        @forelse($resepTerbaru as $r)
          <div class="antrian-item">
            <div class="antrian-nama">{{ $r->pasien->nama ?? '-' }}</div>
            <div class="antrian-rm">{{ $r->no_resep }}</div>
            <span class="badge b-{{ $r->status }}">{{ ucfirst($r->status) }}</span>
          </div>
        @empty
          <p style="color:#aaa;text-align:center;padding:20px">Belum ada resep hari ini</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

<script>
// Data pasien untuk fungsi JS (navigasi sidebar dll)
let pasienData = @json($antrianJson);
let resepData  = @json($resepJson);
</script>
@endsection
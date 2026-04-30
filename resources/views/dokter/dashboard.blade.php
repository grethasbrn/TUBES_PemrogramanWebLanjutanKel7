@extends('layouts.dokter')

@section('content')
<div class="page-section active dashboard-page" id="sec-dashboard">
  <div class="page-header hero-dashboard">
    <div>
      <div class="hero-badge">💊 Pharmbee Dashboard</div>
      <div class="page-title">Dashboard</div>
      <div class="page-sub">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
      <p class="hero-text">Selamat datang kembali, kelola pasien dan resep hari ini dengan mudah.</p>
    </div>

    <button class="btn btn-primary btn-resep" onclick="showSection('buat-resep')">
      ✍️ Buat Resep Baru
    </button>
  </div>

  <div class="metrics">
    <div class="metric pretty-card">
      <div class="metric-icon">👥</div>
      <div class="metric-label">Pasien Hari Ini</div>
      <div class="metric-val">{{ $pasienHariIni }}</div>
      <div class="metric-sub info">Menunggu & diperiksa</div>
    </div>

    <div class="metric pretty-card">
      <div class="metric-icon">📝</div>
      <div class="metric-label">Resep Dibuat</div>
      <div class="metric-val">{{ $resepTerbaru->count() }}</div>
      <div class="metric-sub info">Hari ini</div>
    </div>

    <div class="metric pretty-card">
      <div class="metric-icon">✅</div>
      <div class="metric-label">Selesai Diperiksa</div>
      <div class="metric-val selesai">
        {{ $antrian->where('status', 'Selesai')->count() }}
      </div>
      <div class="metric-sub up">Sudah ditangani</div>
    </div>

    <div class="metric pretty-card">
      <div class="metric-icon">⏳</div>
      <div class="metric-label">Menunggu Antrian</div>
      <div class="metric-val menunggu">
        {{ $antrian->where('status', 'Menunggu')->count() }}
      </div>
      <div class="metric-sub warn">Belum diperiksa</div>
    </div>
  </div>

  <div class="grid2">
    <div class="card pretty-card big-card">
      <div class="card-head">
        <div>
          <div class="card-title">Antrian Pasien Hari Ini</div>
          <div class="card-subtitle">Daftar pasien yang masuk hari ini</div>
        </div>
        <span class="mini-badge">{{ $antrian->count() }} pasien</span>
      </div>

      <div id="dashAntrianList">
        @forelse($antrian as $p)
          <div class="antrian-item" onclick="selectPasien('{{ $p->id }}')">
            <div class="patient-left">
              <div class="avatar-mini">{{ strtoupper(substr($p->nama, 0, 1)) }}</div>
              <div>
                <div class="antrian-nama">{{ $p->nama }}</div>
                <div class="antrian-rm">{{ $p->no_rm }}</div>
              </div>
            </div>

            <span class="badge {{ $p->status === 'Menunggu' ? 'b-baru' : 'b-validasi' }}">
              {{ $p->status }}
            </span>
          </div>
        @empty
          <div class="empty-state">
            <div class="empty-icon">🩺</div>
            <div class="empty-title">Belum ada pasien hari ini</div>
            <small>Silakan tambah pasien baru atau buat resep baru.</small>
          </div>
        @endforelse
      </div>
    </div>

    <div class="card pretty-card big-card">
      <div class="card-head">
        <div>
          <div class="card-title">Resep Terbaru</div>
          <div class="card-subtitle">Resep yang baru dibuat hari ini</div>
        </div>
        <span class="mini-badge">{{ $resepTerbaru->count() }} resep</span>
      </div>

      <div id="dashResepList">
        @forelse($resepTerbaru as $r)
          <div class="antrian-item">
            <div class="patient-left">
              <div class="avatar-mini">💊</div>
              <div>
                <div class="antrian-nama">{{ $r->pasien->nama ?? '-' }}</div>
                <div class="antrian-rm">{{ $r->no_resep }}</div>
              </div>
            </div>

            <span class="badge b-{{ $r->status }}">{{ ucfirst($r->status) }}</span>
          </div>
        @empty
          <div class="empty-state">
            <div class="empty-icon">💊</div>
            <div class="empty-title">Belum ada resep hari ini</div>
            <small>Resep yang dibuat akan muncul di sini.</small>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<style>
.dashboard-page {
  animation: fadeIn 0.45s ease;
}

.hero-dashboard {
  background:
    radial-gradient(circle at top right, rgba(176, 124, 255, 0.22), transparent 32%),
    linear-gradient(135deg, #fff7ed, #f3e8ff);
  border: 1px solid #eadcf8;
  border-radius: 28px;
  padding: 26px;
  box-shadow: 0 16px 40px rgba(120, 90, 160, 0.10);
  margin-bottom: 24px;
}

.hero-badge {
  display: inline-block;
  margin-bottom: 8px;
  padding: 6px 12px;
  border-radius: 999px;
  background: rgba(139, 107, 214, 0.12);
  color: #7c5ac7;
  font-size: 12px;
  font-weight: 700;
}

.hero-text {
  margin-top: 8px;
  margin-bottom: 0;
  color: #8b7d72;
  font-size: 14px;
}

.btn-resep {
  background: linear-gradient(135deg, #8b6bd6, #b07cff) !important;
  border: none !important;
  border-radius: 16px !important;
  padding: 13px 22px !important;
  box-shadow: 0 12px 26px rgba(139, 107, 214, 0.32);
  transition: 0.25s ease;
}

.btn-resep:hover {
  transform: translateY(-3px);
  box-shadow: 0 18px 34px rgba(139, 107, 214, 0.42);
}

.pretty-card {
  border: 1px solid #eee2d5 !important;
  border-radius: 24px !important;
  background: rgba(255, 253, 248, 0.96) !important;
  box-shadow: 0 14px 30px rgba(92, 74, 58, 0.08);
  transition: all 0.25s ease;
}

.pretty-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(92, 74, 58, 0.14);
}

.metric {
  position: relative;
  overflow: hidden;
  animation: fadeInUp 0.5s ease;
}

.metric::after {
  content: "";
  position: absolute;
  width: 90px;
  height: 90px;
  right: -35px;
  top: -35px;
  border-radius: 50%;
  background: rgba(176, 124, 255, 0.10);
}

.metric-icon {
  width: 44px;
  height: 44px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  font-size: 20px;
  margin-bottom: 12px;
}

.metric:nth-child(1) .metric-icon { background: #ede9fe; }
.metric:nth-child(2) .metric-icon { background: #ffe4e6; }
.metric:nth-child(3) .metric-icon { background: #dcfce7; }
.metric:nth-child(4) .metric-icon { background: #fef3c7; }

.metric-val {
  font-size: 34px !important;
  font-weight: 900 !important;
  letter-spacing: 1px;
}

.metric-val.selesai {
  color: var(--teal);
}

.metric-val.menunggu {
  color: var(--amber);
}

.big-card {
  min-height: 210px;
}

.card-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 18px;
}

.card-subtitle {
  margin-top: 4px;
  color: #a49a92;
  font-size: 13px;
}

.mini-badge {
  background: #f2e9ff;
  color: #7c5ac7;
  border-radius: 999px;
  padding: 6px 11px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}

.antrian-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding: 14px 16px;
  border-radius: 18px;
  background: #fffaf3;
  border: 1px solid #f0e3d4;
  margin-bottom: 10px;
  transition: 0.22s ease;
  cursor: pointer;
}

.antrian-item:hover {
  background: #f7efff;
  transform: translateX(5px);
}

.patient-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar-mini {
  width: 38px;
  height: 38px;
  border-radius: 14px;
  background: linear-gradient(135deg, #ede9fe, #fff1f2);
  display: grid;
  place-items: center;
  color: #7c5ac7;
  font-weight: 800;
}

.antrian-nama {
  font-weight: 800;
}

.antrian-rm {
  margin-top: 3px;
  color: #a49a92;
  font-size: 12px;
}

.empty-state {
  color: #a9a0a0;
  text-align: center;
  padding: 36px 20px;
  font-size: 15px;
}

.empty-icon {
  font-size: 42px;
  margin-bottom: 10px;
}

.empty-title {
  color: #8d8380;
  font-weight: 800;
  margin-bottom: 4px;
}

.empty-state small {
  color: #bbb;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(12px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<script>
let pasienData = @json($antrianJson);
let resepData  = @json($resepJson);
</script>
@endsection
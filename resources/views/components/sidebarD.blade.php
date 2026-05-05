<div class="topbar">
  <div class="logo">
    <div class="logo-icon">💊</div>
    <div class="logo-text">
      <div class="l1">Pharmbee</div>
    </div>
  </div>

  <div class="topbar-right">
    <button class="notif-btn">
      <span>🔔</span>
      <span class="notif-badge"></span>
    </button>

    <div class="avatar-wrap">
      <div class="avatar">DT</div>
      <div class="avatar-info">
        <div class="avatar-name">dr. Tirta</div>
        <div class="avatar-role">Dokter</div>
      </div>
    </div>
  </div>
</div>

<div class="sidebar">
  <div class="nav-section">Menu</div>

  <div class="nav-item active" id="nav-dashboard" onclick="showSection('dashboard')">
    <i class="bi bi-grid-1x2 nav-icon"></i>
    <span>Dashboard</span>
  </div>

  <div class="nav-item" id="nav-pasien" onclick="showSection('pasien')">
    <i class="bi bi-person-vcard nav-icon"></i>
    <span>Data Pasien</span>
  </div>

  <div class="nav-section">Resep</div>

  <div class="nav-item" id="nav-buat-resep" onclick="showSection('buat-resep')">
    <i class="bi bi-clipboard2-pulse nav-icon"></i>
    <span>Buat Resep</span>
  </div>

  <div class="nav-item" id="nav-resep-status" onclick="showSection('resep-status')">
    <i class="bi bi-activity nav-icon"></i>
    <span>Status Resep</span>
  </div>

  <div class="nav-section">Laporan</div>

  <div class="nav-item" id="nav-riwayat" onclick="showSection('riwayat')">
    <i class="bi bi-clock-history nav-icon"></i>
    <span>Riwayat</span>
  </div>

  {{-- LOGOUT --}}
  <div class="nav-section">Akun</div>
  <form method="POST" action="/logout">
    @csrf
    <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;color:inherit;">
      <i class="bi bi-box-arrow-left nav-icon"></i>
      <span>Logout</span>
    </button>
  </form>
</div>
<div class="topbar">

  <div class="logo">

    <img 
      src="{{ asset('images/pharmbee-logo.png') }}" 
      class="logo-img"
      alt="Pharmbee Logo"
    >

    <div class="logo-text">
      <div class="l1">Pharmbee</div>
    </div>

  </div>


  <div class="topbar-right">

    <!-- DATE & TIME -->
    <div class="date-chip">
      <div id="dateTag"></div>
    </div>

    <!-- USER -->
    <div class="avatar-wrap">

      <div class="avatar">
        {{ strtoupper(substr(Auth::user()->name ?? 'D', 0, 1)) }}
      </div>

      <div class="avatar-info">

        <div class="avatar-name">
          {{ Auth::user()->name ?? 'Dokter' }}
        </div>

        <div class="avatar-role">
          {{ Auth::user()->role ?? 'Dokter' }}
        </div>

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


  <div class="nav-section">Akun</div>


  <form method="POST" action="/logout">
    @csrf

    <button 
      type="submit" 
      class="nav-item" 
      style="width:100%;background:none;border:none;cursor:pointer;text-align:left;padding-bottom:50px"
    >

      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        height="24px" 
        viewBox="0 -960 960 960" 
        width="24px" 
        fill="currentColor"
      >
        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 
        23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 
        102-102H360v-80h327L585-622l55-58 
        200 200-200 200Z"/>
      </svg>

      <span>Logout</span>

    </button>

  </form>


</div>


<script>

function updateDateTime() {

    const now = new Date();

    const tanggal = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    const jam = now.toLocaleTimeString('id-ID', {
        hour:'2-digit',
        minute:'2-digit',
        second:'2-digit'
    });

    document.getElementById('dateTag').innerHTML =
        `${tanggal} • ${jam}`;
}


updateDateTime();

setInterval(updateDateTime,1000);

</script>
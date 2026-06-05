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

    <div class="date-chip">
      <div id="dateTag"></div>
    </div>


    <!-- USER DROPDOWN -->
    <div class="profile-menu">

      <div class="avatar-wrap" onclick="toggleProfileMenu()">

        <div class="avatar">
          {{ strtoupper(substr(Auth::user()->name ?? 'D',0,1)) }}
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


      <div class="profile-dropdown" id="profileDropdown">

        <form method="POST" action="/logout">
          @csrf

          <button type="submit" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            Logout
          </button>

        </form>

      </div>

    </div>

  </div>

</div>





<!-- SIDEBAR -->
<div class="sidebar">


<<<<<<< HEAD
  <div class="sidebar-top">

    <div class="nav-section">
      Menu
    </div>

=======
  <a class="nav-item {{ request()->is('dokter/dashboard*') ? 'active' : '' }}" href="{{ url('dokter/dashboard') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M183.5-183.5Q160-207 160-240t23.5-56.5Q207-320 240-320t56.5 23.5Q320-273 320-240t-23.5 56.5Q273-160 240-160t-56.5-23.5Zm240 0Q400-207 400-240t23.5-56.5Q447-320 480-320t56.5 23.5Q560-273 560-240t-23.5 56.5Q513-160 480-160t-56.5-23.5Zm240 0Q640-207 640-240t23.5-56.5Q687-320 720-320t56.5 23.5Q800-273 800-240t-23.5 56.5Q753-160 720-160t-56.5-23.5Zm-480-240Q160-447 160-480t23.5-56.5Q207-560 240-560t56.5 23.5Q320-513 320-480t-23.5 56.5Q273-400 240-400t-56.5-23.5Zm240 0Q400-447 400-480t23.5-56.5Q447-560 480-560t56.5 23.5Q560-513 560-480t-23.5 56.5Q513-400 480-400t-56.5-23.5Zm240 0Q640-447 640-480t23.5-56.5Q687-560 720-560t56.5 23.5Q800-513 800-480t-23.5 56.5Q753-400 720-400t-56.5-23.5Zm-480-240Q160-687 160-720t23.5-56.5Q207-800 240-800t56.5 23.5Q320-753 320-720t-23.5 56.5Q273-640 240-640t-56.5-23.5Zm240 0Q400-687 400-720t23.5-56.5Q447-800 480-800t56.5 23.5Q560-753 560-720t-23.5 56.5Q513-640 480-640t-56.5-23.5Zm240 0Q640-687 640-720t23.5-56.5Q687-800 720-800t56.5 23.5Q800-753 800-720t-23.5 56.5Q753-640 720-640t-56.5-23.5Z"/></svg>
    <span>Dashboard</span>
  </a>

  <a class="nav-item {{ request()->is('dokter/data*') ? 'active' : '' }}" href="{{ url('dokter/data') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M555-435q-35-35-35-85t35-85q35-35 85-35t85 35q35 35 35 85t-35 85q-35 35-85 35t-85-35ZM400-160v-76q0-21 10-40t28-30q45-27 95.5-40.5T640-360q56 0 106.5 13.5T842-306q18 11 28 30t10 40v76H400Zm86-80h308q-35-20-74-30t-80-10q-41 0-80 10t-74 30Zm182.5-251.5Q680-503 680-520t-11.5-28.5Q657-560 640-560t-28.5 11.5Q600-537 600-520t11.5 28.5Q623-480 640-480t28.5-11.5ZM640-520Zm0 280ZM120-400v-80h320v80H120Zm0-320v-80h480v80H120Zm324 160H120v-80h360q-14 17-22.5 37T444-560Z"/></svg>
    <span>Data Pasien</span>
  </a>


  <div class="nav-section">Resep</div>
  <a class="nav-item {{ request()->is('dokter/prescription*') ? 'active' : '' }}" href="{{ url('dokter/prescription') }}">
    <i class="bi bi-clipboard2-pulse nav-icon"></i>
    <span>Buat Resep</span>
  </a>

  <a class="nav-item {{ request()->is('dokter/status*') ? 'active' : '' }}" href="{{ url('dokter/status') }}">
    <i class="bi bi-activity nav-icon"></i>
    <span>Status Resep</span>
  </a>


  <div class="nav-section">Laporan</div>
  <a class="nav-item {{ request()->is('dokter/history*') ? 'active' : '' }}" href="{{ url('dokter/history') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M120-120v-80l80-80v160h-80Zm160 0v-240l80-80v320h-80Zm160 0v-320l80 81v239h-80Zm160 0v-239l80-80v319h-80Zm160 0v-400l80-80v480h-80ZM120-327v-113l280-280 160 160 280-280v113L560-447 400-607 120-327Z"/></svg>
    <span>Riwayat</span>
  </a>


  <div class="nav-section">Akun</div>


  <form method="POST" action="/logout">
    @csrf
>>>>>>> 15df2dcdfaa3fc3aa30e7d69e1900a2f95186639

    <button 
      type="button"
      class="sidebar-toggle" 
      onclick="toggleSidebar()"
    >
      <i class="bi bi-list"></i>
    </button>

  </div>





  <div class="nav-item active" 
       id="nav-dashboard" 
       onclick="showSection('dashboard')">

    <i class="bi bi-grid-1x2 nav-icon"></i>
    <span>Dashboard</span>

  </div>



  <div class="nav-item" 
       id="nav-pasien" 
       onclick="showSection('pasien')">

    <i class="bi bi-person-vcard nav-icon"></i>
    <span>Data Pasien</span>

  </div>





  <div class="nav-section">
    Resep
  </div>



  <div class="nav-item" 
       id="nav-buat-resep" 
       onclick="showSection('buat-resep')">

    <i class="bi bi-clipboard2-pulse nav-icon"></i>
    <span>Buat Resep</span>

  </div>




  <div class="nav-item" 
       id="nav-resep-status" 
       onclick="showSection('resep-status')">

    <i class="bi bi-activity nav-icon"></i>
    <span>Status Resep</span>

  </div>






  <div class="nav-section">
    Laporan
  </div>



  <div class="nav-item" 
       id="nav-riwayat" 
       onclick="showSection('riwayat')">

    <i class="bi bi-clock-history nav-icon"></i>
    <span>Riwayat</span>

  </div>


</div>






<script>


function updateDateTime(){

    const now = new Date();


    const tanggal = now.toLocaleDateString('id-ID',{

        weekday:'long',
        day:'numeric',
        month:'long',
        year:'numeric'

    });


    const jam = now.toLocaleTimeString('id-ID',{

        hour:'2-digit',
        minute:'2-digit',
        second:'2-digit'

    });


    document.getElementById('dateTag').innerHTML =
        `${tanggal} • ${jam}`;

}





function toggleProfileMenu(){

    document
      .getElementById('profileDropdown')
      .classList
      .toggle('show');

}





function toggleSidebar(){

    document.body.classList.toggle('sidebar-close');

}





updateDateTime();

setInterval(updateDateTime,1000);


</script>
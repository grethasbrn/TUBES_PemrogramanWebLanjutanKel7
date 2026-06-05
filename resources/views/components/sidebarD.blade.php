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


  <div class="sidebar-top">

    <div class="nav-section">
      Menu
    </div>


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
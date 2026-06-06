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
      ☰
    </button>

  </div>




  <a class="nav-item {{ request()->is('dokter/dashboard*') ? 'active' : '' }}"
     href="{{ url('dokter/dashboard') }}">

    <i class="bi bi-grid-1x2 nav-icon"></i>
    <span>Dashboard</span>

  </a>



  <a class="nav-item {{ request()->is('dokter/data*') ? 'active' : '' }}"
     href="{{ url('dokter/data') }}">

    <i class="bi bi-person-vcard nav-icon"></i>
    <span>Data Pasien</span>

  </a>





  <div class="nav-section">
    Resep
  </div>



  <a class="nav-item {{ request()->is('dokter/prescription*') ? 'active' : '' }}"
     href="{{ url('dokter/prescription') }}">

    <i class="bi bi-clipboard2-pulse nav-icon"></i>
    <span>Buat Resep</span>

  </a>




  <a class="nav-item {{ request()->is('dokter/status*') ? 'active' : '' }}"
     href="{{ url('dokter/status') }}">

    <i class="bi bi-activity nav-icon"></i>
    <span>Status Resep</span>

  </a>






  <div class="nav-section">
    Laporan
  </div>



  <a class="nav-item {{ request()->is('dokter/history*') ? 'active' : '' }}"
     href="{{ url('dokter/history') }}">

    <i class="bi bi-clock-history nav-icon"></i>
    <span>Riwayat</span>

  </a>


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
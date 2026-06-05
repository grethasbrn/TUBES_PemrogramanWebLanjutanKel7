<!-- TOPBAR -->
<div class="topbar">

  <div class="logo">

    <div class="logo-icon">
      <img 
        src="{{ asset('images/pharmbee-logo.png') }}" 
        class="pharmbee-logo"
        alt="Pharmbee"
      >
    </div>

    <div class="logo-text">
      <div class="l1">Pharmbee</div>
    </div>

  </div>


  <div class="topbar-right">

    <div class="date-chip">
      <div id="dateTag"></div>
    </div>


    <a class="notif-btn" href="{{ url('apoteker/alerts') }}">
      <span>🔔</span>
      <span class="notif-badge" id="notifBadge"></span>
    </a>


    <div class="profile-menu">

      <div class="avatar-wrap" onclick="toggleProfileMenu()">

        <div class="avatar">
          {{ strtoupper(substr(Auth::user()->name ?? 'A',0,1)) }}
        </div>

        <div class="avatar-info">

          <div class="avatar-name">
            {{ Auth::user()->name ?? 'Apoteker' }}
          </div>

          <div class="avatar-role">
            {{ Auth::user()->role ?? 'Apoteker' }}
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





  <a class="nav-item {{ request()->is('apoteker/dashboard*') ? 'active' : '' }}" 
     href="{{ url('apoteker/dashboard') }}">

    <i class="bi bi-grid-1x2 nav-icon"></i>
    <span>Dashboard</span>

  </a>



  <a class="nav-item {{ request()->is('apoteker/stock*') ? 'active' : '' }}" 
     href="{{ url('apoteker/stock') }}">

    <i class="bi bi-capsule-pill nav-icon"></i>
    <span>Stok</span>

  </a>



  <a class="nav-item {{ request()->is('apoteker/alerts*') ? 'active' : '' }}" 
     href="{{ url('apoteker/alerts') }}">

    <i class="bi bi-bell nav-icon"></i>
    <span>Peringatan</span>

  </a>





  <div class="nav-section">
    VALIDASI & TRANSAKSI
  </div>



  <a class="nav-item {{ request()->is('apoteker/prescription*') ? 'active' : '' }}" 
     href="{{ url('apoteker/prescription') }}">

    <i class="bi bi-clipboard2-check nav-icon"></i>
    <span>Resep</span>

  </a>




  <a class="nav-item {{ request()->is('apoteker/invoice*') ? 'active' : '' }}" 
     href="{{ url('apoteker/invoice') }}">

    <i class="bi bi-receipt nav-icon"></i>
    <span>Invoice</span>

  </a>





  <div class="nav-section">
    LAPORAN
  </div>



  <a class="nav-item {{ request()->is('apoteker/report*') ? 'active' : '' }}" 
     href="{{ url('apoteker/report') }}">

    <i class="bi bi-graph-up nav-icon"></i>
    <span>Laporan</span>

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
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


    <!-- USER DROPDOWN -->
    <div class="profile-menu">

      <div class="avatar-wrap" onclick="toggleProfileMenu()">

        <div class="avatar">
          {{ strtoupper(substr(Auth::user()->name ?? 'A',0,1)) }}
        </div>


        <div class="avatar-info">

          <div class="avatar-name">
            {{ Auth::user()->name ?? 'Admin RS' }}
          </div>

          <div class="avatar-role">
            {{ Auth::user()->role ?? 'Admin' }}
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


  <div class="nav-section">Menu</div>



  <a 
    href="{{ url('admin/dashboard') }}"
    class="nav-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}"
  >

    <i class="bi bi-grid-1x2 nav-icon"></i>
    Dashboard

  </a>



  <a 
    href="{{ url('admin/data') }}"
    class="nav-item {{ request()->is('admin/data*') ? 'active' : '' }}"
  >

    <i class="bi bi-people nav-icon"></i>
    Data Pasien

  </a>



  <a 
    href="{{ url('admin/invoice') }}"
    class="nav-item {{ request()->is('admin/invoice*') ? 'active' : '' }}"
  >

    <i class="bi bi-receipt nav-icon"></i>
    Invoice

  </a>





  <div class="nav-section">Laporan</div>



  <a 
    href="{{ url('admin/report') }}"
    class="nav-item {{ request()->is('admin/report*') ? 'active' : '' }}"
  >

    <i class="bi bi-graph-up-arrow nav-icon"></i>
    Laporan

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




updateDateTime();

setInterval(updateDateTime,1000);


</script>
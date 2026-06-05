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

    <!-- DATE & TIME -->
    <div class="date-chip">
      <div id="dateTag"></div>
    </div>

    <!-- USER -->
    <div class="avatar-wrap">

      <div class="avatar">
        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
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

  </div>

</div>
<!-- SIDEBAR -->
<div class="sidebar">
  <div class="nav-section">Menu</div>
  <a class="nav-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M183.5-183.5Q160-207 160-240t23.5-56.5Q207-320 240-320t56.5 23.5Q320-273 320-240t-23.5 56.5Q273-160 240-160t-56.5-23.5Zm240 0Q400-207 400-240t23.5-56.5Q447-320 480-320t56.5 23.5Q560-273 560-240t-23.5 56.5Q513-160 480-160t-56.5-23.5Zm240 0Q640-207 640-240t23.5-56.5Q687-320 720-320t56.5 23.5Q800-273 800-240t-23.5 56.5Q753-160 720-160t-56.5-23.5Zm-480-240Q160-447 160-480t23.5-56.5Q207-560 240-560t56.5 23.5Q320-513 320-480t-23.5 56.5Q273-400 240-400t-56.5-23.5Zm240 0Q400-447 400-480t23.5-56.5Q447-560 480-560t56.5 23.5Q560-513 560-480t-23.5 56.5Q513-400 480-400t-56.5-23.5Zm240 0Q640-447 640-480t23.5-56.5Q687-560 720-560t56.5 23.5Q800-513 800-480t-23.5 56.5Q753-400 720-400t-56.5-23.5Zm-480-240Q160-687 160-720t23.5-56.5Q207-800 240-800t56.5 23.5Q320-753 320-720t-23.5 56.5Q273-640 240-640t-56.5-23.5Zm240 0Q400-687 400-720t23.5-56.5Q447-800 480-800t56.5 23.5Q560-753 560-720t-23.5 56.5Q513-640 480-640t-56.5-23.5Zm240 0Q640-687 640-720t23.5-56.5Q687-800 720-800t56.5 23.5Q800-753 800-720t-23.5 56.5Q753-640 720-640t-56.5-23.5Z"/></svg>
    Dashboard
  </a>
  <a class="nav-item {{ request()->is('admin/data') && request()->url() == url('admin/data') ? 'active' : '' }}" href="{{ url('admin/data') }}" id="menu-data">    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M555-435q-35-35-35-85t35-85q35-35 85-35t85 35q35 35 35 85t-35 85q-35 35-85 35t-85-35ZM400-160v-76q0-21 10-40t28-30q45-27 95.5-40.5T640-360q56 0 106.5 13.5T842-306q18 11 28 30t10 40v76H400Zm86-80h308q-35-20-74-30t-80-10q-41 0-80 10t-74 30Zm182.5-251.5Q680-503 680-520t-11.5-28.5Q657-560 640-560t-28.5 11.5Q600-537 600-520t11.5 28.5Q623-480 640-480t28.5-11.5ZM640-520Zm0 280ZM120-400v-80h320v80H120Zm0-320v-80h480v80H120Zm324 160H120v-80h360q-14 17-22.5 37T444-560Z"/></svg>
    PatientData
  </a>
  <a class="nav-item" href="{{ url('admin/data') }}#sec-validasi" id="menu-validasi" onclick="showSection('sec-validasi')">
   <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
    Validasi Pasien
  </a>
  <a class="nav-item {{ request()->is('admin/queue*') ? 'active' : '' }}" href="{{ url('admin/queue') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M538.5-138.5Q480-197 480-280t58.5-141.5Q597-480 680-480t141.5 58.5Q880-363 880-280t-58.5 141.5Q763-80 680-80t-141.5-58.5ZM747-185l28-28-75-75v-112h-40v128l87 87Zm-547 65q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v250q-18-13-38-22t-42-16v-212h-80v120H280v-120h-80v560h212q7 22 16 42t22 38H200Z"/></svg>
    Queue
  </a>
  <div class="nav-section">Payment & Invoice</div>
  <a class="nav-item {{ request()->is('admin/invoice*') ? 'active' : '' }}" href="{{ url('admin/invoice') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M560-440q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM280-320q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80h400q0-33 23.5-56.5T840-480v-160q-33 0-56.5-23.5T760-720H360q0 33-23.5 56.5T280-640v160q33 0 56.5 23.5T360-400Zm440 240H120q-33 0-56.5-23.5T40-240v-440h80v440h680v80ZM280-400v-320 320Z"/></svg>
    Payment
  </a>
  <div class="nav-section">Report</div>
  <a class="nav-item {{ request()->is('admin/report*') ? 'active' : '' }}" href="{{ url('admin/report') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M120-120v-80l80-80v160h-80Zm160 0v-240l80-80v320h-80Zm160 0v-320l80 81v239h-80Zm160 0v-239l80-80v319h-80Zm160 0v-400l80-80v480h-80ZM120-327v-113l280-280 160 160 280-280v113L560-447 400-607 120-327Z"/></svg>
    Report
  </a>

  {{-- LOGOUT --}}
  <div class="nav-section">Akun</div>
  <form method="POST" action="/logout">
    @csrf
    <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;text-align:left; padding-bottom:50px">
      <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
      <span>Logout</span>
    </button>
  </form>
</div>


<script>
function updateDateTime() {

    const now = new Date();

    const options = {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    };

    const tanggal = now.toLocaleDateString('id-ID', options);

    const jam = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    document.getElementById('dateTag').innerHTML =
        `${tanggal} • ${jam}`;
}

// jalankan pertama kali
updateDateTime();

// update tiap detik
setInterval(updateDateTime, 1000);
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const menuData = document.getElementById('menu-data');
    const menuValidasi = document.getElementById('menu-validasi');

    function setActiveMenu() {

        // reset
        menuData.classList.remove('active');
        menuValidasi.classList.remove('active');

        // hanya aktif jika berada di /admin/data
        const isDataPage = window.location.pathname.includes('/admin/data');

        if (isDataPage) {

            if (window.location.hash === '#sec-validasi') {
                menuValidasi.classList.add('active');
            } else {
                menuData.classList.add('active');
            }

        }
    }

    setActiveMenu();

    window.addEventListener('hashchange', setActiveMenu);

});
</script>
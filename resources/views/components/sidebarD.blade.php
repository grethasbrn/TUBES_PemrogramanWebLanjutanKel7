<!-- TOPBAR -->
<div class="topbar">
  <div class="logo">
    <div class="logo-icon">💊</div>
    <div class="logo-text">
      <div class="l1">Pharmbee</div>
    </div>
  </div>
  <div class="topbar-right">
    <div class="date-chip" id="dateChip"></div>
    <a class="notif-btn" href="{{ url('dokter/status') }}">
      <span>🔔</span>
      <span class="notif-badge" id="notifBadge"></span>
    </a>
    <div class="avatar-wrap">
      <div class="avatar">DT</div>
      <div class="avatar-info">
        <div class="avatar-name">dr. Tirta</div>
        <div class="avatar-role">Dokter</div>
      </div>
    </div>
  </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="nav-section">Menu</div>
  <a class="nav-item {{ request()->is('dokter/dashboard*') ? 'active' : '' }}" href="{{ url('dokter/dashboard') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M183.5-183.5Q160-207 160-240t23.5-56.5Q207-320 240-320t56.5 23.5Q320-273 320-240t-23.5 56.5Q273-160 240-160t-56.5-23.5Zm240 0Q400-207 400-240t23.5-56.5Q447-320 480-320t56.5 23.5Q560-273 560-240t-23.5 56.5Q513-160 480-160t-56.5-23.5Zm240 0Q640-207 640-240t23.5-56.5Q687-320 720-320t56.5 23.5Q800-273 800-240t-23.5 56.5Q753-160 720-160t-56.5-23.5Zm-480-240Q160-447 160-480t23.5-56.5Q207-560 240-560t56.5 23.5Q320-513 320-480t-23.5 56.5Q273-400 240-400t-56.5-23.5Zm240 0Q400-447 400-480t23.5-56.5Q447-560 480-560t56.5 23.5Q560-513 560-480t-23.5 56.5Q513-400 480-400t-56.5-23.5Zm240 0Q640-447 640-480t23.5-56.5Q687-560 720-560t56.5 23.5Q800-513 800-480t-23.5 56.5Q753-400 720-400t-56.5-23.5Zm-480-240Q160-687 160-720t23.5-56.5Q207-800 240-800t56.5 23.5Q320-753 320-720t-23.5 56.5Q273-640 240-640t-56.5-23.5Zm240 0Q400-687 400-720t23.5-56.5Q447-800 480-800t56.5 23.5Q560-753 560-720t-23.5 56.5Q513-640 480-640t-56.5-23.5Zm240 0Q640-687 640-720t23.5-56.5Q687-800 720-800t56.5 23.5Q800-753 800-720t-23.5 56.5Q753-640 720-640t-56.5-23.5Z"/></svg>
    Dashboard
  </a>
  <a class="nav-item {{ request()->is('dokter/data*') ? 'active' : '' }}" href="{{ url('dokter/data') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M555-435q-35-35-35-85t35-85q35-35 85-35t85 35q35 35 35 85t-35 85q-35 35-85 35t-85-35ZM400-160v-76q0-21 10-40t28-30q45-27 95.5-40.5T640-360q56 0 106.5 13.5T842-306q18 11 28 30t10 40v76H400Zm86-80h308q-35-20-74-30t-80-10q-41 0-80 10t-74 30Zm182.5-251.5Q680-503 680-520t-11.5-28.5Q657-560 640-560t-28.5 11.5Q600-537 600-520t11.5 28.5Q623-480 640-480t28.5-11.5ZM640-520Zm0 280ZM120-400v-80h320v80H120Zm0-320v-80h480v80H120Zm324 160H120v-80h360q-14 17-22.5 37T444-560Z"/></svg>
    Patient Data
  </a>
  <div class="nav-section">Prescription</div>
  <a class="nav-item {{ request()->is('dokter/prescription*') ? 'active' : '' }}" href="{{ url('dokter/prescription') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="m678-134 46-46-64-64-46 46q-14 14-14 32t14 32q14 14 32 14t32-14Zm102-102 46-46q14-14 14-32t-14-32q-14-14-32-14t-32 14l-46 46 64 64ZM735-77q-37 37-89 37t-89-37q-37-37-37-89t37-89l148-148q37-37 89-37t89 37q37 37 37 89t-37 89L735-77ZM200-200v-560 560Zm0 80q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h168q13-36 43.5-58t68.5-22q38 0 68.5 22t43.5 58h168q33 0 56.5 23.5T840-760v245q-20-5-40-5t-40 3v-243H200v560h243q-3 20-3 40t5 40H200Zm301.5-678.5Q510-807 510-820t-8.5-21.5Q493-850 480-850t-21.5 8.5Q450-833 450-820t8.5 21.5Q467-790 480-790t21.5-8.5ZM280-600v-80h400v80H280Zm0 160v-80h400v34q-8 5-15.5 11.5T649-460l-20 20H280Zm0 160v-80h269l-49 49q-8 8-14.5 15.5T474-280H280Z"/></svg>
    Prescription
  </a>
  <a class="nav-item {{ request()->is('dokter/status*') ? 'active' : '' }}" href="{{ url('dokter/status') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M480-80q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80Zm0-420ZM160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v13q-11 22-16 45t-4 47q-10-2-19.5-3.5T480-720q-66 0-113 47t-47 113v280h320v-257q18 8 38.5 12.5T720-520v240h80v8₀H16₀Zm475-435q-35-35-35-85t35-85q35-35 85-35t85 35q35 35 35 85t-35 85q-35 35-85 3 five" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
    Status
  </a>
  <div class="nav-section">Reports</div>
  <a class="nav-item {{ request()->is('dokter/history*') ? 'active' : '' }}" href="{{ url('dokter/history') }}">
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M480-80q-155 0-269-103T82-440h81q15 121 105.5 200.5T480-160q134 0 227-93t93-227q0-134-93-227t-227-93q-86 0-159.5 42.5T204-640h116v80H88q29-140 139-230t253-90q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm112-232L440-464v-216h80v184l128 128-56 56Z"/></svg>
    History
  </a>
</div>



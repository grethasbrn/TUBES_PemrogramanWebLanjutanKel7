<!-- TOPBAR -->
<div class="topbar">
  <div class="logo">
    <div class="logo-icon">🏥</div>
    <div class="logo-text">
      <div class="l1">Pharmbee
      </div>
    </div>
  </div>
  <div class="topbar-right">
    <div class="date-chip" id="dateChip">
      <div id="dateTag"></div>
    </div>
    <button class="notif-btn" onclick="showSection('invoice')" title="Invoice masuk">
      🔔<span class="notif-badge" id="notifBadge"></span>
    </button>
    <div class="avatar-wrap">
      <div class="avatar">AD</div>
      <div class="avatar-info">
        <div class="avatar-name">Admin RSU</div>
        <div class="avatar-role">Administrator</div>
      </div>
    </div>
  </div>
</div>

<!-- SIDEBAR -->
 <div class="sidebar">
  <div class="nav-section">Menu</div>
    <a class="nav-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Dashboard
    </a>
    <a class="nav-item {{ request()->is('admin/data*') ? 'active' : '' }}" href="{{ url('admin/data') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      PatientData
    </a>
    <a class="nav-item {{ request()->is('admin/queue*') ? 'active' : '' }}" href="{{ url('admin/queue') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Queue
    </a>
  <div class="nav-section">Patient</div>
    <a class="nav-item {{ request()->is('admin/invoice*') ? 'active' : '' }}" href="{{ url('admin/invoice') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Invoice
    </a>
    <a class="nav-item {{ request()->is('admin/payment*') ? 'active' : '' }}" href="{{ url('admin/payment') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Payment
    </a>
  <div class="nav-section">Report</div>
    <a class="nav-item {{ request()->is('admin/report*') ? 'active' : '' }}" href="{{ url('admin/report') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Report
    </a>
  
  </div>
</div>

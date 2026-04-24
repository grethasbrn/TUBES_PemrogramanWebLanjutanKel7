<!-- SIDEBAR -->
 <div class="sidebar">
  <div class="nav-group">
    <div class="nav-label">Kasir</div>
    <a class="nav-item {{ request()->is('admin/dashboard*') ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Dashboard
    </a>
    <a class="nav-item {{ request()->is('admin/payment*') ? 'active' : '' }}" href="{{ url('admin/payment') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Payment
    </a>
  </div>
  <div class="nav-group">
    <div class="nav-label">Administration</div>
    <a class="nav-item {{ request()->is('admin/add*') ? 'active' : '' }}" href="{{ url('admin/add') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Add Patient
    </a>
    <a class="nav-item {{ request()->is('admin/data*') ? 'active' : '' }}" href="{{ url('admin/data') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Patient Data
    </a>
  </div>
  <div class="nav-group">
    <div class="nav-label">Document</div>
    <a class="nav-item {{ request()->is('admin/invoice*') ? 'active' : '' }}" href="{{ url('admin/invoice') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Invoice History
    </a>
    <a class="nav-item {{ request()->is('admin/report*') ? 'active' : '' }}" href="{{ url('admin/report') }}">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/><rect x="9" y="9" width="5.5" height="5.5" rx="1.5" fill="currentColor" opacity=".5"/></svg>
      Report
    </a>
  </div>
  <div class="sidebar-footer">
    <div class="shift-card">
      <div class="shift-label">Shift Aktif</div>
      <div class="shift-time" id="shiftTime">08:00</div>
      <div class="shift-name">Admin RS · Pagi</div>
    </div>
  </div>
</div>

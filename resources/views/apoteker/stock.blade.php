@extends('layouts.apoteker')

@section('content')

<div class="page-section active" id="sec-stock">
  <div class="page-header">
    <div>
      <div class="page-title">Stok Obat</div>
      <div class="page-sub">Manajemen inventaris per batch</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalTambahBatch')">+ Input Batch Baru</button>
  </div>

  <div class="search-row">
    <input type="text" class="search-input" placeholder="Cari nama obat, batch..." id="stockSearch" oninput="renderStockTable()">
    <div class="custom-dropdown">
      <div class="dropdown-selected">
          Semua Tipe
          <span>▼</span>
      </div>
      <div class="dropdown-options">
          <div class="dropdown-option" data-value="">Semua Tipe</div>
          <div class="dropdown-option" data-value="Tablet">Tablet</div>
          <div class="dropdown-option" data-value="Sirup">Sirup</div>
          <div class="dropdown-option" data-value="Kapsul">Kapsul</div>
          <div class="dropdown-option" data-value="Injeksi">Injeksi</div>
          <div class="dropdown-option" data-value="Salep">Salep</div>
      </div>
      <input type="hidden" id="stockFilterTipe" value="">
    </div>

    <div class="custom-dropdown">
      <div class="dropdown-selected">
          Semua Status
          <span>▼</span>
      </div>
      <div class="dropdown-options">
          <div class="dropdown-option" data-value="">Semua Status</div>
          <div class="dropdown-option" data-value="aman">Aman</div>
          <div class="dropdown-option" data-value="kritis">Kritis (&lt;10)</div>
          <div class="dropdown-option" data-value="expired">Expired</div>
          <div class="dropdown-option" data-value="exp-soon">Exp &lt; 90 hari</div>
      </div>
      <input type="hidden" id="stockFilterStatus" value="">
    </div>
  </div>

  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Nama Obat</th>
          <th>Tipe</th>
          <th>No. Batch</th>
          <th>Jumlah</th>
          <th>Harga Satuan</th>
          <th>Tgl Expired</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="stockTableBody"></tbody>
    </table>
  </div>
</div>

@include('apoteker.Batch.index')

<script>
    const stockData = @json($stockData);

    function getStatusLabel(status) {
        const map = {
            'aman'    : '<span class="badge badge-success">Aman</span>',
            'kritis'  : '<span class="badge badge-warning">Kritis</span>',
            'expired' : '<span class="badge badge-danger">Expired</span>',
            'exp-soon': '<span class="badge badge-warning">Exp &lt; 90 hari</span>',
        };
        return map[status] || status;
    }

    function renderStockTable() {
        const search       = document.getElementById('stockSearch').value.toLowerCase();
        const filterTipe   = document.getElementById('stockFilterTipe').value;
        const filterStatus = document.getElementById('stockFilterStatus').value;

        const filtered = stockData.filter(b => {
            const matchSearch = b.nama_obat.toLowerCase().includes(search) ||
                                b.no_batch.toLowerCase().includes(search);
            const matchTipe   = filterTipe   ? b.tipe   === filterTipe   : true;
            const matchStatus = filterStatus ? b.status === filterStatus : true;
            return matchSearch && matchTipe && matchStatus;
        });

        const tbody = document.getElementById('stockTableBody');

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:2rem;color:#aaa;">Belum ada data batch.</td></tr>`;
            return;
        }

        tbody.innerHTML = filtered.map(b => `
            <tr>
                <td>${b.nama_obat}</td>
                <td>${b.tipe}</td>
                <td>${b.no_batch}</td>
                <td>${b.jumlah}</td>
                <td>Rp ${Number(b.harga).toLocaleString('id-ID')}</td>
                <td>${b.tgl_expired ?? '-'}</td>
                <td>${getStatusLabel(b.status)}</td>
                <td>
                    <form action="/apoteker/batch/${b.id}" method="POST"
                          onsubmit="return confirm('Hapus batch ini?')">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" style="color:#fff;background:#A63D33;
                        border-radius:5px;cursor:pointer; padding:6px;border:none">Hapus</button>
                    </form>
                </td>
            </tr>
        `).join('');
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderStockTable();

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('add') === 'true') {
            openModal('modalTambahBatch');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });

    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('open');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('open');
    }

    document.querySelectorAll('.custom-dropdown').forEach(dropdown => {

    const selected = dropdown.querySelector('.dropdown-selected');
    const options = dropdown.querySelector('.dropdown-options');
    const hidden = dropdown.querySelector('input[type="hidden"]');

    selected.addEventListener('click', (e) => {

        e.stopPropagation();

        document.querySelectorAll('.dropdown-options').forEach(opt => {
            if (opt !== options) opt.classList.remove('show');
        });

        options.classList.toggle('show');
    });

    dropdown.querySelectorAll('.dropdown-option').forEach(option => {

        option.addEventListener('click', () => {

            selected.innerHTML =
                option.textContent + '<span>▼</span>';

            hidden.value = option.dataset.value;

            options.classList.remove('show');

            renderStockTable();
        });

    });

});

document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-options').forEach(opt => {
        opt.classList.remove('show');
    });
});
</script>
@endsection
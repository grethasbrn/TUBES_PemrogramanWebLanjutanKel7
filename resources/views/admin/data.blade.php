@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-pasien">
  <div class="page-header">
    <div>
      <div class="page-title">Data Pasien</div>
      <div class="page-sub">Input dan kelola data pasien yang mendaftar</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalTambahPasien')">+ Daftar Pasien Baru</button>
  </div>

  <div class="search-row">
    <input type="text" class="search-input" placeholder="Cari nama, NIK, atau No. RM..." id="srchPasien">
  </div>

  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>NIK</th>
          <th>Tgl Lahir</th>
          <th>Jenis</th>
          <th>Poli</th>
        </tr>
      </thead>
      <tbody id="tblPasienBody"></tbody>
    </table>
  </div>
</div>

<!-- ================= MODAL ================= -->
<div id="modalTambahPasien" class="modal">
  <div class="modal-content">
    <h3>Tambah Pasien</h3>

    <form id="formPasien">
      @csrf

      <input type="text" id="no_rm" placeholder="No RM" required>
      <input type="text" id="nama" placeholder="Nama Pasien" required>
      <input type="text" id="nik" placeholder="NIK" required>
      <input type="date" id="tgl_lahir" required>

      <select id="jenis">
        <option value="BPJS">BPJS</option>
        <option value="Mandiri">Mandiri</option>
      </select>

      <select id="poli">
        <option value="Umum">Umum</option>
        <option value="Anak">Anak</option>
        <option value="Gigi">Gigi</option>
      </select>

      <button type="submit" class="btn btn-primary">Simpan</button>
      <button type="button" onclick="closeModal()">Batal</button>
    </form>
  </div>
</div>

@endsection


@section('scripts')
<script>
// ================= MODAL =================
function openModal(id){
    document.getElementById(id).style.display = 'flex';
}

function closeModal(){
    document.getElementById('modalTambahPasien').style.display = 'none';
}

// ================= LOAD DATA =================
async function loadPasien(){
    let res = await fetch('/pasien');
    let data = await res.json();

    let tbody = document.getElementById('tblPasienBody');
    tbody.innerHTML = '';

    data.forEach(p => {
        tbody.innerHTML += `
        <tr>
            <td>${p.no_rm}</td>
            <td>${p.nama}</td>
            <td>${p.nik}</td>
            <td>${p.tgl_lahir}</td>
            <td>${p.jenis}</td>
            <td>${p.poli}</td>
        </tr>`;
    });
}

// ================= SUBMIT =================
document.getElementById('formPasien').addEventListener('submit', async function(e){
    e.preventDefault();

    let data = {
        no_rm: document.getElementById('no_rm').value,
        nama: document.getElementById('nama').value,
        nik: document.getElementById('nik').value,
        tgl_lahir: document.getElementById('tgl_lahir').value,
        jenis: document.getElementById('jenis').value,
        poli: document.getElementById('poli').value
    };

    await fetch('/pasien', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    });

    alert("Data berhasil disimpan");
    closeModal();
    this.reset();
    loadPasien();
});

// load awal
loadPasien();
</script>

<style>
/* ===== MODAL TAMBAHAN (TIDAK MERUSAK CSS KAMU) ===== */
.modal{
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.4);
  justify-content:center;
  align-items:center;
  z-index:999;
}

.modal-content{
  background:#fff;
  padding:20px;
  border-radius:12px;
  width:320px;
  display:flex;
  flex-direction:column;
  gap:10px;
}

.modal-content input,
.modal-content select{
  padding:8px;
  border:1px solid #ddd;
  border-radius:8px;
}
</style>
@endsection
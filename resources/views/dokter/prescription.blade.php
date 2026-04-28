@extends('layouts.dokter')

@section('content')

<div class="page-section active" id="sec-buat-resep">
  <div class="page-header">
    <div>
      <div class="page-title">Buat Resep</div>
      <div class="page-sub">Tulis resep obat untuk pasien</div>
    </div>
  </div>

  <div class="grid2" style="align-items:start">
    <!-- Form Resep -->
    <div class="card" id="formResepCard">
      <!-- Step 1: Pilih Pasien -->
      <div id="step1">
        <div class="card-title">① Pilih Pasien</div>
        <div class="search-row">
          <input type="text" class="search-input" placeholder="Cari pasien..." id="resepPasienSearch" oninput="renderResepPasienList()">
        </div>
        <div id="resepPasienList" style="max-height:300px;overflow-y:auto"></div>
      </div>

      <!-- Step 2: Form Resep -->
      <div id="step2" style="display:none">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
          <div class="card-title" style="margin-bottom:0">② Isi Resep</div>
          <button class="btn btn-sm" onclick="resetResepForm()">← Ganti Pasien</button>
        </div>

        <!-- Pasien terpilih info -->
        <div id="selectedPasienInfo" style="background:var(--blue-light);border-radius:10px;padding:12px 14px;margin-bottom:16px;border:1px solid var(--blue-mid)"></div>

        <!-- Jenis pembayaran indicator -->
        <div id="bayarInfo" style="margin-bottom:14px"></div>

        <div class="modal-section">Diagnosa & Keluhan</div>
        <div class="fg">
          <label>Diagnosa</label>
          <input type="text" id="rDiagnosa" placeholder="Contoh: Hipertensi Grade I, Diabetes Mellitus Tipe 2...">
        </div>
        <div class="fr">
          <div class="fg">
            <label>Keluhan Utama</label>
            <input type="text" id="rKeluhan" placeholder="Keluhan pasien saat ini">
          </div>
          <div class="fg">
            <label>Tekanan Darah</label>
            <input type="text" id="rTD" placeholder="Contoh: 130/80 mmHg">
          </div>
        </div>

        <div class="modal-section">Obat yang Diresepkan</div>
        <div id="obatListForm"></div>
        <button class="btn btn-sm" style="margin-bottom:14px;width:100%" onclick="openCatalog()">＋ Tambah Obat</button>

        <div class="modal-section">Catatan untuk Apoteker</div>
        <div class="fg">
          <label>Catatan / Instruksi Khusus</label>
          <textarea id="rCatatanApoteker" rows="3" placeholder="Contoh: Pasien alergi penisilin. Jika stok amoxicillin habis, gunakan eritromisin. Mohon hubungi jika ada substitusi..."></textarea>
        </div>
        <div class="fr">
          <div class="fg">
            <label>Kontrol Ulang</label>
            <input type="date" id="rKontrol">
          </div>
          <div class="fg">
            <label>Cara Konsumsi</label>
            <select id="rCara">
              <option>Sesuai dosis tertera</option>
              <option>Sesudah makan</option>
              <option>Sebelum makan</option>
              <option>Bersama makan</option>
            </select>
          </div>
        </div>

        <div style="display:flex;gap:8px;margin-top:6px">
          <button class="btn btn-sm" onclick="simpanDraft()">💾 Simpan Draft</button>
          <button class="btn btn-primary" style="flex:1" onclick="kirimResep()">📤 Kirim ke Apoteker</button>
        </div>
      </div>
    </div>

    <!-- Preview Resep -->
    <div id="resepPreviewPanel">
      <div class="card" style="text-align:center;padding:40px 20px;color:var(--text3)">
        <div style="font-size:32px;margin-bottom:10px">✍️</div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:16px">Pilih pasien untuk mulai menulis resep</div>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modalCatalog">
  <div class="modal" style="max-width:500px">
    <button class="modal-close" onclick="closeModal('modalCatalog')">✕</button>
    <h3 id="catalogTitle">Katalog Obat</h3>
    <div id="catalogDesc" style="font-size:13px;margin-bottom:14px;padding:10px 12px;border-radius:8px"></div>
    <div class="fg">
      <label>Cari Obat</label>
      <input type="text" id="catalogSearch" oninput="renderCatalog()" placeholder="Ketik nama obat...">
    </div>
    <div class="obat-catalog" id="catalogList"></div>
    <div id="selectedObatForm" style="display:none">
      <div class="modal-section">Detail Obat Dipilih</div>
      <div id="selectedObatName" style="font-weight:600;font-family:'Cormorant Garamond',serif;font-size:16px;margin-bottom:10px"></div>
      <div class="fr">
        <div class="fg">
          <label>Dosis</label>
          <input type="text" id="catalogDosis" placeholder="Contoh: 3x1">
        </div>
        <div class="fg">
          <label>Durasi</label>
          <input type="text" id="catalogDurasi" placeholder="Contoh: 5 hari">
        </div>
      </div>
      <div class="fg">
        <label>Instruksi Konsumsi</label>
        <select id="catalogInstruksi">
          <option>Sesudah makan</option>
          <option>Sebelum makan</option>
          <option>Saat makan</option>
          <option>Sesuai kebutuhan</option>
          <option>Malam sebelum tidur</option>
        </select>
      </div>
      <div class="fg">
        <label>Catatan Obat Ini</label>
        <input type="text" id="catalogCatatan" placeholder="Opsional...">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeModal('modalCatalog')">Batal</button>
      <button class="btn btn-primary" id="catalogAddBtn" onclick="addObatFromCatalog()" disabled>＋ Tambah ke Resep</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
// Data pasien dari controller
let pasienData = @json($pasienJson ?? []);
let resepData = [];

// Inisialisasi saat halaman load
document.addEventListener('DOMContentLoaded', function () {
    renderResepPasienList();
});
</script>
@endsection
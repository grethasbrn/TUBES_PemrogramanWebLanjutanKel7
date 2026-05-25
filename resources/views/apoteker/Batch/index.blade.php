<style>
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:16px;padding:28px;width:100%;max-width:560px;max-height:88vh;overflow-y:auto;position:relative;box-shadow:0 10px 25px rgba(0,0,0,0.2)}
.modal h3{font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:600;margin-bottom:18px;color:var(--text)}
.modal-close{float:right;background:none;border:none;font-size:20px;cursor:pointer;color:var(--text3);margin-top:-4px}
.fg{margin-bottom:14px}
.fg label{display:block;font-size:11px;color:var(--text2);margin-bottom:5px;font-weight:500;text-transform:uppercase;letter-spacing:.05em}
.fg input,.fg select,.fg textarea{width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--cream3);background:var(--cream);font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--purple)}
.fr{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.modal-footer{display:flex;justify-content:flex-end;gap:8px;margin-top:18px;padding-top:14px;border-top:1px solid var(--cream3)}
/* info box kuning kecil */
.info-box{background:#fffbe6;border:1px solid #ffe58f;border-radius:8px;padding:8px 12px;font-size:12px;color:#7c6000;margin-top:5px; margin-bottom:8px}
</style>

<div class="modal-overlay" id="modalTambahBatch" onclick="closeModal('modalTambahBatch')">
  <div class="modal" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="closeModal('modalTambahBatch')">✕</button>
    <h3>Input Batch Obat Baru</h3>

    <form action="{{ route('batch.store') }}" method="POST">
        @csrf

        <div class="fg">
          <label>Nama Obat</label>
          <input type="text" name="nama_obat" placeholder="Contoh: Paracetamol 500mg" required>
        </div>

        <div class="fr">
          <div class="fg">
            <label>Tipe Obat</label>
            <select name="tipe">
              <option value="Tablet">Tablet</option>
              <option value="Sirup">Sirup</option>
              <option value="Kapsul">Kapsul</option>
              <option value="Injeksi">Injeksi</option>
              <option value="Salep">Salep</option>
            </select>
          </div>
          <div class="fg">
            <label>No. Batch</label>
            <input type="text" name="no_batch" placeholder="Contoh: BT-2026-001" required>
          </div>
        </div>

        {{-- ✅ BARU: Kategori Obat --}}
        <div class="fg">
          <label>Kategori Obat</label>
          <select name="kategori" id="inputKategori" onchange="handleKategoriChange()" required>
            <option value="mandiri">Mandiri (hanya pasien umum)</option>
            <option value="bpjs">BPJS (bisa untuk semua pasien)</option>
          </select>
        </div>

        <div class="fr">
          <div class="fg">
            <label>Jumlah (unit)</label>
            <input type="number" name="jumlah" placeholder="0" min="1" required>
          </div>
          <div class="fg">
            <label>Harga Mandiri (Rp)</label>
            <input type="number" name="harga" id="inputHargaMandiri"
                   placeholder="Harga pasien umum" min="0" required>
          </div>
        </div>

        {{-- ✅ BARU: Harga BPJS otomatis 0, tersembunyi --}}
        <input type="hidden" name="harga_bpjs" id="inputHargaBpjs" value="0">

        {{-- Info box yang muncul saat kategori BPJS dipilih --}}
        <div class="info-box" id="infoBpjs" style="display:none">
          ℹ️ Obat BPJS: harga untuk pasien BPJS = <strong>Rp 0</strong> (ditanggung BPJS).
          Isi harga mandiri untuk pasien umum yang membeli obat ini.
        </div>

        {{-- Info box yang muncul saat kategori Mandiri dipilih --}}
        <div class="info-box" id="infoMandiri" style="display:block">
          ℹ️ Obat Mandiri: hanya bisa diberikan ke pasien umum, tidak bisa untuk pasien BPJS.
        </div>

        <div class="fr">
          <div class="fg">
            <label>Tanggal Kadaluarsa</label>
            <input type="date" name="tgl_expired" required>
          </div>
          <div class="fg">
            <label>Tanggal Masuk</label>
            <input type="date" name="tgl_masuk" value="{{ date('Y-m-d') }}">
          </div>
        </div>

        <div class="fg">
          <label>Supplier / Keterangan</label>
          <input type="text" name="supplier" placeholder="Nama supplier">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn" onclick="closeModal('modalTambahBatch')">Batal</button>
          <button type="submit" class="btn btn-primary">💾 Simpan Batch</button>
        </div>
    </form>
  </div>
</div>

<script>
function handleKategoriChange() {
    const kategori    = document.getElementById('inputKategori').value;
    const infoBpjs    = document.getElementById('infoBpjs');
    const infoMandiri = document.getElementById('infoMandiri');
    const hargaBpjs   = document.getElementById('inputHargaBpjs');
    const hargaMandiri = document.getElementById('inputHargaMandiri');

    if (kategori === 'bpjs') {
        // Obat BPJS: harga_bpjs selalu 0, harga mandiri tetap diisi
        hargaBpjs.value = 0;
        hargaMandiri.placeholder = 'Harga jika dibeli pasien umum';
        infoBpjs.style.display    = 'block';
        infoMandiri.style.display = 'none';
    } else {
        // Obat Mandiri: harga_bpjs tidak relevan (null)
        hargaBpjs.value = '';
        hargaMandiri.placeholder = 'Harga pasien umum';
        infoBpjs.style.display    = 'none';
        infoMandiri.style.display = 'block';
    }
}
</script>
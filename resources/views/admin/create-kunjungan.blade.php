@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-create-pasien">
  <div class="page-header">
    <div>
      <div class="page-title">Daftar Kunjungan Baru</div>
      <div class="page-sub">Isi formulir untuk mendaftarkan kunjungan pasien</div>
    </div>
    <a href="{{ route('pasien.index') }}" class="btn btn-secondary">← Kembali</a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:1.2rem;padding:.8rem 1rem;background:#fdecea;border-left:4px solid #e53935;border-radius:6px;color:#b71c1c;font-size:.9rem;">
      <strong>Terdapat kesalahan input:</strong>
      <ul style="margin:.4rem 0 0 1.2rem;padding:0;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- ALERT: Pasien Lama Terdeteksi --}}
  <div id="existingPatientAlert" class="alert-existing-patient" style="display:none;">
    <strong>✅ Pasien dengan NIK ini sudah terdaftar!</strong><br>
    Data otomatis terisi dari pendaftaran sebelumnya. Kunjungan baru akan dibuat tanpa menghapus riwayat lama.
  </div>

  <div class="form-card">
    {{-- Action sekarang ke kunjungan.store --}}
    <form action="{{ route('pasien.store') }}" method="POST" id="kunjunganForm">
      @csrf

      {{-- ===== CEK NIK DULU ===== --}}
      <div class="form-section-title">Identifikasi Pasien</div>
      <div class="form-grid">

        <div class="form-group">
          <label for="nik">NIK <span class="required">*</span></label>
          <input type="text" id="nik" name="nik"
            class="form-input @error('nik') is-error @enderror"
            placeholder="16 digit NIK" maxlength="16"
            value="{{ old('nik') }}"
            autocomplete="off">
          <small class="form-hint" style="font-size:11px;color:#A8998A;">
            Ketik NIK lalu pindah ke field berikutnya — sistem akan cek apakah pasien sudah terdaftar.
          </small>
          @error('nik')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="no_rm">No. Rekam Medis</label>
          <input type="text" id="no_rm" name="no_rm"
            class="form-input"
            value="{{ old('no_rm') }}"
            readonly
            style="background:#f0ebe6;color:#A8998A;cursor:not-allowed;">
          <small class="form-hint" style="font-size:11px;color:#A8998A;">
            Otomatis terisi. Pasien lama: RM lama. Pasien baru: RM baru dibuat.
          </small>
        </div>

      </div>

      {{-- ===== DATA PRIBADI (muncul setelah NIK dicek) ===== --}}
      <div id="sectionDataPribadi">
        <div class="form-section-title">Data Pribadi</div>
        <div class="form-grid">

          <div class="form-group">
            <label for="nama">Nama Lengkap <span class="required">*</span></label>
            <input type="text" id="nama" name="nama"
              class="form-input @error('nama') is-error @enderror"
              placeholder="Nama sesuai KTP"
              value="{{ old('nama') }}">
            @error('nama')<span class="form-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="tgl_lahir">Tanggal Lahir <span class="required">*</span></label>
            <input type="date" id="tgl_lahir" name="tgl_lahir"
              class="form-input @error('tgl_lahir') is-error @enderror"
              value="{{ old('tgl_lahir') }}">
            @error('tgl_lahir')<span class="form-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
            <select id="jenis_kelamin" name="jenis_kelamin"
              class="form-input @error('jenis_kelamin') is-error @enderror">
              <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>— Pilih —</option>
              <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
              <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin')<span class="form-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="no_telepon">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon"
              class="form-input @error('no_telepon') is-error @enderror"
              placeholder="08xx-xxxx-xxxx"
              value="{{ old('no_telepon') }}">
            @error('no_telepon')<span class="form-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group" style="grid-column: 1 / -1;">
            <label for="alamat">Alamat Lengkap</label>
            <textarea id="alamat" name="alamat" rows="2"
              class="form-input @error('alamat') is-error @enderror"
              placeholder="Jl. ..., Kelurahan, Kecamatan, Kota">{{ old('alamat') }}</textarea>
            @error('alamat')<span class="form-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="pekerjaan">Pekerjaan</label>
            <input type="text" id="pekerjaan" name="pekerjaan"
              class="form-input @error('pekerjaan') is-error @enderror"
              placeholder="Pekerjaan pasien"
              value="{{ old('pekerjaan') }}">
            @error('pekerjaan')<span class="form-error">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="alergi">Alergi Obat</label>
            <input type="text" id="alergi" name="alergi"
              class="form-input @error('alergi') is-error @enderror"
              placeholder="Contoh: Penisilin (kosongkan jika tidak ada)"
              value="{{ old('alergi') }}">
            @error('alergi')<span class="form-error">{{ $message }}</span>@enderror
          </div>

        </div>
      </div>

      {{-- ===== JENIS PEMBAYARAN ===== --}}
      <div class="form-section-title">Jenis Pembayaran</div>
      <div class="form-grid">

        <div class="form-group">
          <label for="jenis">Jenis Pasien <span class="required">*</span></label>
          <select id="jenis" name="jenis"
            class="form-input @error('jenis') is-error @enderror">
            <option value="" disabled {{ old('jenis') ? '' : 'selected' }}>— Pilih —</option>
            <option value="BPJS"    {{ old('jenis') == 'BPJS'    ? 'selected' : '' }}>BPJS</option>
            <option value="Mandiri" {{ old('jenis') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
          </select>
          @error('jenis')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" id="field-bpjs" style="display:{{ old('jenis') == 'BPJS' ? 'flex' : 'none' }};">
          <label for="no_bpjs">No. BPJS <span class="required">*</span></label>
          <input type="text" id="no_bpjs" name="no_bpjs"
            class="form-input @error('no_bpjs') is-error @enderror"
            placeholder="Contoh: 0001234567890"
            value="{{ old('no_bpjs') }}">
          @error('no_bpjs')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      {{-- ===== TUJUAN KUNJUNGAN — POLI & DOKTER ===== --}}
      <div class="form-section-title">
        Tujuan Kunjungan
        <span style="font-weight:400;color:#C4B5A5;font-size:.75rem;">(per kunjungan ini)</span>
      </div>
      <div class="form-grid">

        {{-- Poli: dari daftar dokter aktif --}}
        <div class="form-group">
          <label for="poli_tujuan">Poli Tujuan <span class="required">*</span></label>
          <select id="poli_tujuan" name="poli_tujuan"
            class="form-input @error('poli_tujuan') is-error @enderror">
            <option value="" disabled {{ old('poli_tujuan') ? '' : 'selected' }}>— Pilih Poli —</option>
            @foreach($polis as $poli)
              <option value="{{ $poli }}" {{ old('poli_tujuan') == $poli ? 'selected' : '' }}>
                {{ $poli }}
              </option>
            @endforeach
          </select>
          @error('poli_tujuan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        {{-- Dokter: muncul setelah poli dipilih via AJAX --}}
        <div class="form-group">
          <label for="dokter_id">Dokter <span class="required">*</span></label>
          <select id="dokter_id" name="dokter_id"
            class="form-input @error('dokter_id') is-error @enderror"
            disabled>
            <option value="">— Pilih poli dulu —</option>
          </select>
          <small id="dokter-hint" class="form-hint" style="font-size:11px;color:#A8998A;">
            Pilih poli tujuan terlebih dahulu.
          </small>
          @error('dokter_id')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="jenis_kunjungan">Jenis Kunjungan</label>
          <select id="jenis_kunjungan" name="jenis_kunjungan"
            class="form-input @error('jenis_kunjungan') is-error @enderror">
            <option value="Rawat Jalan" {{ old('jenis_kunjungan') == 'Rawat Jalan' ? 'selected' : '' }}>Rawat Jalan</option>
            <option value="Rawat Inap"  {{ old('jenis_kunjungan') == 'Rawat Inap'  ? 'selected' : '' }}>Rawat Inap</option>
            <option value="UGD"         {{ old('jenis_kunjungan') == 'UGD'         ? 'selected' : '' }}>UGD</option>
          </select>
          @error('jenis_kunjungan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="keluhan">Keluhan Utama</label>
          <textarea id="keluhan" name="keluhan" rows="2"
            class="form-input @error('keluhan') is-error @enderror"
            placeholder="Keluhan yang dirasakan pasien...">{{ old('keluhan') }}</textarea>
          @error('keluhan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="riwayat_penyakit">Riwayat Penyakit</label>
          <textarea id="riwayat_penyakit" name="riwayat_penyakit" rows="2"
            class="form-input @error('riwayat_penyakit') is-error @enderror"
            placeholder="Contoh: Hipertensi sejak 2020...">{{ old('riwayat_penyakit') }}</textarea>
          @error('riwayat_penyakit')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      {{-- ===== DATA MEDIS (VITAL SIGN) ===== --}}
      <div class="form-section-title">
        Data Medis
        <span style="font-weight:400;color:#C4B5A5;font-size:.75rem;">(untuk Dokter)</span>
      </div>
      <div class="form-grid">

        <div class="form-group">
          <label for="berat_badan">Berat Badan (kg)</label>
          <input type="number" id="berat_badan" name="berat_badan"
            class="form-input @error('berat_badan') is-error @enderror"
            placeholder="Contoh: 65" step="0.1" min="1" max="300"
            value="{{ old('berat_badan') }}">
          @error('berat_badan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="tinggi_badan">Tinggi Badan (cm)</label>
          <input type="number" id="tinggi_badan" name="tinggi_badan"
            class="form-input @error('tinggi_badan') is-error @enderror"
            placeholder="Contoh: 165" step="0.1" min="1" max="300"
            value="{{ old('tinggi_badan') }}">
          @error('tinggi_badan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="tekanan_darah">Tekanan Darah</label>
          <input type="text" id="tekanan_darah" name="tekanan_darah"
            class="form-input @error('tekanan_darah') is-error @enderror"
            placeholder="Contoh: 120/80"
            value="{{ old('tekanan_darah') }}">
          @error('tekanan_darah')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      <div class="form-actions">
        <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">✔ Daftarkan Kunjungan</button>
      </div>

    </form>
  </div>
</div>

<script>
// ══════════════════════════════════════════════════════════
// 1. CEK NIK — AUTOFILL + DETEKSI PASIEN LAMA
// ══════════════════════════════════════════════════════════
const nikInput        = document.getElementById('nik');
const noRmInput       = document.getElementById('no_rm');
const existingAlert   = document.getElementById('existingPatientAlert');
let   isExistingPasien = false;

nikInput.addEventListener('blur', function () {
  const nik = this.value.trim();
  if (nik.length < 16) {
    existingAlert.style.display = 'none';
    isExistingPasien = false;
    return;
  }

  fetch('/admin/pasien/cek-nik/' + nik)
    .then(r => r.json())
    .then(data => {
      if (data.found) {
        isExistingPasien = true;
        existingAlert.style.display = 'block';

        // Autofill data master pasien (tidak bisa diubah — readonly visual)
        document.getElementById('no_rm').value          = data.data.no_rm        || '';
        document.getElementById('nama').value           = data.data.nama          || '';
        document.getElementById('tgl_lahir').value      = data.data.tgl_lahir     || '';
        document.getElementById('jenis_kelamin').value  = data.data.jenis_kelamin || '';
        document.getElementById('alamat').value         = data.data.alamat        || '';
        document.getElementById('no_telepon').value     = data.data.no_telepon    || '';
        document.getElementById('pekerjaan').value      = data.data.pekerjaan     || '';
        document.getElementById('alergi').value         = data.data.alergi        || '';

        // Jenis bayar
        const jenisSelect = document.getElementById('jenis');
        if (data.data.jenis) {
          jenisSelect.value = data.data.jenis;
          jenisSelect.dispatchEvent(new Event('change'));
        }
        document.getElementById('no_bpjs').value = data.data.no_bpjs || '';

        // Kosongkan field kunjungan (bisa diubah tiap kunjungan)
        document.getElementById('poli_tujuan').value      = '';
        document.getElementById('keluhan').value          = '';
        document.getElementById('riwayat_penyakit').value = '';
        document.getElementById('berat_badan').value      = '';
        document.getElementById('tinggi_badan').value     = '';
        document.getElementById('tekanan_darah').value    = '';
        resetDokterDropdown();

      } else {
        isExistingPasien = false;
        existingAlert.style.display = 'none';
        noRmInput.value = '';
      }
    })
    .catch(err => {
      console.error('Gagal cek NIK:', err);
      existingAlert.style.display = 'none';
    });
});

nikInput.addEventListener('focus', () => {
  existingAlert.style.display = 'none';
});

// ══════════════════════════════════════════════════════════
// 2. SHOW/HIDE FIELD BPJS
// ══════════════════════════════════════════════════════════
document.getElementById('jenis').addEventListener('change', function () {
  document.getElementById('field-bpjs').style.display =
    this.value === 'BPJS' ? 'flex' : 'none';
});

// ══════════════════════════════════════════════════════════
// 3. DROPDOWN DOKTER BY POLI — AJAX
// ══════════════════════════════════════════════════════════
const poliSelect   = document.getElementById('poli_tujuan');
const dokterSelect = document.getElementById('dokter_id');
const dokterHint   = document.getElementById('dokter-hint');

poliSelect.addEventListener('change', function () {
  const poli = this.value;
  if (!poli) { resetDokterDropdown(); return; }

  dokterSelect.disabled = true;
  dokterSelect.innerHTML = '<option value="">Memuat dokter...</option>';
  dokterHint.textContent = 'Mengambil data dokter...';

  fetch('/admin/kunjungan/dokter-by-poli?poli=' + encodeURIComponent(poli))
    .then(r => r.json())
    .then(dokters => {
      dokterSelect.innerHTML = '<option value="" disabled selected>— Pilih Dokter —</option>';

      if (dokters.length === 0) {
        dokterSelect.innerHTML += '<option value="" disabled>Tidak ada dokter aktif di poli ini</option>';
        dokterHint.textContent = 'Tidak ada dokter aktif di poli ini.';
        dokterSelect.disabled = true;
      } else {
        dokters.forEach(d => {
          const opt = document.createElement('option');
          opt.value       = d.id;
          opt.textContent = 'dr. ' + d.nama;
          dokterSelect.appendChild(opt);
        });
        dokterSelect.disabled = false;
        dokterHint.textContent = dokters.length + ' dokter tersedia.';
      }
    })
    .catch(() => {
      dokterHint.textContent = 'Gagal memuat dokter. Coba refresh.';
      dokterSelect.disabled  = true;
    });
});

function resetDokterDropdown() {
  dokterSelect.innerHTML  = '<option value="">— Pilih poli dulu —</option>';
  dokterSelect.disabled   = true;
  dokterHint.textContent  = 'Pilih poli tujuan terlebih dahulu.';
}
</script>

<style>
.form-card {
  background: #fff;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 12px rgba(0,0,0,.06);
  margin-top: 1.5rem;
}
.form-section-title {
  font-size: .78rem;
  font-weight: 700;
  color: #A8998A;
  text-transform: uppercase;
  letter-spacing: .08em;
  margin: 1.6rem 0 .8rem;
  padding-bottom: .4rem;
  border-bottom: 1px solid #f0ebe6;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.2rem 2rem;
}
.form-group { display: flex; flex-direction: column; gap: .4rem; }
.form-group label { font-size: .85rem; font-weight: 500; color: #555; letter-spacing: .02em; }
.required { color: #e53935; }
.form-input {
  padding: .6rem .9rem;
  border: 1.5px solid #ddd;
  border-radius: 8px;
  font-size: .95rem;
  font-family: inherit;
  color: #333;
  background: #fafafa;
  transition: border-color .2s, box-shadow .2s;
  outline: none;
  resize: vertical;
}
.form-input:focus { border-color: #7c5cbf; box-shadow: 0 0 0 3px rgba(124,92,191,.12); background: #fff; }
.form-input:disabled { background: #f5f5f5; color: #aaa; cursor: not-allowed; }
.form-input.is-error { border-color: #e53935; }
.form-error { font-size: .8rem; color: #e53935; }
.form-hint  { font-size: 11px; color: #A8998A; }
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: .8rem;
  margin-top: 2rem;
  padding-top: 1.4rem;
  border-top: 1px solid #eee;
}
.alert-existing-patient {
  background: #d4edda;
  border: 2px solid #28a745;
  border-radius: 8px;
  padding: 12px 20px;
  margin-bottom: 1.2rem;
  color: #155724;
  font-weight: 500;
}
@media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
</style>

@endsection
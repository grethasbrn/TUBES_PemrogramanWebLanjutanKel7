@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-create-pasien">
  <div class="page-header">
    <div>
      <div class="page-title">Daftar Pasien Baru</div>
      <div class="page-sub">Isi formulir data pasien yang akan mendaftar</div>
    </div>
    <a href="{{ route('pasien.index') }}" class="btn btn-secondary">← Kembali</a>
  </div>

  {{-- ALERT DUPLIKAT NIK --}}
  @if(session('duplicate_error'))
    <div class="alert-duplicate">
      <strong>⚠️ {{ session('duplicate_error') }}</strong>
    </div>
  @endif

  {{-- ALERT DETEKSI PASIEN LAMA --}}
  <div id="existingPatientAlert" class="alert-existing-patient" style="display: none;">
    <strong>✅ Pasien dengan NIK ini sudah terdaftar!</strong><br>
    Data otomatis terisi dari pendaftaran sebelumnya. Klik "Daftarkan Pasien" untuk mencatat kunjungan baru.
  </div>

  @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:1.2rem; padding:.8rem 1rem; background:#fdecea; border-left:4px solid #e53935; border-radius:6px; color:#b71c1c; font-size:.9rem;">
      <strong>Terdapat kesalahan input:</strong>
      <ul style="margin:.4rem 0 0 1.2rem; padding:0;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="form-card">
    <form action="{{ route('pasien.store') }}" method="POST" id="patientForm">
      @csrf

      {{-- ===== DATA PRIBADI ===== --}}
      <div class="form-section-title">Data Pribadi</div>
      <div class="form-grid">

        <div class="form-group">
          <label for="no_rm">No. Rekam Medis <span class="required">*</span></label>
          <input type="text" id="no_rm" name="no_rm"
            class="form-input @error('no_rm') is-error @enderror"
            value="{{ old('no_rm', $noRm) }}"
            readonly
            style="background:#f0ebe6;color:#A8998A;cursor:not-allowed;">
          <small class="form-hint" style="font-size:11px;color:#A8998A;">Untuk pasien baru akan dibuatkan RM. Pasien lama akan menggunakan RM yang sudah ada.</small>
          @error('no_rm')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="nama">Nama Lengkap <span class="required">*</span></label>
          <input type="text" id="nama" name="nama"
            class="form-input @error('nama') is-error @enderror"
            placeholder="Nama sesuai KTP"
            value="{{ old('nama', session('duplicate_pasien')->nama ?? '') }}">
          @error('nama')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="nik">NIK <span class="required">*</span></label>
          <input type="text" id="nik" name="nik"
            class="form-input @error('nik') is-error @enderror"
            placeholder="16 digit NIK" maxlength="16"
            value="{{ old('nik', session('duplicate_pasien')->nik ?? '') }}"
            autocomplete="off">
          <small class="form-hint" style="font-size:11px;color:#A8998A;">Setelah NIK diisi, sistem akan otomatis mendeteksi apakah pasien sudah pernah daftar</small>
          @error('nik')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="tgl_lahir">Tanggal Lahir <span class="required">*</span></label>
          <input type="date" id="tgl_lahir" name="tgl_lahir"
            class="form-input @error('tgl_lahir') is-error @enderror"
            value="{{ old('tgl_lahir', session('duplicate_pasien')->tgl_lahir ?? '') }}">
          @error('tgl_lahir')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
          <select id="jenis_kelamin" name="jenis_kelamin"
            class="form-input @error('jenis_kelamin') is-error @enderror">
            <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>— Pilih —</option>
            <option value="L" {{ old('jenis_kelamin', session('duplicate_pasien')->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin', session('duplicate_pasien')->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
          </select>
          @error('jenis_kelamin')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="alamat">Alamat Lengkap</label>
          <textarea id="alamat" name="alamat" rows="2"
            class="form-input @error('alamat') is-error @enderror"
            placeholder="Jl. ..., Kelurahan, Kecamatan, Kota, Provinsi">{{ old('alamat', session('duplicate_pasien')->alamat ?? '') }}</textarea>
          @error('alamat')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="no_telepon">No. Telepon</label>
          <input type="text" id="no_telepon" name="no_telepon"
            class="form-input @error('no_telepon') is-error @enderror"
            placeholder="08xx-xxxx-xxxx"
            value="{{ old('no_telepon', session('duplicate_pasien')->no_telepon ?? '') }}">
          @error('no_telepon')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="pekerjaan">Pekerjaan</label>
          <input type="text" id="pekerjaan" name="pekerjaan"
            class="form-input @error('pekerjaan') is-error @enderror"
            placeholder="Pekerjaan pasien"
            value="{{ old('pekerjaan', session('duplicate_pasien')->pekerjaan ?? '') }}">
          @error('pekerjaan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      {{-- ===== JENIS PASIEN & PEMBAYARAN ===== --}}
      <div class="form-section-title">Jenis Pasien & Pembayaran</div>
      <div class="form-grid">

        <div class="form-group">
          <label for="jenis">Jenis Pasien <span class="required">*</span></label>
          <select id="jenis" name="jenis"
            class="form-input @error('jenis') is-error @enderror">
            <option value="" disabled {{ old('jenis') ? '' : 'selected' }}>— Pilih —</option>
            <option value="BPJS"    {{ old('jenis', session('duplicate_pasien')->jenis ?? '') == 'BPJS'    ? 'selected' : '' }}>BPJS</option>
            <option value="Mandiri" {{ old('jenis', session('duplicate_pasien')->jenis ?? '') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
          </select>
          @error('jenis')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        {{-- Field No. BPJS: muncul hanya jika jenis = BPJS --}}
        <div class="form-group" id="field-bpjs" style="display: {{ old('jenis', session('duplicate_pasien')->jenis ?? '') == 'BPJS' ? 'flex' : 'none' }};">
          <label for="no_bpjs">No. BPJS <span class="required">*</span></label>
          <input type="text" id="no_bpjs" name="no_bpjs"
            class="form-input @error('no_bpjs') is-error @enderror"
            placeholder="Contoh: 0001234567890"
            value="{{ old('no_bpjs', session('duplicate_pasien')->no_bpjs ?? '') }}">
          @error('no_bpjs')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      {{-- ===== TUJUAN & KELUHAN ===== --}}
      <div class="form-section-title">Tujuan & Keluhan</div>
      <div class="form-grid">

        <div class="form-group">
          <label for="poli_tujuan">Poli Tujuan <span class="required">*</span></label>
          <select id="poli_tujuan" name="poli_tujuan"
            class="form-input @error('poli_tujuan') is-error @enderror">
            <option value="" disabled {{ old('poli_tujuan') ? '' : 'selected' }}>— Pilih Poli —</option>
            @foreach(['Umum','Anak','Penyakit Dalam','Bedah','Gigi','Kebidanan','Mata','UGD'] as $poli)
              <option value="{{ $poli }}" {{ old('poli_tujuan', session('duplicate_pasien')->poli_tujuan ?? '') == $poli ? 'selected' : '' }}>
                {{ $poli }}
              </option>
            @endforeach
          </select>
          @error('poli_tujuan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="jenis_kunjungan">Jenis Kunjungan</label>
          <select id="jenis_kunjungan" name="jenis_kunjungan"
            class="form-input @error('jenis_kunjungan') is-error @enderror">
            <option value="Rawat Jalan" {{ old('jenis_kunjungan', session('duplicate_pasien')->jenis_kunjungan ?? '') == 'Rawat Jalan' ? 'selected' : '' }}>Rawat Jalan</option>
            <option value="Rawat Inap"  {{ old('jenis_kunjungan', session('duplicate_pasien')->jenis_kunjungan ?? '') == 'Rawat Inap'  ? 'selected' : '' }}>Rawat Inap</option>
            <option value="UGD"         {{ old('jenis_kunjungan', session('duplicate_pasien')->jenis_kunjungan ?? '') == 'UGD'         ? 'selected' : '' }}>UGD</option>
          </select>
          @error('jenis_kunjungan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="status">Status <span class="required">*</span></label>
          <select id="status" name="status"
            class="form-input @error('status') is-error @enderror">
            <option value="" disabled {{ old('status') ? '' : 'selected' }}>— Pilih Status —</option>
            <option value="Menunggu"  {{ old('status', session('duplicate_pasien')->status ?? '') == 'Menunggu'  ? 'selected' : '' }}>Menunggu</option>
            <option value="Diperiksa" {{ old('status', session('duplicate_pasien')->status ?? '') == 'Diperiksa' ? 'selected' : '' }}>Diperiksa</option>
            <option value="Selesai"   {{ old('status', session('duplicate_pasien')->status ?? '') == 'Selesai'   ? 'selected' : '' }}>Selesai</option>
          </select>
          @error('status')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="keluhan">Keluhan Utama</label>
          <textarea id="keluhan" name="keluhan" rows="2"
            class="form-input @error('keluhan') is-error @enderror"
            placeholder="Keluhan yang dirasakan pasien...">{{ old('keluhan', session('duplicate_pasien')->keluhan ?? '') }}</textarea>
          @error('keluhan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="riwayat_penyakit">Riwayat Penyakit</label>
          <textarea id="riwayat_penyakit" name="riwayat_penyakit" rows="2"
            class="form-input @error('riwayat_penyakit') is-error @enderror"
            placeholder="Contoh: Hipertensi sejak 2020, Diabetes Mellitus...">{{ old('riwayat_penyakit', session('duplicate_pasien')->riwayat_penyakit ?? '') }}</textarea>
          @error('riwayat_penyakit')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      {{-- ===== DATA MEDIS UNTUK DOKTER ===== --}}
      <div class="form-section-title">Data Medis <span style="font-weight:400;color:#C4B5A5">(untuk Dokter)</span></div>
      <div class="form-grid">

        <div class="form-group">
          <label for="berat_badan">Berat Badan (kg)</label>
          <input type="number" id="berat_badan" name="berat_badan"
            class="form-input @error('berat_badan') is-error @enderror"
            placeholder="Contoh: 65" step="0.1" min="1" max="300"
            value="{{ old('berat_badan', session('duplicate_pasien')->berat_badan ?? '') }}">
          @error('berat_badan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="tinggi_badan">Tinggi Badan (cm)</label>
          <input type="number" id="tinggi_badan" name="tinggi_badan"
            class="form-input @error('tinggi_badan') is-error @enderror"
            placeholder="Contoh: 165" step="0.1" min="1" max="300"
            value="{{ old('tinggi_badan', session('duplicate_pasien')->tinggi_badan ?? '') }}">
          @error('tinggi_badan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="tekanan_darah">Tekanan Darah</label>
          <input type="text" id="tekanan_darah" name="tekanan_darah"
            class="form-input @error('tekanan_darah') is-error @enderror"
            placeholder="Contoh: 120/80"
            value="{{ old('tekanan_darah', session('duplicate_pasien')->tekanan_darah ?? '') }}">
          @error('tekanan_darah')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="alergi">Alergi Obat</label>
          <input type="text" id="alergi" name="alergi"
            class="form-input @error('alergi') is-error @enderror"
            placeholder="Contoh: Penisilin (kosongkan jika tidak ada)"
            value="{{ old('alergi', session('duplicate_pasien')->alergi ?? '') }}">
          @error('alergi')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      <div class="form-actions">
        <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">✔ Daftarkan Pasien</button>
      </div>

    </form>
  </div>
</div>

<script>
  const jenisSelect = document.getElementById('jenis');
  const bpjsField = document.getElementById('field-bpjs');

  jenisSelect.addEventListener('change', function () {
    bpjsField.style.display = this.value === 'BPJS' ? 'flex' : 'none';
  });

  // ========== FITUR DETEKSI NIK ==========
  const nikInput = document.getElementById('nik');
  const existingAlert = document.getElementById('existingPatientAlert');
  let isExistingPatient = false;
  const originalRm = document.getElementById('no_rm').value;

  nikInput.addEventListener('blur', function() {
    const nik = this.value.trim();
    
    if (nik.length < 16) {
      existingAlert.style.display = 'none';
      isExistingPatient = false;
      return;
    }
    
    // Gunakan URL manual
    const cekUrl = '/admin/pasien/cek-nik/' + nik;
    
    fetch(cekUrl)
      .then(response => response.json())
      .then(data => {
        if (data.found) {
          isExistingPatient = true;
          
          existingAlert.style.display = 'block';
          
          // Auto-fill semua field
          document.getElementById('nama').value = data.data.nama || '';
          document.getElementById('tgl_lahir').value = data.data.tgl_lahir || '';
          
          const jkSelect = document.getElementById('jenis_kelamin');
          if (data.data.jenis_kelamin) {
            jkSelect.value = data.data.jenis_kelamin;
          }
          
          document.getElementById('alamat').value = data.data.alamat || '';
          document.getElementById('no_telepon').value = data.data.no_telepon || '';
          document.getElementById('pekerjaan').value = data.data.pekerjaan || '';
          
          const jenisPasienSelect = document.getElementById('jenis');
          if (data.data.jenis) {
            jenisPasienSelect.value = data.data.jenis;
            jenisPasienSelect.dispatchEvent(new Event('change'));
          }
          
          document.getElementById('no_bpjs').value = data.data.no_bpjs || '';
          document.getElementById('poli_tujuan').value = data.data.poli_tujuan || '';
          document.getElementById('jenis_kunjungan').value = data.data.jenis_kunjungan || 'Rawat Jalan';
          document.getElementById('riwayat_penyakit').value = data.data.riwayat_penyakit || '';
          document.getElementById('keluhan').value = data.data.keluhan || '';
          document.getElementById('berat_badan').value = data.data.berat_badan || '';
          document.getElementById('tinggi_badan').value = data.data.tinggi_badan || '';
          document.getElementById('tekanan_darah').value = data.data.tekanan_darah || '';
          document.getElementById('alergi').value = data.data.alergi || '';
          
          // Set No. RM dari data lama (untuk pasien lama)
          document.getElementById('no_rm').value = data.data.no_rm;
          
        } else {
          isExistingPatient = false;
          existingAlert.style.display = 'none';
          document.getElementById('no_rm').value = originalRm;
        }
      })
      .catch(error => {
        console.error('Error checking NIK:', error);
        existingAlert.style.display = 'none';
      });
  });
  
  nikInput.addEventListener('focus', function() {
    existingAlert.style.display = 'none';
  });
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

  .form-group {
    display: flex;
    flex-direction: column;
    gap: .4rem;
  }

  .form-group label {
    font-size: .85rem;
    font-weight: 500;
    color: #555;
    letter-spacing: .02em;
  }

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

  .form-input:focus {
    border-color: #7c5cbf;
    box-shadow: 0 0 0 3px rgba(124,92,191,.12);
    background: #fff;
  }

  .form-input.is-error { border-color: #e53935; }
  .form-error { font-size: .8rem; color: #e53935; }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .8rem;
    margin-top: 2rem;
    padding-top: 1.4rem;
    border-top: 1px solid #eee;
  }

  .alert-duplicate {
    background: #fff0f0;
    border: 2px solid #e53935;
    border-radius: 8px;
    padding: 12px 20px;
    margin-bottom: 1.2rem;
    color: #b71c1c;
    font-weight: 500;
    text-align: center;
  }
  
  .alert-existing-patient {
    background: #d4edda;
    border: 2px solid #28a745;
    border-radius: 8px;
    padding: 12px 20px;
    margin-bottom: 1.2rem;
    color: #155724;
    font-weight: 500;
    text-align: center;
  }

  @media (max-width: 640px) {
    .form-grid { grid-template-columns: 1fr; }
  }
</style>

@endsection
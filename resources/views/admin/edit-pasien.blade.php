@extends('layouts.admin')

@section('content')
<div class="page-section active">
  <div class="page-header">
    <div>
      <div class="page-title">Edit Data Pasien</div>
      <div class="page-sub">Ubah data pasien yang sudah terdaftar</div>
    </div>
    <a href="{{ route('pasien.index') }}" class="btn btn-secondary">← Kembali</a>
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
    <form action="{{ route('pasien.update', $pasien->id) }}" method="POST">
      @csrf
      @method('PUT')

      {{-- ===== DATA PRIBADI ===== --}}
      <div class="form-section-title">Data Pribadi</div>
      <div class="form-grid">

        <div class="form-group">
          <label>No. Rekam Medis <span class="required">*</span></label>
          <input type="text" name="no_rm"
            class="form-input @error('no_rm') is-error @enderror"
            placeholder="Contoh: RM-2024-001"
            value="{{ old('no_rm', $pasien->no_rm) }}">
          @error('no_rm')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>Nama Lengkap <span class="required">*</span></label>
          <input type="text" name="nama"
            class="form-input @error('nama') is-error @enderror"
            placeholder="Nama sesuai KTP"
            value="{{ old('nama', $pasien->nama) }}">
          @error('nama')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>NIK <span class="required">*</span></label>
          <input type="text" name="nik"
            class="form-input @error('nik') is-error @enderror"
            placeholder="16 digit NIK" maxlength="16"
            value="{{ old('nik', $pasien->nik) }}">
          @error('nik')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>Tanggal Lahir <span class="required">*</span></label>
          <input type="date" name="tgl_lahir"
            class="form-input @error('tgl_lahir') is-error @enderror"
            value="{{ old('tgl_lahir', $pasien->tgl_lahir) }}">
          @error('tgl_lahir')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>Jenis Kelamin <span class="required">*</span></label>
          <select name="jenis_kelamin"
            class="form-input @error('jenis_kelamin') is-error @enderror">
            <option value="" disabled>— Pilih —</option>
            <option value="L" {{ old('jenis_kelamin', $pasien->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin', $pasien->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
          </select>
          @error('jenis_kelamin')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label>Alamat Lengkap</label>
          <textarea name="alamat" rows="2"
            class="form-input @error('alamat') is-error @enderror"
            placeholder="Jl. ..., Kelurahan, Kecamatan, Kota, Provinsi">{{ old('alamat', $pasien->alamat) }}</textarea>
          @error('alamat')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>No. Telepon</label>
          <input type="text" name="no_telepon"
            class="form-input @error('no_telepon') is-error @enderror"
            placeholder="08xx-xxxx-xxxx"
            value="{{ old('no_telepon', $pasien->no_telepon) }}">
          @error('no_telepon')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>Pekerjaan</label>
          <input type="text" name="pekerjaan"
            class="form-input @error('pekerjaan') is-error @enderror"
            placeholder="Pekerjaan pasien"
            value="{{ old('pekerjaan', $pasien->pekerjaan) }}">
          @error('pekerjaan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      {{-- ===== JENIS PASIEN & PEMBAYARAN ===== --}}
      <div class="form-section-title">Jenis Pasien & Pembayaran</div>
      <div class="form-grid">

        <div class="form-group">
          <label>Jenis Pasien <span class="required">*</span></label>
          <select name="jenis"
            class="form-input @error('jenis') is-error @enderror">
            <option value="" disabled>— Pilih —</option>
            <option value="BPJS"    {{ old('jenis', $pasien->jenis) == 'BPJS'    ? 'selected' : '' }}>BPJS</option>
            <option value="Mandiri" {{ old('jenis', $pasien->jenis) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
          </select>
          @error('jenis')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      {{-- ===== TUJUAN & KELUHAN ===== --}}
      <div class="form-section-title">Tujuan & Keluhan</div>
      <div class="form-grid">

        <div class="form-group">
          <label>Poli Tujuan <span class="required">*</span></label>
          <select name="poli_tujuan"
            class="form-input @error('poli_tujuan') is-error @enderror">
            <option value="" disabled>— Pilih Poli —</option>
            @foreach(['Umum','Anak','Penyakit Dalam','Bedah','Gigi','Kebidanan','Mata','UGD'] as $poli)
              <option value="{{ $poli }}" {{ old('poli_tujuan', $pasien->poli_tujuan) == $poli ? 'selected' : '' }}>
                {{ $poli }}
              </option>
            @endforeach
          </select>
          @error('poli_tujuan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>Jenis Kunjungan</label>
          <select name="jenis_kunjungan"
            class="form-input @error('jenis_kunjungan') is-error @enderror">
            <option value="Rawat Jalan" {{ old('jenis_kunjungan', $pasien->jenis_kunjungan) == 'Rawat Jalan' ? 'selected' : '' }}>Rawat Jalan</option>
            <option value="Rawat Inap"  {{ old('jenis_kunjungan', $pasien->jenis_kunjungan) == 'Rawat Inap'  ? 'selected' : '' }}>Rawat Inap</option>
            <option value="UGD"         {{ old('jenis_kunjungan', $pasien->jenis_kunjungan) == 'UGD'         ? 'selected' : '' }}>UGD</option>
          </select>
          @error('jenis_kunjungan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>Status <span class="required">*</span></label>
          <select name="status"
            class="form-input @error('status') is-error @enderror">
            <option value="" disabled>— Pilih Status —</option>
            <option value="Menunggu"  {{ old('status', $pasien->status) == 'Menunggu'  ? 'selected' : '' }}>Menunggu</option>
            <option value="Diperiksa" {{ old('status', $pasien->status) == 'Diperiksa' ? 'selected' : '' }}>Diperiksa</option>
            <option value="Selesai"   {{ old('status', $pasien->status) == 'Selesai'   ? 'selected' : '' }}>Selesai</option>
          </select>
          @error('status')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label>Keluhan Utama</label>
          <textarea name="keluhan" rows="2"
            class="form-input @error('keluhan') is-error @enderror"
            placeholder="Keluhan yang dirasakan pasien...">{{ old('keluhan', $pasien->keluhan) }}</textarea>
          @error('keluhan')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label>Riwayat Penyakit / Alergi</label>
          <textarea name="riwayat_penyakit" rows="2"
            class="form-input @error('riwayat_penyakit') is-error @enderror"
            placeholder="Contoh: Hipertensi, Alergi Penisilin...">{{ old('riwayat_penyakit', $pasien->riwayat_penyakit) }}</textarea>
          @error('riwayat_penyakit')<span class="form-error">{{ $message }}</span>@enderror
        </div>

      </div>

      <div class="form-actions">
        <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
      </div>

    </form>
  </div>
</div>

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

  @media (max-width: 640px) {
    .form-grid { grid-template-columns: 1fr; }
  }
</style>

@endsection
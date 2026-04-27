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

  {{-- Tampilkan error validasi --}}
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
    <form action="{{ route('pasien.store') }}" method="POST">
      @csrf

      <div class="form-grid">

        {{-- NO. RM --}}
        <div class="form-group">
          <label for="no_rm">No. Rekam Medis <span class="required">*</span></label>
          <input
            type="text"
            id="no_rm"
            name="no_rm"
            class="form-input @error('no_rm') is-error @enderror"
            placeholder="Contoh: RM-2024-001"
            value="{{ old('no_rm') }}"
          >
          @error('no_rm')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- NAMA --}}
        <div class="form-group">
          <label for="nama">Nama Lengkap <span class="required">*</span></label>
          <input
            type="text"
            id="nama"
            name="nama"
            class="form-input @error('nama') is-error @enderror"
            placeholder="Nama sesuai KTP"
            value="{{ old('nama') }}"
          >
          @error('nama')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- NIK --}}
        <div class="form-group">
          <label for="nik">NIK <span class="required">*</span></label>
          <input
            type="text"
            id="nik"
            name="nik"
            class="form-input @error('nik') is-error @enderror"
            placeholder="16 digit NIK"
            maxlength="16"
            value="{{ old('nik') }}"
          >
          @error('nik')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- TANGGAL LAHIR --}}
        <div class="form-group">
          <label for="tgl_lahir">Tanggal Lahir <span class="required">*</span></label>
          <input
            type="date"
            id="tgl_lahir"
            name="tgl_lahir"
            class="form-input @error('tgl_lahir') is-error @enderror"
            value="{{ old('tgl_lahir') }}"
          >
          @error('tgl_lahir')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- JENIS PEMBAYARAN --}}
        <div class="form-group">
          <label for="jenis">Jenis Pembayaran <span class="required">*</span></label>
          <select
            id="jenis"
            name="jenis"
            class="form-input @error('jenis') is-error @enderror"
          >
            <option value="" disabled {{ old('jenis') ? '' : 'selected' }}>-- Pilih Jenis --</option>
            <option value="BPJS"    {{ old('jenis') == 'BPJS'    ? 'selected' : '' }}>BPJS</option>
            <option value="Mandiri" {{ old('jenis') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
          </select>
          @error('jenis')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- POLI TUJUAN --}}
        <div class="form-group">
          <label for="poli_tujuan">Poli Tujuan <span class="required">*</span></label>
          <select
            id="poli_tujuan"
            name="poli_tujuan"
            class="form-input @error('poli_tujuan') is-error @enderror"
          >
            <option value="" disabled {{ old('poli_tujuan') ? '' : 'selected' }}>-- Pilih Poli --</option>
            @foreach(['Umum','Anak','Penyakit Dalam','Bedah','Gigi','Kebidanan','Mata','UGD'] as $poli)
              <option value="{{ $poli }}" {{ old('poli_tujuan') == $poli ? 'selected' : '' }}>
                {{ $poli }}
              </option>
            @endforeach
          </select>
          @error('poli_tujuan')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- STATUS --}}
        <div class="form-group">
          <label for="status">Status <span class="required">*</span></label>
          <select
            id="status"
            name="status"
            class="form-input @error('status') is-error @enderror"
          >
            <option value="" disabled {{ old('status') ? '' : 'selected' }}>-- Pilih Status --</option>
            <option value="Menunggu"  {{ old('status') == 'Menunggu'  ? 'selected' : '' }}>Menunggu</option>
            <option value="Diperiksa" {{ old('status') == 'Diperiksa' ? 'selected' : '' }}>Diperiksa</option>
            <option value="Selesai"   {{ old('status') == 'Selesai'   ? 'selected' : '' }}>Selesai</option>
          </select>
          @error('status')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

      </div>{{-- end .form-grid --}}

      <div class="form-actions">
        <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Pasien</button>
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

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.4rem 2rem;
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

  .required {
    color: #e53935;
  }

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
  }

  .form-input:focus {
    border-color: #7c5cbf;
    box-shadow: 0 0 0 3px rgba(124,92,191,.12);
    background: #fff;
  }

  .form-input.is-error {
    border-color: #e53935;
  }

  .form-error {
    font-size: .8rem;
    color: #e53935;
  }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .8rem;
    margin-top: 2rem;
    padding-top: 1.4rem;
    border-top: 1px solid #eee;
  }

  @media (max-width: 640px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

@endsection
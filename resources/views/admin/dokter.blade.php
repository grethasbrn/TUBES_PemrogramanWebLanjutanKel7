@extends('layouts.admin')

@section('content')

<div class="page-section active">
  <div class="page-header">
    <div>
      <div class="page-title">Manajemen Dokter</div>
      <div class="page-sub">Kelola data dokter rumah sakit</div>
    </div>
    <button class="btn btn-primary" onclick="openModal()">+ Tambah Dokter</button>
  </div>

  @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px">
      <ul style="margin:0;padding-left:16px">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Spesialisasi</th>
            <th>No. Telepon</th>
            <th>Email</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($dokters as $i => $d)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d->nama }}</td>
            <td>{{ $d->spesialisasi }}</td>
            <td>{{ $d->no_telepon ?? '-' }}</td>
            <td>{{ $d->email ?? '-' }}</td>
            <td>
              <span style="background:{{ $d->status === 'Aktif' ? '#d1fae5' : '#fee2e2' }};color:{{ $d->status === 'Aktif' ? '#065f46' : '#991b1b' }};padding:2px 10px;border-radius:99px;font-size:12px">
                {{ $d->status }}
              </span>
            </td>
            <td style="display:flex;gap:8px">
              <button class="btn btn-teal" style="padding:4px 12px;font-size:12px"
                onclick="openEdit({{ json_encode($d) }})">Edit</button>
              <form method="POST" action="{{ url('admin/dokter/'.$d->id) }}"
                onsubmit="return confirm('Hapus dokter ini?')">
                @csrf @method('DELETE')
                <button class="btn" style="background:#ef4444;color:#fff;padding:4px 12px;font-size:12px">Hapus</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" style="text-align:center;color:#888">Belum ada data dokter</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:999;align-items:center;justify-content:center;margin-top:50px;">
  <div style="background:#fff;border-radius:12px;padding:28px;width:480px;max-width:95vw">
    <div style="font-size:16px;font-weight:700;margin-bottom:20px">Tambah Dokter</div>
    <form method="POST" action="{{ url('admin/dokter') }}">
      @csrf
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">Nama</label>
        <input name="nama" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">Spesialisasi / Poli</label>
        <input name="spesialisasi" required placeholder="Contoh: Sp.PD, Sp.A, Umum..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">No. Telepon</label>
        <input name="no_telepon" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">Email</label>
        <input name="email" type="email" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <!-- PASSWORD - wajib untuk akun login dokter -->
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">Password <span style="color:#888;font-size:11px">(untuk login dokter)</span></label>
        <input name="password" type="password" required minlength="6" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <div style="margin-bottom:20px">
        <label style="font-size:13px;color:#555">Status</label>
        <select name="status" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px">
          <option value="Aktif">Aktif</option>
          <option value="Tidak Aktif">Tidak Aktif</option>
        </select>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end">
        <button type="button" onclick="closeModal()" class="btn" style="background:#e5e7eb;color:#333">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:28px;width:480px;max-width:95vw">
    <div style="font-size:16px;font-weight:700;margin-bottom:20px">Edit Dokter</div>
    <form method="POST" id="formEdit" action="">
      @csrf @method('PUT')
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">Nama</label>
        <input name="nama" id="editNama" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">Spesialisasi / Poli</label>
        <input name="spesialisasi" id="editSpesialisasi" required placeholder="Contoh: Sp.PD, Sp.A, Umum..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">No. Telepon</label>
        <input name="no_telepon" id="editTelepon" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">Email</label>
        <input name="email" id="editEmail" type="email" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <!-- PASSWORD OPSIONAL saat edit -->
      <div style="margin-bottom:12px">
        <label style="font-size:13px;color:#555">Password Baru <span style="color:#888;font-size:11px">(kosongkan jika tidak diubah)</span></label>
        <input name="password" type="password" minlength="6" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px;box-sizing:border-box">
      </div>
      <div style="margin-bottom:20px">
        <label style="font-size:13px;color:#555">Status</label>
        <select name="status" id="editStatus" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px">
          <option value="Aktif">Aktif</option>
          <option value="Tidak Aktif">Tidak Aktif</option>
        </select>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end">
        <button type="button" onclick="closeEdit()" class="btn" style="background:#e5e7eb;color:#333">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function openModal() {
    document.getElementById('modalTambah').style.display = 'flex';
  }
  function closeModal() {
    document.getElementById('modalTambah').style.display = 'none';
  }
  function openEdit(d) {
    document.getElementById('formEdit').action = '/admin/dokter/' + d.id;
    document.getElementById('editNama').value         = d.nama;
    document.getElementById('editSpesialisasi').value = d.spesialisasi;
    document.getElementById('editTelepon').value      = d.no_telepon ?? '';
    document.getElementById('editEmail').value        = d.email ?? '';
    document.getElementById('editStatus').value       = d.status;
    document.getElementById('modalEdit').style.display = 'flex';
  }
  function closeEdit() {
    document.getElementById('modalEdit').style.display = 'none';
  }
</script>
@endpush
@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h2>Manajemen Dokter</h2>
</div>

{{-- Flash message --}}
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- Form Tambah Dokter --}}
<div class="card" style="margin-bottom:20px;padding:20px">
    <h3 style="margin-bottom:16px">Tambah Dokter</h3>
    <form method="POST" action="{{ route('admin.dokter.store') }}">
        @csrf
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <input name="name" placeholder="Nama Dokter" required
                style="padding:9px 12px;border:1px solid #ddd;border-radius:8px;flex:1">
            <input name="email" type="email" placeholder="Email" required
                style="padding:9px 12px;border:1px solid #ddd;border-radius:8px;flex:1">
            <input name="password" type="password" placeholder="Password" required
                style="padding:9px 12px;border:1px solid #ddd;border-radius:8px;flex:1">
            <input name="poli" placeholder="Poli (cth: Umum)" 
                style="padding:9px 12px;border:1px solid #ddd;border-radius:8px;flex:1">
            <button type="submit"
                style="padding:9px 20px;background:#7C3AED;color:white;border:none;border-radius:8px;cursor:pointer">
                Tambah
            </button>
        </div>
    </form>
</div>

{{-- Tabel Daftar Dokter --}}
<div class="card" style="padding:20px">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="border-bottom:2px solid #eee;text-align:left">
                <th style="padding:10px">Nama</th>
                <th style="padding:10px">Email</th>
                <th style="padding:10px">Poli</th>
                <th style="padding:10px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dokters as $d)
            <tr style="border-bottom:1px solid #f0f0f0">
                <td style="padding:10px">{{ $d->name }}</td>
                <td style="padding:10px">{{ $d->email }}</td>
                <td style="padding:10px">{{ $d->poli ?? '-' }}</td>
                <td style="padding:10px;display:flex;gap:8px">

                    {{-- Edit (modal sederhana atau form inline) --}}
                    <form method="POST" action="{{ route('admin.dokter.update', $d->id) }}"
                          style="display:inline">
                        @csrf @method('PUT')
                        <input name="name" value="{{ $d->name }}" required
                            style="padding:5px 8px;border:1px solid #ddd;border-radius:6px;width:120px">
                        <input name="poli" value="{{ $d->poli }}"
                            style="padding:5px 8px;border:1px solid #ddd;border-radius:6px;width:80px">
                        <button type="submit"
                            style="padding:5px 12px;background:#059669;color:white;border:none;border-radius:6px;cursor:pointer">
                            Simpan
                        </button>
                    </form>

                    {{-- Hapus --}}
                    <form method="POST" action="{{ route('admin.dokter.destroy', $d->id) }}"
                          onsubmit="return confirm('Hapus dokter ini?')"
                          style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                            style="padding:5px 12px;background:#DC2626;color:white;border:none;border-radius:6px;cursor:pointer">
                            Hapus
                        </button>
                    </form>

                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#999">
                Belum ada dokter terdaftar.
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
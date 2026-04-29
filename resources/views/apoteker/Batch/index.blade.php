{{-- resources/views/apoteker/batch/index.blade.php --}}
@extends('layouts.apoteker')

@section('title', 'Batch Obat')

@section('content')
<div class="batch-page">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Batch Obat</h1>
            <p class="page-subtitle">Manajemen stok masuk per batch</p>
        </div>
        <button class="btn-primary" onclick="openModal()">
            + Input Batch Obat
        </button>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel --}}
    <div class="table-card">
        <div class="table-wrapper">
            <table class="batch-table">
                <thead>
                    <tr>
                        <th>NAMA OBAT</th>
                        <th>TIPE</th>
                        <th>NO BATCH</th>
                        <th>JUMLAH</th>
                        <th>HARGA</th>
                        <th>TGL MASUK</th>
                        <th>TGL EXPIRED</th>
                        <th>SUPPLIER</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td class="nama-obat">{{ $batch->nama_obat }}</td>
                            <td>{{ $batch->tipe }}</td>
                            <td class="no-batch">{{ $batch->no_batch }}</td>
                            <td>{{ number_format($batch->jumlah, 0, ',', '.') }}</td>
                            <td>{{ $batch->harga_formatted }}</td>
                            <td>{{ $batch->tgl_masuk->format('d M Y') }}</td>
                            <td>{{ $batch->tgl_expired->format('d M Y') }}</td>
                            <td>{{ $batch->supplier ?? '-' }}</td>
                            <td>
                                @if($batch->sudah_expired)
                                    <span class="badge badge-expired">Expired</span>
                                @elseif($batch->mendekati_expired)
                                    <span class="badge badge-warning">Hampir Expired</span>
                                @else
                                    <span class="badge badge-ok">OK</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('batch.destroy', $batch) }}" method="POST"
                                      onsubmit="return confirm('Hapus batch ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty-state">
                                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                                <p>Belum ada data batch obat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
            <div class="pagination-wrapper">
                {{ $batches->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Input Batch --}}
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>
<div class="modal" id="batchModal">
    <div class="modal-header">
        <h2 class="modal-title">Input Batch Obat</h2>
        <button class="modal-close" onclick="closeModal()">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form action="{{ route('batch.store') }}" method="POST" class="modal-form">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nama Obat <span class="required">*</span></label>
                <input type="text" name="nama_obat" class="form-input @error('nama_obat') is-error @enderror"
                       value="{{ old('nama_obat') }}" placeholder="Contoh: Paracetamol 500mg">
                @error('nama_obat')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tipe Obat <span class="required">*</span></label>
                <select name="tipe" class="form-input @error('tipe') is-error @enderror">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Tablet" {{ old('tipe') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                    <option value="Kapsul" {{ old('tipe') == 'Kapsul' ? 'selected' : '' }}>Kapsul</option>
                    <option value="Sirup" {{ old('tipe') == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                    <option value="Salep" {{ old('tipe') == 'Salep' ? 'selected' : '' }}>Salep</option>
                    <option value="Injeksi" {{ old('tipe') == 'Injeksi' ? 'selected' : '' }}>Injeksi</option>
                    <option value="Tetes" {{ old('tipe') == 'Tetes' ? 'selected' : '' }}>Tetes</option>
                    <option value="Supositoria" {{ old('tipe') == 'Supositoria' ? 'selected' : '' }}>Supositoria</option>
                </select>
                @error('tipe')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">No Batch <span class="required">*</span></label>
                <input type="text" name="no_batch" class="form-input @error('no_batch') is-error @enderror"
                       value="{{ old('no_batch') }}" placeholder="Contoh: BTH-2026-001">
                @error('no_batch')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Jumlah <span class="required">*</span></label>
                <input type="number" name="jumlah" class="form-input @error('jumlah') is-error @enderror"
                       value="{{ old('jumlah') }}" placeholder="0" min="1">
                @error('jumlah')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Harga Satuan (Rp) <span class="required">*</span></label>
                <input type="number" name="harga" class="form-input @error('harga') is-error @enderror"
                       value="{{ old('harga') }}" placeholder="0" min="0">
                @error('harga')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Supplier</label>
                <input type="text" name="supplier" class="form-input"
                       value="{{ old('supplier') }}" placeholder="Nama distributor / supplier">
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Masuk <span class="required">*</span></label>
                <input type="date" name="tgl_masuk" class="form-input @error('tgl_masuk') is-error @enderror"
                       value="{{ old('tgl_masuk', date('Y-m-d')) }}">
                @error('tgl_masuk')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Expired <span class="required">*</span></label>
                <input type="date" name="tgl_expired" class="form-input @error('tgl_expired') is-error @enderror"
                       value="{{ old('tgl_expired') }}">
                @error('tgl_expired')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
            <button type="submit" class="btn-primary">Simpan Batch</button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .batch-page {
        padding: 2rem;
        max-width: 100%;
    }

    /* Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }
    .page-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0 0 0.25rem;
    }
    .page-subtitle {
        font-size: 0.875rem;
        color: #888;
        margin: 0;
    }

    /* Button Primary (sama dengan dashboard) */
    .btn-primary {
        background: #7c6ff7;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        white-space: nowrap;
    }
    .btn-primary:hover { background: #6a5ee0; }

    /* Alert */
    .alert {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        margin-bottom: 1.25rem;
    }
    .alert-success {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    /* Table Card */
    .table-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #ede8e0;
        overflow: hidden;
    }
    .table-wrapper {
        overflow-x: auto;
    }
    .batch-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .batch-table thead tr {
        background: #f5f0e8;
    }
    .batch-table th {
        padding: 0.9rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #888;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }
    .batch-table tbody tr {
        border-top: 1px solid #f0ebe3;
        transition: background 0.15s;
    }
    .batch-table tbody tr:hover {
        background: #faf7f2;
    }
    .batch-table td {
        padding: 0.9rem 1rem;
        color: #2d2d2d;
        vertical-align: middle;
    }
    .nama-obat { font-weight: 500; }
    .no-batch  { color: #888; font-family: monospace; font-size: 0.8rem; }

    /* Badge Status */
    .badge {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .badge-ok      { background: #f0fdf4; color: #16a34a; }
    .badge-warning { background: #fffbeb; color: #d97706; }
    .badge-expired { background: #fff1f2; color: #e11d48; }

    /* Delete button */
    .btn-delete {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: none;
        border: 1px solid #fca5a5;
        color: #ef4444;
        border-radius: 6px;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-delete:hover {
        background: #fff1f2;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem !important;
        color: #aaa;
    }
    .empty-state svg { margin: 0 auto 0.75rem; display: block; opacity: 0.4; }
    .empty-state p   { margin: 0; font-size: 0.9rem; }

    /* Pagination */
    .pagination-wrapper {
        padding: 1rem 1.25rem;
        border-top: 1px solid #f0ebe3;
    }

    /* ── MODAL ── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 40;
        backdrop-filter: blur(2px);
    }
    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        border-radius: 14px;
        width: 90%;
        max-width: 680px;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 50;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal.is-open,
    .modal-overlay.is-open {
        display: block;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f0ebe3;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }
    .modal-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }
    .modal-close {
        background: none;
        border: none;
        color: #888;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 6px;
        display: flex;
        transition: background 0.15s;
    }
    .modal-close:hover { background: #f5f0e8; color: #333; }

    /* Form */
    .modal-form { padding: 1.5rem; }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 560px) {
        .form-grid { grid-template-columns: 1fr; }
    }
    .form-group { display: flex; flex-direction: column; gap: 0.35rem; }
    .form-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: #555;
    }
    .required { color: #ef4444; }
    .form-input {
        border: 1px solid #e0d9cf;
        border-radius: 8px;
        padding: 0.55rem 0.85rem;
        font-size: 0.875rem;
        color: #1a1a1a;
        background: #fdfcfa;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        width: 100%;
        box-sizing: border-box;
    }
    .form-input:focus {
        border-color: #7c6ff7;
        box-shadow: 0 0 0 3px rgba(124,111,247,0.12);
        background: #fff;
    }
    .form-input.is-error { border-color: #ef4444; }
    .form-error { font-size: 0.75rem; color: #ef4444; }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid #f0ebe3;
    }
    .btn-cancel {
        background: none;
        border: 1px solid #e0d9cf;
        border-radius: 8px;
        padding: 0.6rem 1.25rem;
        font-size: 0.875rem;
        color: #555;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-cancel:hover { background: #f5f0e8; }
</style>
@endpush

@push('scripts')
<script>
    function openModal() {
        document.getElementById('batchModal').classList.add('is-open');
        document.getElementById('modalOverlay').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('batchModal').classList.remove('is-open');
        document.getElementById('modalOverlay').classList.remove('is-open');
        document.body.style.overflow = '';
    }
    // Buka otomatis jika ada error validasi
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', openModal);
    @endif

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endpush
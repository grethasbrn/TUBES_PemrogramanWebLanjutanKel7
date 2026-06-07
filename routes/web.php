<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResepController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\Admindoktercontroller as AdminDokterController;
use App\Http\Controllers\KunjunganController;

Route::get('/', function () { return view('login'); });
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| APOTEKER
|--------------------------------------------------------------------------
*/
Route::prefix('apoteker')->middleware(['auth', 'role:apoteker'])->group(function () {

    Route::get('/dashboard', [BatchController::class, 'dashboard'])->name('apoteker.dashboard');
    Route::get('/stock', [BatchController::class, 'index'])->name('apoteker.stock');
    Route::get('/alerts', [BatchController::class, 'alerts'])->name('apoteker.alerts');
    Route::get('/prescription', [ResepController::class, 'apotekerPrescription'])->name('apoteker.prescription');
    Route::get('/invoice', [InvoiceController::class, 'apotekerIndex'])->name('apoteker.invoice');
    Route::get('/report', [ReportController::class, 'index'])->name('apoteker.report');

    // API routes
    Route::get('/api/report', [ReportController::class, 'apiStats']);
    Route::get('/api/resep', [ResepController::class, 'indexApoteker']);
    Route::post('/api/resep/{id}/update-obat', [ResepController::class, 'updateObat']);
    Route::post('/api/resep/{id}/status', [ResepController::class, 'updateStatus']);
    Route::post('/api/resep/store', [ResepController::class, 'store']);
    Route::get('/api/invoice', [InvoiceController::class, 'apiIndex']);
    Route::get('/api/stok', function () {
        return response()->json(\App\Models\Batch::select('nama_obat', 'harga', 'harga_bpjs', 'jumlah')->get());
    });
    Route::get('/api/dashboard', [BatchController::class, 'apiDashboard']);
    Route::get('/api/obat/search', function (\Illuminate\Http\Request $request) {
        $q = $request->get('q', '');
        return response()->json(
            \App\Models\Batch::where('nama_obat', 'LIKE', "%{$q}%")
                ->where('jumlah', '>', 0)
                ->where(fn($query) => $query->whereNull('tgl_expired')->orWhere('tgl_expired', '>', now()))
                ->select('nama_obat as nama', 'jumlah as stok', 'harga')
                ->orderBy('nama_obat')
                ->limit(10)
                ->get()
        );
    })->name('apoteker.obat.search');

    Route::post('/invoice/resep/{id}/kirim', [InvoiceController::class, 'kirimDariResep'])->name('resep.kirim');
    Route::get('/batch', [BatchController::class, 'index'])->name('batch.index');
    Route::post('/batch', [BatchController::class, 'store'])->name('batch.store');
    Route::delete('/batch/{batch}', [BatchController::class, 'destroy'])->name('batch.destroy');
});

/*
|--------------------------------------------------------------------------
| DOKTER
|--------------------------------------------------------------------------
*/
Route::prefix('dokter')->middleware(['auth', 'role:dokter'])->group(function () {

    Route::get('/dashboard', [DokterController::class, 'dashboard']);
    Route::get('/data', [DokterController::class, 'data']);
    Route::get('/prescription', [DokterController::class, 'prescription']);
    Route::get('/status', [DokterController::class, 'status']);
    Route::get('/history', [DokterController::class, 'history']);

    // API routes
    Route::get('/api/pasien', [DokterController::class, 'apiPasien']);

    // ← UBAH: dulu /api/pasien/{id}/status sekarang pakai kunjungan_id
    Route::post('/api/kunjungan/{id}/status', [DokterController::class, 'updateStatus'])
         ->name('dokter.kunjungan.status');

    Route::get('/api/resep', [ResepController::class, 'index']);
    Route::post('/api/resep/store', [ResepController::class, 'store']);
    Route::get('/api/obat', function () {
        return response()->json(
            \App\Models\Batch::where('jumlah', '>', 0)
                ->where(fn($q) => $q->whereNull('tgl_expired')->orWhere('tgl_expired', '>', now()))
                ->select('id', 'nama_obat', 'jumlah', 'harga', 'harga_bpjs')
                ->orderBy('nama_obat')
                ->get()
        );
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/api/stats', [DashboardController::class, 'stats']);

    // DATA PASIEN — tetap ada untuk lihat master data & edit
    Route::get('/data', [PasienController::class, 'index'])->name('pasien.index');
    Route::get('/pasien/{id}/edit', [PasienController::class, 'edit'])->name('pasien.edit');
    Route::put('/pasien/{id}', [PasienController::class, 'update'])->name('pasien.update');
    Route::delete('/pasien/{id}', [PasienController::class, 'destroy'])->name('pasien.destroy');

    // ← UBAH: create & store pasien sekarang lewat KunjunganController
    Route::get('/pasien/create', [KunjunganController::class, 'create'])->name('pasien.create');
    Route::post('/pasien', [KunjunganController::class, 'store'])->name('pasien.store');

    // ← UBAH: cek NIK sekarang lewat KunjunganController
    Route::get('/pasien/cek-nik/{nik}', [KunjunganController::class, 'cekNik'])
         ->name('pasien.cekNik');

    // ← UBAH: validasi sekarang lewat KunjunganController (validasi kunjungan, bukan pasien)
    Route::post('/kunjungan/{id}/validasi', [KunjunganController::class, 'updateValidasi'])
         ->name('kunjungan.updateValidasi');

    // ← BARU: dropdown dokter by poli (AJAX)
    Route::get('/kunjungan/dokter-by-poli', [KunjunganController::class, 'dokterByPoli'])
         ->name('kunjungan.dokterByPoli');

    // ← UBAH: queue sekarang menampilkan kunjungan, bukan antrian pasien lama
    Route::get('/queue', [KunjunganController::class, 'queue'])->name('kunjungan.queue');

    // ← UBAH: kirim ke dokter sekarang lewat KunjunganController
    Route::post('/kunjungan/kirim-semua', [KunjunganController::class, 'kirimSemua'])
         ->name('kunjungan.kirimSemua');
    Route::post('/kunjungan/{id}/kirim', [KunjunganController::class, 'kirimKeDokter'])
         ->name('kunjungan.kirim');

    // DOKTER — AdminDokterController (manajemen akun user dokter)
    Route::get('/dokter', [AdminDokterController::class, 'index'])->name('admin.dokter.index');
    Route::post('/dokter', [AdminDokterController::class, 'store'])->name('admin.dokter.store');
    Route::put('/dokter/{id}', [AdminDokterController::class, 'update'])->name('admin.dokter.update');
    Route::delete('/dokter/{id}', [AdminDokterController::class, 'destroy'])->name('admin.dokter.destroy');

    // INVOICE
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/invoice/{id}/download', [InvoiceController::class, 'downloadPdf'])->name('invoice.download');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::post('/invoice/{id}/bayar', [InvoiceController::class, 'bayar'])->name('invoice.bayar');
    Route::post('/invoice/{id}/bpjs-selesai', [InvoiceController::class, 'selesaikanBpjs'])->name('invoice.bpjs');
    Route::post('/invoice/{id}/status', [InvoiceController::class, 'updateStatus'])->name('invoice.status');

    // REPORT
    Route::get('/report', [AdminReportController::class, 'index'])->name('admin.report.index');
    Route::get('/report/stats', [AdminReportController::class, 'stats'])->name('admin.report.stats');
});
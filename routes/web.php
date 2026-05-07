<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\ResepController;

Route::get('/', function () { return view('login'); });
Route::get('/login', function () { return view('login'); });
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

Route::prefix('apoteker')->group(function () {
    Route::get('/dashboard', [BatchController::class, 'dashboard'])->name('apoteker.dashboard');
    Route::get('/stock', [BatchController::class, 'index'])->name('apoteker.stock');
    Route::get('/alerts', [BatchController::class, 'alerts'])->name('apoteker.alerts'); // ✅ FIXED
    Route::get('/prescription', function () { return view('apoteker.prescription'); });
    Route::get('/invoice', function () { return view('apoteker.invoice'); });
    Route::get('/report', [ReportController::class, 'index'])->name('apoteker.report');
    Route::get('/api/report', [ReportController::class, 'apiStats']);
    Route::get('/api/resep', [ResepController::class, 'index']);
    Route::get('/api/stok', function () {
        return response()->json(\App\Models\Batch::select('nama_obat', 'harga', 'harga_bpjs', 'jumlah')->get());
    });
    Route::post('/api/resep/{id}/status', [ResepController::class, 'updateStatus']);
    Route::get('/batch', [BatchController::class, 'index'])->name('batch.index');
    Route::post('/batch', [BatchController::class, 'store'])->name('batch.store');
    Route::delete('/batch/{batch}', [BatchController::class, 'destroy'])->name('batch.destroy');
    // ✅ HAPUS baris duplikat /apoteker/alerts yang salah
});

Route::prefix('dokter')->group(function () {
    Route::get('/dashboard', [DokterController::class, 'dashboard']);
    Route::get('/data', [DokterController::class, 'data']);
    Route::get('/prescription', [DokterController::class, 'prescription']);
    Route::get('/status', function () { return view('dokter.status'); });
    Route::get('/history', function () { return view('dokter.history'); });
    Route::get('/api/pasien', [DokterController::class, 'apiPasien']);
    Route::post('/api/pasien/{id}/status', [DokterController::class, 'updateStatus']);
    Route::get('/api/resep', [ResepController::class, 'index']);
    Route::post('/api/resep/store', [ResepController::class, 'store']);
    Route::get('/api/obat', function () {
        return response()->json(
            \App\Models\Batch::where('jumlah', '>', 0)
                ->whereNull('tgl_expired')
                ->orWhere('tgl_expired', '>', now())
                ->select('id', 'nama_obat', 'jumlah', 'satuan')
                ->orderBy('nama_obat')
                ->get()
            );
        });
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/data', [PasienController::class, 'index'])->name('pasien.index');
    Route::get('/pasien/create', [PasienController::class, 'create'])->name('pasien.create');
    Route::post('/pasien', [PasienController::class, 'store'])->name('pasien.store');
    Route::post('/pasien/{id}/validasi', [PasienController::class, 'updateValidasi']);
    Route::get('/pasien/{id}/edit', [PasienController::class, 'edit'])->name('pasien.edit');
    Route::put('/pasien/{id}', [PasienController::class, 'update'])->name('pasien.update');
    Route::delete('/pasien/{id}', [PasienController::class, 'destroy'])->name('pasien.destroy');
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::post('/invoice/{id}/bayar', [InvoiceController::class, 'bayar'])->name('invoice.bayar');
    Route::post('/invoice/{id}/status', [InvoiceController::class, 'updateStatus'])->name('invoice.status');
    Route::get('/payment', function () { return view('admin.payment'); });
    Route::get('/report', function () { return view('admin.report'); });
    Route::get('/api/stats', [DashboardController::class, 'stats']);
    Route::get('/queue', [PasienController::class, 'queue'])->name('admin.queue');
    Route::post('/pasien/kirim-semua', [PasienController::class, 'kirimSemua'])->name('pasien.kirimSemua');
    Route::post('/pasien/{id}/kirim-dokter', [PasienController::class, 'kirimKeDokter'])->name('pasien.kirimDokter');
});
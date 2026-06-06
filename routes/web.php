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

Route::get('/', function () { return view('login'); });
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| APOTEKER
|--------------------------------------------------------------------------
*/
Route::prefix('apoteker')->middleware(['auth', 'role:apoteker'])->group(function () {

    Route::get('/dashboard', [BatchController::class, 'dashboard'])->name('apoteker.dashboard');
    Route::get('/stock', [BatchController::class, 'index'])->name('apoteker.stock');
    Route::get('/alerts', [BatchController::class, 'alerts'])->name('apoteker.alerts');
    Route::get('/prescription', [ResepController::class, 'apotekerPrescription']);
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
    Route::get('/prescription', [ResepController::class, 'prescription']);
    Route::get('/status', [DokterController::class, 'status']);
    Route::get('/history', [DokterController::class, 'history']);

    // API routes
    Route::get('/api/pasien', [DokterController::class, 'apiPasien']);
    Route::post('/api/pasien/{id}/status', [DokterController::class, 'updateStatus']);
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

    // DATA PASIEN
    Route::get('/data', [PasienController::class, 'index'])->name('pasien.index');
    Route::get('/pasien/create', [PasienController::class, 'create'])->name('pasien.create');
    Route::get('/pasien/cek-nik/{nik}', [PasienController::class, 'cekNik']);
    Route::post('/pasien', [PasienController::class, 'store'])->name('pasien.store');
    Route::post('/pasien/{id}/validasi', [PasienController::class, 'updateValidasi']);
    Route::get('/pasien/{id}/edit', [PasienController::class, 'edit'])->name('pasien.edit');
    Route::put('/pasien/{id}', [PasienController::class, 'update'])->name('pasien.update');
    Route::delete('/pasien/{id}', [PasienController::class, 'destroy'])->name('pasien.destroy');

    // QUEUE
    Route::get('/queue', [PasienController::class, 'queue'])->name('pasien.queue');
    Route::post('/pasien/kirim-semua', [PasienController::class, 'kirimSemua']);
    Route::post('/pasien/{id}/kirim', [PasienController::class, 'kirimKeDokter']);

    // DOKTER
    Route::get('/dokter', [DokterController::class, 'index'])->name('admin.dokter.index');
    Route::post('/dokter', [DokterController::class, 'store'])->name('admin.dokter.store');
    Route::put('/dokter/{id}', [DokterController::class, 'update'])->name('admin.dokter.update');
    Route::delete('/dokter/{id}', [DokterController::class, 'destroy'])->name('admin.dokter.destroy');

    // INVOICE
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/invoice/{id}/download', [InvoiceController::class, 'downloadPdf'])->name('invoice.download');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::post('/invoice/{id}/bayar', [InvoiceController::class, 'bayar'])->name('invoice.bayar');
    Route::post('/invoice/{id}/status', [InvoiceController::class, 'updateStatus'])->name('invoice.status');

    // PAYMENT
    Route::get('/payment', [InvoiceController::class, 'payment'])->name('payment.index');

    // REPORT
    Route::get('/report', [AdminReportController::class, 'index'])->name('admin.report.index');
    Route::get('/report/stats', [AdminReportController::class, 'stats'])->name('admin.report.stats');
});
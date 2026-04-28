<?php

use App\Http\Controllers\BatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('login');
});

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('apoteker')->group(function () {
    Route::get('/dashboard', function () { return view('apoteker.dashboard'); });
    Route::get('/stock', function () { return view('apoteker.stock'); });
    Route::get('/alerts', function () { return view('apoteker.alerts'); });
    Route::get('/prescription', function () { return view('apoteker.prescription'); });
    Route::get('/invoice', function () { return view('apoteker.invoice'); });
    Route::get('/report', function () { return view('apoteker.report'); });
});

Route::prefix('dokter')->group(function () {
    Route::get('/dashboard', function () { return view('dokter.dashboard'); });
    Route::get('/data', function () { return view('dokter.data'); });
    Route::get('/prescription', function () { return view('dokter.prescription'); });
    Route::get('/status', function () { return view('dokter.status'); });
    Route::get('/history', function () { return view('dokter.history'); });
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/data', [PasienController::class, 'index'])->name('pasien.index');
    Route::get('/pasien/create', [PasienController::class, 'create'])->name('pasien.create');
    Route::post('/pasien', [PasienController::class, 'store'])->name('pasien.store');
    Route::post('/pasien/{id}/validasi', [PasienController::class, 'updateValidasi']);

    // Edit, Update, Delete pasien
    Route::get('/pasien/{id}/edit', [PasienController::class, 'edit'])->name('pasien.edit');
    Route::put('/pasien/{id}', [PasienController::class, 'update'])->name('pasien.update');
    Route::delete('/pasien/{id}', [PasienController::class, 'destroy'])->name('pasien.destroy');

    Route::get('/queue', function () { return view('admin.queue'); });
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/payment', function () { return view('admin.payment'); });
    Route::get('/report', function () { return view('admin.report'); });

    Route::get('/batch', [BatchController::class, 'index']);
    Route::post('/batch/store', [BatchController::class, 'store']);

    // API untuk dashboard stats
    Route::get('/api/stats', [DashboardController::class, 'stats']);
});
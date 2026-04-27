<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasienController;

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('login');
});

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| APOTEKER
|--------------------------------------------------------------------------
*/
Route::prefix('apoteker')->group(function () {
    Route::get('/dashboard', function () {
        return view('apoteker.dashboard');
    });

    Route::get('/stock', function () {
        return view('apoteker.stock');
    });

    Route::get('/alerts', function () {
        return view('apoteker.alerts');
    });

    Route::get('/prescription', function () {
        return view('apoteker.prescription');
    });

    Route::get('/invoice', function () {
        return view('apoteker.invoice');
    });

    Route::get('/report', function () {
        return view('apoteker.report');
    });
});

/*
|--------------------------------------------------------------------------
| DOKTER
|--------------------------------------------------------------------------
*/
Route::prefix('dokter')->group(function () {
    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    });

    Route::get('/data', function () {
        return view('dokter.data');
    });

    Route::get('/prescription', function () {
        return view('dokter.prescription');
    });

    Route::get('/status', function () {
        return view('dokter.status');
    });

    Route::get('/history', function () {
        return view('dokter.history');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });

    // ✅ DATA PASIEN — pakai controller supaya $pasiens terkirim ke view
    Route::get('/data', [PasienController::class, 'index'])
        ->name('pasien.index');

    // ✅ CREATE PASIEN (FORM)
    Route::get('/pasien/create', [PasienController::class, 'create'])
        ->name('pasien.create');

    // ✅ STORE PASIEN (SAVE KE DB)
    Route::post('/pasien', [PasienController::class, 'store'])
        ->name('pasien.store');

    Route::get('/queue', function () {
        return view('admin.queue');
    });

    // invoice pakai controller
    Route::get('/invoice', [InvoiceController::class, 'index'])
        ->name('invoice.index');

    Route::get('/payment', function () {
        return view('admin.payment');
    });

    Route::get('/report', function () {
        return view('admin.report');
    });
});
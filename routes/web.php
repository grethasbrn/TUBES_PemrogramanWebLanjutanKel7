<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('login');
});

Route::post('/login', [AuthController::class, 'login']);

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

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });

    Route::get('/payment', function () {
        return view('admin.payment');
    });

    Route::get('/add', function () {
        return view('admin.add');
    });

    Route::get('/data', function () {
        return view('admin.data');
    });
});

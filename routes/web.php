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


Route::get('/', function () { 
    return view('login'); 
});

Route::get('/login', function () { 
    return view('login'); 
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/logout', [AuthController::class, 'logout']);



/*
|--------------------------------------------------------------------------
| APOTEKER
|--------------------------------------------------------------------------
*/

Route::prefix('apoteker')->middleware(['auth', 'role:apoteker'])->group(function () {

    Route::get('/dashboard', [BatchController::class, 'dashboard'])
        ->name('apoteker.dashboard');

    Route::get('/stock', [BatchController::class, 'index'])
        ->name('apoteker.stock');

    Route::get('/alerts', [BatchController::class, 'alerts'])
        ->name('apoteker.alerts');

    Route::get('/prescription', [ResepController::class, 'apotekerPrescription']);

    Route::get('/invoice', [InvoiceController::class, 'apotekerIndex'])
        ->name('apoteker.invoice');

    Route::get('/report', [ReportController::class, 'index'])
        ->name('apoteker.report');

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

});






/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {


    Route::get('/dashboard', [DashboardController::class, 'index']);



    // DATA PASIEN
    Route::get('/data', [PasienController::class, 'index'])
        ->name('pasien.index');

    Route::get('/pasien/create', [PasienController::class, 'create'])
        ->name('pasien.create');

    Route::post('/pasien', [PasienController::class, 'store'])
        ->name('pasien.store');

    Route::get('/pasien/{id}/edit', [PasienController::class, 'edit'])
        ->name('pasien.edit');

    Route::put('/pasien/{id}', [PasienController::class, 'update'])
        ->name('pasien.update');

    Route::delete('/pasien/{id}', [PasienController::class, 'destroy'])
        ->name('pasien.destroy');



    // QUEUE
    Route::get('/queue', [PasienController::class, 'queue'])
        ->name('pasien.queue');

    Route::post('/pasien/{id}/kirim', [PasienController::class, 'kirimKeDokter']);



    // DOKTER
    Route::get('/dokter', [DokterController::class, 'index'])
        ->name('admin.dokter.index');

    Route::post('/dokter', [DokterController::class, 'store'])
        ->name('admin.dokter.store');

    Route::put('/dokter/{id}', [DokterController::class, 'update'])
        ->name('admin.dokter.update');

    Route::delete('/dokter/{id}', [DokterController::class, 'destroy'])
        ->name('admin.dokter.destroy');




    // INVOICE
    Route::get('/invoice', [InvoiceController::class, 'index'])
        ->name('invoice.index');

    Route::post('/invoice/store', [InvoiceController::class, 'store'])
        ->name('invoice.store');

    Route::post('/invoice/{id}/bayar', [InvoiceController::class, 'bayar'])
        ->name('invoice.bayar');




    // PAYMENT (INI YANG FIX)
    Route::get('/payment', [InvoiceController::class, 'payment'])
        ->name('payment.index');




// REPORT
Route::get('/report', [AdminReportController::class, 'index'])
    ->name('admin.report.index');

Route::get('/report/stats', [AdminReportController::class, 'stats'])
    ->name('admin.report.stats');


});
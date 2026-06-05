<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Invoice; 
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function stats()
    {
        $today = Carbon::today();

        $totalHariIni = Pasien::whereDate('created_at', $today)->count();

        $menungguValidasi = Pasien::where('validasi', 'Menunggu')->count();

        $antriPerPoli = Pasien::whereDate('created_at', $today)
            ->selectRaw('poli_tujuan, count(*) as total')
            ->groupBy('poli_tujuan')
            ->get();

        $statusPasien = Pasien::whereDate('created_at', $today)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        $aktivitas = Pasien::latest()->take(5)->get(['nama', 'no_rm', 'status', 'created_at']);

        $invoiceMasuk = Invoice::where('status', 'masuk')->count();

        $pemasukanHariIni = Invoice::where('status', 'Lunas')
            ->whereDate('updated_at', $today) 
            ->sum('total_tagihan');

        // PERBAIKAN: Menggunakan standar Koleksi Laravel agar terhindar dari error typo loop konvensional
        $pemasukan7Hari = collect(range(6, 0))->map(function ($i) {
            return (float) Invoice::where('status', 'Lunas')
                ->whereDate('updated_at', Carbon::today()->subDays($i))
                ->sum('total_tagihan');
        })->all();

        return response()->json([
            'total_hari_ini'    => (int)$totalHariIni,
            'menunggu_validasi' => (int)$menungguValidasi,
            'invoice'           => (int)$invoiceMasuk,
            'pemasukan'         => (float)$pemasukanHariIni, 
            'antri_per_poli'    => $antriPerPoli,
            'status_pasien'     => $statusPasien,
            'aktivitas'         => $aktivitas,
            'pemasukan_7_hari'  => $pemasukan7Hari 
        ]);
    }
}
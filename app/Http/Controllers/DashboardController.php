<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
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

        // Total pasien hari ini
        $totalHariIni = Pasien::whereDate('created_at', $today)->count();

        // Menunggu validasi
        $menungguValidasi = Pasien::where('validasi', 'Menunggu')->count();

        // Antrian per poli (semua pasien aktif hari ini)
        $antriPerPoli = Pasien::whereDate('created_at', $today)
            ->selectRaw('poli_tujuan, count(*) as total')
            ->groupBy('poli_tujuan')
            ->get();

        // Status pasien hari ini
        $statusPasien = Pasien::whereDate('created_at', $today)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        // Aktivitas terbaru (5 pasien terakhir)
        $aktivitas = Pasien::latest()->take(5)->get(['nama', 'no_rm', 'status', 'created_at']);

        return response()->json([
            'total_hari_ini'    => $totalHariIni,
            'menunggu_validasi' => $menungguValidasi,
            'invoice'           => 0, // bisa disambung ke model Invoice nanti
            'pemasukan'         => 0, // bisa disambung ke model Payment nanti
            'antri_per_poli'    => $antriPerPoli,
            'status_pasien'     => $statusPasien,
            'aktivitas'         => $aktivitas,
        ]);
    }
}
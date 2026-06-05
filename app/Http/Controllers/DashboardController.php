<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // Antrian per poli
        $antriPerPoli = Pasien::whereDate('created_at', $today)
            ->selectRaw('poli_tujuan, count(*) as total')
            ->groupBy('poli_tujuan')
            ->get();

        // Status pasien hari ini
        $statusPasien = Pasien::whereDate('created_at', $today)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        // Aktivitas terbaru
        $aktivitas = Pasien::latest()->take(5)->get(['nama', 'no_rm', 'status', 'created_at']);

        // Invoice masuk hari ini
        $invoiceHariIni = DB::table('invoices')
            ->whereDate('created_at', $today)
            ->count();

        // Pemasukan hari ini (status Lunas)
        $pemasukanHariIni = DB::table('invoices')
            ->whereDate('created_at', $today)
            ->where('status', 'Lunas')
            ->sum('total_tagihan');

        // Jumlah transaksi lunas hari ini
        $transaksiLunas = DB::table('invoices')
            ->whereDate('created_at', $today)
            ->where('status', 'Lunas')
            ->count();

        // Chart pemasukan 7 hari terakhir
        $pemasukan7Hari = [];
        $labels7Hari    = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl    = Carbon::today()->subDays($i);
            $total  = DB::table('invoices')
                ->whereDate('created_at', $tgl)
                ->where('status', 'Lunas')
                ->sum('total_tagihan');
            $pemasukan7Hari[] = (int) $total;
            $labels7Hari[]    = $tgl->translatedFormat('D, d M') ?? $tgl->format('D, d M');
        }

        return response()->json([
            'total_hari_ini'    => $totalHariIni,
            'menunggu_validasi' => $menungguValidasi,
            'invoice'           => $invoiceHariIni,
            'pemasukan'         => (int) $pemasukanHariIni,
            'transaksi_lunas'   => $transaksiLunas,
            'antri_per_poli'    => $antriPerPoli,
            'status_pasien'     => $statusPasien,
            'aktivitas'         => $aktivitas,
            'pemasukan_7hari'   => $pemasukan7Hari,
            'labels_7hari'      => $labels7Hari,
        ]);
    }
}
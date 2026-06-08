<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * ✅ FIX: Semua query yang pakai kolom kunjungan (validasi, poli_tujuan, status)
     * dipindah ke model Kunjungan — kolom-kolom itu sudah tidak ada di tabel pasiens
     *
     * Bug lama:
     *   Pasien::where('validasi', 'Menunggu')          → kolom tidak ada di pasiens
     *   Pasien::selectRaw('poli_tujuan, ...')          → kolom tidak ada di pasiens
     *   Pasien::selectRaw('status, ...')               → kolom tidak ada di pasiens
     */
    public function stats()
    {
        $today = Carbon::today();

        // Total pasien baru hari ini (tetap dari pasiens — tgl daftar master)
        $totalHariIni = Pasien::whereDate('created_at', $today)->count();

        // ✅ FIX: menungguValidasi → dari kunjungans
        $menungguValidasi = Kunjungan::where('validasi', 'Menunggu')->count();

        // ✅ FIX: antriPerPoli → dari kunjungans
        $antriPerPoli = Kunjungan::whereDate('created_at', $today)
            ->selectRaw('poli_tujuan, count(*) as total')
            ->groupBy('poli_tujuan')
            ->get();

        // ✅ FIX: statusPasien → dari kunjungans
        $statusKunjungan = Kunjungan::whereDate('created_at', $today)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        // Aktivitas terbaru: pasien terbaru (nama & no_rm tetap dari pasiens)
        $aktivitas = Pasien::latest()->take(5)->get(['nama', 'no_rm', 'created_at']);

        $invoiceHariIni    = DB::table('invoices')->whereDate('created_at', $today)->count();
        $pemasukanHariIni  = DB::table('invoices')->whereDate('created_at', $today)->where('status', 'Lunas')->sum('total_tagihan');
        $transaksiLunas    = DB::table('invoices')->whereDate('created_at', $today)->where('status', 'Lunas')->count();

        $pemasukan7Hari = [];
        $labels7Hari    = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $pemasukan7Hari[] = (int) DB::table('invoices')
                ->whereDate('created_at', $tgl)
                ->where('status', 'Lunas')
                ->sum('total_tagihan');
            $labels7Hari[] = $tgl->translatedFormat('D, d M') ?? $tgl->format('D, d M');
        }

        return response()->json([
            'total_hari_ini'    => $totalHariIni,
            'menunggu_validasi' => $menungguValidasi,
            'invoice'           => $invoiceHariIni,
            'pemasukan'         => (int) $pemasukanHariIni,
            'transaksi_lunas'   => $transaksiLunas,
            'antri_per_poli'    => $antriPerPoli,
            'status_pasien'     => $statusKunjungan,  // tetap key sama agar frontend tidak perlu diubah
            'aktivitas'         => $aktivitas,
            'pemasukan_7hari'   => $pemasukan7Hari,
            'labels_7hari'      => $labels7Hari,
        ]);
    }
}
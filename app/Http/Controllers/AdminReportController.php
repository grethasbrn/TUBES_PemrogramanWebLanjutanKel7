<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.report');
    }

    /**
     * ✅ FIX: Query poli_tujuan & trend kunjungan dipindah ke tabel kunjungans
     *
     * Bug lama:
     *   DB::table('pasiens')->selectRaw('poli_tujuan, ...')
     *   → kolom poli_tujuan sudah tidak ada di pasiens, ada di kunjungans
     *
     *   JOIN invoices → pasiens untuk ambil poli_tujuan
     *   → pasiens tidak punya kolom itu, hasilnya selalu null
     */
    public function stats(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Total pasien baru (master) — tetap dari pasiens
        $totalPasien = DB::table('pasiens')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count();

        $totalInvoice = DB::table('invoices')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count();

        $totalPemasukan = DB::table('invoices')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'Lunas')
            ->sum('total_tagihan');

        $totalTagihan = DB::table('invoices')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->sum('total_tagihan');

        // ✅ FIX: Trend kunjungan — dari tabel kunjungans (bukan pasiens)
        $trendData = DB::table('kunjungans')
            ->selectRaw('WEEK(created_at, 1) as minggu, COUNT(*) as total')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupByRaw('WEEK(created_at, 1)')
            ->orderByRaw('WEEK(created_at, 1)')
            ->get();

        $trendLabels = $trendData->map(fn($r) => 'Minggu ke-' . ($r->minggu % 4 + 1))->values();
        $trendValues = $trendData->pluck('total')->values();

        // Distribusi jenis pasien — tetap dari pasiens (kolom jenis ada di sana)
        $jenisPasien = DB::table('pasiens')
            ->selectRaw('jenis, COUNT(*) as total')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('jenis')
            ->get();

        // ✅ FIX: Per poli — dari kunjungans (poli_tujuan ada di sana)
        $perPoli = DB::table('kunjungans')
            ->selectRaw('poli_tujuan, COUNT(*) as total')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->whereNotNull('poli_tujuan')
            ->groupBy('poli_tujuan')
            ->orderByDesc('total')
            ->get();

        $lunas = DB::table('invoices')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'Lunas')
            ->sum('total_tagihan');

        $belumLunas = DB::table('invoices')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', '!=', 'Lunas')
            ->sum('total_tagihan');

        // ✅ FIX: JOIN invoices ke kunjungans via reseps untuk dapat poli_tujuan
        // Bug lama: JOIN ke pasiens — pasiens tidak punya kolom poli_tujuan
        $detail = DB::table('invoices')
            ->leftJoin('reseps', 'invoices.resep_id', '=', 'reseps.id')
            ->leftJoin('kunjungans', 'reseps.kunjungan_id', '=', 'kunjungans.id')
            ->selectRaw('
                invoices.no_invoice,
                invoices.nama,
                kunjungans.poli_tujuan,
                invoices.jenis,
                invoices.total_tagihan,
                invoices.no_referensi,
                invoices.status,
                invoices.created_at
            ')
            ->whereMonth('invoices.created_at', $bulan)
            ->whereYear('invoices.created_at', $tahun)
            ->orderByDesc('invoices.created_at')
            ->get()
            ->map(function ($row) {
                $row->poli_tujuan  = $row->poli_tujuan  ?? '-';
                $row->no_referensi = $row->no_referensi ?? '-';
                return $row;
            });

        return response()->json([
            'stats'        => [
                'total_pasien'    => $totalPasien,
                'total_invoice'   => $totalInvoice,
                'total_pemasukan' => $totalPemasukan,
                'total_tagihan'   => $totalTagihan,
            ],
            'trend'        => ['labels' => $trendLabels, 'values' => $trendValues],
            'jenis_pasien' => $jenisPasien,
            'per_poli'     => $perPoli,
            'keuangan'     => ['lunas' => $lunas, 'belum_lunas' => $belumLunas],
            'detail'       => $detail,
        ]);
    }
}
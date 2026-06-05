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

    public function stats(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // --- Stats Kartu ---
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

        // --- Tren Kunjungan (per minggu dalam bulan) ---
        $trendData = DB::table('pasiens')
            ->selectRaw('WEEK(created_at, 1) as minggu, COUNT(*) as total')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupByRaw('WEEK(created_at, 1)')
            ->orderByRaw('WEEK(created_at, 1)')
            ->get();

        $trendLabels = $trendData->map(fn($r) => 'Minggu ke-' . ($r->minggu % 4 + 1))->values();
        $trendValues = $trendData->pluck('total')->values();

        // --- Distribusi Jenis Pasien ---
        $jenisPasien = DB::table('pasiens')
            ->selectRaw('jenis, COUNT(*) as total')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('jenis')
            ->get();

        // --- Pasien per Poli ---
        $perPoli = DB::table('pasiens')
            ->selectRaw('poli_tujuan, COUNT(*) as total')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->whereNotNull('poli_tujuan')
            ->groupBy('poli_tujuan')
            ->orderByDesc('total')
            ->get();

        // --- Ringkasan Keuangan ---
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

        // --- Detail Transaksi ---
        $detail = DB::table('invoices')
            ->leftJoin('pasiens', 'invoices.no_rm', '=', 'pasiens.no_rm')
            ->selectRaw('
                invoices.no_invoice,
                invoices.nama,
                pasiens.poli_tujuan,
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
                $row->poli_tujuan = $row->poli_tujuan ?? '-';
                $row->no_referensi = $row->no_referensi ?? '-';
                return $row;
            });

        return response()->json([
            'stats' => [
                'total_pasien'    => $totalPasien,
                'total_invoice'   => $totalInvoice,
                'total_pemasukan' => $totalPemasukan,
                'total_tagihan'   => $totalTagihan,
            ],
            'trend' => [
                'labels' => $trendLabels,
                'values' => $trendValues,
            ],
            'jenis_pasien' => $jenisPasien,
            'per_poli'     => $perPoli,
            'keuangan' => [
                'lunas'       => $lunas,
                'belum_lunas' => $belumLunas,
            ],
            'detail' => $detail,
        ]);
    }
}

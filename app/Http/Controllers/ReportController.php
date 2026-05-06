<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Batch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('apoteker.report');
    }

    public function apiStats(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year  = $request->year  ?? Carbon::now()->year;

        $prevMonth = Carbon::create($year, $month, 1)->subMonth();

        // Total resep bulan ini
        $totalResep = Resep::whereMonth('created_at', $month)
                           ->whereYear('created_at', $year)
                           ->count();

        // Total resep bulan lalu
        $totalResepLalu = Resep::whereMonth('created_at', $prevMonth->month)
                               ->whereYear('created_at', $prevMonth->year)
                               ->count();

        // Resep selesai bulan ini
        $resepSelesai = Resep::whereMonth('created_at', $month)
                             ->whereYear('created_at', $year)
                             ->where('status', 'selesai')
                             ->count();

        // Completion rate
        $completionRate = $totalResep > 0
            ? round(($resepSelesai / $totalResep) * 100, 1)
            : 0;

        // % perubahan resep
        $pctResep = $totalResepLalu > 0
            ? round((($totalResep - $totalResepLalu) / $totalResepLalu) * 100, 1)
            : 0;

        // Ambil SEMUA resep bulan ini (tidak hanya selesai)
        $resepsBulanIni = Resep::whereMonth('created_at', $month)
                               ->whereYear('created_at', $year)
                               ->whereNotIn('status', ['ditolak', 'draft'])
                               ->get();

        $totalPendapatan = 0;
        $obatCount = [];

        foreach ($resepsBulanIni as $r) {
            foreach ($r->obat_list ?? [] as $obat) {
                $namaObat = $obat['nama'] ?? '';
                $qty      = intval($obat['qty'] ?? 1);

                $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($namaObat) . '%'])
                              ->first();
                $harga = $batch ? floatval($batch->harga) : floatval($obat['harga'] ?? 0);

                $totalPendapatan += $harga * $qty;

                if ($namaObat) {
                    $obatCount[$namaObat] = ($obatCount[$namaObat] ?? 0) + $qty;
                }
            }
        }

        // Pendapatan bulan lalu
        $resepsBulanLalu = Resep::whereMonth('created_at', $prevMonth->month)
                                ->whereYear('created_at', $prevMonth->year)
                                ->whereNotIn('status', ['ditolak', 'draft'])
                                ->get();

        $pendapatanLalu = 0;
        foreach ($resepsBulanLalu as $r) {
            foreach ($r->obat_list ?? [] as $obat) {
                $namaObat = $obat['nama'] ?? '';
                $qty      = intval($obat['qty'] ?? 1);
                $batch    = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($namaObat) . '%'])->first();
                $harga    = $batch ? floatval($batch->harga) : floatval($obat['harga'] ?? 0);
                $pendapatanLalu += $harga * $qty;
            }
        }

        $pctPendapatan = $pendapatanLalu > 0
            ? round((($totalPendapatan - $pendapatanLalu) / $pendapatanLalu) * 100, 1)
            : 0;

        // Top 10 obat terlaris
        arsort($obatCount);
        $topObat    = array_slice($obatCount, 0, 10, true);
        $topObatArr = [];
        foreach ($topObat as $nama => $qty) {
            $topObatArr[] = ['nama' => $nama, 'qty' => $qty];
        }

        // Pendapatan per minggu
        $weeks       = [];
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        for ($w = 1; $w <= 4; $w++) {
            $startDay = ($w - 1) * 7 + 1;
            $endDay   = $w === 4 ? $daysInMonth : $w * 7;
            $start    = Carbon::create($year, $month, $startDay)->startOfDay();
            $end      = Carbon::create($year, $month, $endDay)->endOfDay();

            $resepsMinggu = Resep::whereBetween('created_at', [$start, $end])
                                 ->whereNotIn('status', ['ditolak', 'draft'])
                                 ->get();

            $pendapatanMinggu = 0;
            foreach ($resepsMinggu as $r) {
                foreach ($r->obat_list ?? [] as $obat) {
                    $namaObat = $obat['nama'] ?? '';
                    $qty      = intval($obat['qty'] ?? 1);
                    $batch    = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($namaObat) . '%'])->first();
                    $harga    = $batch ? floatval($batch->harga) : floatval($obat['harga'] ?? 0);
                    $pendapatanMinggu += $harga * $qty;
                }
            }

            $weeks[] = ['label' => 'W' . $w, 'pendapatan' => $pendapatanMinggu];
        }

        return response()->json([
            'totalResep'       => $totalResep,
            'pctResep'         => $pctResep,
            'resepSelesai'     => $resepSelesai,
            'completionRate'   => $completionRate,
            'totalPendapatan'  => $totalPendapatan,
            'pctPendapatan'    => $pctPendapatan,
            'topObat'          => $topObatArr,
            'pendapatanMinggu' => $weeks,
        ]);
    }
}
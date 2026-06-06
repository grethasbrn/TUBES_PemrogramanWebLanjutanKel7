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

        $totalResep = Resep::whereMonth('created_at', $month)
                           ->whereYear('created_at', $year)
                           ->count();

        $totalResepLalu = Resep::whereMonth('created_at', $prevMonth->month)
                               ->whereYear('created_at', $prevMonth->year)
                               ->count();

        $resepSelesai = Resep::whereMonth('created_at', $month)
                             ->whereYear('created_at', $year)
                             ->where('status', 'selesai')
                             ->count();

        $completionRate = $totalResep > 0
            ? round(($resepSelesai / $totalResep) * 100, 1)
            : 0;

        $pctResep = $totalResepLalu > 0
            ? round((($totalResep - $totalResepLalu) / $totalResepLalu) * 100, 1)
            : 0;

        // Preload SEMUA batch obat sekali saja — hindari N+1 query
        $semuaBatch = Batch::all()->keyBy(fn($b) => strtolower($b->nama_obat));

        $cariHarga = function (string $namaObat) use ($semuaBatch): float {
            $key = strtolower($namaObat);
            // Cari exact match dulu, fallback ke contains match
            if ($semuaBatch->has($key)) {
                return (float) $semuaBatch[$key]->harga;
            }
            $found = $semuaBatch->first(fn($b) => str_contains(strtolower($b->nama_obat), $key));
            return $found ? (float) $found->harga : 0;
        };

        $hitungPendapatan = function ($reseps) use ($cariHarga): float {
            $total = 0;
            foreach ($reseps as $r) {
                foreach ($r->obat_list ?? [] as $obat) {
                    $namaObat = $obat['nama'] ?? '';
                    $qty      = intval($obat['qty'] ?? 1);
                    $harga    = floatval($obat['harga'] ?? 0) ?: $cariHarga($namaObat);
                    $total   += $harga * $qty;
                }
            }
            return $total;
        };

        $resepsBulanIni = Resep::whereMonth('created_at', $month)
                               ->whereYear('created_at', $year)
                               ->whereNotIn('status', ['ditolak', 'draft'])
                               ->get();

        $totalPendapatan = $hitungPendapatan($resepsBulanIni);

        $obatCount = [];
        foreach ($resepsBulanIni as $r) {
            foreach ($r->obat_list ?? [] as $obat) {
                $namaObat = $obat['nama'] ?? '';
                $qty      = intval($obat['qty'] ?? 1);
                if ($namaObat) {
                    $obatCount[$namaObat] = ($obatCount[$namaObat] ?? 0) + $qty;
                }
            }
        }

        $resepsBulanLalu = Resep::whereMonth('created_at', $prevMonth->month)
                                ->whereYear('created_at', $prevMonth->year)
                                ->whereNotIn('status', ['ditolak', 'draft'])
                                ->get();

        $pendapatanLalu  = $hitungPendapatan($resepsBulanLalu);

        $pctPendapatan = $pendapatanLalu > 0
            ? round((($totalPendapatan - $pendapatanLalu) / $pendapatanLalu) * 100, 1)
            : 0;

        arsort($obatCount);
        $topObatArr = [];
        foreach (array_slice($obatCount, 0, 10, true) as $nama => $qty) {
            $topObatArr[] = ['nama' => $nama, 'qty' => $qty];
        }

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

            $weeks[] = ['label' => 'W' . $w, 'pendapatan' => $hitungPendapatan($resepsMinggu)];
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
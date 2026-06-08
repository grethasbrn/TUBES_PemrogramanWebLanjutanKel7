<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Resep;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BatchController extends Controller
{
    public function dashboard()
    {
        $totalJenisObat = Batch::distinct('nama_obat')->count('nama_obat');

        $tambahBulanIni = Batch::whereMonth('tgl_masuk', Carbon::now()->month)
                               ->whereYear('tgl_masuk', Carbon::now()->year)
                               ->distinct('nama_obat')
                               ->count('nama_obat');

        $resepHariIni   = Resep::whereDate('created_at', Carbon::today())->count();
        $resepKemarin   = Resep::whereDate('created_at', Carbon::yesterday())->count();
        $selisihKemarin = $resepHariIni - $resepKemarin;

        $stokKritis = Batch::where('jumlah', '<=', 10)->count();

        $mendekatiExpired = Batch::whereBetween('tgl_expired', [
                                Carbon::today(),
                                Carbon::today()->addDays(90)
                            ])->count();

        return view('apoteker.dashboard', compact(
            'totalJenisObat',
            'tambahBulanIni',
            'resepHariIni',
            'selisihKemarin',
            'stokKritis',
            'mendekatiExpired'
        ));
    }

    // ── API untuk widget dashboard ──────────────────────
    public function apiDashboard()
    {
        // Resep 7 hari terakhir
        $resep7hari = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'tanggal' => $date->format('d/m'),
                'jumlah'  => Resep::whereDate('created_at', $date)->count(),
            ];
        });

        // Alert aktif
        $alerts = collect();

        $expired = Batch::where('tgl_expired', '<', Carbon::today())->get();
        foreach ($expired as $b) {
            $alerts->push([
                'icon'  => '🔴',
                'pesan' => "{$b->nama_obat} sudah expired",
                'sub'   => 'Expired: ' . Carbon::parse($b->tgl_expired)->format('d/m/Y'),
            ]);
        }

        $mendekati = Batch::whereBetween('tgl_expired', [
            Carbon::today(), Carbon::today()->addDays(90)
        ])->get();
        foreach ($mendekati as $b) {
            $alerts->push([
                'icon'  => '🟡',
                'pesan' => "{$b->nama_obat} mendekati expired",
                'sub'   => 'Expired: ' . Carbon::parse($b->tgl_expired)->format('d/m/Y'),
            ]);
        }

        $kritis = Batch::where('jumlah', '<=', 10)->where('jumlah', '>', 0)->get();
        foreach ($kritis as $b) {
            $alerts->push([
                'icon'  => '🟠',
                'pesan' => "{$b->nama_obat} stok kritis",
                'sub'   => "Sisa: {$b->jumlah} unit",
            ]);
        }

        // Aktivitas terbaru (resep terbaru)
        $aktivitas = Resep::with('kunjungan.pasien')->latest()->take(8)->get()->map(function ($r) {
            $statusMap = [
                'baru'     => '📥 Resep baru masuk',
                'validasi' => '✅ Resep divalidasi',
                'siap'     => '💊 Resep siap diambil',
                'selesai'  => '🎉 Resep selesai',
                'ditolak'  => '❌ Resep ditolak',
                'draft'    => '📝 Resep draft',
            ];
            return [
                'icon'  => '📋',
                'pesan' => ($statusMap[$r->status] ?? 'Resep') . ' — ' . ($r->kunjungan?->pasien?->nama ?? '-'),
                'waktu' => $r->created_at->diffForHumans(),
            ];
        });

        // Distribusi tipe obat
        $distribusiTipe = Batch::selectRaw('tipe, COUNT(*) as jumlah')
            ->whereNotNull('tipe')
            ->groupBy('tipe')
            ->get()
            ->map(fn($b) => ['tipe' => $b->tipe, 'jumlah' => $b->jumlah]);

        return response()->json([
            'resep7hari'     => $resep7hari,
            'alerts'         => $alerts->values(),
            'aktivitas'      => $aktivitas,
            'distribusiTipe' => $distribusiTipe,
        ]);
    }

    public function alerts()
    {
        $sudahExpired = Batch::where('tgl_expired', '<', Carbon::today())->get();

        $mendekatiExpired = Batch::whereBetween('tgl_expired', [
            Carbon::today(),
            Carbon::today()->addDays(90)
        ])->get();

        $stokKritis = Batch::where('jumlah', '<=', 10)->get();

        return view('apoteker.alerts', compact(
            'sudahExpired',
            'mendekatiExpired',
            'stokKritis'
        ));
    }

    public function index()
    {
        $batches = Batch::all();
        $nextNoBatch = $this->generateNoBatch();

        $stockData = $batches->map(function($b) {
            $tglExpired = $b->tgl_expired?->format('Y-m-d');

            if (!$b->tgl_expired) {
                $status = 'aman';
            } elseif ($b->tgl_expired->isPast()) {
                $status = 'expired';
            } elseif (now()->diffInDays($b->tgl_expired) <= 90) {
                $status = 'exp-soon';
            } elseif ($b->jumlah <= 10) {
                $status = 'kritis';
            } else {
                $status = 'aman';
            }

            return [
                'id'          => $b->id,
                'nama_obat'   => $b->nama_obat,
                'tipe'        => $b->tipe,
                'no_batch'    => $b->no_batch,
                'jumlah'      => $b->jumlah,
                'harga'       => $b->harga,
                'tgl_expired' => $tglExpired,
                'status'      => $status,
            ];
        });

        return view('apoteker.stock', compact('batches', 'stockData', 'nextNoBatch'));
    }

    private function generateNoBatch(): string
    {
        $last = Batch::orderBy('id', 'desc')->first();
        $nextNumber = $last ? $last->id + 1 : 1;
        return 'BCH-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'nama_obat'   => 'required|string|max:255',
            'tipe'        => 'required|string|max:100',
            'kategori'    => 'required|in:mandiri,bpjs',
            'no_batch'    => 'required|string|unique:batches,no_batch',
            'jumlah'      => 'required|integer|min:1',
            'harga'       => 'required|integer|min:0',
            'tgl_expired' => 'required|date|after_or_equal:today',
            'tgl_masuk'   => 'required|date',
            'supplier'    => 'nullable|string|max:255',
            'harga_bpjs' => 'nullable|integer|min:0',
        ]);

        Batch::create([
            'nama_obat'   => $request->nama_obat,
            'tipe'        => $request->tipe,
            'kategori'    => $request->kategori,
            'no_batch'    => $request->no_batch,
            'jumlah'      => $request->jumlah,
            'harga'       => $request->harga,
            'harga_bpjs' => $request->input('harga_bpjs', 0),
            'tgl_expired' => $request->tgl_expired,
            'tgl_masuk'   => $request->tgl_masuk,
            'supplier'    => $request->supplier,
        ]);

        return redirect()->route('batch.index')->with('success', 'Batch obat berhasil ditambahkan!');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return redirect()->route('batch.index')->with('success', 'Batch obat berhasil dihapus.');
    }
}
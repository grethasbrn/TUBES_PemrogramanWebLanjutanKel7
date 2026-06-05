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

    // ✅ METHOD BARU
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

        return view('apoteker.stock', compact('batches', 'stockData'));
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
            'tgl_expired' => 'required|date|after:today',
            'tgl_masuk'   => 'required|date',
            'supplier'    => 'nullable|string|max:255',
        ], [
            'nama_obat.required'   => 'Nama obat wajib diisi.',
            'tipe.required'        => 'Tipe obat wajib diisi.',
            'kategori.required'    => 'Kategori obat wajib dipilih.',
            'no_batch.required'    => 'No batch wajib diisi.',
            'no_batch.unique'      => 'No batch sudah digunakan.',
            'jumlah.required'      => 'Jumlah wajib diisi.',
            'harga.required'       => 'Harga wajib diisi.',
            'tgl_expired.required' => 'Tanggal expired wajib diisi.',
            'tgl_expired.after'    => 'Tanggal expired harus setelah hari ini.',
            'tgl_masuk.required'   => 'Tanggal masuk wajib diisi.',
        ]);

        Batch::create([
            'nama_obat'   => $request->nama_obat,
            'tipe'        => $request->tipe,
            'kategori'    => $request->kategori,
            'no_batch'    => $request->no_batch,
            'jumlah'      => $request->jumlah,
            'harga'       => $request->harga,
            'harga_bpjs'  => 0,
            'tgl_expired' => $request->tgl_expired,
            'tgl_masuk'   => $request->tgl_masuk,
            'supplier'    => $request->supplier,
        ]);

        return redirect()->route('batch.index')
            ->with('success', 'Batch obat berhasil ditambahkan!');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return redirect()->route('batch.index')
            ->with('success', 'Batch obat berhasil dihapus.');
    }
}
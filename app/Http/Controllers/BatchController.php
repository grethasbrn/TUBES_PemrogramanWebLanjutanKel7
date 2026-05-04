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
        // Total jenis obat (distinct nama obat)
        $totalJenisObat = Batch::distinct('nama_obat')->count('nama_obat');

        // Tambahan bulan ini
        $tambahBulanIni = Batch::whereMonth('tgl_masuk', Carbon::now()->month)
                               ->whereYear('tgl_masuk', Carbon::now()->year)
                               ->distinct('nama_obat')
                               ->count('nama_obat');

        // Resep hari ini
        $resepHariIni   = Resep::whereDate('created_at', Carbon::today())->count();
        $resepKemarin   = Resep::whereDate('created_at', Carbon::yesterday())->count();
        $selisihKemarin = $resepHariIni - $resepKemarin;

        // Stok kritis: jumlah <= 10 (sesuaikan angkanya jika perlu)
        $stokKritis = Batch::where('jumlah', '<=', 10)->count();

        // Mendekati expired dalam 90 hari
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

    public function index()
    {
        $batches = Batch::all(); 
        return view('apoteker.stock', compact('batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat'   => 'required|string|max:255',
            'tipe'        => 'required|string|max:100',
            'no_batch'    => 'required|string|unique:batches,no_batch',
            'jumlah'      => 'required|integer|min:1',
            'harga'       => 'required|integer|min:0',
            'harga_bpjs'  => 'required|integer|min:0',
            'tgl_expired' => 'required|date|after:today',
            'tgl_masuk'   => 'required|date',
            'supplier'    => 'nullable|string|max:255',
        ], [
            'nama_obat.required'   => 'Nama obat wajib diisi.',
            'tipe.required'        => 'Tipe obat wajib diisi.',
            'no_batch.required'    => 'No batch wajib diisi.',
            'no_batch.unique'      => 'No batch sudah digunakan.',
            'jumlah.required'      => 'Jumlah wajib diisi.',
            'jumlah.min'           => 'Jumlah minimal 1.',
            'harga.required'       => 'Harga wajib diisi.',
            'harga_bpjs.required'  => 'Harga BPJS wajib diisi.',
            'tgl_expired.required' => 'Tanggal expired wajib diisi.',
            'tgl_expired.after'    => 'Tanggal expired harus setelah hari ini.',
            'tgl_masuk.required'   => 'Tanggal masuk wajib diisi.',
        ]);

        Batch::create($request->all());

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
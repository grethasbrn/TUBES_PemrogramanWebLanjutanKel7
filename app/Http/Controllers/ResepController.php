<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Batch;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ResepController extends Controller
{
    /**
     * Simpan resep baru dari dokter
     */
    public function store(Request $request)
    {
        $request->validate([
            'pasien_id'  => 'required|exists:pasiens,id',
            'diagnosa'   => 'required|string',
            'obat_list'  => 'required|array',
            'status'     => 'required|in:draft,baru',
        ]);

        $resep = Resep::create([
            'pasien_id'       => $request->pasien_id,
            'no_resep'        => Resep::generateNoResep(),
            'diagnosa'        => $request->diagnosa,
            'catatan_dokter'  => $request->catatan_dokter,
            'tanggal_kontrol' => $request->tanggal_kontrol,
            'status'          => $request->status,
            'obat_list'       => $request->obat_list,
        ]);

        // Update status pasien jika resep dikirim (bukan draft)
        if ($request->status === 'baru') {
            $pasien = Pasien::find($request->pasien_id);
            if ($pasien) {
                $pasien->status = 'Selesai';
                $pasien->save();
            }
        }

        return response()->json([
            'success'  => true,
            'no_resep' => $resep->no_resep,
            'resep'    => $resep,
        ]);
    }

    /**
     * API: Ambil semua resep (tidak dibatasi hari ini)
     */
    public function index()
    {
        $reseps = Resep::with('pasien')
            ->latest()
            ->get()
            ->map(function ($r) {
                // Cek stok dan harga untuk setiap obat
                $obatList = collect($r->obat_list ?? [])->map(function ($obat) {
                    $namaObat = $obat['nama'] ?? '';

                    $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($namaObat) . '%'])
                        ->where('jumlah', '>', 0)
                        ->where(function ($q) {
                            $q->whereNull('tgl_expired')
                              ->orWhere('tgl_expired', '>', now());
                        })
                        ->first();

                    $jenisPasien = $r->pasien->jenis ?? 'Mandiri'; // ambil dari data pasien

                    $obat['stok']  = $batch ? $batch->jumlah : 0;
                    $obat['harga'] = $batch
                        ? ($jenisPasien === 'BPJS' ? 0 : (float) $batch->harga)
                        : 0;

                    return $obat;
                })->toArray();

                return [
                    'id'       => (string) $r->id,
                    'pasienId' => (string) $r->pasien_id,
                    'no_resep' => $r->no_resep ?? '-',
                    'pasien'   => $r->pasien->nama ?? '-',
                    'rm'       => $r->pasien->no_rm ?? '-',
                    'dokter'   => $r->pasien->dokter ?? '-',
                    'bayar'    => $r->pasien->jenis ?? 'Mandiri',
                    'diagnosa' => $r->diagnosa ?? '-',
                    'tanggal'  => $r->created_at->toDateString(),
                    'status'   => $r->status ?? 'baru',
                    'obat'     => $obatList,
                ];
            });

        return response()->json($reseps);
    }

    /**
     * Update status resep
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,baru,validasi,siap,selesai,ditolak',
        ]);

        $resep = Resep::findOrFail($id);
        $resep->status = $request->status;
        $resep->save();

        return response()->json([
            'success'  => true,
            'status'   => $resep->status,
            'no_resep' => $resep->no_resep,
        ]);
    }

    public function prescription()
    {
        return view('dokter.prescription');
    }

}
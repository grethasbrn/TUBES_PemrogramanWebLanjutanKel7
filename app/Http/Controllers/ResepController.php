<?php

namespace App\Http\Controllers;

use App\Models\Resep;
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
            $pasien->status = 'Selesai';
            $pasien->save();
        }

        return response()->json([
            'success'  => true,
            'no_resep' => $resep->no_resep,
            'resep'    => $resep,
        ]);
    }

    /**
     * API: Ambil semua resep hari ini
     */
    public function index()
    {
        $reseps = Resep::with('pasien')
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id'       => (string) $r->id,
                    'pasienId' => (string) $r->pasien_id,
                    'pasien'   => $r->pasien->nama ?? '-',
                    'rm'       => $r->pasien->no_rm ?? '-',
                    'bayar'    => $r->pasien->jenis ?? '-',
                    'diagnosa' => $r->diagnosa,
                    'tanggal'  => $r->created_at->toDateString(),
                    'status'   => $r->status,
                    'obat'     => $r->obat_list,
                ];
            });

        return response()->json($reseps);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,baru,validasi,siap,selesai,ditolak',
        ]);

        $resep = Resep::findOrFail($id);
        $resep->status = $request->status;
        $resep->save();

        return response()->json([
            'success' => true,
            'status'  => $resep->status,
            'no_resep' => $resep->no_resep,
        ]);
    }
}
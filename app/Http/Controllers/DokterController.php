<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Resep;
use Carbon\Carbon;

class DokterController extends Controller
{
    /**
     * Dashboard dokter - tampilkan statistik hari ini
     */

    public function dashboard()
    {
    $today = Carbon::today();

    $pasienHariIni = Pasien::whereDate('created_at', $today)
        ->whereIn('status', ['Menunggu', 'Diperiksa'])
        ->count();

    $antrian = Pasien::whereDate('created_at', $today)
        ->orderBy('created_at')
        ->take(8)
        ->get(['id', 'nama', 'no_rm', 'jenis', 'poli_tujuan', 'status', 'keluhan']);

    $resepTerbaru = Resep::with('pasien')
        ->whereDate('created_at', $today)
        ->latest()
        ->take(5)
        ->get();

    // ✅ Format untuk JavaScript — dilakukan di sini, bukan di blade
    $antrianJson = $antrian->map(function ($p) {
        return [
            'id'     => (string) $p->id,
            'nama'   => $p->nama,
            'rm'     => $p->no_rm,
            'bayar'  => $p->jenis ?? 'BPJS',
            'poli'   => $p->poli_tujuan,
            'status' => $p->status,
            'keluhan'=> $p->keluhan ?? '-',
        ];
    });

    $resepJson = $resepTerbaru->map(function ($r) {
        return [
            'id'      => (string) $r->id,
            'pasien'  => $r->pasien->nama ?? '-',
            'rm'      => $r->pasien->no_rm ?? '-',
            'diagnosa'=> $r->diagnosa,
            'status'  => $r->status,
        ];
    });

    return view('dokter.dashboard', compact(
        'pasienHariIni',
        'antrian',
        'resepTerbaru',
        'antrianJson',   // ← tambahkan ini
        'resepJson'      // ← dan ini
    ));
    }

    /**
     * Halaman data pasien dokter - ambil SEMUA pasien dari DB
     */
    public function data()
    {
        // Ambil pasien hari ini yang ditujukan untuk dokter
        $pasiens = Pasien::whereDate('created_at', Carbon::today())
            ->orderBy('created_at')
            ->get();

        // Format data pasien ke format yang dibutuhkan JavaScript di frontend
        $pasienJson = $pasiens->map(function ($p) {
            return [
                'id'         => (string) $p->id,
                'nama'       => $p->nama,
                'rm'         => $p->no_rm,
                'usia'       => $p->usia,  // dari accessor
                'jk'         => $p->jenis_kelamin ?? '-',
                'bayar'      => $p->jenis,  // BPJS atau Mandiri
                'poli'       => $p->poli_tujuan,
                'status'     => $p->status,
                'tgl'        => $p->created_at->toDateString(),
                'keluhan'    => $p->keluhan ?? '-',
                'riwayat'    => $p->riwayat_penyakit ?? '-',
                'alergi'     => $p->alergi ?? '-',
                'bb'         => $p->berat_badan,
                'tb'         => $p->tinggi_badan,
                'td'         => $p->tekanan_darah ?? '-',
                'noBPJS'     => $p->no_bpjs ?? '',
            ];
        });

        return view('dokter.data', compact('pasiens', 'pasienJson'));
    }

    /**
     * API: ambil data pasien (untuk fetch dari JavaScript)
     */
    public function apiPasien()
    {
        $pasiens = Pasien::whereDate('created_at', Carbon::today())
            ->orderBy('created_at')
            ->get()
            ->map(function ($p) {
                return [
                    'id'      => (string) $p->id,
                    'nama'    => $p->nama,
                    'rm'      => $p->no_rm,
                    'usia'    => $p->usia,
                    'jk'      => $p->jenis_kelamin ?? '-',
                    'bayar'   => $p->jenis,
                    'poli'    => $p->poli_tujuan,
                    'status'  => $p->status,
                    'tgl'     => $p->created_at->toDateString(),
                    'keluhan' => $p->keluhan ?? '-',
                    'riwayat' => $p->riwayat_penyakit ?? '-',
                    'alergi'  => $p->alergi ?? '-',
                    'bb'      => $p->berat_badan,
                    'tb'      => $p->tinggi_badan,
                    'td'      => $p->tekanan_darah ?? '-',
                ];
            });

        return response()->json($pasiens);
    }

    /**
     * API: update status pasien oleh dokter
     */
    public function updateStatus(\Illuminate\Http\Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->status = $request->status; // 'Diperiksa' atau 'Selesai'
        $pasien->save();

        return response()->json(['success' => true, 'status' => $pasien->status]);
    }

    public function prescription()
    {
    $pasiens = Pasien::whereDate('created_at', Carbon::today())
        ->whereNotIn('status', ['Selesai'])
        ->orderBy('created_at')
        ->get();

    $pasienJson = $pasiens->map(function ($p) {
        return [
            'id'     => (string) $p->id,
            'nama'   => $p->nama,
            'rm'     => $p->no_rm,
            'usia'   => $p->usia,
            'jk'     => $p->jenis_kelamin ?? '-',
            'bayar'  => $p->jenis,
            'poli'   => $p->poli_tujuan,
            'status' => $p->status,
            'keluhan'=> $p->keluhan ?? '-',
            'riwayat'=> $p->riwayat_penyakit ?? '-',
            'alergi' => $p->alergi ?? '-',
            'bb'     => $p->berat_badan,
            'tb'     => $p->tinggi_badan,
            'td'     => $p->tekanan_darah ?? '-',
        ];
    });

    return view('dokter.prescription', compact('pasienJson'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Resep;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DokterController extends Controller
{
    /**
     * Ambil poli dokter yang sedang login.
     * Jika kolom poli null/kosong, fallback ke null (tampilkan semua — untuk backward compat).
     */
    private function getPoliDokter(): ?string
    {
        return Auth::user()->poli ?: null;
    }

    /**
     * Scope query Pasien berdasarkan poli dokter yang login.
     */
    private function queryPasienPoli()
    {
        $poli = $this->getPoliDokter();
        $query = Pasien::query()->where('status_kirim', 'Terkirim'); 

        if ($poli) {
            $query->where('poli_tujuan', $poli);
        }

        return $query;
    }

    public function dashboard()
    {
        $today = Carbon::today();

        $pasienHariIni = $this->queryPasienPoli()
            ->whereIn('status', ['Menunggu', 'Diperiksa'])
            ->count();

        $antrian = $this->queryPasienPoli()
            ->whereIn('status', ['Menunggu', 'Diperiksa'])
            ->orderBy('created_at')
            ->take(8)
            ->get(['id', 'nama', 'no_rm', 'jenis', 'poli_tujuan', 'status', 'keluhan']);

        $resepTerbaru = Resep::with('pasien')
            ->whereDate('created_at', $today)
            ->latest()
            ->take(5)
            ->get();

        $antrianJson = $antrian->map(function ($p) {
            return [
                'id'      => (string) $p->id,
                'nama'    => $p->nama,
                'rm'      => $p->no_rm,
                'bayar'   => $p->jenis ?? 'BPJS',
                'poli'    => $p->poli_tujuan,
                'status'  => $p->status,
                'keluhan' => $p->keluhan ?? '-',
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
            'antrianJson',
            'resepJson'
        ));
    }

    public function data()
    {
        $pasiens = $this->queryPasienPoli()
            ->whereIn('status', ['Menunggu', 'Diperiksa'])
            ->orderBy('created_at')
            ->get();

        $pasienJson = $pasiens->map(function ($p) {
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
                'noBPJS'  => $p->no_bpjs ?? '',
            ];
        });

        return view('dokter.data', compact('pasiens', 'pasienJson'));
    }

    public function apiPasien()
    {
        $pasiens = $this->queryPasienPoli()
            ->whereNotIn('status', ['Selesai'])
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

    public function updateStatus(\Illuminate\Http\Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->status = $request->status;
        $pasien->save();

        return response()->json(['success' => true, 'status' => $pasien->status]);
    }

    public function prescription()
    {
        $pasiens = $this->queryPasienPoli()
            ->whereNotIn('status', ['Selesai'])
            ->orderBy('created_at')
            ->get();

        $pasienJson = $pasiens->map(function ($p) {
            return [
                'id'      => (string) $p->id,
                'nama'    => $p->nama,
                'rm'      => $p->no_rm,
                'usia'    => $p->usia,
                'jk'      => $p->jenis_kelamin ?? '-',
                'bayar'   => $p->jenis,
                'poli'    => $p->poli_tujuan,
                'status'  => $p->status,
                'keluhan' => $p->keluhan ?? '-',
                'riwayat' => $p->riwayat_penyakit ?? '-',
                'alergi'  => $p->alergi ?? '-',
                'bb'      => $p->berat_badan,
                'tb'      => $p->tinggi_badan,
                'td'      => $p->tekanan_darah ?? '-',
            ];
        });

        // ✅ Ambil obat tersedia dari database
        $obatList = Batch::where('jumlah', '>', 0)
            ->where(function ($q) {
                $q->whereNull('tgl_expired')
                  ->orWhere('tgl_expired', '>', now());
            })
            ->orderBy('nama_obat')
            ->get(['id', 'nama_obat', 'tipe', 'kategori', 'harga', 'jumlah']);

        $obatJson = $obatList->map(fn($o) => [
            'id'       => $o->id,
            'nama'     => $o->nama_obat,
            'tipe'     => $o->tipe,
            'kategori' => $o->kategori,
            'harga'    => $o->harga,
            'stok'     => $o->jumlah,
        ]);

        return view('dokter.prescription', compact('pasienJson', 'obatJson'));
    }
}
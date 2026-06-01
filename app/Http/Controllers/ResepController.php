<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Batch;
use App\Models\Pasien;
use App\Models\Invoice;
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
     * API: Ambil semua resep untuk DOKTER (termasuk draft milik sendiri)
     */
    public function index()
    {
        $reseps = Resep::with('pasien')
            ->latest()
            ->get()
            ->map(function ($r) {
                $obatList = collect($r->obat_list ?? [])->map(function ($obat) use ($r) {
                    $namaObat = $obat['nama'] ?? '';
                    $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($namaObat) . '%'])
                        ->where('jumlah', '>', 0)
                        ->where(function ($q) {
                            $q->whereNull('tgl_expired')
                              ->orWhere('tgl_expired', '>', now());
                        })
                        ->first();

                    $jenisPasien = $r->pasien->jenis ?? 'Mandiri';
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
     * API: Ambil resep untuk APOTEKER — hanya yang sudah dikirim dokter (bukan draft)
     */
    public function indexApoteker()
    {
        $reseps = Resep::with('pasien')
            ->whereNotIn('status', ['draft'])
            ->latest()
            ->get()
            ->map(function ($r) {
                $obatList = collect($r->obat_list ?? [])->map(function ($obat) use ($r) {
                    $namaObat = $obat['nama'] ?? '';
                    $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($namaObat) . '%'])
                        ->where('jumlah', '>', 0)
                        ->where(function ($q) {
                            $q->whereNull('tgl_expired')
                              ->orWhere('tgl_expired', '>', now());
                        })
                        ->first();

                    $jenisPasien = $r->pasien->jenis ?? 'Mandiri';
                    $obat['stok']  = $batch ? $batch->jumlah : 0;
                    $obat['harga'] = $batch
                        ? ($jenisPasien === 'BPJS' ? 0 : (float) $batch->harga)
                        : 0;
                    return $obat;
                })->toArray();

                return [
                    'id'              => (string) $r->id,
                    'pasienId'        => (string) $r->pasien_id,
                    'no_resep'        => $r->no_resep ?? '-',
                    'pasien'          => $r->pasien->nama ?? '-',
                    'rm'              => $r->pasien->no_rm ?? '-',
                    'dokter'          => $r->pasien->dokter ?? '-',
                    'bayar'           => $r->pasien->jenis ?? 'Mandiri',
                    'diagnosa'        => $r->diagnosa ?? '-',
                    'catatan_dokter'  => $r->catatan_dokter ?? '-',
                    'tanggal_kontrol' => $r->tanggal_kontrol?->format('d/m/Y') ?? '-',
                    'tanggal'         => $r->created_at->toDateString(),
                    'status'          => $r->status ?? 'baru',
                    'obat'            => $obatList,
                ];
            });

        return response()->json($reseps);
    }

    /**
     * Update status resep
     */
    public function updateStatus(Request $request, $id)
    {
        \Log::info('MASUK UPDATE STATUS', [
            'id'     => $id,
            'status' => $request->status
        ]);

        $request->validate([
            'status' => 'required|in:draft,baru,validasi,siap,selesai,ditolak',
        ]);

        $resep = Resep::with('pasien')->findOrFail($id);
        $resep->status = $request->status;
        $resep->save();

        // Auto buat invoice saat apoteker konfirmasi siap
        if ($request->status === 'siap' && !Invoice::where('resep_id', $id)->exists()) {

            \Log::info('MASUK BUAT INVOICE', ['resep_id' => $id]);

            $pasien = $resep->pasien;
            $isBPJS = ($pasien->jenis ?? 'Mandiri') === 'BPJS';

            $obatList = collect($resep->obat_list)->map(function ($item) use ($resep) {
                $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($item['nama']) . '%'])
                    ->where('jumlah', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('tgl_expired')
                          ->orWhere('tgl_expired', '>', now());
                    })
                    ->first();

                $jenisPasien = $resep->pasien->jenis ?? 'Mandiri';
                $item['harga'] = $batch
                    ? ($jenisPasien === 'BPJS' ? 0 : (float) $batch->harga)
                    : 0;
                return $item;
            })->toArray();

            $resep->obat_list = $obatList;
            $resep->save();

            $subtotal = collect($obatList)->sum(function ($item) use ($isBPJS) {
                if ($isBPJS) return 0;
                return ($item['jumlah'] ?? 0) * ($item['harga'] ?? 0);
            });

            $ppn = $isBPJS ? 0 : round($subtotal * 0.11);

            Invoice::create([
                'no_invoice'    => 'INV-' . now()->format('Ymd') . '-' . str_pad($resep->id, 4, '0', STR_PAD_LEFT),
                'resep_id'      => $resep->id,
                'no_rm'         => $pasien->no_rm ?? '-',
                'nama'          => $pasien->nama  ?? '-',
                'jenis'         => $pasien->jenis ?? 'Mandiri',
                'status'        => 'masuk',
                'subtotal'      => $subtotal,
                'ppn'           => $ppn,
                'total_tagihan' => $isBPJS ? 0 : ($subtotal + $ppn),
            ]);
        }

        return response()->json(['success' => true, 'status' => $resep->status]);
    }

    public function updateObat(Request $request, $id)
    {
        $resep = Resep::findOrFail($id);
        $resep->obat_list = $request->obat_list;
        $resep->save();

        return response()->json(['success' => true]);
    }

    /**
     * Halaman prescription untuk DOKTER
     */
    public function prescription()
    {
        $pasienJson = Pasien::select(
            'id','nama','no_rm','jenis','poli_tujuan',
            'tgl_lahir','jenis_kelamin','keluhan',
            'alergi','berat_badan','tinggi_badan','tekanan_darah'
        )
        ->whereIn('status', ['Diperiksa','Menunggu'])
        ->get();

        $obatJson = Batch::where('jumlah', '>', 0)
            ->where(function ($q) {
                $q->whereNull('tgl_expired')
                  ->orWhere('tgl_expired', '>', now());
            })
            ->select('id','nama_obat','tipe','kategori','jumlah','harga','harga_bpjs')
            ->orderBy('nama_obat')
            ->get()
            ->map(fn($b) => [
                'id'       => $b->id,
                'nama'     => $b->nama_obat,
                'tipe'     => $b->tipe,
                'kategori' => $b->kategori,
                'stok'     => $b->jumlah,
                'harga'    => (float) $b->harga,
            ]);

        return view('dokter.prescription', compact('pasienJson', 'obatJson'));
    }

    /**
     * Halaman prescription untuk APOTEKER — tampilkan resep masuk dari dokter
     */
    public function apotekerPrescription()
    {
        return view('apoteker.prescription');
    }
}
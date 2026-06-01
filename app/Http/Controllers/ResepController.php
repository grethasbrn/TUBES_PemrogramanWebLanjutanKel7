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

    public function show($id)
    {
        $resep = Resep::with('pasien')->findOrFail($id);

        return view('apoteker.detail', compact('resep'));
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

        return \DB::transaction(function () use ($request, $id) {

            $resep = Resep::with('pasien')->findOrFail($id);
            $resep->status = $request->status;
            $resep->save();

            if ($request->status === 'siap') {
                $this->createInvoice($resep);
            }

            return response()->json([
                'success' => true,
                'status' => $resep->status
            ]);
        });
    }

    private function createInvoice($resep)
    {
        if (Invoice::where('resep_id', $resep->id)->exists()) {
            return;
        }

        $pasien = $resep->pasien;
        $isBPJS = ($pasien->jenis ?? 'Mandiri') === 'BPJS';

        $subtotal = collect($resep->obat_list)->sum(function ($item) use ($isBPJS) {
            if ($isBPJS) return 0;
            return ($item['jumlah'] ?? 0) * ($item['harga'] ?? 0);
        });

        $ppn = $isBPJS ? 0 : round($subtotal * 0.11);

        Invoice::create([
            'no_invoice'    => 'INV-' . now()->format('Ymd') . '-' . str_pad($resep->id, 4, '0', STR_PAD_LEFT),
            'resep_id'      => $resep->id,
            'no_rm'         => $pasien->no_rm ?? '-',
            'nama'          => $pasien->nama ?? '-',
            'jenis'         => $pasien->jenis ?? 'Mandiri',
            'status'        => 'masuk',
            'subtotal'      => $subtotal,
            'ppn'           => $ppn,
            'total_tagihan' => $isBPJS ? 0 : ($subtotal + $ppn),
        ]);
    }

    public function apotekerIndex(Request $request)
    {
        $reseps = Resep::with('pasien')
            ->whereIn('status', ['baru','validasi'])
            ->get();

        $selectedResep = null;

        if ($request->resep) {
            $selectedResep = Resep::with('pasien')->find($request->resep);
        }

        return view('apoteker.invoice', compact('reseps','selectedResep'));
    }

    public function updateObat(Request $request, $id)
    {
        $resep = Resep::findOrFail($id);
        $resep->obat_list = $request->obat_list;
        $resep->save();

        return response()->json(['success' => true]);
    }

    public function prescription()
    {
        $pasienJson = Pasien::select('id','nama','no_rm','jenis','poli_tujuan',
                                    'tgl_lahir','jenis_kelamin','keluhan',
                                    'alergi','berat_badan','tinggi_badan','tekanan_darah')
            ->whereIn('status', ['Diperiksa','Menunggu'])
            ->get();

        return view('dokter.prescription', compact('pasienJson'));
    }

    public function sendInvoice(Request $request, $id)
    {
        $resep = Resep::with('pasien')->findOrFail($id);
        $obat  = $request->input('obat');

        if (!$obat) {
            return back()->with('error', 'Data obat kosong');
        }

        $subtotal = 0;

        // Simpan obat BESERTA harga dari Batch
        $obatDenganHarga = [];
        foreach ($obat as $o) {
            $namaObat = $o['nama'] ?? '';
            $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($namaObat) . '%'])
                ->where('jumlah', '>', 0)
                ->first();

            $harga  = $batch ? (float) $batch->harga : 0;
            $jumlah = (int) ($o['jumlah'] ?? 0);

            $obatDenganHarga[] = [
                'nama'   => $namaObat,
                'dosis'  => $o['dosis'] ?? '',
                'jumlah' => $jumlah,
                'harga'  => $harga, // ← simpan harga
            ];

            $subtotal += $harga * $jumlah;
        }

        $resep->obat_list = $obatDenganHarga; // ← simpan dengan harga
        $resep->status    = 'siap';
        $resep->save();

        $isBPJS = ($resep->pasien->jenis ?? 'Mandiri') === 'BPJS';
        $ppn    = $isBPJS ? 0 : round($subtotal * 0.11);
        $total  = $isBPJS ? 0 : ($subtotal + $ppn);

        if (!Invoice::where('resep_id', $resep->id)->exists()) {
            Invoice::create([
                'no_invoice'    => 'INV-' . now()->format('YmdHis'),
                'resep_id'      => $resep->id,
                'nama'          => $resep->pasien->nama,
                'no_rm'         => $resep->pasien->no_rm,
                'jenis'         => $resep->pasien->jenis ?? 'Mandiri',
                'status'        => 'masuk',
                'subtotal'      => $subtotal,
                'ppn'           => $ppn,
                'total_tagihan' => $total,
                'tanggal'       => now(),
            ]);
        }

        return redirect()->route('apoteker.index')
            ->with('success', 'Invoice berhasil dikirim');
    }

    public function cekObat(Request $request)
    {
        $nama = $request->nama;

        $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($nama) . '%'])
            ->where('jumlah', '>', 0)
            ->where(function ($q) {
                $q->whereNull('tgl_expired')
                ->orWhere('tgl_expired', '>', now());
            })
            ->first();

        if (!$batch) {
            return response()->json([
                'stok' => 0,
                'harga' => 0
            ]);
        }

        return response()->json([
            'stok' => $batch->jumlah,
            'harga' => $batch->harga
        ]);
    }

    public function searchObat(Request $request)
    {
        $keyword = $request->get('q');

        $obat = Batch::whereRaw('LOWER(nama_obat) LIKE ?', ['%' . strtolower($keyword) . '%'])
                    ->where('jumlah', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('tgl_expired')
                        ->orWhere('tgl_expired', '>', now());
                    })
                    ->select('nama_obat', 'jumlah', 'harga')
                    ->limit(5)
                    ->get();

        $dataFormatted = $obat->map(function($item) {
            return [
                'nama'  => $item->nama_obat,
                'stok'  => $item->jumlah,
                'harga' => $item->harga,
            ];
        });

        return response()->json($dataFormatted);
    }

}
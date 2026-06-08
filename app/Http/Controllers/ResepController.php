<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Batch;
use App\Models\Pasien;
use App\Models\Kunjungan;
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
            'kunjungan_id'  => 'required|exists:kunjungans,id',
            'diagnosa'   => 'required|string',
            'obat_list'  => 'required|array',
            'status'     => 'required|in:draft,baru',
        ]);

        $kunjungan = Kunjungan::findOrFail($request->kunjungan_id);

        $resep = Resep::create([
            'kunjungan_id'    => $request->kunjungan_id,
            'pasien_id'       => $kunjungan->pasien_id, 
            'no_resep'        => Resep::generateNoResep(),
            'diagnosa'        => $request->diagnosa,
            'catatan_dokter'  => $request->catatan_dokter,
            'tanggal_kontrol' => $request->tanggal_kontrol,
            'status'          => $request->status,
            'obat_list'       => $request->obat_list,
        ]);

        // Update status pasien jika resep dikirim (bukan draft)
        if ($request->status === 'baru') {
            $kunjungan->status = 'Selesai';
            $kunjungan->save();
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
        $reseps = Resep::with('kunjungan.pasien')
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

                    $jenisPasien = $r->kunjungan?->pasien?->jenis ?? 'Mandiri';
                    $obat['stok']  = $batch ? $batch->jumlah : 0;
                    $obat['harga'] = $batch
                        ? ($jenisPasien === 'BPJS' ? 0 : (float) $batch->harga)
                        : 0;
                    return $obat;
                })->toArray();

                return [
                    'id'           => (string) $r->id,
                    'kunjunganId'  => (string) $r->kunjungan?->id ?? '-',
                    'no_resep'     => $r->no_resep ?? '-',
                    'pasien'       => $r->kunjungan?->pasien?->nama ?? '-',
                    'rm'           => $r->kunjungan?->pasien?->no_rm ?? '-',
                    'dokter' => $r->kunjungan?->dokter?->nama ?? '-',
                    'bayar'        => $r->kunjungan?->pasien?->jenis ?? 'Mandiri',
                    'diagnosa'     => $r->diagnosa ?? '-',
                    'tanggal'      => $r->created_at->toDateString(),
                    'status'       => $r->status ?? 'baru',
                    'alasan_tolak' => $r->alasan_tolak ?? null, // ← untuk notif dokter
                    'obat'         => $obatList,
                ];
            });

        return response()->json($reseps);
    }

    /**
     * API: Ambil resep untuk APOTEKER — hanya yang sudah dikirim dokter (bukan draft)
     */
    public function indexApoteker()
    {
        $reseps = Resep::with('kunjungan.pasien')
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

                    $jenisPasien = $r->kunjungan?->pasien?->jenis ?? 'Mandiri';
                    $obat['stok']  = $batch ? $batch->jumlah : 0;
                    $obat['harga'] = $batch
                        ? ($jenisPasien === 'BPJS' ? 0 : (float) $batch->harga)
                        : 0;
                    return $obat;
                })->toArray();

                return [
                    'id'              => (string) $r->id,
                    'pasienId'        => (string) $r->kunjungan?->pasien?->id ?? '-',
                    'no_resep'        => $r->no_resep ?? '-',
                    'pasien'          => $r->kunjungan?->pasien?->nama ?? '-',
                    'rm'              => $r->kunjungan?->pasien?->no_rm ?? '-',
                    'dokter' => $r->kunjungan?->dokter?->nama ?? '-',
                    'bayar'           => $r->kunjungan?->pasien?->jenis ?? 'Mandiri',
                    'diagnosa'        => $r->diagnosa ?? '-',
                    'catatan_dokter'  => $r->catatan_dokter ?? '-',
                    'tanggal_kontrol' => $r->tanggal_kontrol?->format('d/m/Y') ?? '-',
                    'tanggal'         => $r->created_at->toDateString(),
                    'status'          => $r->status ?? 'baru',
                    'alasan_tolak'    => $r->alasan_tolak ?? null,
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
        $request->validate([
            'status'       => 'required|in:draft,baru,validasi,siap,selesai,ditolak',
            'alasan_tolak' => 'nullable|string',
        ]);

        $resep = Resep::with('kunjungan.pasien')->findOrFail($id);
        $resep->status = $request->status;

        if ($request->status === 'ditolak') {
            $resep->alasan_tolak = $request->alasan_tolak;
            if ($resep->kunjungan?->pasien) {
                $resep->kunjungan->pasien->status = 'Diperiksa';
                $resep->kunjungan->pasien->save();
            }
        }

        $resep->save();

        // Auto buat invoice saat status = siap
        if ($request->status === 'siap' && !Invoice::where('resep_id', $id)->exists()) {
            $invoiceController = new InvoiceController();
            $method = new \ReflectionMethod(InvoiceController::class, 'buatInvoiceDariResep');
            $method->setAccessible(true);
            $method->invoke($invoiceController, $resep);
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
        $dokter = auth()->user();
        $poli   = $dokter->poli;

        $dokterModel = \App\Models\Dokter::where('email', auth()->user()->email)->first();

        $pasienJson = Kunjungan::with('pasien')
            ->where('status_kirim', 'Terkirim')
            ->whereIn('status', ['Menunggu', 'Diperiksa'])
            ->when($dokterModel, fn($q) => $q->where('dokter_id', $dokterModel->id))
            ->get()
            ->map(fn($k) => [
                'id'      => (string) $k->id,   // kunjungan_id — penting untuk submit resep
                'nama'    => $k->pasien->nama ?? '-',
                'rm'      => $k->pasien->no_rm ?? '-',
                'usia'    => $k->pasien->usia ?? '-',
                'jk'      => $k->pasien->jenis_kelamin ?? '-',
                'bayar'   => $k->pasien->jenis ?? '-',
                'poli'    => $k->poli_tujuan,
                'status'  => $k->status,
                'keluhan' => $k->keluhan ?? '-',
                'alergi'  => $k->pasien->alergi ?? '-',
                'bb'      => $k->berat_badan,
                'tb'      => $k->tinggi_badan,
                'td'      => $k->tekanan_darah ?? '-',
            ]);

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
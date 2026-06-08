<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Resep;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DokterController extends Controller
{
    // ── Helper: ambil dokter_id dari user yang login ──────────────────────
    private function getDokter()
    {
        // Cari record dokter berdasarkan email user yang login
        return \App\Models\Dokter::where('email', Auth::user()->email)->first();
    }

    // ── Query kunjungan sesuai dokter yang login ──────────────────────────
    private function queryKunjungan()
    {
        $dokter = $this->getDokter();

        return Kunjungan::with('pasien')
            ->where('status_kirim', 'Terkirim')
            ->when($dokter, fn($q) => $q->where('dokter_id', $dokter->id));
    }

    // ── Dashboard ─────────────────────────────────────────────────────────
    public function dashboard()
    {
        $today = Carbon::today();

        $pasienHariIni = $this->queryKunjungan()
            ->whereDate('created_at', $today)
            ->whereIn('status', ['Menunggu', 'Diperiksa'])
            ->count();

        $antrian = $this->queryKunjungan()
            ->whereDate('created_at', $today)
            ->whereIn('status', ['Menunggu', 'Diperiksa'])
            ->orderBy('created_at')
            ->take(8)
            ->get();

        $dokter = $this->getDokter();

        $resepTerbaru = Resep::with('kunjungan.pasien')
            ->whereHas('kunjungan', function ($q) use ($dokter) {
                $q->where('dokter_id', $dokter->id);
            })
            ->whereDate('created_at', $today)
            ->latest()
            ->take(5)
            ->get();

        $antrianJson = $antrian->map(fn($k) => [
            'id'      => (string) $k->id,
            'nama'    => $k->pasien->nama ?? '-',
            'rm'      => $k->pasien->no_rm ?? '-',
            'bayar'   => $k->pasien->jenis ?? 'BPJS',
            'poli'    => $k->poli_tujuan,
            'status'  => $k->status,
            'keluhan' => $k->keluhan ?? '-',
        ]);

        $resepJson = $resepTerbaru->map(fn($r) => [
            'id'      => (string) $r->id,
            'pasien'  => $r->kunjungan->pasien->nama ?? '-',
            'rm'      => $r->kunjungan->pasien->no_rm ?? '-',
            'diagnosa'=> $r->diagnosa,
            'status'  => $r->status,
        ]);

        return view('dokter.dashboard', compact(
            'pasienHariIni', 'antrian', 'resepTerbaru', 'antrianJson', 'resepJson'
        ));
    }

    // ── Daftar antrian pasien ─────────────────────────────────────────────
    public function data()
    {
        $kunjungans = $this->queryKunjungan()
            ->whereIn('status', ['Menunggu', 'Diperiksa'])
            ->orderBy('created_at')
            ->get();

        $pasienJson = $kunjungans->map(fn($k) => $this->kunjunganToJson($k));

        return view('dokter.data', compact('kunjungans', 'pasienJson'));
    }

    // ── API: antrian pasien ───────────────────────────────────────────────
    public function apiPasien()
    {
        return response()->json(
            $this->queryKunjungan()
                ->whereNotIn('status', ['Selesai'])
                ->orderBy('created_at')
                ->get()
                ->map(fn($k) => $this->kunjunganToJson($k))
        );
    }

    // ── Update status kunjungan ───────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        // $id sekarang adalah kunjungan_id
        $kunjungan = Kunjungan::findOrFail($id);
        $kunjungan->status = $request->status;

        if ($request->status === 'Selesai') {
            $kunjungan->validasi = 'Valid';
        }

        $kunjungan->save();

        return response()->json([
            'success' => true,
            'status'  => $kunjungan->status,
        ]);
    }

    // ── Halaman resep ─────────────────────────────────────────────────────
    public function prescription()
    {
        $kunjungans = $this->queryKunjungan()
            ->whereNotIn('status', ['Selesai'])
            ->orderBy('created_at')
            ->get();

        $pasienJson = $kunjungans->map(fn($k) => $this->kunjunganToJson($k))->values();

        $obatList = Batch::where('jumlah', '>', 0)
            ->where(fn($q) => $q->whereNull('tgl_expired')->orWhere('tgl_expired', '>', now()))
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

    // ── Status resep ──────────────────────────────────────────────────────
    public function status()
    {
        $dokter = $this->getDokter();

        $resepTerbaru = Resep::with('kunjungan.pasien')
            ->when($dokter, fn($q) => $q->whereHas('kunjungan', fn($q2) =>
                $q2->where('dokter_id', $dokter->id)
            ))
            ->whereDate('created_at', $today)
            ->latest()
            ->take(5)
            ->get()
            ->whereNotIn('status', ['draft'])
            ->latest()
            ->get();

        $resepJson = $reseps->map(fn($r) => [
            'id'              => (string) $r->id,
            'no_resep'        => $r->no_resep ?? '-',
            'pasien'          => $r->kunjungan->pasien->nama ?? '-',
            'rm'              => $r->kunjungan->pasien->no_rm ?? '-',
            'diagnosa'        => $r->diagnosa ?? '-',
            'status'          => $r->status,
            'tanggal'         => $r->created_at->format('d/m/Y'),
            'catatan_dokter'  => $r->catatan_dokter ?? '-',
            'tanggal_kontrol' => $r->tanggal_kontrol?->format('d/m/Y') ?? '-',
            'obat'            => $r->obat_list ?? [],
        ]);

        return view('dokter.status', compact('resepJson'));
    }

    // ── Riwayat ───────────────────────────────────────────────────────────
    public function history()
    {
        $dokter = $this->getDokter();

        $kunjungans = $this->queryKunjungan()
            ->orderBy('created_at', 'desc')
            ->get();

        $reseps = Resep::with('kunjungan.pasien')
            ->when($dokter, fn($q) => $q->whereHas('kunjungan', fn($q2) =>
                $q2->where('dokter_id', $dokter->id)
            ))
            ->latest()
            ->get();

        $pasienJson = $kunjungans->map(fn($k) => [
            'id'           => (string) $k->id,
            'nama'         => $k->pasien->nama ?? '-',
            'rm'           => $k->pasien->no_rm ?? '-',
            'usia'         => $k->pasien->usia ?? '-',
            'jk'           => $k->pasien->jenis_kelamin ?? '-',
            'bayar'        => $k->pasien->jenis ?? '-',
            'poli'         => $k->poli_tujuan,
            'status'       => $k->status,
            'tgl'          => $k->created_at->toDateString(),
            'no_kunjungan' => $k->no_kunjungan,
        ]);

        $resepJson = $reseps->map(fn($r) => [
            'id'          => (string) $r->id,
            'kunjunganId' => (string) $r->kunjungan_id,
            'no_resep'    => $r->no_resep ?? '-',
            'diagnosa'    => $r->diagnosa ?? '-',
            'status'      => $r->status,
            'tanggal'     => $r->created_at->toDateString(),
            'obat'        => $r->obat_list ?? [],
        ]);

        return view('dokter.history', compact('pasienJson', 'resepJson'));
    }

    // ── Helper: map kunjungan ke JSON untuk view ──────────────────────────
    private function kunjunganToJson(Kunjungan $k): array
    {
        $p = $k->pasien;
        return [
            'id'           => (string) $k->id,  // pakai kunjungan ID, bukan pasien ID
            'kunjungan_id' => (string) $k->id,
            'nama'         => $p->nama ?? '-',
            'rm'           => $p->no_rm ?? '-',
            'usia'         => $p?->usia ?? '-',
            'jk'           => $p->jenis_kelamin ?? '-',
            'bayar'        => $p->jenis ?? '-',
            'poli'         => $k->poli_tujuan,
            'status'       => $k->status,
            'tgl'          => $k->created_at->toDateString(),
            'keluhan'      => $k->keluhan ?? '-',
            'riwayat'      => $k->riwayat_penyakit ?? '-',
            'alergi'       => $p->alergi ?? '-',
            'bb'           => $k->berat_badan,
            'tb'           => $k->tinggi_badan,
            'td'           => $k->tekanan_darah ?? '-',
            'noBPJS'       => $p->no_bpjs ?? '',
            'no_kunjungan' => $k->no_kunjungan,
        ];
    }
}
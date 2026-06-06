<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Resep;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DokterController extends Controller
{
    // ===== ADMIN: Manajemen Dokter =====
    public function index()
    {
        $dokters = DB::table('dokters')->orderBy('nama')->get();
        return view('admin.dokter', compact('dokters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'spesialisasi' => 'required|string|max:255',
            'no_telepon'   => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'status'       => 'required|in:Aktif,Tidak Aktif',
        ]);
        DB::table('dokters')->insert([
            'nama' => $request->nama, 'spesialisasi' => $request->spesialisasi,
            'no_telepon' => $request->no_telepon, 'email' => $request->email,
            'status' => $request->status, 'created_at' => now(), 'updated_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Dokter berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'spesialisasi' => 'required|string|max:255',
            'no_telepon'   => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'status'       => 'required|in:Aktif,Tidak Aktif',
        ]);
        DB::table('dokters')->where('id', $id)->update([
            'nama' => $request->nama, 'spesialisasi' => $request->spesialisasi,
            'no_telepon' => $request->no_telepon, 'email' => $request->email,
            'status' => $request->status, 'updated_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Dokter berhasil diupdate');
    }

    public function destroy($id)
    {
        DB::table('dokters')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Dokter berhasil dihapus');
    }

    // ===== DOKTER: Dashboard & Pasien =====
    private function getPoliDokter(): ?string { return Auth::user()->poli ?: null; }

    private function queryPasienPoli()
    {
        $poli = $this->getPoliDokter();
        $query = Pasien::query()->where('status_kirim', 'Terkirim');
        if ($poli) $query->where('poli_tujuan', $poli);
        return $query;
    }

    public function dashboard()
    {
        $today = Carbon::today();
        $pasienHariIni = $this->queryPasienPoli()->whereDate('created_at', $today)->whereIn('status', ['Menunggu', 'Diperiksa'])->count();
        $antrian = $this->queryPasienPoli()->whereDate('created_at', $today)->whereIn('status', ['Menunggu', 'Diperiksa'])->orderBy('created_at')->take(8)->get(['id', 'nama', 'no_rm', 'jenis', 'poli_tujuan', 'status', 'keluhan']);
        $resepTerbaru = Resep::with('pasien')->whereDate('created_at', $today)->latest()->take(5)->get();
        $antrianJson = $antrian->map(fn($p) => ['id' => (string)$p->id, 'nama' => $p->nama, 'rm' => $p->no_rm, 'bayar' => $p->jenis ?? 'BPJS', 'poli' => $p->poli_tujuan, 'status' => $p->status, 'keluhan' => $p->keluhan ?? '-']);
        $resepJson = $resepTerbaru->map(fn($r) => ['id' => (string)$r->id, 'pasien' => $r->pasien->nama ?? '-', 'rm' => $r->pasien->no_rm ?? '-', 'diagnosa' => $r->diagnosa, 'status' => $r->status]);
        return view('dokter.dashboard', compact('pasienHariIni', 'antrian', 'resepTerbaru', 'antrianJson', 'resepJson'));
    }

    public function data()
    {
        $pasiens = $this->queryPasienPoli()->whereIn('status', ['Menunggu', 'Diperiksa'])->orderBy('created_at')->get();
        $pasienJson = $pasiens->map(fn($p) => ['id' => (string)$p->id, 'nama' => $p->nama, 'rm' => $p->no_rm, 'usia' => $p->usia, 'jk' => $p->jenis_kelamin ?? '-', 'bayar' => $p->jenis, 'poli' => $p->poli_tujuan, 'status' => $p->status, 'tgl' => $p->created_at->toDateString(), 'keluhan' => $p->keluhan ?? '-', 'riwayat' => $p->riwayat_penyakit ?? '-', 'alergi' => $p->alergi ?? '-', 'bb' => $p->berat_badan, 'tb' => $p->tinggi_badan, 'td' => $p->tekanan_darah ?? '-', 'noBPJS' => $p->no_bpjs ?? '']);
        return view('dokter.data', compact('pasiens', 'pasienJson'));
    }

    public function apiPasien()
    {
        return response()->json($this->queryPasienPoli()->whereNotIn('status', ['Selesai'])->orderBy('created_at')->get()->map(fn($p) => ['id' => (string)$p->id, 'nama' => $p->nama, 'rm' => $p->no_rm, 'usia' => $p->usia, 'jk' => $p->jenis_kelamin ?? '-', 'bayar' => $p->jenis, 'poli' => $p->poli_tujuan, 'status' => $p->status, 'tgl' => $p->created_at->toDateString(), 'keluhan' => $p->keluhan ?? '-', 'riwayat' => $p->riwayat_penyakit ?? '-', 'alergi' => $p->alergi ?? '-', 'bb' => $p->berat_badan, 'tb' => $p->tinggi_badan, 'td' => $p->tekanan_darah ?? '-']));
    }

    public function updateStatus(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        $pasien->status = $request->status;

        if ($request->status === 'Selesai') {
            $pasien->validasi = 'Valid';
        }

        $pasien->save();

        return response()->json([
            'success' => true,
            'status' => $pasien->status
        ]);
    }

    public function prescription()
    {
        $pasiens = $this->queryPasienPoli()->whereNotIn('status', ['Selesai'])->orderBy('created_at')->get();
        $pasienJson = $pasiens->map(fn($p) => ['id' => (string)$p->id, 'nama' => $p->nama, 'rm' => $p->no_rm, 'usia' => $p->usia, 'jk' => $p->jenis_kelamin ?? '-', 'bayar' => $p->jenis, 'poli' => $p->poli_tujuan, 'status' => $p->status, 'keluhan' => $p->keluhan ?? '-', 'riwayat' => $p->riwayat_penyakit ?? '-', 'alergi' => $p->alergi ?? '-', 'bb' => $p->berat_badan, 'tb' => $p->tinggi_badan, 'td' => $p->tekanan_darah ?? '-'])->values();
        $obatList = Batch::where('jumlah', '>', 0)->where(fn($q) => $q->whereNull('tgl_expired')->orWhere('tgl_expired', '>', now()))->orderBy('nama_obat')->get(['id', 'nama_obat', 'tipe', 'kategori', 'harga', 'jumlah']);
        $obatJson = $obatList->map(fn($o) => ['id' => $o->id, 'nama' => $o->nama_obat, 'tipe' => $o->tipe, 'kategori' => $o->kategori, 'harga' => $o->harga, 'stok' => $o->jumlah]);
        return view('dokter.prescription', compact('pasienJson', 'obatJson'));
    }

    public function status()
    {
        $reseps = Resep::with('pasien')
            ->whereHas('pasien', function ($q) {
                $poli = $this->getPoliDokter();
                if ($poli) $q->where('poli_tujuan', $poli);
            })
            ->whereNotIn('status', ['draft'])
            ->latest()
            ->get();

        $resepJson = $reseps->map(function ($r) {
            return [
                'id'              => (string) $r->id,
                'no_resep'        => $r->no_resep ?? '-',
                'pasien'          => $r->pasien->nama ?? '-',
                'rm'              => $r->pasien->no_rm ?? '-',
                'diagnosa'        => $r->diagnosa ?? '-',
                'status'          => $r->status,
                'tanggal'         => $r->created_at->format('d/m/Y'),
                'catatan_dokter'  => $r->catatan_dokter ?? '-',
                'tanggal_kontrol' => $r->tanggal_kontrol?->format('d/m/Y') ?? '-',
                'obat'            => $r->obat_list ?? [],
            ];
        });

        return view('dokter.status', compact('resepJson'));
    }

    public function history()
    {
        $pasiens = $this->queryPasienPoli()
            ->orderBy('created_at', 'desc')
            ->get();

        $reseps = Resep::with('pasien')
            ->whereHas('pasien', function ($q) {
                $poli = $this->getPoliDokter();
                if ($poli) $q->where('poli_tujuan', $poli);
            })
            ->latest()
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
                'tgl'    => $p->created_at->toDateString(),
            ];
        });

        $resepJson = $reseps->map(function ($r) {
            return [
                'id'       => (string) $r->id,
                'pasienId' => (string) $r->pasien_id,
                'no_resep' => $r->no_resep ?? '-',
                'diagnosa' => $r->diagnosa ?? '-',
                'status'   => $r->status,
                'tanggal'  => $r->created_at->toDateString(),
                'obat'     => $r->obat_list ?? [],
            ];
        });

        return view('dokter.history', compact('pasienJson', 'resepJson'));
    }
}
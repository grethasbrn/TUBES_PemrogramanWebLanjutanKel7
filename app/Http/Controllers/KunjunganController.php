<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KunjunganController extends Controller
{
    public function create()
    {
        $polis = Dokter::where('status', 'Aktif')
            ->select('spesialisasi')
            ->distinct()
            ->orderBy('spesialisasi')
            ->pluck('spesialisasi');

        // Generate no RM sementara untuk ditampilkan di form
        // Akan di-generate ulang saat store() jika pasien baru
        $noRm = $this->generateNoRm();

        return view('admin.create-kunjungan', compact('polis', 'noRm'));
    }

    public function cekNik($nik)
    {
        $pasien = Pasien::where('nik', $nik)->first();

        if ($pasien) {
            return response()->json([
                'found' => true,
                'data'  => [
                    'pasien_id'     => $pasien->id,
                    'no_rm'         => $pasien->no_rm,
                    'nama'          => $pasien->nama,
                    'tgl_lahir'     => $pasien->tgl_lahir,
                    'jenis_kelamin' => $pasien->jenis_kelamin,
                    'alamat'        => $pasien->alamat,
                    'no_telepon'    => $pasien->no_telepon,
                    'pekerjaan'     => $pasien->pekerjaan,
                    'jenis'         => $pasien->jenis,
                    'no_bpjs'       => $pasien->no_bpjs,
                    'alergi'        => $pasien->alergi,
                ],
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function dokterByPoli(Request $request)
    {
        $dokters = Dokter::where('status', 'Aktif')
            ->where('spesialisasi', $request->poli)
            ->select('id', 'nama', 'spesialisasi')
            ->orderBy('nama')
            ->get();

        return response()->json($dokters);
    }

    public function store(Request $request)
    {
        $rules = [
            'nik'              => 'required|digits:16',
            'dokter_id'        => 'required|exists:dokters,id',
            'poli_tujuan'      => 'required|string',
            'jenis_kunjungan'  => 'nullable|string',
            'keluhan'          => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
            'berat_badan'      => 'nullable|numeric|min:1|max:300',
            'tinggi_badan'     => 'nullable|numeric|min:1|max:300',
            'tekanan_darah'    => 'nullable|string|max:20',
        ];

        if (!Pasien::where('nik', $request->nik)->exists()) {
            $rules = array_merge($rules, [
                'nama'          => 'required|string|max:255',
                'tgl_lahir'     => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'jenis'         => 'required|in:BPJS,Mandiri',
                'alamat'        => 'nullable|string',
                'no_telepon'    => 'nullable|string|max:20',
                'pekerjaan'     => 'nullable|string|max:100',
                'alergi'        => 'nullable|string|max:255',
            ]);
        }

        $request->validate($rules);

        if ($request->jenis === 'BPJS') {
            $request->validate(['no_bpjs' => 'required|string|max:20']);
        }

        return DB::transaction(function () use ($request) {

            $pasien = Pasien::where('nik', $request->nik)->first();

            if (!$pasien) {
                $pasien = Pasien::create([
                    'no_rm'         => $this->generateNoRm(),
                    'nama'          => $request->nama,
                    'nik'           => $request->nik,
                    'tgl_lahir'     => $request->tgl_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat'        => $request->alamat,
                    'no_telepon'    => $request->no_telepon,
                    'pekerjaan'     => $request->pekerjaan,
                    'jenis'         => $request->jenis,
                    'no_bpjs'       => $request->jenis === 'BPJS' ? $request->no_bpjs : null,
                    'alergi'        => $request->alergi ?? '-',
                ]);
            } else {
                $pasien->update([
                    'jenis'      => $request->jenis ?? $pasien->jenis,
                    'no_bpjs'    => ($request->jenis ?? $pasien->jenis) === 'BPJS'
                                        ? ($request->no_bpjs ?? $pasien->no_bpjs)
                                        : null,
                    'alergi'     => $request->alergi     ?? $pasien->alergi,
                    'alamat'     => $request->alamat      ?? $pasien->alamat,
                    'no_telepon' => $request->no_telepon ?? $pasien->no_telepon,
                    'pekerjaan'  => $request->pekerjaan  ?? $pasien->pekerjaan,
                ]);
            }

            $kunjungan = Kunjungan::create([
                'pasien_id'        => $pasien->id,
                'dokter_id'        => $request->dokter_id,
                'no_kunjungan'     => Kunjungan::generateNoKunjungan(),
                'poli_tujuan'      => $request->poli_tujuan,
                'jenis_kunjungan'  => $request->jenis_kunjungan ?? 'Rawat Jalan',
                'keluhan'          => $request->keluhan,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'berat_badan'      => $request->berat_badan,
                'tinggi_badan'     => $request->tinggi_badan,
                'tekanan_darah'    => $request->tekanan_darah,
                'status'           => 'Menunggu',
                'validasi'         => 'Menunggu',
                'status_kirim'     => 'Belum',
            ]);

            return redirect()->route('kunjungan.queue')
                ->with('success', "Kunjungan {$pasien->nama} ({$kunjungan->no_kunjungan}) berhasil didaftarkan.");
        });
    }

    public function queue()
    {
        $belumDikirim = Kunjungan::with(['pasien', 'dokter'])
            ->where('status_kirim', 'Belum')
            ->whereIn('validasi', ['Menunggu', 'Valid'])
            ->orderBy('created_at')
            ->get();

        $sudahDikirim = Kunjungan::with(['pasien', 'dokter'])
            ->where('status_kirim', 'Terkirim')
            ->orderBy('updated_at', 'desc')
            ->take(50)
            ->get();

        $belumDikirimJson = $belumDikirim->map(fn($k) => [
            'id'           => $k->id,
            'no_kunjungan' => $k->no_kunjungan,
            'pasien_id'    => $k->pasien_id,
            'rm'           => $k->pasien->no_rm ?? '-',
            'nama'         => $k->pasien->nama  ?? '-',
            'poli'         => $k->poli_tujuan,
            'dokter'       => $k->dokter->nama  ?? '-',
            'jenis'        => $k->pasien->jenis ?? '-',
            'validasi'     => $k->validasi,
            'status_kirim' => $k->status_kirim,
        ]);

        return view('admin.queue-kunjungan', compact(
            'belumDikirim', 'sudahDikirim', 'belumDikirimJson'
        ));
    }

    public function updateValidasi(Request $request, $id)
    {
        try {
            $kunjungan = Kunjungan::with('pasien')->findOrFail($id);

            $statusDb = match($request->input('validasi')) {
                'valid'   => 'Valid',
                'invalid' => 'Tidak Valid',
                default   => 'Menunggu',
            };

            if ($kunjungan->pasien->jenis === 'BPJS'
                && $statusDb === 'Valid'
                && empty($kunjungan->pasien->no_bpjs))
            {
                return response()->json([
                    'success' => false,
                    'message' => 'No BPJS belum diisi, tidak bisa disetujui.',
                ], 422);
            }

            $kunjungan->validasi = $statusDb;
            $kunjungan->save();

            return response()->json([
                'success' => true,
                'message' => "Validasi diperbarui menjadi {$statusDb}.",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function kirimKeDokter($id)
    {
        $kunjungan = Kunjungan::findOrFail($id);

        if ($kunjungan->validasi !== 'Valid') {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan belum divalidasi.',
            ], 422);
        }

        if ($kunjungan->status_kirim === 'Terkirim') {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan sudah dikirim sebelumnya.',
            ], 422);
        }

        $kunjungan->update([
            'status_kirim' => 'Terkirim',
            'status'       => 'Menunggu',
        ]);

        return response()->json([
            'success'      => true,
            'no_kunjungan' => $kunjungan->no_kunjungan,
            'nama'         => $kunjungan->pasien->nama ?? '-',
        ]);
    }

    public function kirimSemua()
    {
        $jumlah = Kunjungan::where('validasi', 'Valid')
            ->where('status_kirim', 'Belum')
            ->update([
                'status_kirim' => 'Terkirim',
                'status'       => 'Menunggu',
            ]);

        return response()->json([
            'success' => true,
            'jumlah'  => $jumlah,
            'message' => $jumlah > 0
                ? "{$jumlah} kunjungan berhasil dikirim ke dokter."
                : 'Tidak ada kunjungan yang perlu dikirim.',
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
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

    private function generateNoRm(): string
    {
        $today = date('dmy');
        $count = Pasien::whereDate('created_at', today())
            ->lockForUpdate()
            ->count();
        return 'RM-' . $today . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}
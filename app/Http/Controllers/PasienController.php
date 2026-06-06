<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;

class PasienController extends Controller
{
    /*
    |---------------------------
    | HALAMAN DATA PASIEN
    |---------------------------
    */
    public function index()
    {
        $pasiens = Pasien::all();

        $pasienJson = $pasiens->map(function ($p) {
            $validasiBPJS = match($p->validasi) {
                'Valid', 'Disetujui'        => 'valid',
                'Tidak Valid', 'Ditolak'    => 'invalid',
                default                     => 'pending',
            };

            return [
                'id'           => (string) $p->id,
                'nama'         => $p->nama,
                'rm'           => $p->no_rm,
                'nik'          => $p->nik,
                'tglLahir'     => $p->tgl_lahir,
                'jk'           => $p->jenis_kelamin ?? '-',
                'jenisBayar'   => $p->jenis,
                'noBPJS'       => $p->no_bpjs ?? '',
                'validasi'     => $p->validasi,
                'validasiBPJS' => $validasiBPJS,
                'statusKirim'  => $p->status_kirim,
            ];
        });

        return view('admin.data', compact('pasiens', 'pasienJson'));
    }

    /*
    |---------------------------
    | HALAMAN QUEUE
    |---------------------------
    */
    public function queue()
    {
        $belumDikirim = Pasien::where('status_kirim', 'Belum')
            ->orderBy('created_at')
            ->get();

        $sudahDikirim = Pasien::where('status_kirim', 'Terkirim')
            ->orderBy('updated_at', 'desc')
            ->get();

        $belumDikirimJson = $belumDikirim->map(function($p) {
            return [
                'id'          => $p->id,
                'rm'          => $p->no_rm,
                'nama'        => $p->nama,
                'poli'        => $p->poli_tujuan,
                'jenis'       => $p->jenis,
                'status_kirim'=> $p->status_kirim,
            ];
        });

        return view('admin.queue', compact('belumDikirim', 'sudahDikirim', 'belumDikirimJson'));
    }

    /*
    |---------------------------
    | FORM TAMBAH PASIEN
    |---------------------------
    */
    public function create()
    {
        $noRm = $this->generateNoRm();
        return view('admin.create-pasien', compact('noRm'));
    }

    // Generate no_rm secara aman (thread-safe)
    private function generateNoRm(): string
    {
        return \DB::transaction(function () {
            $today = date('dmy');
            // lockForUpdate mencegah dua request baca count yang sama bersamaan
            $countToday = Pasien::whereDate('created_at', today())
                ->lockForUpdate()
                ->count();
            return 'RM-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
        });
    }

    /*
    |---------------------------
    | SIMPAN PASIEN BARU
    |---------------------------
    */
    public function store(Request $request)
    {
        $rules = [
            'nama'             => 'required|string',
            'nik'              => 'required|digits:16',
            'tgl_lahir'        => 'required|date',
            'jenis_kelamin'    => 'required|in:L,P',
            'jenis'            => 'required|in:BPJS,Mandiri',
            'poli_tujuan'      => 'required',
            'status'           => 'required',
            'alamat'           => 'nullable|string',
            'no_telepon'       => 'nullable|string|max:20',
            'pekerjaan'        => 'nullable|string|max:100',
            'jenis_kunjungan'  => 'nullable|string',
            'keluhan'          => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
            'berat_badan'      => 'nullable|numeric|min:1|max:300',
            'tinggi_badan'     => 'nullable|numeric|min:1|max:300',
            'tekanan_darah'    => 'nullable|string|max:10',
            'alergi'           => 'nullable|string|max:255',
        ];

        if ($request->jenis === 'BPJS') {
            $rules['no_bpjs'] = 'required|string|max:20';
        }

        $request->validate($rules);

        $existingPasien = Pasien::where('nik', $request->nik)->first();
        
        if ($existingPasien) {
            $existingPasien->update([
                'poli_tujuan'      => $request->poli_tujuan,
                'jenis_kunjungan'  => $request->jenis_kunjungan ?? 'Rawat Jalan',
                'keluhan'          => $request->keluhan,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'berat_badan'      => $request->berat_badan,
                'tinggi_badan'     => $request->tinggi_badan,
                'tekanan_darah'    => $request->tekanan_darah,
                'alergi'           => $request->alergi ?? '-',
                'status'           => $request->status,
                'nama'             => $request->nama,
                'tgl_lahir'        => $request->tgl_lahir,
                'jenis_kelamin'    => $request->jenis_kelamin,
                'alamat'           => $request->alamat,
                'no_telepon'       => $request->no_telepon,
                'pekerjaan'        => $request->pekerjaan,
                'jenis'            => $request->jenis,
                'no_bpjs'          => $request->jenis === 'BPJS' ? $request->no_bpjs : null,
                'updated_at'       => now(),
            ]);
            
            return redirect()->route('pasien.index')->with('success', 
                'Data pasien ' . $existingPasien->nama . ' berhasil diperbarui');
        } else {
            $noRm = $this->generateNoRm();
            
            Pasien::create([
                'no_rm'            => $noRm,
                'nama'             => $request->nama,
                'nik'              => $request->nik,
                'tgl_lahir'        => $request->tgl_lahir,
                'jenis_kelamin'    => $request->jenis_kelamin,
                'jenis'            => $request->jenis,
                'no_bpjs'          => $request->jenis === 'BPJS' ? $request->no_bpjs : null,
                'poli_tujuan'      => $request->poli_tujuan,
                'jenis_kunjungan'  => $request->jenis_kunjungan ?? 'Rawat Jalan',
                'keluhan'          => $request->keluhan,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'berat_badan'      => $request->berat_badan,
                'tinggi_badan'     => $request->tinggi_badan,
                'tekanan_darah'    => $request->tekanan_darah,
                'alergi'           => $request->alergi ?? '-',
                'status'           => $request->status,
                'alamat'           => $request->alamat,
                'no_telepon'       => $request->no_telepon,
                'pekerjaan'        => $request->pekerjaan,
                'validasi'         => 'Menunggu',
                'status_kirim'     => 'Belum',
            ]);
            
            return redirect()->route('pasien.index')->with('success', 'Pasien baru berhasil ditambahkan');
        }
    }

    /*
    |---------------------------
    | FORM EDIT PASIEN
    |---------------------------
    */
    public function edit($id)
    {
        $pasien = Pasien::findOrFail($id);
        return view('admin.edit-pasien', compact('pasien'));
    }

    /*
    |---------------------------
    | UPDATE DATA PASIEN
    |---------------------------
    */
    public function update(Request $request, $id)
    {
        $request->validate([
            'no_rm'            => 'required',
            'nama'             => 'required',
            'nik'              => 'required|digits:16',
            'tgl_lahir'        => 'required|date',
            'jenis_kelamin'    => 'required|in:L,P',
            'jenis'            => 'required|in:BPJS,Mandiri',
            'poli_tujuan'      => 'required',
            'status'           => 'required',
            'alamat'           => 'nullable|string',
            'no_telepon'       => 'nullable|string|max:20',
            'pekerjaan'        => 'nullable|string|max:100',
            'jenis_kunjungan'  => 'nullable|string',
            'keluhan'          => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
            'berat_badan'      => 'nullable|numeric',
            'tinggi_badan'     => 'nullable|numeric',
            'tekanan_darah'    => 'nullable|string|max:20',
            'alergi'           => 'nullable|string|max:255',
        ]);

        $pasien = Pasien::findOrFail($id);
        $pasien->update([
            'no_rm'            => $request->no_rm,
            'nama'             => $request->nama,
            'nik'              => $request->nik,
            'tgl_lahir'        => $request->tgl_lahir,
            'jenis_kelamin'    => $request->jenis_kelamin,
            'jenis'            => $request->jenis,
            'no_bpjs'          => $request->jenis === 'BPJS' ? $request->no_bpjs : null,
            'poli_tujuan'      => $request->poli_tujuan,
            'status'           => $request->status,
            'alamat'           => $request->alamat,
            'no_telepon'       => $request->no_telepon,
            'pekerjaan'        => $request->pekerjaan,
            'jenis_kunjungan'  => $request->jenis_kunjungan ?? 'Rawat Jalan',
            'keluhan'          => $request->keluhan,
            'riwayat_penyakit' => $request->riwayat_penyakit,
            'berat_badan'      => $request->berat_badan,
            'tinggi_badan'     => $request->tinggi_badan,
            'tekanan_darah'    => $request->tekanan_darah,
            'alergi'           => $request->alergi ?? '-',
        ]);

        return redirect()->route('pasien.index')->with('success', 'Data pasien berhasil diupdate');
    }

    /*
    |---------------------------
    | HAPUS PASIEN
    |---------------------------
    */
    public function destroy($id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->delete();
        return redirect()->route('pasien.index')->with('success', 'Pasien berhasil dihapus');
    }

    /*
    |---------------------------
    | CEK NIK (AJAX)
    |---------------------------
    */
    public function cekNik($nik)
    {
        $pasien = Pasien::where('nik', $nik)->first();
        
        if ($pasien) {
            return response()->json([
                'found' => true,
                'data' => [
                    'nama'          => $pasien->nama,
                    'tgl_lahir'     => $pasien->tgl_lahir,
                    'jenis_kelamin' => $pasien->jenis_kelamin,
                    'alamat'        => $pasien->alamat,
                    'no_telepon'    => $pasien->no_telepon,
                    'pekerjaan'     => $pasien->pekerjaan,
                    'jenis'         => $pasien->jenis,
                    'no_bpjs'       => $pasien->no_bpjs,
                    'poli_tujuan'   => $pasien->poli_tujuan,
                    'jenis_kunjungan' => $pasien->jenis_kunjungan,
                    'riwayat_penyakit' => $pasien->riwayat_penyakit,
                    'keluhan'       => $pasien->keluhan,
                    'berat_badan'   => $pasien->berat_badan,
                    'tinggi_badan'  => $pasien->tinggi_badan,
                    'tekanan_darah' => $pasien->tekanan_darah,
                    'alergi'        => $pasien->alergi,
                    'no_rm'         => $pasien->no_rm,
                ]
            ]);
        }
        
        return response()->json(['found' => false]);
    }

    /*
    |---------------------------
    | UPDATE VALIDASI PASIEN
    |---------------------------
    */
    public function updateValidasi(Request $request, $id)
    {
        try {
            $pasien = Pasien::findOrFail($id);
            
            $inputValidasi = $request->input('validasi');
            
            $statusDatabase = match($inputValidasi) {
                'valid'   => 'Valid',
                'invalid' => 'Tidak Valid',
                default   => 'Menunggu'
            };

            if ($request->has('jenisBayar')) {
                $pasien->jenis = $request->input('jenisBayar');
                $pasien->no_bpjs = null;
                $statusDatabase = 'Valid';
            }

            if ($pasien->jenis === 'BPJS' && $statusDatabase === 'Valid' && empty($pasien->no_bpjs)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No BPJS belum diisi, tidak bisa disetujui'
                ], 422);
            }

            $pasien->validasi = $statusDatabase;
            $pasien->save();

            return response()->json([
                'success' => true,
                'message' => 'Status validasi berhasil diperbarui menjadi ' . $statusDatabase
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |---------------------------
    | KIRIM 1 PASIEN KE DOKTER
    |---------------------------
    */
    public function kirimKeDokter(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        if ($pasien->validasi !== 'Valid') {
            return response()->json([
                'success' => false,
                'message' => 'Pasien belum divalidasi.'
            ], 422);
        }

        if ($pasien->status_kirim === 'Terkirim') {
            return response()->json([
                'success' => false,
                'message' => 'Pasien sudah dikirim sebelumnya.'
            ], 422);
        }

        $pasien->update([
            'status_kirim' => 'Terkirim',
            'status'       => 'Menunggu',
        ]);

        return response()->json([
            'success' => true,
            'nama'    => $pasien->nama,
        ]);
    }

    /*
    |---------------------------
    | KIRIM SEMUA KE DOKTER
    |---------------------------
    */
    public function kirimSemua(Request $request)
    {
        $jumlah = Pasien::where('validasi', 'Valid')
                        ->where('status_kirim', 'Belum')
                        ->update([
                            'status_kirim' => 'Terkirim',
                            'status'       => 'Diperiksa',
                        ]);

        return response()->json([
            'success' => true,
            'jumlah'  => $jumlah,
            'message' => $jumlah > 0
                ? "$jumlah pasien berhasil dikirim ke dokter."
                : 'Tidak ada pasien yang perlu dikirim.',
        ]);
    }

    public function report()
    {
        $transaksi = Pasien::orderBy('created_at', 'desc')->get();
        $totalPasien = Pasien::count();

        $totalPendapatan = Pasien::sum(
            \DB::raw("
                CASE
                    WHEN jenis = 'BPJS' THEN 150000
                    WHEN jenis = 'Mandiri' THEN 300000
                    ELSE 0
                END
            ")
        );

        return view('admin.report', compact(
            'transaksi',
            'totalPasien',
            'totalPendapatan'
        ));
    }
}
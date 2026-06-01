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
        // mapping nilai validasi ke format yang dipakai JS
        $validasiBPJS = match($p->validasi) {
            'Disetujui' => 'valid',
            'Ditolak'   => 'invalid',
            default     => 'pending',  // 'Menunggu'
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
        'validasiBPJS' => $validasiBPJS,   // <-- tambahkan ini
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
        $belumDikirim = Pasien::where('validasi', 'Disetujui')
                              ->where('status_kirim', 'Belum')
                              ->orderBy('created_at')
                              ->get();

        $sudahDikirim = Pasien::where('status_kirim', 'Terkirim')
                              ->orderBy('updated_at', 'desc')
                              ->get();

        return view('admin.queue', compact('belumDikirim', 'sudahDikirim'));
    }

    /*
    |---------------------------
    | FORM TAMBAH PASIEN
    |---------------------------
    */
    public function create()
{
    $today = date('dmy'); // contoh: 010626
    $countToday = Pasien::whereDate('created_at', today())->count();
    $noRm = 'RM-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

    return view('admin.create-pasien', compact('noRm'));
}
    /*
    |---------------------------
    | SIMPAN PASIEN BARU
    |---------------------------
    */
    public function store(Request $request)
    {
        $rules = [
            'no_rm'            => 'required|unique:pasiens,no_rm',
            'nama'             => 'required',
            'nik'              => 'required|digits:16|unique:pasiens,nik',
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

        Pasien::create([
            'no_rm'            => $request->no_rm,
            'nama'             => $request->nama,
            'nik'              => $request->nik,
            'tgl_lahir'        => $request->tgl_lahir,
            'jenis_kelamin'    => $request->jenis_kelamin,
            'jenis'            => $request->jenis,
            'no_bpjs'          => $request->jenis === 'BPJS' ? $request->no_bpjs : null,
            'poli_tujuan'      => $request->poli_tujuan,
            'status'           => $request->status,
            'validasi'         => 'Menunggu',
            'status_kirim'     => 'Belum',
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

        return redirect()->route('pasien.index')->with('success', 'Pasien berhasil ditambahkan');
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
    | UPDATE VALIDASI PASIEN
    |---------------------------
    */
    public function updateValidasi(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        $validasi = $request->validasi; // 'Disetujui' atau 'Ditolak'

        if ($pasien->jenis === 'BPJS' && $validasi === 'Disetujui' && empty($pasien->no_bpjs)) {
            return response()->json([
                'success' => false,
                'message' => 'No BPJS belum diisi, tidak bisa disetujui'
            ], 422);
        }

        $pasien->validasi = $validasi;
        $pasien->save();

        return response()->json(['success' => true]);
    }

    /*
    |---------------------------
    | KIRIM 1 PASIEN KE DOKTER
    |---------------------------
    */
    public function kirimKeDokter(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        if ($pasien->validasi !== 'Disetujui') {
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
            'status'       => 'Diperiksa',
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
        $jumlah = Pasien::where('validasi', 'Disetujui')
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
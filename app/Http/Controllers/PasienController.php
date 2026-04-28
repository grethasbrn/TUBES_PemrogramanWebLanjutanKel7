<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;

class PasienController extends Controller
{
    /*
    |---------------------------
    | TAMPIL DATA PASIEN
    |---------------------------
    */
    public function index()
    {
        $pasiens = Pasien::all();
        $pasienJson = $pasiens->map(function ($p) {
            return [
                'id'           => (string) $p->id,
                'nama'         => $p->nama,
                'rm'           => $p->no_rm,
                'nik'          => $p->nik,
                'tglLahir'     => $p->tgl_lahir,
                'jk'           => $p->jenis_kelamin ?? '-',
                'jenisBayar'   => $p->jenis,
                'noBPJS'       => $p->no_bpjs ?? '',
                'validasiBPJS' => $p->validasi === 'Valid' ? 'valid'
                                : ($p->validasi === 'Tidak Valid' ? 'invalid'
                                : 'pending'),
            ];
        });
        return view('admin.data', compact('pasiens', 'pasienJson'));
    }

    /*
    |---------------------------
    | FORM TAMBAH PASIEN
    |---------------------------
    */
    public function create()
    {
        return view('admin.create-pasien');
    }

    /*
    |---------------------------
    | SIMPAN PASIEN KE DB
    |---------------------------
    */
    public function store(Request $request)
    {
    // ✅ BENAR: validate() hanya boleh berisi RULES validasi
    $request->validate([
        'no_rm'           => 'required',
        'nama'            => 'required',
        'nik'             => 'required|digits:16',
        'tgl_lahir'       => 'required|date',
        'jenis_kelamin'   => 'required|in:L,P',
        'jenis'           => 'required|in:BPJS,Mandiri',
        'poli_tujuan'     => 'required',
        'status'          => 'required',
        'alamat'          => 'nullable|string',
        'no_telepon'      => 'nullable|string|max:20',
        'pekerjaan'       => 'nullable|string|max:100',
        'jenis_kunjungan' => 'nullable|string',
        'keluhan'         => 'nullable|string',
        'riwayat_penyakit'=> 'nullable|string',
        'berat_badan'     => 'nullable|numeric|min:1|max:300',
        'tinggi_badan'    => 'nullable|numeric|min:1|max:300',
        'tekanan_darah'   => 'nullable|string|max:10',
        'alergi'          => 'nullable|string|max:255',
    ]);

    // ✅ BENAR: data yang disimpan ada di create(), terpisah dari validate()
    Pasien::create([
        'no_rm'           => $request->no_rm,
        'nama'            => $request->nama,
        'nik'             => $request->nik,
        'tgl_lahir'       => $request->tgl_lahir,
        'jenis_kelamin'   => $request->jenis_kelamin,
        'jenis'           => $request->jenis,
        'poli_tujuan'     => $request->poli_tujuan,
        'status'          => $request->status,
        'validasi'        => 'Menunggu',
        'alamat'          => $request->alamat,
        'no_telepon'      => $request->no_telepon,
        'pekerjaan'       => $request->pekerjaan,
        'jenis_kunjungan' => $request->jenis_kunjungan ?? 'Rawat Jalan',
        'keluhan'         => $request->keluhan,
        'riwayat_penyakit'=> $request->riwayat_penyakit,
        'berat_badan'     => $request->berat_badan,
        'tinggi_badan'    => $request->tinggi_badan,
        'tekanan_darah'   => $request->tekanan_darah,
        'alergi'          => $request->alergi ?? '-',
    ]);

    return redirect()->route('pasien.index')
        ->with('success', 'Pasien berhasil ditambahkan');
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
    | UPDATE PASIEN DI DB
    |---------------------------
    */
    public function update(Request $request, $id)
    {
    $request->validate([
        'no_rm'           => 'required',
        'nama'            => 'required',
        'nik'             => 'required|digits:16',
        'tgl_lahir'       => 'required|date',
        'jenis_kelamin'   => 'required|in:L,P',
        'jenis'           => 'required|in:BPJS,Mandiri',
        'poli_tujuan'     => 'required',
        'status'          => 'required',
        'alamat'          => 'nullable|string',
        'no_telepon'      => 'nullable|string|max:20',
        'pekerjaan'       => 'nullable|string|max:100',
        'jenis_kunjungan' => 'nullable|string',
        'keluhan'         => 'nullable|string',
        'riwayat_penyakit'=> 'nullable|string',
        'berat_badan'     => 'nullable|numeric',
        'tinggi_badan'    => 'nullable|numeric',
        'tekanan_darah'   => 'nullable|string|max:20',
        'alergi'          => 'nullable|string|max:255',
    ]);

    $pasien = Pasien::findOrFail($id);
    $pasien->update([
        'no_rm'           => $request->no_rm,
        'nama'            => $request->nama,
        'nik'             => $request->nik,
        'tgl_lahir'       => $request->tgl_lahir,
        'jenis_kelamin'   => $request->jenis_kelamin,
        'jenis'           => $request->jenis,
        'poli_tujuan'     => $request->poli_tujuan,
        'status'          => $request->status,
        'alamat'          => $request->alamat,
        'no_telepon'      => $request->no_telepon,
        'pekerjaan'       => $request->pekerjaan,
        'jenis_kunjungan' => $request->jenis_kunjungan ?? 'Rawat Jalan',
        'keluhan'         => $request->keluhan,
        'riwayat_penyakit'=> $request->riwayat_penyakit,
        'berat_badan'     => $request->berat_badan,
        'tinggi_badan'    => $request->tinggi_badan,
        'tekanan_darah'   => $request->tekanan_darah,
        'alergi'          => $request->alergi ?? '-',
    ]);

    return redirect()->route('pasien.index')
        ->with('success', 'Data pasien berhasil diupdate');
    }

    /*
    |---------------------------
    | HAPUS PASIEN DARI DB
    |---------------------------
    */
    public function destroy($id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->delete();

        return redirect()->route('pasien.index')
            ->with('success', 'Pasien berhasil dihapus');
    }

    /*
    |---------------------------
    | UPDATE VALIDASI PASIEN
    |---------------------------
    */
    public function updateValidasi(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        $map = ['valid' => 'Valid', 'invalid' => 'Tidak Valid', 'pending' => 'Menunggu'];
        $pasien->validasi = $map[$request->validasi] ?? 'Menunggu';
        $pasien->save();

        return response()->json(['success' => true]);
    }
}
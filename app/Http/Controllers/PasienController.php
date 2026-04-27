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
   public function index(){
    $pasiens = Pasien::all();
    $pasienJson = $pasiens->map(function($p) {
        return [
            'id'          => (string) $p->id,  // cast ke string
            'nama'        => $p->nama,
            'rm'          => $p->no_rm,
            'nik'         => $p->nik,
            'tglLahir'    => $p->tgl_lahir,
            'jk'          => $p->jenis_kelamin ?? '-',
            'jenisBayar'  => $p->jenis,
            'noBPJS'      => $p->no_bpjs ?? '',
            'validasiBPJS'=> $p->validasi === 'Valid' ? 'valid'
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
        $request->validate([
            'no_rm' => 'required',
            'nama' => 'required',
            'nik' => 'required',
            'tgl_lahir' => 'required',
            'jenis' => 'required',
            'poli_tujuan' => 'required',
            'status' => 'required',
        ]);

        Pasien::create([
            'no_rm' => $request->no_rm,
            'nama' => $request->nama,
            'nik' => $request->nik,
            'tgl_lahir' => $request->tgl_lahir,
            'jenis' => $request->jenis,
            'poli_tujuan' => $request->poli_tujuan,
            'status' => $request->status,
            'validasi' => 'Menunggu',
        ]);

        return redirect()->route('pasien.index')
            ->with('success', 'Pasien berhasil ditambahkan');
    }
    
    public function updateValidasi(Request $request, $id){
    $pasien = Pasien::findOrFail($id);
    
    // Map dari JS value ke DB value
    $map = ['valid' => 'Valid', 'invalid' => 'Tidak Valid', 'pending' => 'Menunggu'];
    $pasien->validasi = $map[$request->validasi] ?? 'Menunggu';
    $pasien->save();
    
    return response()->json(['success' => true]);
}
}
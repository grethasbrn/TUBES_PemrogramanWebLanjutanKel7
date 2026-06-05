<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DokterController extends Controller
{
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
            'nama'         => $request->nama,
            'spesialisasi' => $request->spesialisasi,
            'no_telepon'   => $request->no_telepon,
            'email'        => $request->email,
            'status'       => $request->status,
            'created_at'   => now(),
            'updated_at'   => now(),
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
            'nama'         => $request->nama,
            'spesialisasi' => $request->spesialisasi,
            'no_telepon'   => $request->no_telepon,
            'email'        => $request->email,
            'status'       => $request->status,
            'updated_at'   => now(),
        ]);

        return redirect()->back()->with('success', 'Dokter berhasil diupdate');
    }

    public function destroy($id)
    {
        DB::table('dokters')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Dokter berhasil dihapus');
    }
}

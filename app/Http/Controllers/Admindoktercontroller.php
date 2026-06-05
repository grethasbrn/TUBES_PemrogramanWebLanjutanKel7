<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminDokterController extends Controller
{
    /**
     * Daftar semua akun dokter.
     */
    public function index()
    {
        $dokters = User::where('role', 'dokter')->orderBy('poli')->get();
        return view('admin.dokter', compact('dokters'));
    }

    /**
     * Simpan dokter baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'poli'     => ['required', Rule::in(['Umum','Anak','Penyakit Dalam','Bedah','Gigi','Kebidanan','Mata','UGD'])],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'dokter',
            'poli'     => $request->poli,
        ]);

        return redirect()->route('admin.dokter.index')
            ->with('success', "Akun dokter {$request->name} (Poli {$request->poli}) berhasil dibuat.");
    }

    /**
     * Update data dokter.
     */
    public function update(Request $request, $id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($dokter->id)],
            'password' => 'nullable|string|min:6',
            'poli'     => ['required', Rule::in(['Umum','Anak','Penyakit Dalam','Bedah','Gigi','Kebidanan','Mata','UGD'])],
        ]);

        $dokter->name  = $request->name;
        $dokter->email = $request->email;
        $dokter->poli  = $request->poli;

        if ($request->filled('password')) {
            $dokter->password = Hash::make($request->password);
        }

        $dokter->save();

        return redirect()->route('admin.dokter.index')
            ->with('success', "Akun {$dokter->name} berhasil diperbarui.");
    }

    /**
     * Hapus akun dokter.
     */
    public function destroy($id)
    {
        $dokter = User::where('role', 'dokter')->findOrFail($id);
        $nama   = $dokter->name;
        $dokter->delete();

        return redirect()->route('admin.dokter.index')
            ->with('success', "Akun {$nama} berhasil dihapus.");
    }
}
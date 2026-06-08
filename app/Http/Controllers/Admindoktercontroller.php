<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminDokterController extends Controller
{
    /**
     * Daftar semua dokter.
     */
    public function index()
    {
        $dokters = Dokter::orderBy('nama')->get();
        return view('admin.dokter', compact('dokters'));
    }

    /**
     * Simpan dokter baru → insert ke tabel dokters DAN users.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:100',
            'spesialisasi' => 'required|string|max:100',
            'no_telepon'   => 'required|string|max:20',
            'email'        => 'required|email|unique:dokters,email|unique:users,email',
            'password'     => 'required|string|min:6',
            'status'       => 'required|in:Aktif,Tidak Aktif',
        ]);

        // 1. Simpan ke tabel dokters (untuk tampilan manajemen)
        Dokter::create([
            'nama'         => $request->nama,
            'spesialisasi' => $request->spesialisasi,
            'no_telepon'   => $request->no_telepon,
            'email'        => $request->email,
            'status'       => $request->status,
        ]);

        // 2. Buat akun login di tabel users (untuk bisa login)
        User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'dokter',
            'poli'     => $request->spesialisasi,
        ]);

        return redirect()->route('admin.dokter.index')
            ->with('success', "Dokter {$request->nama} berhasil ditambahkan dan akun login telah dibuat.");
    }

    /**
     * Update data dokter → update tabel dokters DAN users.
     */
    public function update(Request $request, $id)
    {
        $dokter = Dokter::findOrFail($id);

        $request->validate([
            'nama'         => 'required|string|max:100',
            'spesialisasi' => 'required|string|max:100',
            'no_telepon'   => 'required|string|max:20',
            'email'        => ['required', 'email', Rule::unique('dokters', 'email')->ignore($dokter->id)],
            'password'     => 'nullable|string|min:6',
            'status'       => 'required|in:Aktif,Tidak Aktif',
        ]);

        // 1. Update tabel dokters
        $dokter->update([
            'nama'         => $request->nama,
            'spesialisasi' => $request->spesialisasi,
            'no_telepon'   => $request->no_telepon,
            'email'        => $request->email,
            'status'       => $request->status,
        ]);

        // 2. Update akun login di tabel users
        $user = User::where('email', $dokter->email)->where('role', 'dokter')->first();
        if ($user) {
            $user->name  = $request->nama;
            $user->email = $request->email;
            $user->poli  = $request->spesialisasi;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
        }

        return redirect()->route('admin.dokter.index')
            ->with('success', "Data {$dokter->nama} berhasil diperbarui.");
    }

    /**
     * Hapus dokter → hapus dari tabel dokters DAN users.
     */
    public function destroy($id)
    {
        $dokter = Dokter::findOrFail($id);
        $nama   = $dokter->nama;

        $kunjunganAktif = \App\Models\Kunjungan::where('dokter_id', $id)
            ->whereNotIn('status', ['Selesai'])
            ->count();

        if ($kunjunganAktif > 0) {
            return redirect()->back()->withErrors(
                "Tidak bisa hapus dokter {$nama}, masih ada {$kunjunganAktif} pasien aktif."
            );
        }

        User::where('email', $dokter->email)->where('role', 'dokter')->delete();
        $dokter->delete();

        return redirect()->route('admin.dokter.index')
            ->with('success', "Dokter {$nama} berhasil dihapus.");

        // Hapus akun login juga
        User::where('email', $dokter->email)->where('role', 'dokter')->delete();

        $dokter->delete();

        return redirect()->route('admin.dokter.index')
            ->with('success', "Dokter {$nama} berhasil dihapus.");
    }
}
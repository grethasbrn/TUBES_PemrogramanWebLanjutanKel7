<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::all(); 
        return view('apoteker.stock', compact('batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat'   => 'required|string|max:255',
            'tipe'        => 'required|string|max:100',
            'no_batch'    => 'required|string|unique:batches,no_batch',
            'jumlah'      => 'required|integer|min:1',
            'harga'       => 'required|integer|min:0',
            'tgl_expired' => 'required|date|after:today',
            'tgl_masuk'   => 'required|date',
            'supplier'    => 'nullable|string|max:255',
        ], [
            'nama_obat.required'   => 'Nama obat wajib diisi.',
            'tipe.required'        => 'Tipe obat wajib diisi.',
            'no_batch.required'    => 'No batch wajib diisi.',
            'no_batch.unique'      => 'No batch sudah digunakan.',
            'jumlah.required'      => 'Jumlah wajib diisi.',
            'jumlah.min'           => 'Jumlah minimal 1.',
            'harga.required'       => 'Harga wajib diisi.',
            'tgl_expired.required' => 'Tanggal expired wajib diisi.',
            'tgl_expired.after'    => 'Tanggal expired harus setelah hari ini.',
            'tgl_masuk.required'   => 'Tanggal masuk wajib diisi.',
        ]);

        Batch::create($request->all());

        return redirect()->route('batch.index')
            ->with('success', 'Batch obat berhasil ditambahkan!');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return redirect()->route('batch.index')
            ->with('success', 'Batch obat berhasil dihapus.');
    }
}
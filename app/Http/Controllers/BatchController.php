<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;

class BatchController extends Controller
{
    public function index()
    {
        return response()->json(Batch::latest()->get());
    }

    public function store(Request $request)
    {
        $data = Batch::create([
            'nama_obat' => $request->nama,
            'tipe' => $request->tipe,
            'no_batch' => $request->batch,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
            'tgl_expired' => $request->exp,
            'tgl_masuk' => $request->masuk,
            'supplier' => $request->supplier
        ]);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
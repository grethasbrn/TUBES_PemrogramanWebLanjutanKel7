<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pasien;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('pasien')->get();
        return view('admin.invoice', compact('invoices'));
    }

    public function store(Request $request)
    {
        Invoice::create([
            'kode_invoice' => 'INV-' . time(),
            'pasien_id' => $request->pasien_id,
            'tanggal' => now(),
            'status' => 'Masuk',
            'bayar' => $request->bayar,
            'total' => $request->total,
        ]);

        return back()->with('success','Invoice berhasil dibuat');
    }

    public function bayar($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->status = 'Lunas';
        $invoice->save();

        return back()->with('success','Invoice dibayar');
    }
}
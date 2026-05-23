<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Resep; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('resep')->orderBy('created_at', 'desc')->get();

        $selectedInvoice = null;
        if ($request->has('id')) {
            $selectedInvoice = Invoice::with('resep')->find($request->id);
        }

        return view('admin.invoice', compact('invoices', 'selectedInvoice'));
    }
    
    public function store(Request $request)
    {
        $resep = Resep::findOrFail($request->resep_id);

        // Cegah duplikat invoice untuk resep yang sama
        if (Invoice::where('resep_id', $resep->id)->exists()) {
            return redirect()->back()->with('error', 'Invoice untuk resep ini sudah ada.');
        }

        $subtotal = collect($resep->obat_list)
            ->sum(fn($item) => ($item['jumlah'] ?? 0) * ($item['harga'] ?? 0));

        $isBPJS = $resep->jenis === 'BPJS';
        $ppn    = $isBPJS ? 0 : round($subtotal * 0.11);

        Invoice::create([
            'no_invoice'    => 'INV-' . now()->format('Ymd') . '-' . str_pad($resep->id, 4, '0', STR_PAD_LEFT),
            'resep_id'      => $resep->id,
            'no_rm'         => $resep->no_rm,
            'nama'          => $resep->nama,
            'jenis'         => $resep->jenis,
            'status'        => 'Masuk',
            'subtotal'      => $subtotal,
            'total_tagihan' => $isBPJS ? 0 : ($subtotal + $ppn),
        ]);

        return redirect()->route('invoice.index')
                         ->with('success', 'Invoice berhasil dibuat.');
    }

    public function bayar(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->update([
            'status' => 'Lunas',
            'diproses_oleh' => auth()->id(),
            'no_referensi' => $request->no_referensi ?? 'REF-' . time()
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil diproses!');
    }

    public function downloadPdf($id)
    {
        $inv = Invoice::with('resep')->findOrFail($id);

        $pdf = Pdf::loadView('admin.invoice_pdf', compact('inv'));
        
        return $pdf->stream('Invoice-'.$inv->no_invoice.'.pdf');
    }

    public function show($id)
    {
        $invoice = Invoice::with('resep')->findOrFail($id);
        
        return view('invoice.show', compact('invoice'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->get();

        $selectedInvoice = null;
        if ($request->has('id')) {
            $selectedInvoice = Invoice::with('resep')->find($request->id);
        }

        return view('admin.invoice', compact('invoices', 'selectedInvoice'));
    }
    
    public function show($id)
    {
        $invoice = Invoice::with('resep')->findOrFail($id);
        
        return view('invoice.show', compact('invoice'));
    }

    public function downloadPdf($id)
    {
        $inv = Invoice::with('resep')->findOrFail($id);

        $pdf = Pdf::loadView('admin.invoice_pdf', compact('inv'));
        
        return $pdf->stream('Invoice-'.$inv->no_invoice.'.pdf');
    }


    public function bayar(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => 'Lunas',
            'created_at' => now(),
            'updated_at' => now(),
            'diproses_oleh' => auth()->id(),
            'no_referensi' => $request->no_referensi ?? 'REF-' . time()
        ]);

return redirect()->back()->with('success', 'Pembayaran berhasil diproses!');    }
}
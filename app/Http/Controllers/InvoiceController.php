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
        $resep = Resep::with('pasien')->findOrFail($request->resep_id);

        if (Invoice::where('resep_id', $resep->id)->exists()) {
            return redirect()->back()->with('error', 'Invoice untuk resep ini sudah ada.');
        }

        $pasien  = $resep->pasien;
        $isBPJS  = ($pasien->jenis ?? 'Mandiri') === 'BPJS';

        // Ambil harga dari Batch karena obat_list tidak menyimpan harga
        $subtotal = collect($resep->obat_list)->sum(function ($item) use ($isBPJS) {
            if ($isBPJS) return 0;

            $batch = \App\Models\Batch::whereRaw('LOWER(nama_obat) LIKE ?', [
                    '%' . strtolower($item['nama'] ?? '') . '%'
                ])
                ->where('jumlah', '>', 0)
                ->first();

            return ($item['jumlah'] ?? 0) * ($batch ? (float) $batch->harga : 0);
        });

        $ppn = $isBPJS ? 0 : round($subtotal * 0.11);

        Invoice::create([
            'no_invoice'    => 'INV-' . now()->format('Ymd') . '-' . str_pad($resep->id, 4, '0', STR_PAD_LEFT),
            'resep_id'      => $resep->id,
            'no_rm'         => $pasien->no_rm ?? '-',
            'nama'          => $pasien->nama ?? '-',
            'jenis'         => $pasien->jenis ?? 'Mandiri',
            'status'        => 'masuk',
            'subtotal'      => $subtotal,
            'total_tagihan' => $isBPJS ? 0 : ($subtotal + $ppn),
        ]);

        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function bayar(Request $request, $id)
    {
        return \DB::transaction(function () use ($request, $id) {

            $invoice = Invoice::with('resep')->findOrFail($id);

            // kalau sudah lunas, stop
            if (strtolower($invoice->status) === 'lunas') {
                return redirect()->back()->with('info', 'Invoice sudah dibayar');
            }

            $resep = $invoice->resep;

            foreach ($resep->obat_list as $item) {

                $batch = \App\Models\Batch::whereRaw('LOWER(nama_obat) LIKE ?', [
                        '%' . strtolower($item['nama'] ?? '') . '%'
                    ])
                    ->where('jumlah', '>', 0)
                    ->first();

                if (!$batch) {
                    throw new \Exception("Obat {$item['nama']} tidak ditemukan di stok");
                }

                if ($batch->jumlah < ($item['jumlah'] ?? 0)) {
                    throw new \Exception("Stok {$item['nama']} tidak cukup");
                }

                $batch->jumlah -= $item['jumlah'];
                $batch->save();
            }

            // update invoice
            $invoice->update([
                'status' => 'Lunas',
                'diproses_oleh' => auth()->id(),
                'no_referensi' => $request->no_referensi ?? 'REF-' . time()
            ]);

            // optional: update resep
            $resep->status = 'selesai';
            $resep->save();

            return redirect()->back()->with('success', 'Pembayaran berhasil & stok dikurangi!');
        });
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

    public function apiIndex()
    {
        return Invoice::with('resep')
            ->latest()
            ->get()
            ->map(fn($inv) => [
                'id'         => $inv->id,
                'no_invoice' => $inv->no_invoice,
                'nama'       => $inv->nama,
                'no_rm'      => $inv->no_rm,
                'jenis'      => $inv->jenis,
                'status'     => strtolower($inv->status),
                'subtotal'   => $inv->subtotal,
                'ppn'        => $inv->ppn,
                'total'      => $inv->total_tagihan,
                'tanggal'    => $inv->created_at->toDateString(),
                'diagnosa'   => $inv->resep->diagnosa ?? '-',
                'obat'       => $inv->resep->obat_list ?? [],
            ]);
    }
}
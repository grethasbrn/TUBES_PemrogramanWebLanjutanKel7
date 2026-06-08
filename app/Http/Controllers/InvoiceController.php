<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Invoice;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * ✅ FIX: Ubah dari private → public
     * Bug lama: private sehingga ResepController terpaksa pakai ReflectionMethod (anti-pattern)
     */
    public function buatInvoiceDariResep(Resep $resep): Invoice
    {
        // Guard: hindari duplikat invoice untuk resep yang sama
        $existing = Invoice::where('resep_id', $resep->id)->first();
        if ($existing) {
            return $existing;
        }

        $pasien   = $resep->pasien;
        $isBPJS   = ($pasien->jenis ?? 'Mandiri') === 'BPJS';
        $obatList = $resep->obat_list ?? [];

        $subtotal = collect($obatList)->sum(function ($item) use ($isBPJS) {
            if ($isBPJS) return 0;
            $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', [
                '%' . strtolower($item['nama'] ?? '') . '%'
            ])->where('jumlah', '>', 0)->first();
            return ($item['jumlah'] ?? 0) * ($batch ? (float) $batch->harga : 0);
        });

        $ppn   = $isBPJS ? 0 : round($subtotal * 0.11);
        $total = $isBPJS ? 0 : ($subtotal + $ppn);

        return Invoice::create([
            'no_invoice'    => 'INV-' . now()->format('Ymd') . '-' . str_pad($resep->id, 4, '0', STR_PAD_LEFT),
            'resep_id'      => $resep->id,
            'no_rm'         => $pasien->no_rm ?? '-',
            'nama'          => $pasien->nama   ?? '-',
            'jenis'         => $pasien->jenis  ?? 'Mandiri',
            'status'        => 'masuk',
            'subtotal'      => $subtotal,
            'ppn'           => $ppn,
            'total_tagihan' => $total,
        ]);
    }

    /**
     * ✅ FIX: kurangiStok — tambah guard agar tidak bisa dipanggil dua kali
     * Bug lama: tidak ada flag pencegah double-deduct stok
     */
    private function kurangiStok(Resep $resep): void
    {
        // Guard: cek flag stok_dikurangi — hanya kurangi sekali
        // Catatan: tambahkan kolom ini via migration (lihat instruksi di bawah)
        if ($resep->stok_dikurangi) {
            throw new \Exception("Stok untuk resep {$resep->no_resep} sudah pernah dikurangi.");
        }

        foreach ($resep->obat_list ?? [] as $item) {
            $batch = Batch::whereRaw('LOWER(nama_obat) LIKE ?', [
                '%' . strtolower($item['nama'] ?? '') . '%'
            ])->where('jumlah', '>', 0)->lockForUpdate()->first();

            if (!$batch) {
                throw new \Exception("Obat {$item['nama']} tidak ditemukan di stok");
            }

            $qty = (int) ($item['jumlah'] ?? 0);
            if ($batch->jumlah < $qty) {
                throw new \Exception("Stok {$item['nama']} tidak cukup (sisa: {$batch->jumlah})");
            }

            $batch->jumlah -= $qty;
            $batch->save();
        }

        // Tandai bahwa stok sudah dikurangi
        $resep->stok_dikurangi = true;
        $resep->save();
    }

    // ─── Admin: daftar semua invoice ───────────────────────────────────────
    public function index(Request $request)
    {
        $invoices = Invoice::with('resep')->orderBy('created_at', 'desc')->get();

        $selectedInvoice = null;
        if ($request->has('id')) {
            $selectedInvoice = Invoice::with('resep')->find($request->id);
        }

        return view('admin.invoice', compact('invoices', 'selectedInvoice'));
    }

    // ─── Apoteker: daftar resep masuk ─────────────────────────────────────
    public function apotekerIndex(Request $request)
    {
        $reseps = Resep::with('pasien')
            ->whereNotIn('status', ['draft'])
            ->latest()
            ->get();

        $selectedResep = null;
        if ($request->has('resep')) {
            $selectedResep = Resep::with('pasien')->find($request->resep);
        }

        return view('apoteker.invoice', compact('reseps', 'selectedResep'));
    }

    // ─── Apoteker: kirim invoice dari resep ───────────────────────────────
    public function kirimDariResep(Request $request, $id)
    {
        $resep = Resep::with('pasien')->findOrFail($id);

        $obatList = collect($request->obat ?? [])
            ->filter(fn($o) => !empty($o['nama']))
            ->values()
            ->toArray();

        $resep->obat_list = $obatList;
        $resep->status    = 'siap';
        $resep->save();

        if (!Invoice::where('resep_id', $resep->id)->exists()) {
            $this->buatInvoiceDariResep($resep);
        }

        return redirect()->route('apoteker.invoice')
            ->with('success', 'Invoice berhasil dikirim ke admin!');
    }

    // ─── Admin: buat invoice manual ───────────────────────────────────────
    public function store(Request $request)
    {
        $resep = Resep::with('pasien')->findOrFail($request->resep_id);

        if (Invoice::where('resep_id', $resep->id)->exists()) {
            return redirect()->back()->with('error', 'Invoice untuk resep ini sudah ada.');
        }

        $this->buatInvoiceDariResep($resep);

        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dibuat.');
    }

    // ─── Admin: proses bayar ───────────────────────────────────────────────
    public function bayar(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $invoice = Invoice::with('resep')->findOrFail($id);

            if (strtolower($invoice->status) === 'lunas') {
                return redirect()->back()->with('info', 'Invoice sudah dibayar');
            }

            $resep = $invoice->resep;
            $this->kurangiStok($resep);  // sudah ada guard double-deduct di dalam

            $invoice->update([
                'status'        => 'Lunas',
                'diproses_oleh' => auth()->id(),
                'no_referensi'  => $request->no_referensi ?? 'REF-' . time(),
            ]);

            $resep->status = 'selesai';
            $resep->save();

            return redirect()->back()->with('success', 'Pembayaran berhasil & stok dikurangi!');
        });
    }

    // ─── BPJS: selesaikan tanpa bayar ─────────────────────────────────────
    public function selesaikanBpjs(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {

            $invoice = Invoice::with('resep')->findOrFail($id);

            if ($invoice->jenis !== 'BPJS') {
                return redirect()->back()->with('error', 'Hanya untuk pasien BPJS.');
            }

            if (strtolower($invoice->status) === 'lunas') {
                return redirect()->back()->with('info', 'Invoice sudah diselesaikan');
            }

            $this->kurangiStok($invoice->resep);

            $invoice->update([
                'status'        => 'Lunas',
                'diproses_oleh' => auth()->id(),
                'no_referensi'  => 'BPJS-' . now()->format('YmdHis'),
            ]);

            $invoice->resep->update(['status' => 'selesai']);

            return redirect()->back()->with('success', 'Resep BPJS selesai & stok dikurangi!');
        });
    }

    public function downloadPdf($id)
    {
        $inv = Invoice::with('resep')->findOrFail($id);
        $pdf = Pdf::loadView('admin.invoice_pdf', compact('inv'));
        return $pdf->stream('Invoice-' . $inv->no_invoice . '.pdf');
    }

    public function show($id)
    {
        $invoice = Invoice::with('resep')->findOrFail($id);
        return view('invoice.show', compact('invoice'));
    }

    public function updateStatus(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->status = $request->status;
        $invoice->save();
        return redirect()->back()->with('success', 'Status invoice diperbarui.');
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

    public function payment()
    {
        $payments = Invoice::orderBy('created_at', 'desc')->get();
        return view('admin.payment', compact('payments'));
    }
}
@extends('layouts.admin')

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center">
    <h2>Detail Invoice</h2>
    <a href="{{ route('invoice.index') }}"
       style="padding:8px 16px;background:#6B7280;color:white;border-radius:8px;text-decoration:none">
        ← Kembali
    </a>
</div>

<div class="card" style="padding:24px;max-width:700px">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div>
            <div style="font-size:12px;color:#999">No Invoice</div>
            <div style="font-weight:600">{{ $invoice->no_invoice }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:#999">Status</div>
            <div style="font-weight:600">{{ $invoice->status }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:#999">Nama Pasien</div>
            <div>{{ $invoice->nama }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:#999">No RM</div>
            <div>{{ $invoice->no_rm }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:#999">Jenis Pembayaran</div>
            <div>{{ $invoice->jenis }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:#999">Tanggal</div>
            <div>{{ $invoice->created_at->format('d/m/Y') }}</div>
        </div>
    </div>

    <hr style="border:none;border-top:1px solid #eee;margin-bottom:20px">

    {{-- Daftar Obat --}}
    <h4 style="margin-bottom:12px">Daftar Obat</h4>
    <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
        <thead>
            <tr style="background:#f9f9f9;text-align:left">
                <th style="padding:8px 12px;font-size:13px">Nama Obat</th>
                <th style="padding:8px 12px;font-size:13px">Jumlah</th>
                <th style="padding:8px 12px;font-size:13px">Harga</th>
                <th style="padding:8px 12px;font-size:13px">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->resep->obat_list ?? [] as $obat)
            <tr style="border-bottom:1px solid #f0f0f0">
                <td style="padding:8px 12px">{{ $obat['nama'] ?? '-' }}</td>
                <td style="padding:8px 12px">{{ $obat['jumlah'] ?? '-' }}</td>
                <td style="padding:8px 12px">Rp {{ number_format($obat['harga'] ?? 0, 0, ',', '.') }}</td>
                <td style="padding:8px 12px">Rp {{ number_format(($obat['harga'] ?? 0) * ($obat['jumlah'] ?? 0), 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Ringkasan Biaya --}}
    <div style="text-align:right">
        <div style="margin-bottom:6px">Subtotal: <strong>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</strong></div>
        <div style="margin-bottom:6px">PPN (11%): <strong>Rp {{ number_format($invoice->ppn, 0, ',', '.') }}</strong></div>
        <div style="font-size:18px;font-weight:700;color:#7C3AED">
            Total: Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}
        </div>
    </div>

</div>

@endsection
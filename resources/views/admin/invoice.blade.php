@extends('layouts.admin')

@section('content')
<div class="page-section active" id="sec-invoice">
    <div class="page-header">
        <div>
            <div class="page-title">Invoice Masuk</div>
            <div class="page-sub">Invoice dari apoteker untuk diproses pembayaran</div>
        </div>
    </div>

    <div class="grid2" style="align-items:start">
        {{-- BAGIAN KIRI: LIST INVOICE --}}
        <div>
            <div class="search-row">
                <input type="text" class="search-input" placeholder="Cari..." id="srchInvoice">
                <select class="filter-sel" id="fltrInvoiceStatus">
                    <option value="">Semua</option>
                    <option value="Masuk">Masuk</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </div>

            <div id="invoiceList">
                @forelse($invoices as $inv)
                    @php
                        $isSelected = request('id') == $inv->id;
                        $payBadge = $inv->jenis === 'BPJS' ? 'b-bpjs' : 'b-mandiri';
                        $statusBadge = [
                            'Masuk' => 'b-danger',
                            'Diproses' => 'b-warn',
                            'Lunas' => 'b-selesai'
                        ][$inv->status] ?? 'b-warn';
                    @endphp

                    <a href="?id={{ $inv->id }}" class="card {{ $isSelected ? 'active' : '' }}" 
                       style="display:block; text-decoration:none; margin-bottom:12px; border-color:{{ $isSelected ? '#A63D33' : 'var(--cream3)' }}; background:{{ $isSelected ? 'var(--red-light)' : 'var(--white)' }}">
                        
                        <div style="display:flex; justify-content:space-between; align-items:flex-start">
                            <div>
                                <div style="font-family:'Cormorant Garamond',serif; font-size:15px; font-weight:600; color:var(--text)">{{ $inv->no_invoice }}</div>
                                <div style="font-size:12px; color:var(--text2); margin-top:2px">{{ $inv->nama }} · {{ $inv->no_rm }}</div>
                                
                                <div style="margin-top:8px; display:flex; gap:6px">
                                    <span class="badge {{ $payBadge }}">{{ $inv->jenis }}</span>
                                    <span class="badge {{ $statusBadge }}">{{ $inv->status }}</span>
                                </div>
                            </div>

                            <div style="text-align:right">
                                <div style="font-family:'Cormorant Garamond',serif; font-size:17px; font-weight:600; color:var(--text)">Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}</div>
                                <div style="font-size:11px; color:var(--text3); margin-top:4px">{{ $inv->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="color:var(--text3); font-size:13px; padding:24px; text-align:center">Tidak ada invoice</div>
                @endforelse
            </div>
        </div>

        {{-- BAGIAN KANAN: DETAIL INVOICE --}}
        <div id="invoiceDetail">
            @if($selectedInvoice)
                @php
                    $inv = $selectedInvoice;
                    $isBPJS = $inv->jenis === 'BPJS';
                    $ppn = $isBPJS ? 0 : round($inv->subtotal * 0.11);
                @endphp
                
                <div class="invoice-card card">
                    <div class="inv-header" style="background: #A63D33; margin: -22px -22px 18px -22px; padding: 22px; border-radius: 12px 12px 0 0; color: white; display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="inv-title" style="color: white; font-size: 18px;">{{ $inv->no_invoice }}</div>
                            <div class="inv-no" style="color: rgba(255,255,255,0.8); font-size: 13px;">{{ $inv->nama }} · {{ $inv->no_rm }}</div>
                        </div>
                        <span class="badge" style="background: rgba(255,255,255,0.2); color: white;">{{ $inv->status }}</span>
                    </div>

                    <div class="detail-body">
                        <div style="display:flex; gap:6px; margin-bottom:12px">
                            <span class="badge {{ $isBPJS ? 'b-bpjs' : 'b-mandiri' }}">{{ $inv->jenis }}</span>
                        </div>

                        <div class="alert-banner info" style="{{ $isBPJS ? 'background: var(--teal-light); color: #0F6E56;' : '' }} font-size: 12px; margin-bottom:15px">
                            <span>{{ $isBPJS ? '🏥 Pasien BPJS — biaya ditanggung pemerintah' : '💳 Pasien Mandiri — biaya dibayar penuh' }}</span>
                        </div>

                        <div class="inv-section-title" style="font-weight: bold; margin-bottom: 8px;">RINCIAN OBAT</div>

                        <div style="border: 1px solid var(--cream3); border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
                            @foreach($inv->resep->obat_list as $item)
                            <div class="inv-row" style="padding: 10px 12px; background: var(--white); display:flex; justify-content:space-between; border-bottom: 1px solid var(--cream3)">
                                <div>
                                    <div style="font-weight: 500; color: var(--text);">{{ $item['nama'] }}</div>
                                    <div style="font-size: 11px; color: var(--text3);">{{ $item['qty'] }} × Rp {{ number_format($item['harga'], 0, ',', '.') }}</div>
                                </div>
                                <div style="font-weight: 600;">Rp {{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}</div>
                            </div>
                            @endforeach

                            <div style="padding: 12px; background: var(--cream2);">
                                <div class="inv-row" style="display:flex; justify-content:space-between; margin-bottom:4px">
                                    <span style="color: var(--text2);">Subtotal</span>
                                    <span>Rp {{ number_format($inv->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if(!$isBPJS)
                                <div class="inv-row" style="display:flex; justify-content:space-between; margin-bottom:4px">
                                    <span>PPN 11%</span>
                                    <span>Rp {{ number_format($ppn, 0, ',', '.') }}</span>
                                </div>
                                @else
                                <div class="inv-row" style="display:flex; justify-content:space-between; color: var(--teal);">
                                    <span>Ditanggung BPJS</span>
                                    <span>- Rp {{ number_format($inv->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @endif
                            </div>

                            <div class="inv-total-row" style="padding: 12px; background: var(--cream); border-top: 1px solid var(--cream3); color: #A63D33; display:flex; justify-content:space-between; align-items:center">
                                <span>Total Tagihan</span>
                                <span style="font-size: 19px; font-weight:bold;">Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($inv->status !== 'Lunas')
                            <form action="{{ route('invoice.bayar', $inv->id) }}" method="POST" onsubmit="return confirm('Proses pembayaran sekarang?')">
                                @csrf
                                <div style="display: flex; gap: 8px; margin-top: 20px;">
                                    <button type="submit" class="btn btn-danger" style="flex: 1; padding: 12px; font-weight: 600;">💳 Proses Pembayaran</button>
                                </div>
                            </form>
                        @else
                            <div class="alert-banner info" style="background: var(--green-light); color: #1F6B43; border: 1px solid var(--green); text-align:center; padding:10px">
                                <span style="font-weight: 600;">✅ Invoice ini sudah lunas</span>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card" style="text-align:center; padding: 50px 20px; color: var(--text3);">
                    <div style="font-size: 40px; margin-bottom: 10px;">🧾</div>
                    <div class="label">Pilih invoice untuk melihat detail</div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // JS Hanya untuk filter list di sisi client (opsional)
    document.getElementById('srchInvoice').addEventListener('input', function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('#invoiceList .card').forEach(card => {
            let text = card.innerText.toLowerCase();
            card.style.display = text.includes(val) ? 'block' : 'none';
        });
    });
</script>
@endsection
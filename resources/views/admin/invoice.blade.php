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
                    $total  = $isBPJS ? 0 : ($inv->subtotal + $ppn);
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
                                    <div style="font-weight: 500; color: var(--text);">{{ $item['nama'] ?? 'Obat' }}</div>
                                    <div style="font-size: 11px; color: var(--text3);">
                                        {{ $item['jumlah'] ?? 0 }} × 
                                        
                                        Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div style="font-weight: 600;">
                                    {{-- Kalkulasi otomatis: Jumlah x Harga --}}
                                    Rp {{ number_format(($item['jumlah'] ?? 0) * ($item['harga'] ?? 0), 0, ',', '.') }}
                                </div>
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
                                <span style="font-size: 19px; font-weight:bold;">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($inv->status !== 'Lunas')
                            <form action="{{ route('invoice.bayar', $inv->id) }}" method="POST" onsubmit="return confirm('Proses pembayaran sekarang?')">
                                @csrf
                                <div style="display: flex; gap: 8px; margin-top: 20px;">
                                    <button type="submit" style="background: #A63D33; color: white; padding: 12px; text-align: center; text-decoration: none; font-weight: 600; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; border:none; width:350px">
                                        Proses Pembayaran
                                    </button>
                                </div>
                            </form>
                        @else
                            <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                                <div class="alert-banner" style="background: #e6f4ea; color: #1e7e34; border: 1px solid #c3e6cb; text-align:center; padding:12px; border-radius: 8px; margin-bottom: 0;">
                                    <span style="font-weight: 600;"> ✓ &nbsp; Pembayaran Lunas</span>
                                </div>
                
                                <a href="{{ route('invoice.download', $inv->id) }}" 
                                target="_blank" 
                                class="btn" 
                                style="background: #A63D33; color: white; padding: 12px; text-align: center; text-decoration: none; font-weight: 600; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    Download Invoice (PDF)
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card" style="display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; min-height: 250px; padding: 40px 20px; color: var(--text3); text-align: center;">
                <div style="display: flex !important; align-items: center !important; justify-content: center !important; width: 60px; height: 60px; border-radius: 50%; background-color: rgba(166, 61, 51, 0.1); margin-bottom: 16px; flex-shrink: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#A63D33" style="display: block !important;">
                    <path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480ZM240-160h360v-80H200v44q0 17 11.5 28.5T240-160Zm-40 0v-80 80Z"/>
                    </svg>
                </div>
                <div class="label" style="font-size: 14px; font-weight: 500; line-height: 1.4; max-width: 280px; margin: 0 auto;">
                    Pilih invoice untuk melihat detail
                </div>
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
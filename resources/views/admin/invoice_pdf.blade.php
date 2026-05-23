<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Invoice {{ $inv->no_invoice }}</title>  <style>
    /* ── Reset & Base ─────────────────────────────── */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 11px;
      color: #1C1814;
      background: #fff;
      line-height: 1.5;
    }
    .body {padding: 20px 36px 36px 36px;}
    .page {
      width: 100%;
      padding: 0;
      position: relative;
    }
    .header-strip {
      background: #A63D33;
      padding: 28px 36px 22px 36px;
      position: relative;
      overflow: hidden;
    }
    .header-strip::before {
      content: '';
      position: absolute;
      top: -30px; right: -30px;
      width: 120px; height: 120px;
      border-radius: 50%;
      background: rgba(255,255,255,0.07);
    }
    .header-strip::after {
      content: '';
      position: absolute;
      bottom: -20px; right: 60px;
      width: 70px; height: 70px;
      border-radius: 50%;
      background: rgba(255,255,255,0.05);
    }
    .header-inner {display: table;width: 100%;}
    .header-left  { display: table-cell; vertical-align: middle; width: 60%; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 40%; }
    .brand-name {
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      letter-spacing: -0.3px;
    }
    .inv-number {
      font-size: 17px;
      font-weight: 700;
      color: #fff;
      letter-spacing: 0.02em;
    }
    .inv-date-lbl {
      font-size: 9px;
      color: rgba(255,255,255,0.65);
      text-transform: uppercase;
      letter-spacing: 0.07em;
      margin-top: 3px;
    }
    .inv-date-val {
      font-size: 11px;
      color: rgba(255,255,255,0.9);
      margin-top: 1px;
    }
    .info-grid {
      display: table;
      width: 100%;
      margin-bottom: 22px;
      border: 1px solid #EDE8E0;
      border-radius: 6px;
      overflow: hidden;
    }
    .info-col {
      display: table-cell;
      width: 50%;
      padding: 14px 16px;
      vertical-align: top;
    }
    .info-col:first-child {border-right: 1px solid #EDE8E0;background: #FAF8F5;}
    .info-col:last-child {background: #fff;}
    .info-label {
      font-size: 8.5px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #9C8E7E;
      margin-bottom: 3px;
    }
    .info-value {
      font-size: 12px;
      font-weight: 600;
      color: #1C1814;
    }
    .info-value-sm {
      font-size: 10.5px;
      color: #4A3F35;
      margin-top: 1px;
    }
    .info-row { margin-bottom: 11px; }
    .info-row:last-child { margin-bottom: 0; }
    .badge {
      display: inline-block;
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      padding: 2px 8px;
      border-radius: 99px;
    }
    .badge-bpjs    { background: #E1F5EE; color: #0F6E56; }
    .badge-mandiri { background: #E6F1FB; color: #185FA5; }
    .badge-lunas   { background: #EAF3DE; color: #2B7A4B; }
    .badge-masuk   { background: #FAECE7; color: #A63D33; }
    .badge-diproses{ background: #FAEEDA; color: #854F0B; }
    .section-title {
      font-size: 8.5px;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: #9C8E7E;
      margin-bottom: 8px;
      padding-bottom: 5px;
      border-bottom: 1px solid #EDE8E0;
    }
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 0;
    }
    .items-table thead tr {background: #A63D33;}
    .items-table thead th {
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #fff;
      padding: 8px 10px;
      text-align: left;
    }
    .items-table thead th:nth-child(2),.items-table thead th:nth-child(3) {text-align: center;}
    .items-table thead th:last-child {text-align: right;}
    .items-table tbody tr {border-bottom: 1px solid #EDE8E0;}
    .items-table tbody tr:last-child {border-bottom: none;}
    .items-table tbody tr:nth-child(even) {background: #FAF8F5;}
    .items-table tbody td {padding: 9px 10px;font-size: 11px;color: #1C1814;vertical-align: middle;}
    .items-table tbody td:nth-child(2) {text-align: center;color: #6B5C4E;}
    .items-table tbody td:nth-child(3) {text-align: center;color: #6B5C4E;}
    .items-table tbody td:last-child {text-align: right;font-weight: 600;}
    .summary-wrap {
      margin-top: 0;
      border: 1px solid #EDE8E0;
      border-top: none;
      border-radius: 0 0 6px 6px;
      overflow: hidden;
    }
    .items-box {
      border: 1px solid #EDE8E0;
      border-radius: 6px 6px 0 0;
      overflow: hidden;
      margin-bottom: 0;
    }
    .sum-row {
      display: table;
      width: 100%;
      padding: 6px 10px;
      background: #FAF8F5;
      border-top: 1px solid #EDE8E0;
    }
    .sum-row-left  { display: table-cell; font-size: 11px; color: #6B5C4E; }
    .sum-row-right { display: table-cell; text-align: right; font-size: 11px; color: #1C1814; }
    .sum-bpjs .sum-row-left,
    .sum-bpjs .sum-row-right { color: #0F6E56; font-weight: 600; }
    .total-row {
      display: table;
      width: 100%;
      padding: 11px 10px;
      background: #FAECE7;
      border-top: 1px solid #F5C4B3;
    }
    .total-row-left  { display: table-cell; font-size: 12px; font-weight: 700; color: #A63D33; }
    .total-row-right { display: table-cell; text-align: right; font-size: 15px; font-weight: 700; color: #A63D33; }
    .bpjs-notice {
      background: #E1F5EE;
      border-top: 1px solid #9FE1CB;
      padding: 8px 10px;
      font-size: 10px;
      color: #0F6E56;
      text-align: center;
    }
    .footer-section {display: table;width: 100%;margin-top: 30px;}
    .footer-spacer {display: table-cell;width: 65%;}
    .footer-right {display: table-cell;width: 35%;vertical-align: top;text-align: center;}
    .signature-box {
      border: 1px solid #EDE8E0;
      border-radius: 6px;
      padding: 10px;
      min-height: 80px;
      background: #FAF8F5; 
    }
    .signature-label {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #9C8E7E;
      margin-bottom: 6px;
      text-align: center;
    }
    .signature-line {
      margin-top: 8px;
      border-top: 1px solid #9C8E7E;
      padding-top: 4px;
      font-size: 11px;
      font-weight: 700;
      color: #1C1814;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="page">
  <div class="header-strip">
    <div class="header-inner">
      <div class="header-left">
        <div class="brand-name">Pharmbee Hospital</div>
      </div>
      <div class="header-right">
        <div class="inv-number">{{ $inv->no_invoice }}</div>
        <div class="inv-date-lbl">Tanggal Invoice</div>
        <div class="inv-date-val">{{ $inv->created_at->format('d F Y') }}</div>
      </div>
    </div>
  </div>

  <div class="body">
    <div class="info-grid">
      <div class="info-col">
        <div class="info-row">
          <div class="info-label">Nama Pasien</div>
          <div class="info-value">{{ $inv->nama }}</div> {{-- Sesuaikan dengan kolom 'nama' --}}
        </div>
        <div class="info-row">
          <div class="info-label">No. Rekam Medis</div>
          <div class="info-value">{{ $inv->no_rm }}</div>
        </div>
      </div>

      <div class="info-col">
        <div class="info-row">
          <div class="info-label">Jenis Pembayaran</div>
          <div class="info-value">
            <span class="badge {{ $inv->jenis == 'BPJS' ? 'badge-bpjs' : 'badge-mandiri' }}">
              {{ $inv->jenis }}
            </span>
          </div>
        </div>
        <div class="info-row">
          <div class="info-label">Status</div>
          <div class="info-value">
            <span class="badge {{ $inv->status == 'Lunas' ? 'badge-lunas' : 'badge-masuk' }}">
              {{ $inv->status }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="section-title">Rincian Obat</div>

    <div class="items-box">
      <table class="items-table">
        <thead>
          <tr>
            <th style="width:44%">Nama Obat</th>
            <th style="width:12%">Qty</th>
            <th style="width:22%">Harga Satuan</th>
            <th style="width:22%">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          {{-- PERBAIKAN: Gunakan obat_list dari JSON resep --}}
          @if($inv->resep && $inv->resep->obat_list)
            @foreach($inv->resep->obat_list as $item)
            <tr>
              <td>{{ $item['nama'] ?? '-' }}</td>
              <td>{{ $item['jumlah'] ?? 0 }}</td>
              <td>Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</td>
              <td>Rp {{ number_format(($item['jumlah'] ?? 0) * ($item['harga'] ?? 0), 0, ',', '.') }}</td>
            </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>

    <div class="summary-wrap">
      <div class="sum-row">
        <span class="sum-row-left">Subtotal</span>
        <span class="sum-row-right">Rp {{ number_format($inv->subtotal, 0, ',', '.') }}</span>
      </div>

      @if($inv->jenis == 'BPJS')
        <div class="sum-row sum-bpjs">
          <span class="sum-row-left">Ditanggung BPJS</span>
          <span class="sum-row-right">- Rp {{ number_format($inv->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
          <span class="total-row-left">Total Tagihan</span>
          <span class="total-row-right">Rp 0</span>
        </div>
      @else
        <div class="sum-row">
          <span class="sum-row-left">PPN 11%</span>
          <span class="sum-row-right">Rp {{ number_format($inv->subtotal * 0.11, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
          <span class="total-row-left">Total Tagihan</span>
          @php $totalFinal = $inv->jenis === 'BPJS' ? 0 : round($inv->subtotal * 1.11); @endphp
          <span class="total-row-right">Rp {{ number_format($totalFinal, 0, ',', '.') }}</span>
        </div>
      @endif
    </div>

    <div class="footer-section">
        <div class="footer-spacer"></div>

        <div class="footer-right">
            <div class="signature-label">Tanda Tangan</div>
            <div class="signature-box"></div>
            <div class="signature-line">
                {{ auth()->user()->name ?? 'Admin' }}
            </div>
        </div>
    </div>
  </div>
</div>
</body>
</html>
@extends('layouts.admin')

@section('content')

<div class="page-section active" id="sec-pembayaran">
  <div class="page-header">
    <div>
      <div class="page-title">Transaksi Pembayaran</div>
      <div class="page-sub">Proses pembayaran pasien & riwayat transaksi</div>
    </div>
  </div>

  <div class="grid2" style="align-items:start">
    <!-- Form Bayar -->
    <div>
      <div class="card" style="margin-bottom:14px">
        <div class="card-title">Proses Pembayaran</div>
        <div class="fg">
          <label>Pilih Invoice / No. RM</label>
          <select>
            <option value="">— Pilih invoice —</option>
          </select>
        </div>
        <div id="bayarSummary" style="display:none">
          <div id="bayarInfoBox" style="background:var(--cream);border-radius:10px;padding:14px;margin-bottom:14px;font-size:13px"></div>
          <div class="fg"><label>Metode Pembayaran</label>
            <div id="metodeBayarOpts"></div>
          </div>
          <div id="bpjsNote" style="display:none;background:var(--teal-light);border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:12px;color:#0F6E56">
            🏥 Pasien BPJS: biaya ditanggung sesuai ketentuan. Pasien hanya membayar komponen non-tanggungan (jika ada).
          </div>
          <div id="mandiriNote" style="display:none;background:var(--purple-light);border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:12px;color:#534AB7">
            💳 Pasien Mandiri: total tagihan dibayar penuh oleh pasien.
          </div>
          <div id="detailTagihan" style="border:1px solid var(--cream3);border-radius:10px;overflow:hidden;margin-bottom:14px"></div>
          <div class="fg"><label>Nominal Bayar (Rp)</label>
            <input type="number" id="nominalBayar" placeholder="Masukkan jumlah pembayaran">
          </div>
          <div class="fg"><label>Catatan</label>
            <input type="text" id="catatanBayar" placeholder="Opsional...">
          </div>
          <button class="btn btn-primary" style="width:100%">💳 Proses Pembayaran</button>
        </div>
      </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="card">
      <div class="card-title">Riwayat Transaksi</div>
      <div class="tbl-wrap">
        <table>
          <thead><tr><th>No.</th><th>Pasien</th><th>Total</th><th>Metode</th><th>Status</th><th>Tgl</th></tr></thead>
          <tbody id="tblTransaksi"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
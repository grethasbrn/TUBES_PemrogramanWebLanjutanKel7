@extends('layouts.dokter')

@section('content')

<div class="page-section active" id="sec-riwayat">
  <div class="page-header">
    <div>
      <div class="page-title">Riwayat Pasien</div>
      <div class="page-sub">Rekam medis dan riwayat resep pasien</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">
    <div>
      <div style="margin-bottom:12px">
        <input type="text" class="search-input" placeholder="Cari pasien..."
               id="riwayatSearch" oninput="renderRiwayatList()"
               style="width:100%;padding:9px 12px;border:1px solid #eee;
                      border-radius:8px;font-size:13px;box-sizing:border-box">
      </div>
      <div id="riwayatPasienList"></div>
    </div>
    <div id="riwayatDetail">
      <div class="card" style="text-align:center;padding:40px;color:#aaa">
        <div style="font-size:32px;margin-bottom:10px">📅</div>
        <div>Pilih pasien untuk lihat riwayat</div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
let riwayatPasienData = @json($pasienJson);
let riwayatResepData  = @json($resepJson);
let selectedRiwayatId = null;

function renderRiwayatList() {
    const q = (document.getElementById('riwayatSearch')?.value || '').toLowerCase();
    let data = [...riwayatPasienData];
    if (q) data = data.filter(p =>
        p.nama.toLowerCase().includes(q) || (p.rm || '').toLowerCase().includes(q)
    );

    const container = document.getElementById('riwayatPasienList');
    if (!data.length) {
        container.innerHTML = `<div style="text-align:center;padding:30px;color:#aaa;font-size:13px">
            Tidak ada data pasien
        </div>`;
        return;
    }

    container.innerHTML = data.map(p => `
        <div onclick="selectRiwayat('${p.id}')"
             style="border:1px solid #eee;border-radius:12px;padding:12px 14px;
                    margin-bottom:8px;cursor:pointer;
                    background:${selectedRiwayatId === p.id ? '#f0ebff' : '#fff'}">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:38px;height:38px;border-radius:50%;
                            background:#EEEDFE;color:#534AB7;font-weight:600;
                            font-size:15px;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    ${p.nama.charAt(0)}
                </div>
                <div>
                    <div style="font-weight:600;font-size:14px">${p.nama}</div>
                    <div style="font-size:12px;color:#888;margin-top:2px">
                        ${p.rm} · ${p.usia ? p.usia+' thn' : ''} · ${p.poli || '-'}
                    </div>
                    <div style="margin-top:5px">
                        <span style="padding:2px 8px;border-radius:10px;font-size:11px;
                                     background:${p.bayar==='BPJS'?'#e3f2fd':'#f3e5f5'};
                                     color:${p.bayar==='BPJS'?'#1565c0':'#6a1b9a'}">
                            ${p.bayar}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function selectRiwayat(id) {
    selectedRiwayatId = id;
    const p = riwayatPasienData.find(x => x.id === id);
    if (!p) return;

    renderRiwayatList();

    const resepPasien = riwayatResepData.filter(r => r.pasienId === id);
    const statusColor = {
        baru:'#fff3e0', validasi:'#e8f0fe', siap:'#e6f4ea',
        selesai:'#f3e8ff', ditolak:'#fce8e6', draft:'#f5f5f5'
    };
    const statusLabel = {
        baru:'Baru', validasi:'Divalidasi', siap:'Siap Ambil',
        selesai:'Selesai', ditolak:'Ditolak', draft:'Draft'
    };

    document.getElementById('riwayatDetail').innerHTML = `
        <div class="card" style="padding:20px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                <div style="width:44px;height:44px;border-radius:50%;
                            background:#EEEDFE;color:#534AB7;font-weight:700;
                            font-size:18px;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0">
                    ${p.nama.charAt(0)}
                </div>
                <div>
                    <div style="font-family:'Cormorant Garamond',serif;
                                font-size:18px;font-weight:600">${p.nama}</div>
                    <div style="font-size:12px;color:#888">${p.rm} · ${p.poli || '-'}</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px">
                <div style="background:#f9f7f5;border-radius:8px;padding:10px">
                    <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                                letter-spacing:.06em;margin-bottom:3px">Usia</div>
                    <div style="font-size:13px;font-weight:500">${p.usia ? p.usia+' tahun' : '-'}</div>
                </div>
                <div style="background:#f9f7f5;border-radius:8px;padding:10px">
                    <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                                letter-spacing:.06em;margin-bottom:3px">Jenis Bayar</div>
                    <div style="font-size:13px;font-weight:500">${p.bayar}</div>
                </div>
                <div style="background:#f9f7f5;border-radius:8px;padding:10px">
                    <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                                letter-spacing:.06em;margin-bottom:3px">Status</div>
                    <div style="font-size:13px;font-weight:500">${p.status}</div>
                </div>
                <div style="background:#f9f7f5;border-radius:8px;padding:10px">
                    <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                                letter-spacing:.06em;margin-bottom:3px">Tgl Daftar</div>
                    <div style="font-size:13px;font-weight:500">${p.tgl}</div>
                </div>
            </div>

            <div style="font-size:10px;color:#aaa;text-transform:uppercase;
                        letter-spacing:.06em;margin-bottom:10px">
                Riwayat Resep (${resepPasien.length})
            </div>

            ${resepPasien.length ? resepPasien.map(r => `
                <div style="border:1px solid #eee;border-radius:10px;
                            padding:12px 14px;margin-bottom:8px">
                    <div style="display:flex;justify-content:space-between;
                                align-items:flex-start;margin-bottom:6px">
                        <div style="font-weight:600;font-size:13px">${r.no_resep}</div>
                        <span style="padding:2px 8px;border-radius:10px;font-size:11px;
                                     font-weight:600;
                                     background:${statusColor[r.status]||'#f5f5f5'}">
                            ${statusLabel[r.status]||r.status}
                        </span>
                    </div>
                    <div style="font-size:12px;color:#666;margin-bottom:4px">
                        🩺 ${r.diagnosa}
                    </div>
                    <div style="font-size:11px;color:#aaa">📅 ${r.tanggal}</div>
                    ${r.obat && r.obat.length ? `
                    <div style="margin-top:8px;padding-top:8px;border-top:1px solid #f0f0f0">
                        ${r.obat.map(o => `
                            <div style="font-size:12px;color:#555;margin-bottom:2px">
                                💊 ${o.nama} — ${o.dosis || ''} (${o.jumlah || '-'})
                            </div>`).join('')}
                    </div>` : ''}
                </div>`).join('')
            : `<div style="text-align:center;padding:20px;color:#aaa;font-size:13px">
                Belum ada riwayat resep
               </div>`}
        </div>
    `;
}

document.addEventListener('DOMContentLoaded', function () {
    renderRiwayatList();
});
</script>
@endsection
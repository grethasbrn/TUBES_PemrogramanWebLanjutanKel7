<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmbee</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    @vite(['resources/css/style3.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app">
    
    @include('components.sidebarAd')

    <div class="main">
         
        <div class="content">
            @yield('content')
        </div>
    </div>

</div>

<script>
// ═══════════════ DATA ═══════════════
const today=new Date();today.setHours(0,0,0,0);
const DAYS=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const MONTHS=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
document.getElementById('dateTag').textContent=`${DAYS[today.getDay()]}, ${today.getDate()} ${MONTHS[today.getMonth()]} ${today.getFullYear()}`;
function fmtDate(d){if(!d)return'—';const[y,m,dy]=d.split('-');return`${dy}/${m}/${y}`}
function fmtRp(n){if(!n)return'Rp 0';if(n>=1e6)return'Rp '+(n/1e6).toFixed(1)+' jt';return'Rp '+Math.round(n).toLocaleString('id-ID')}
function fmtRpFull(n){return'Rp '+Math.round(n||0).toLocaleString('id-ID')}
function nowTime(){const n=new Date();return`${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`}

// Update clock
setInterval(()=>{
  const n=new Date();
  const t=`${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`;
  const el=document.getElementById('shiftTime');if(el)el.textContent=t;
},1000);

let pasienDB=[
  {id:0,nama:'Siti Rahayu',rm:'RM-2026-0091',tgl:'1985-04-12',jk:'P',bpjs:'0001-1234-5678',telp:'081234567890',keluhan:'Kontrol DM rutin',poli:'Poli Penyakit Dalam',regTime:'07:45',tot:8},
  {id:1,nama:'Ahmad Fauzi',rm:'RM-2026-0088',tgl:'1990-07-22',jk:'L',bpjs:null,telp:'085678901234',keluhan:'Batuk pilek 5 hari',poli:'Poli Umum',regTime:'08:10',tot:5},
  {id:2,nama:'Maya Putri',rm:'RM-2026-0087',tgl:'1998-02-14',jk:'P',bpjs:'0001-9876-5432',telp:'087890123456',keluhan:'Batuk alergi',poli:'Poli Anak',regTime:'08:32',tot:3},
  {id:3,nama:'Rudi Hartono',rm:'RM-2026-0085',tgl:'1978-11-30',jk:'L',bpjs:null,telp:'082345678901',keluhan:'Sakit maag',poli:'Poli Umum',regTime:'09:05',tot:12},
  {id:4,nama:'Dewi Lestari',rm:'RM-2026-0082',tgl:'1995-08-08',jk:'P',bpjs:'0002-3456-7890',telp:'089012345678',keluhan:'Demam & sakit kepala',poli:'Poli Umum',regTime:'09:20',tot:2},
];

let resepDB=[
  {id:'RX-20260402-034',pId:0,pas:'Siti Rahayu',rm:'RM-2026-0091',poli:'Poli Penyakit Dalam',diag:'Diabetes terkontrol',status:'selesai',tgl:'2026-04-02',obat:[{n:'Metformin 500mg',tp:'Tablet',jml:60,ar:'2x sehari 1 tablet sesudah makan',sat:'Tablet',hrg:800,sub:null}],cat:'',tot:48000,mb:'bpjs',bayarTime:'09:15'},
  {id:'RX-20260402-035',pId:1,pas:'Ahmad Fauzi',rm:'RM-2026-0088',poli:'Poli Umum',diag:'ISPA, batuk berdahak',status:'siap',tgl:'2026-04-02',obat:[{n:'Amoxicillin 250mg',tp:'Kapsul',jml:10,ar:'3x sehari 1 kapsul',sat:'Kapsul',hrg:1500,sub:null},{n:'Paracetamol 500mg',tp:'Tablet',jml:10,ar:'3x sehari 1 tablet jika demam',sat:'Tablet',hrg:500,sub:null}],cat:'Habiskan antibiotik',tot:20000,mb:null,bayarTime:null},
  {id:'RX-20260402-036',pId:2,pas:'Maya Putri',rm:'RM-2026-0087',poli:'Poli Anak',diag:'Batuk alergi',status:'siap',tgl:'2026-04-02',obat:[{n:'OBH Combi Sirup',tp:'Sirup',jml:1,ar:'3x sehari 1 sendok makan',sat:'Botol',hrg:35000,sub:null},{n:'Cetirizine 10mg',tp:'Tablet',jml:7,ar:'1x sehari malam',sat:'Tablet',hrg:700,sub:null}],cat:'',tot:39900,mb:null,bayarTime:null},
  {id:'RX-20260402-037',pId:3,pas:'Rudi Hartono',rm:'RM-2026-0085',poli:'Poli Umum',diag:'Gastritis',status:'diproses',tgl:'2026-04-02',obat:[{n:'Antasida Doen',tp:'Sirup',jml:1,ar:'3x sehari 2 sendok sebelum makan',sat:'Botol',hrg:18000,sub:null}],cat:'Hindari makanan pedas',tot:18000,mb:null,bayarTime:null},
];

let bayarDB=[
  {rxId:'RX-20260402-034',pas:'Siti Rahayu',met:'BPJS',tot:48000,nom:0,kem:0,wkt:'09:15',st:'lunas',poli:'Poli Penyakit Dalam',bpjsNo:'0001-1234-5678'},
];

let selRx=null;let curMetode='mandiri';let lastPaid=null;

// ═══════════════ NAV ═══════════════
function showPage(id,el){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  if(el)el.classList.add('active');
  const pg=document.getElementById('pg-'+id);
  if(pg)pg.classList.add('active');
  const rMap={pembayaran:renderPayPage,daftar:renderDaftarPage,pasienlist:renderPasienList,invoicelist:renderInvoiceList,laporan:renderLaporan,dashboard:renderDashboard};
  if(rMap[id])rMap[id]();
  updateBadges();
}

function switchMainTab(tab,el){
  document.querySelectorAll('.tb-tab').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
  const tMap={kasir:'pembayaran',pasien:'daftar',invoice:'invoicelist',laporan:'laporan'};
  const pageId=tMap[tab]||tab;
  const navId={'pembayaran':'ni-pembayaran',daftar:'ni-daftar',invoicelist:'ni-invoice',laporan:'ni-laporan'}[pageId];
  showPage(pageId,navId?document.getElementById(navId):null);
}

function updateBadges(){
  const c=resepDB.filter(r=>r.status==='siap').length;
  const el=document.getElementById('payBadge');if(el)el.textContent=c;
}

// ═══════════════ DASHBOARD ═══════════════
function renderDashboard(){
  const siap=resepDB.filter(r=>r.status==='siap').length;
  const selesai=resepDB.filter(r=>r.status==='selesai').length;
  const pendapatan=bayarDB.reduce((s,b)=>s+b.tot,0);
  const totalPas=pasienDB.length;

  document.getElementById('dashStats').innerHTML=`
    <div class="stat gold"><div class="stat-label">Pendapatan Hari Ini</div><div class="stat-val">${fmtRp(pendapatan)}</div><div class="stat-sub up">Shift pagi</div></div>
    <div class="stat sage"><div class="stat-label">Transaksi Selesai</div><div class="stat-val">${selesai}</div><div class="stat-sub">Resep terbayar</div></div>
    <div class="stat rose"><div class="stat-label">Siap Bayar</div><div class="stat-val">${siap}</div><div class="stat-sub warn">Menunggu proses</div></div>
    <div class="stat blue"><div class="stat-label">Pasien Terdaftar</div><div class="stat-val">${totalPas}</div><div class="stat-sub">Hari ini</div></div>`;

  const aw=document.getElementById('dashAlertWrap');
  if(siap>0){aw.innerHTML=`<div class="alert-banner"><svg class="alert-icon" viewBox="0 0 18 18" fill="none"><path d="M9 2L16.5 15H1.5L9 2Z" stroke="#8F3D3A" stroke-width="1.4" fill="none" stroke-linejoin="round"/><path d="M9 7v4" stroke="#8F3D3A" stroke-width="1.3" stroke-linecap="round"/><circle cx="9" cy="12.5" r=".6" fill="#8F3D3A"/></svg><span><strong>${siap} resep</strong> menunggu pembayaran — ${resepDB.filter(r=>r.status==='siap').map(r=>r.pas).join(', ')}</span><button class="btn btn-rose btn-sm" style="margin-left:auto;flex-shrink:0" onclick="showPage('pembayaran',document.getElementById('ni-pembayaran'))">Proses Sekarang</button></div>`;}
  else aw.innerHTML='';

  const siapRx=resepDB.filter(r=>r.status==='siap');
  document.getElementById('dashSiapCount').textContent=siap+' resep';
  document.getElementById('dashSiapList').innerHTML=siapRx.length?siapRx.map(r=>`
    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--paper3)">
      <div><div style="font-weight:600;font-size:13px">${r.pas}</div><div style="font-size:11px;color:var(--ink3)">${r.id} · ${r.poli}</div></div>
      <div style="display:flex;align-items:center;gap:10px"><span style="font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--gold2)">${fmtRp(r.tot)}</span><button class="btn btn-gold btn-xs" onclick="quickPay('${r.id}')">Bayar</button></div>
    </div>`).join(''):'<div class="empty"><div class="empty-icon">✓</div><div class="empty-text">Tidak ada antrian</div></div>';

  document.getElementById('dashActivity').innerHTML=[...bayarDB].reverse().slice(0,4).map(b=>`
    <div class="activity-item">
      <div class="act-dot" style="background:var(--sage)"></div>
      <div style="flex:1">
        <div class="act-label">${b.pas}</div>
        <div class="act-sub">${b.rxId} · <span class="badge ${b.met==='BPJS'?'b-bpjs':'b-mandiri'}" style="font-size:10px;padding:1px 6px">${b.met}</span></div>
      </div>
      <div style="text-align:right"><div style="font-weight:600;font-size:12px;color:var(--gold2)">${fmtRp(b.tot)}</div><div class="act-time">${b.wkt}</div></div>
    </div>`).join('')||'<div class="empty"><div class="empty-text">Belum ada transaksi</div></div>';

  document.getElementById('dashPasienToday').innerHTML=pasienDB.slice(0,4).map(p=>`
    <div class="activity-item">
      <div class="act-dot" style="background:var(--blue)"></div>
      <div style="flex:1"><div class="act-label">${p.nama}</div><div class="act-sub">${p.rm} · ${p.poli}</div></div>
      <div style="text-align:right"><span class="badge ${p.bpjs?'b-bpjs':'b-mandiri'}" style="font-size:10px">${p.bpjs?'BPJS':'Mandiri'}</span><div class="act-time">${p.regTime}</div></div>
    </div>`).join('');

  const mandiriTotal=bayarDB.filter(b=>b.met==='Mandiri').reduce((s,b)=>s+b.tot,0);
  const bpjsTotal=bayarDB.filter(b=>b.met==='BPJS').reduce((s,b)=>s+b.tot,0);
  const grandTotal=mandiriTotal+bpjsTotal||1;
  document.getElementById('dashMetodeSummary').innerHTML=`
    <div style="margin-bottom:12px">
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px"><span style="font-weight:600">Mandiri</span><span style="font-weight:600;color:var(--gold2)">${fmtRp(mandiriTotal)}</span></div>
      <div style="background:var(--paper2);border-radius:20px;height:8px;overflow:hidden"><div style="height:100%;border-radius:20px;background:var(--gold);width:${Math.round(mandiriTotal/grandTotal*100)}%;transition:width .5s"></div></div>
      <div style="font-size:11px;color:var(--ink3);margin-top:3px">${bayarDB.filter(b=>b.met==='Mandiri').length} transaksi</div>
    </div>
    <div>
      <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px"><span style="font-weight:600">BPJS</span><span style="font-weight:600;color:var(--sage2)">${fmtRp(bpjsTotal)}</span></div>
      <div style="background:var(--paper2);border-radius:20px;height:8px;overflow:hidden"><div style="height:100%;border-radius:20px;background:var(--sage);width:${Math.round(bpjsTotal/grandTotal*100)}%;transition:width .5s"></div></div>
      <div style="font-size:11px;color:var(--ink3);margin-top:3px">${bayarDB.filter(b=>b.met==='BPJS').length} transaksi</div>
    </div>`;
}

function quickPay(id){
  selRx=resepDB.find(r=>r.id===id);
  showPage('pembayaran',document.getElementById('ni-pembayaran'));
  renderPayForm();
}

// ═══════════════ PEMBAYARAN ═══════════════
function renderPayPage(){
  renderPayList();
  if(selRx)renderPayForm();
}

function renderPayList(){
  const q=(document.getElementById('paySearch')?.value||'').toLowerCase();
  const fs=document.getElementById('payFilter')?.value||'siap';
  const list=resepDB.filter(r=>{
    if(fs&&r.status!==fs&&fs!=='')return false;
    if(!fs||fs==='siap')if(r.status!=='siap')return false;
    if(q&&!r.pas.toLowerCase().includes(q)&&!r.id.toLowerCase().includes(q))return false;
    return true;
  }).filter(r=>!q||(r.pas.toLowerCase().includes(q)||r.id.toLowerCase().includes(q)));
  
  // when filter is empty show all
  const listFinal=resepDB.filter(r=>{
    const fv=document.getElementById('payFilter')?.value||'siap';
    if(fv&&r.status!==fv)return false;
    if(q&&!r.pas.toLowerCase().includes(q)&&!r.id.toLowerCase().includes(q))return false;
    return true;
  });

  const el=document.getElementById('payRxList');
  const cnt=document.getElementById('payQueueCount');
  const siapCount=resepDB.filter(r=>r.status==='siap').length;
  if(cnt)cnt.textContent=siapCount+' antrian';
  if(!el)return;

  el.innerHTML=listFinal.length?`<div class="rx-list">${listFinal.map(r=>`
    <div class="rx-item${selRx&&selRx.id===r.id?' selected':''}" onclick="selectRx('${r.id}')">
      <div class="rx-item-top">
        <div><div class="rx-no">${r.id}</div><div class="rx-pasien">${r.pas}</div><div class="rx-meta">${r.poli} · ${r.obat.length} obat</div></div>
        ${statusBadge(r.status)}
      </div>
      <div class="rx-bot"><div class="rx-total">${fmtRp(r.tot)}</div><div class="rx-obat-count">${r.obat.map(o=>o.n.split(' ')[0]).join(', ')}</div></div>
    </div>`).join('')}</div>`:'<div class="empty"><div class="empty-icon">📋</div><div class="empty-text">Tidak ada resep</div><div class="empty-sub">Ubah filter untuk melihat semua resep</div></div>';
}

function selectRx(id){
  selRx=resepDB.find(r=>r.id===id);
  renderPayList();
  renderPayForm();
}

function renderPayForm(){
  if(!selRx){return;}
  const r=selRx;
  const p=pasienDB.find(x=>x.id===r.pId);
  const isBPJS=p&&p.bpjs;

  document.getElementById('payFormWrap').innerHTML=`
    <div class="pay-panel">
      <div class="pay-panel-header">
        <div class="pay-panel-title">${r.pas}</div>
        <div class="pay-panel-sub">${r.id} · ${r.poli} · ${r.diag}</div>
      </div>

      <div class="pay-section">
        <div class="pay-section-label">Detail Resep</div>
        ${r.obat.map(o=>`<div class="obat-item">
          <div><div class="obat-nama">${o.n}${o.sub?`<span class="obat-sub-tag">sub: ${o.sub}</span>`:''}</div><div class="obat-aturan">${o.ar||'—'} · ${o.jml} ${o.sat}</div></div>
          <div class="obat-right"><div class="obat-jml">${fmtRp(o.hrg*o.jml)}</div><div class="obat-hrg">${fmtRp(o.hrg)}/${o.sat}</div></div>
        </div>`).join('')}
        ${r.cat?`<div style="margin-top:10px;padding:9px 12px;background:var(--gold-light);border-radius:8px;font-size:12px;color:var(--gold2)">Catatan dokter: ${r.cat}</div>`:''}
      </div>

      <div class="pay-section">
        <div class="pay-section-label">Metode Pembayaran</div>
        <div class="method-cards">
          <div class="method-card mandiri${!isBPJS?' active':''}" id="mcMandiri" onclick="selectMetode('mandiri')">
            <div class="mc-icon mandiri"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><rect x="2" y="4.5" width="16" height="11" rx="2" stroke="#C9972A" stroke-width="1.4"/><path d="M2 8.5h16" stroke="#C9972A" stroke-width="1.3"/><rect x="3.5" y="11" width="5" height="2" rx=".7" fill="#C9972A" opacity=".5"/></svg></div>
            <div class="mc-name">Mandiri</div>
            <div class="mc-desc">Tunai atau transfer</div>
          </div>
          <div class="method-card bpjs${isBPJS?' active':''}" id="mcBPJS" onclick="selectMetode('bpjs')">
            <div class="mc-icon bpjs"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 2.5L17 6v5c0 3.3-3 6-7 7.5C3 17 0 14.3 0 11V6L7 2.5a6 6 0 0 1 3 0Z" stroke="#4A7C59" stroke-width="1.3" fill="none"/><path d="M7 10l2 2 4-4" stroke="#4A7C59" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <div class="mc-name">BPJS</div>
            <div class="mc-desc">Klaim asuransi${!isBPJS?'<br><span style="color:var(--rose);font-size:10px">Pasien tidak punya BPJS</span>':''}${isBPJS?`<br><span style="font-size:10px;color:var(--sage2)">${p.bpjs}</span>`:''}
            </div>
          </div>
        </div>
      </div>

      <div class="pay-section" id="secMandiri" style="${isBPJS?'display:none':''}">
        <div class="pay-section-label">Detail Pembayaran</div>
        <div class="field"><label>Nominal Dibayar (Rp)</label><input type="number" id="nominalBayar" placeholder="0" oninput="hitungKembalian()" style="font-size:16px;font-weight:600"></div>
        <div id="sumRows">
          <div class="sum-row"><span class="sum-label">Total tagihan</span><span class="sum-val">${fmtRpFull(r.tot)}</span></div>
          <div class="sum-row kembalian"><span class="sum-label">Kembalian</span><span class="sum-val" id="kembalianVal">Rp 0</span></div>
          <div class="sum-row total"><span class="sum-label">Total</span><span class="sum-val">${fmtRpFull(r.tot)}</span></div>
        </div>
      </div>

      <div class="pay-section" id="secBPJS" style="${isBPJS?'':'display:none'}">
        <div class="pay-section-label">Detail Klaim BPJS</div>
        <div class="field"><label>No. Kartu BPJS</label><input type="text" id="bpjsNo" value="${p&&p.bpjs?p.bpjs:''}" placeholder="0001-XXXX-XXXX"></div>
        <div class="field"><label>Keterangan Klaim (opsional)</label><input type="text" id="bpjsKet" placeholder="cth. Rawat jalan, kontrol rutin..."></div>
        <div class="sum-row total" style="padding-top:12px">
          <span class="sum-label">Total Klaim</span>
          <span class="sum-val">${fmtRpFull(r.tot)}</span>
        </div>
      </div>

      <div class="pay-section">
        <button class="btn btn-gold" style="width:100%;justify-content:center;padding:13px;font-size:14px" onclick="konfirmasiBayar()">
          Konfirmasi Pembayaran
        </button>
        ${r.status!=='siap'?`<div style="margin-top:8px;padding:8px 12px;background:var(--paper2);border-radius:8px;font-size:12px;color:var(--ink3);text-align:center">Resep ini berstatus <strong>${r.status}</strong> — tidak bisa diproses</div>`:''}
      </div>
    </div>`;

  curMetode=isBPJS?'bpjs':'mandiri';
}

function selectMetode(m){
  curMetode=m;
  const mcM=document.getElementById('mcMandiri');
  const mcB=document.getElementById('mcBPJS');
  const secM=document.getElementById('secMandiri');
  const secB=document.getElementById('secBPJS');
  if(mcM){mcM.className=`method-card mandiri${m==='mandiri'?' active':''}`;}
  if(mcB){mcB.className=`method-card bpjs${m==='bpjs'?' active':''}`;}
  if(secM)secM.style.display=m==='mandiri'?'block':'none';
  if(secB)secB.style.display=m==='bpjs'?'block':'none';
}

function hitungKembalian(){
  if(!selRx)return;
  const bayar=parseInt(document.getElementById('nominalBayar')?.value)||0;
  const kem=Math.max(0,bayar-selRx.tot);
  const el=document.getElementById('kembalianVal');
  if(el){el.textContent=fmtRpFull(kem);el.style.color=bayar>=selRx.tot?'var(--sage)':'var(--rose)';}
}

function konfirmasiBayar(){
  if(!selRx){toast('Pilih resep terlebih dahulu.','d');return;}
  if(selRx.status!=='siap'){toast('Resep ini belum siap dibayar.','d');return;}

  if(curMetode==='mandiri'){
    const nom=parseInt(document.getElementById('nominalBayar')?.value)||0;
    if(nom<selRx.tot){toast('Nominal kurang dari total tagihan!','d');return;}
    const kem=nom-selRx.tot;
    lastPaid={...selRx,metode:'Mandiri',nominal:nom,kembalian:kem,wkt:nowTime()};
    document.getElementById('successSub').textContent=`${selRx.pas} · ${selRx.id}`;
    document.getElementById('successAmount').textContent=fmtRpFull(selRx.tot);
    document.getElementById('successChange').textContent=`Kembalian: ${fmtRpFull(kem)}`;
    bayarDB.unshift({rxId:selRx.id,pas:selRx.pas,met:'Mandiri',tot:selRx.tot,nom,kem,wkt:nowTime(),st:'lunas',poli:selRx.poli,bpjsNo:null});
  } else {
    const bpjsNo=document.getElementById('bpjsNo')?.value;
    if(!bpjsNo){toast('No. BPJS wajib diisi!','d');return;}
    lastPaid={...selRx,metode:'BPJS',bpjsNo,kembalian:0,wkt:nowTime()};
    document.getElementById('successSub').textContent=`${selRx.pas} · ${selRx.id}`;
    document.getElementById('successAmount').textContent=fmtRpFull(selRx.tot);
    document.getElementById('successChange').textContent=`BPJS: ${bpjsNo}`;
    bayarDB.unshift({rxId:selRx.id,pas:selRx.pas,met:'BPJS',tot:selRx.tot,nom:0,kem:0,wkt:nowTime(),st:'lunas',poli:selRx.poli,bpjsNo});
  }

  selRx.status='selesai';selRx.mb=curMetode;selRx.bayarTime=nowTime();
  selRx=null;
  updateBadges();
  renderPayList();
  document.getElementById('payFormWrap').innerHTML=`<div style="background:var(--white);border:1.5px solid var(--sage);border-radius:14px;padding:24px;text-align:center"><div style="font-size:28px;margin-bottom:10px">✓</div><div style="font-size:14px;font-weight:600;color:var(--sage)">Pembayaran berhasil dikonfirmasi!</div><div style="font-size:12px;color:var(--ink3);margin-top:6px">Pilih resep berikutnya untuk memproses</div></div>`;
  document.getElementById('successOverlay').classList.add('show');
}

function closeSuccess(){document.getElementById('successOverlay').classList.remove('show');}

// ═══════════════ INVOICE ═══════════════
function showInvoiceModal(){
  if(!lastPaid)return;
  const r=lastPaid;
  const now=new Date();
  document.getElementById('invNo').textContent=`Invoice · ${r.id} · ${now.getDate()}/${now.getMonth()+1}/${now.getFullYear()} ${r.wkt||nowTime()}`;
  document.getElementById('invBody').innerHTML=`
    <div class="inv-section">
      <div class="inv-section-title">Info Pasien</div>
      <div class="inv-row"><span class="inv-label">Nama Pasien</span><span class="inv-val">${r.pas}</span></div>
      <div class="inv-row"><span class="inv-label">No. RM</span><span class="inv-val">${r.rm}</span></div>
      <div class="inv-row"><span class="inv-label">Poli</span><span class="inv-val">${r.poli}</span></div>
      <div class="inv-row"><span class="inv-label">Metode</span><span class="inv-val">${r.metode||r.mb}</span></div>
      ${r.bpjsNo?`<div class="inv-row"><span class="inv-label">No. BPJS</span><span class="inv-val">${r.bpjsNo}</span></div>`:''}
    </div>
    <div class="inv-section">
      <div class="inv-section-title">Detail Obat</div>
      ${r.obat.map(o=>`<div class="inv-row"><span class="inv-label">${o.n} × ${o.jml} ${o.sat}</span><span class="inv-val">${fmtRpFull(o.hrg*o.jml)}</span></div>`).join('')}
    </div>
    <div class="inv-section">
      <div class="inv-section-title">Ringkasan Pembayaran</div>
      <div class="inv-row"><span class="inv-label">Subtotal</span><span class="inv-val">${fmtRpFull(r.tot)}</span></div>
      ${(r.nominal||0)>0?`<div class="inv-row"><span class="inv-label">Dibayar</span><span class="inv-val">${fmtRpFull(r.nominal)}</span></div><div class="inv-row"><span class="inv-label">Kembalian</span><span class="inv-val" style="color:var(--sage)">${fmtRpFull(r.kembalian)}</span></div>`:''}
      <div class="inv-total-row"><span>TOTAL</span><span>${fmtRpFull(r.tot)}</span></div>
    </div>`;
  document.getElementById('invoiceModal').classList.add('show');
}
function closeInvoiceModal(){document.getElementById('invoiceModal').classList.remove('show');}

// ═══════════════ PENDAFTARAN ═══════════════
let regStep=1;
function renderDaftarPage(){
  updateRegFlow();
  renderRecentReg();
}
function updateRegFlow(){
  const el=document.getElementById('regFlow');
  if(!el)return;
  const steps=[{l:'Input Data Pasien',d:'Nama, RM, keluhan'},{l:'Teruskan ke Dokter',d:'Pilih poli & dokter'},{l:'Pasien Diperiksa',d:'Dokter buat resep'}];
  el.innerHTML=steps.map((s,i)=>`<div class="flow-step ${i===0?'active':''}"><div class="flow-num">${i+1}</div><div class="flow-label">${s.l}</div><div style="font-size:11px;color:var(--ink3);margin-top:2px">${s.d}</div></div>`).join('');
}
function renderRecentReg(){
  const el=document.getElementById('recentReg');
  if(!el)return;
  el.innerHTML=pasienDB.slice(0,3).map(p=>`
    <div class="activity-item">
      <div class="act-dot" style="background:var(--blue)"></div>
      <div style="flex:1"><div class="act-label">${p.nama}</div><div class="act-sub">${p.rm} · ${p.poli}</div></div>
      <div style="text-align:right"><span class="badge ${p.bpjs?'b-bpjs':'b-mandiri'}" style="font-size:10px">${p.bpjs?'BPJS':'Mandiri'}</span><div class="act-time">${p.regTime}</div></div>
    </div>`).join('');
}
function savePasien(){
  const nama=document.getElementById('regNama')?.value.trim();
  const rm=document.getElementById('regRM')?.value.trim();
  const keluhan=document.getElementById('regKeluhan')?.value.trim();
  const poli=document.getElementById('regPoli')?.value;
  if(!nama||!rm){toast('Nama dan No. RM wajib diisi!','d');return;}
  if(!keluhan){toast('Keluhan wajib diisi!','d');return;}
  const newP={
    id:pasienDB.length,nama,rm,
    tgl:document.getElementById('regTgl')?.value||'',
    jk:document.getElementById('regJK')?.value||'P',
    bpjs:document.getElementById('regBPJS')?.value.trim()||null,
    telp:document.getElementById('regTelp')?.value.trim()||'',
    keluhan,poli,regTime:nowTime(),tot:0
  };
  pasienDB.push(newP);
  clearRegForm();
  renderRecentReg();
  updateBadges();
  toast(`${nama} berhasil didaftarkan dan diteruskan ke ${poli}!`,'s');
}
function clearRegForm(){
  ['regNama','regRM','regTgl','regBPJS','regTelp','regKeluhan'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
}

// ═══════════════ PASIEN LIST ═══════════════
function renderPasienList(){
  const q=(document.getElementById('pasienSearch')?.value||'').toLowerCase();
  const list=pasienDB.filter(p=>!q||p.nama.toLowerCase().includes(q)||p.rm.toLowerCase().includes(q)||(p.bpjs&&p.bpjs.includes(q)));
  const w=document.getElementById('pasienTableWrap');
  if(!w)return;
  w.innerHTML=`<table><thead><tr><th>No. RM</th><th>Nama Pasien</th><th>Tgl Lahir</th><th>BPJS / Mandiri</th><th>Keluhan</th><th>Poli Tujuan</th><th>Terdaftar</th><th>Total Resep</th></tr></thead><tbody>${
    list.map(p=>`<tr>
      <td style="font-family:'Playfair Display',serif;font-weight:700;color:var(--ink3)">${p.rm}</td>
      <td style="font-weight:600">${p.nama}</td>
      <td style="color:var(--ink2)">${fmtDate(p.tgl)||'—'}</td>
      <td>${p.bpjs?`<span class="badge b-bpjs">BPJS · ${p.bpjs}</span>`:'<span class="badge b-mandiri">Mandiri</span>'}</td>
      <td style="font-size:12px;color:var(--ink2);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${p.keluhan||''}">${p.keluhan||'—'}</td>
      <td style="font-size:12px;color:var(--ink2)">${p.poli||'—'}</td>
      <td style="color:var(--ink3)">${p.regTime||'—'}</td>
      <td style="font-weight:600">${p.tot}</td>
    </tr>`).join('')
  }</tbody></table>`;
}

// ═══════════════ INVOICE LIST ═══════════════
function renderInvoiceList(){
  const q=(document.getElementById('invSearch')?.value||'').toLowerCase();
  const mf=document.getElementById('invMetFilter')?.value||'';
  const list=bayarDB.filter(b=>{
    if(q&&!b.pas.toLowerCase().includes(q)&&!b.rxId.toLowerCase().includes(q))return false;
    if(mf==='mandiri'&&b.met!=='Mandiri')return false;
    if(mf==='bpjs'&&b.met!=='BPJS')return false;
    return true;
  });
  const w=document.getElementById('invoiceTableWrap');
  if(!w)return;
  const total=list.reduce((s,b)=>s+b.tot,0);
  const totEl=document.getElementById('invTotal');if(totEl)totEl.textContent=fmtRpFull(total);
  w.innerHTML=list.length?`<table><thead><tr><th>No. Resep</th><th>Pasien</th><th>Poli</th><th>Metode</th><th>Total</th><th>Waktu</th><th>Status</th><th>Aksi</th></tr></thead><tbody>${
    list.map(b=>`<tr>
      <td style="font-family:'Playfair Display',serif;font-weight:700">${b.rxId}</td>
      <td style="font-weight:600">${b.pas}</td>
      <td style="font-size:12px;color:var(--ink2)">${b.poli||'—'}</td>
      <td><span class="badge ${b.met==='BPJS'?'b-bpjs':'b-mandiri'}">${b.met}</span></td>
      <td style="font-family:'Playfair Display',serif;font-weight:700;color:var(--gold2)">${fmtRpFull(b.tot)}</td>
      <td style="color:var(--ink3)">${b.wkt}</td>
      <td><span class="badge b-lunas">Lunas</span></td>
      <td><button class="btn btn-xs" onclick="showSavedInvoice('${b.rxId}')">Invoice</button></td>
    </tr>`).join('')
  }</tbody></table>`:'<div class="empty"><div class="empty-icon">📄</div><div class="empty-text">Belum ada invoice</div></div>';
}

function showSavedInvoice(rxId){
  const b=bayarDB.find(x=>x.rxId===rxId);
  const r=resepDB.find(x=>x.id===rxId);
  if(!b||!r)return;
  lastPaid={...r,metode:b.met,bpjsNo:b.bpjsNo,nominal:b.nom,kembalian:b.kem,wkt:b.wkt};
  showInvoiceModal();
}

function exportInvoice(){toast('Mengunduh CSV...','i');}

// ═══════════════ LAPORAN ═══════════════
function renderLaporan(){
  const mandiriTot=bayarDB.filter(b=>b.met==='Mandiri').reduce((s,b)=>s+b.tot,0);
  const bpjsTot=bayarDB.filter(b=>b.met==='BPJS').reduce((s,b)=>s+b.tot,0);
  const grand=mandiriTot+bpjsTot;

  const ls=document.getElementById('laporanStats');
  if(ls)ls.innerHTML=`
    <div class="stat gold"><div class="stat-label">Total Pendapatan</div><div class="stat-val">${fmtRp(grand)}</div><div class="stat-sub up">Hari ini</div></div>
    <div class="stat sage"><div class="stat-label">Transaksi</div><div class="stat-val">${bayarDB.length}</div><div class="stat-sub">Selesai</div></div>
    <div class="stat blue"><div class="stat-label">Pasien Terdaftar</div><div class="stat-val">${pasienDB.length}</div><div class="stat-sub">Hari ini</div></div>
    <div class="stat rose"><div class="stat-label">Resep Pending</div><div class="stat-val">${resepDB.filter(r=>r.status==='siap'||r.status==='diproses').length}</div><div class="stat-sub warn">Belum selesai</div></div>`;

  const lm=document.getElementById('laporanMetode');
  const mData=[{l:'Mandiri',v:mandiriTot,c:mandiriTot,bg:'var(--gold)',cnt:bayarDB.filter(b=>b.met==='Mandiri').length},{l:'BPJS',v:bpjsTot,c:bpjsTot,bg:'var(--sage)',cnt:bayarDB.filter(b=>b.met==='BPJS').length}];
  if(lm)lm.innerHTML=mData.map(m=>`
    <div style="margin-bottom:14px">
      <div style="display:flex;justify-content:space-between;margin-bottom:5px;font-size:13px"><span style="font-weight:600">${m.l}</span><span style="font-weight:700;color:var(--ink)">${fmtRpFull(m.v)}</span></div>
      <div style="background:var(--paper2);border-radius:20px;height:10px;overflow:hidden"><div style="height:100%;border-radius:20px;background:${m.bg};width:${grand?Math.round(m.c/grand*100):0}%;transition:width .5s"></div></div>
      <div style="font-size:11px;color:var(--ink3);margin-top:3px">${m.cnt} transaksi · ${grand?Math.round(m.c/grand*100):0}%</div>
    </div>`).join('');

  const poliCount={};resepDB.filter(r=>r.status==='selesai').forEach(r=>{poliCount[r.poli]=(poliCount[r.poli]||0)+1;});
  const poliMax=Math.max(...Object.values(poliCount),1);
  const lp=document.getElementById('laporanPoli');
  if(lp)lp.innerHTML=Object.entries(poliCount).sort((a,b)=>b[1]-a[1]).map(([poli,cnt])=>`
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;font-size:13px">
      <div style="width:90px;font-size:11px;color:var(--ink2);text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${poli.replace('Poli ','')}</div>
      <div style="flex:1;background:var(--paper2);border-radius:20px;height:10px;overflow:hidden"><div style="height:100%;border-radius:20px;background:var(--blue);width:${Math.round(cnt/poliMax*100)}%"></div></div>
      <div style="width:20px;text-align:right;font-size:11px;font-weight:600">${cnt}</div>
    </div>`).join('')||'<div class="empty"><div class="empty-text">Belum ada data</div></div>';

  const lt=document.getElementById('laporanTable');
  if(lt)lt.innerHTML=`<table><thead><tr><th>No. Resep</th><th>Pasien</th><th>Metode</th><th>Total</th><th>Waktu</th></tr></thead><tbody>${
    bayarDB.map(b=>`<tr><td style="font-family:'Playfair Display',serif;font-weight:700">${b.rxId}</td><td style="font-weight:600">${b.pas}</td><td><span class="badge ${b.met==='BPJS'?'b-bpjs':'b-mandiri'}">${b.met}</span></td><td style="font-weight:600;color:var(--gold2)">${fmtRpFull(b.tot)}</td><td>${b.wkt}</td></tr>`).join('')
  }</tbody></table>`;
}

// ═══════════════ HELPERS ═══════════════
function statusBadge(s){
  const m={baru:'b-baru Baru',diproses:'b-diproses Diproses Apoteker',siap:'b-siap Siap Bayar',selesai:'b-selesai Selesai',batal:'b-batal Dibatalkan'};
  const v=m[s]||'b-baru '+s;const[c,...r]=v.split(' ');return`<span class="badge ${c}">${r.join(' ')}</span>`;
}

function toast(msg,type='s'){
  const map={s:'t-s',d:'t-d',i:'t-i'};
  const icons={s:'✓',d:'✕',i:'ℹ'};
  const el=document.createElement('div');
  el.className=`toast ${map[type]||'t-s'}`;
  el.innerHTML=`<span>${icons[type]||'✓'}</span>${msg}`;
  document.body.appendChild(el);
  setTimeout(()=>{el.style.opacity='0';el.style.transition='opacity .3s';setTimeout(()=>el.remove(),300);},2800);
}

// INIT
renderDashboard();
updateBadges();
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmbee</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    @vite(['resources/css/style1.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app">
    
    @include('components.sidebarD')

    <div class="main">

        <div class="content">
            @yield('content')
        </div>
    </div>

</div>

<script>
// ── DATE ─────────────────────────────────────────────────
const today=new Date();today.setHours(0,0,0,0);
const DAYS=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const MONTHS=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
document.getElementById('dateChip').textContent=`${DAYS[today.getDay()]}, ${today.getDate()} ${MONTHS[today.getMonth()]} ${today.getFullYear()}`;
document.getElementById('rxTanggal').value=today.toISOString().split('T')[0];
function fmtDate(d){if(!d)return'—';const[y,m,dy]=d.split('-');return`${dy}/${m}/${y}`}
function fmtRp(n){return'Rp '+Math.round(n).toLocaleString('id-ID')}

// ── NAV ─────────────────────────────────────────────────
function showPage(name,el){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('page-'+name).classList.add('active');
  if(el)el.classList.add('active');
  if(name==='riwayat')renderRiwayat();
  if(name==='pasien')renderPasienTable();
  if(name==='tulis'){rxGoStep(1);}
}

// ── MODAL ────────────────────────────────────────────────
function closeModal(id){document.getElementById(id).classList.remove('open')}
function closeMBg(e,id){if(e.target===document.getElementById(id))closeModal(id)}

// ── PASIEN DATA ──────────────────────────────────────────
let pasienDB=[
  {id:0,nama:'Siti Rahayu',rm:'RM-2026-0091',tgl:'1985-04-12',jk:'P',bpjs:'0001-1234-5678',telp:'081234567890',totalResep:8},
  {id:1,nama:'Ahmad Fauzi',rm:'RM-2026-0088',tgl:'1990-07-22',jk:'L',bpjs:null,telp:'085678901234',totalResep:5},
  {id:2,nama:'Maya Putri',rm:'RM-2026-0087',tgl:'1998-02-14',jk:'P',bpjs:'0001-9876-5432',telp:'087890123456',totalResep:3},
  {id:3,nama:'Rudi Hartono',rm:'RM-2026-0085',tgl:'1978-11-30',jk:'L',bpjs:null,telp:'082345678901',totalResep:12},
  {id:4,nama:'Dewi Lestari',rm:'RM-2026-0082',tgl:'1995-08-08',jk:'P',bpjs:'0002-3456-7890',telp:'089012345678',totalResep:2},
];

const obatList=[
  {nama:'Paracetamol 500mg',tipe:'Tablet',satuan:'Tablet',harga:500},
  {nama:'Amoxicillin 250mg',tipe:'Kapsul',satuan:'Kapsul',harga:1500},
  {nama:'OBH Combi Sirup',tipe:'Sirup',satuan:'Botol',harga:35000},
  {nama:'Metformin 500mg',tipe:'Tablet',satuan:'Tablet',harga:800},
  {nama:'Cetirizine 10mg',tipe:'Tablet',satuan:'Tablet',harga:700},
  {nama:'Salep Betametason',tipe:'Salep',satuan:'Tube',harga:22000},
  {nama:'Antasida Doen',tipe:'Sirup',satuan:'Botol',harga:18000},
];

let resepDB=[
  {id:'RX-20260402-034',pasienId:0,pasien:'Siti Rahayu',rm:'RM-2026-0091',poli:'Penyakit Dalam',diagnosis:'Diabetes terkontrol, perlu terapi lanjutan',status:'selesai',tanggal:'2026-04-02',obat:[{nama:'Metformin 500mg',tipe:'Tablet',jml:60,aturan:'2x sehari 1 tablet sesudah makan',satuan:'Tablet'},{nama:'Cetirizine 10mg',tipe:'Tablet',jml:10,aturan:'1x sehari 1 tablet malam',satuan:'Tablet'}],catatan:'',total:57000},
  {id:'RX-20260402-035',pasienId:1,pasien:'Ahmad Fauzi',rm:'RM-2026-0088',poli:'Umum',diagnosis:'ISPA, batuk berdahak 5 hari',status:'siap',tanggal:'2026-04-02',obat:[{nama:'Amoxicillin 250mg',tipe:'Kapsul',jml:10,aturan:'3x sehari 1 kapsul',satuan:'Kapsul'},{nama:'Paracetamol 500mg',tipe:'Tablet',jml:10,aturan:'3x sehari 1 tablet jika demam',satuan:'Tablet'}],catatan:'Harap habiskan antibiotik',total:20000},
  {id:'RX-20260402-036',pasienId:2,pasien:'Maya Putri',rm:'RM-2026-0087',poli:'Anak',diagnosis:'Batuk pilek, alergi debu',status:'diproses',tanggal:'2026-04-02',obat:[{nama:'OBH Combi Sirup',tipe:'Sirup',jml:1,aturan:'3x sehari 1 sendok makan',satuan:'Botol'},{nama:'Cetirizine 10mg',tipe:'Tablet',jml:7,aturan:'1x sehari 1 tablet malam',satuan:'Tablet'}],catatan:'',total:40400},
  {id:'RX-20260402-037',pasienId:3,pasien:'Rudi Hartono',rm:'RM-2026-0085',poli:'Umum',diagnosis:'Gastritis, mual muntah',status:'baru',tanggal:'2026-04-02',obat:[{nama:'Antasida Doen',tipe:'Sirup',jml:1,aturan:'3x sehari 2 sendok makan sebelum makan',satuan:'Botol'}],catatan:'Hindari makanan pedas dan asam',total:18000},
  {id:'RX-20260402-031',pasienId:0,pasien:'Siti Rahayu',rm:'RM-2026-0091',poli:'Penyakit Dalam',diagnosis:'Kontrol rutin DM',status:'selesai',tanggal:'2026-04-01',obat:[{nama:'Metformin 500mg',tipe:'Tablet',jml:60,aturan:'2x sehari 1 tablet',satuan:'Tablet'}],catatan:'',total:48000},
];

let buatObat=[];
let rxStep=1;
let selRiwayat=null;

// ── STATUS BADGE ─────────────────────────────────────────
function statusBadge(s){
  const m={baru:'b-baru Baru',diproses:'b-diproses Diproses Apoteker',siap:'b-siap Siap Diambil',selesai:'b-selesai Selesai',batal:'b-batal Dibatalkan'};
  const v=m[s]||'b-baru '+s;const[c,...r]=v.split(' ');
  return`<span class="badge ${c}">${r.join(' ')}</span>`;
}

function tipeBadge(t){
  const m={Tablet:'b-tablet',Sirup:'b-sirup',Kapsul:'b-kapsul',Injeksi:'b-injeksi',Salep:'b-salep'};
  return`<span class="badge ${m[t]||'b-tablet'}">${t}</span>`;
}

// ── DASHBOARD ────────────────────────────────────────────
new Chart(document.getElementById('dashChart'),{
  type:'line',
  data:{
    labels:['Sen','Sel','Rab','Kam','Jum','Sab','Hari ini'],
    datasets:[
      {label:'Ditulis',data:[8,11,9,14,10,7,12],borderColor:'#8B7DB8',backgroundColor:'rgba(139,125,184,.08)',tension:.4,pointRadius:3,borderWidth:2,fill:true},
      {label:'Selesai',data:[7,10,8,12,9,6,8],borderColor:'#52B788',backgroundColor:'rgba(82,183,136,.08)',tension:.4,pointRadius:3,borderWidth:2,fill:true}
    ]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{font:{size:11},color:'#A8998A'}},y:{grid:{color:'rgba(0,0,0,0.04)'},ticks:{font:{size:11},color:'#A8998A'}}}}
});

document.getElementById('dashRecentList').innerHTML=resepDB.slice(0,4).map(r=>`
  <div class="activity-item">
    <div class="act-dot" style="background:${r.status==='selesai'?'var(--green)':r.status==='siap'?'var(--teal)':r.status==='diproses'?'var(--purple)':'var(--amber)'}"></div>
    <div style="flex:1;min-width:0">
      <div style="font-weight:500;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${r.pasien}</div>
      <div style="font-size:11px;color:var(--text2)">${r.id} · ${r.obat.length} obat</div>
    </div>
    ${statusBadge(r.status)}
  </div>`).join('');

document.getElementById('dashPasienList').innerHTML=pasienDB.slice(0,4).map(p=>`
  <div class="activity-item">
    <div class="pasien-avatar" style="width:32px;height:32px;font-size:11px;flex-shrink:0;border-radius:50%;background:var(--purple-light);display:flex;align-items:center;justify-content:center;font-weight:500;color:var(--purple2)">${p.nama.split(' ').map(x=>x[0]).join('').substring(0,2)}</div>
    <div style="flex:1;min-width:0">
      <div style="font-weight:500;font-size:13px">${p.nama}</div>
      <div style="font-size:11px;color:var(--text2)">${p.rm} · ${p.jk==='P'?'Perempuan':'Laki-laki'}</div>
    </div>
    <button class="btn btn-xs" onclick="goTulisResep(${p.id})">Resepkan</button>
  </div>`).join('');

const topObat=[{n:'Paracetamol 500mg',v:340,m:400},{n:'Metformin 500mg',v:290,m:400},{n:'Amoxicillin 250mg',v:210,m:400},{n:'Cetirizine 10mg',v:180,m:400},{n:'Antasida Doen',v:120,m:400}];
document.getElementById('dashTopObat').innerHTML=topObat.map(o=>`
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;font-size:12px">
    <div style="width:90px;color:var(--text2);text-align:right;flex-shrink:0;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${o.n}">${o.n.length>12?o.n.substring(0,11)+'…':o.n}</div>
    <div style="flex:1;background:var(--cream2);border-radius:4px;height:12px;overflow:hidden"><div style="height:100%;border-radius:4px;background:var(--purple);width:${Math.round(o.v/o.m*100)}%"></div></div>
    <div style="width:32px;text-align:right;color:var(--text)">${o.v}</div>
  </div>`).join('');

// ── TULIS RESEP ──────────────────────────────────────────
const pasienMap=[
  {rm:'RM-2026-0091',nama:'Siti Rahayu',tgl:'12/04/1985',bpjs:'Ada'},
  {rm:'RM-2026-0088',nama:'Ahmad Fauzi',tgl:'22/07/1990',bpjs:'—'},
  {rm:'RM-2026-0087',nama:'Maya Putri',tgl:'14/02/1998',bpjs:'Ada'},
  {rm:'RM-2026-0085',nama:'Rudi Hartono',tgl:'30/11/1978',bpjs:'—'},
  {rm:'RM-2026-0082',nama:'Dewi Lestari',tgl:'08/08/1995',bpjs:'Ada'},
];

function autofillPasien(){
  const v=document.getElementById('rxPasien').value;
  if(v===''){
    ['rxRM','rxNamaPasien','rxTglLahir'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('pasienInfoCard').style.display='none';
    document.getElementById('riwayatPasienCard').style.display='none';
    return;
  }
  const p=pasienMap[parseInt(v)];
  const pd=pasienDB[parseInt(v)];
  document.getElementById('rxRM').value=p.rm;
  document.getElementById('rxNamaPasien').value=p.nama;
  document.getElementById('rxTglLahir').value=p.tgl;
  document.getElementById('pasienInfoCard').style.display='block';
  document.getElementById('pasienInfoContent').innerHTML=`
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
      <div style="width:38px;height:38px;border-radius:50%;background:var(--purple-light);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:500;color:var(--purple2);flex-shrink:0">${p.nama.split(' ').map(x=>x[0]).join('').substring(0,2)}</div>
      <div><div style="font-weight:500;font-size:13px">${p.nama}</div><div style="font-size:11px;color:var(--text2)">${p.rm}</div></div>
    </div>
    <div style="font-size:12px;display:grid;grid-template-columns:1fr 1fr;gap:6px">
      <div><span style="color:var(--text3)">Tgl Lahir</span><div style="font-weight:500">${p.tgl}</div></div>
      <div><span style="color:var(--text3)">BPJS</span><div style="font-weight:500">${p.bpjs}</div></div>
      <div><span style="color:var(--text3)">Total Resep</span><div style="font-weight:500">${pd.totalResep} resep</div></div>
      <div><span style="color:var(--text3)">Kelamin</span><div style="font-weight:500">${pd.jk==='P'?'Perempuan':'Laki-laki'}</div></div>
    </div>`;

  const prevResep=resepDB.filter(r=>r.pasienId===parseInt(v)).slice(0,3);
  if(prevResep.length){
    document.getElementById('riwayatPasienCard').style.display='block';
    document.getElementById('riwayatPasienContent').innerHTML=prevResep.map(r=>`
      <div style="padding:7px 0;border-bottom:1px solid var(--cream3);font-size:12px">
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span style="font-weight:500">${fmtDate(r.tanggal)}</span>${statusBadge(r.status)}
        </div>
        <div style="color:var(--text2);margin-top:2px">${r.obat.map(o=>o.nama).join(', ')}</div>
      </div>`).join('');
  }
}

function goTulisResep(pasienId){
  showPage('tulis',document.querySelector('[onclick*=tulis]'));
  setTimeout(()=>{
    document.getElementById('rxPasien').value=pasienId;
    autofillPasien();
  },100);
}

function rxGoStep(n){
  rxStep=n;
  [1,2,3].forEach(i=>document.getElementById('rxStep'+i).style.display=i===n?'block':'none');
  const steps=[{l:'Info Pasien'},{l:'Pilih Obat'},{l:'Konfirmasi'}];
  document.getElementById('rxStepBar').innerHTML=steps.map((s,i)=>{
    const idx=i+1;const cls=idx<n?'done':idx===n?'active':'';const dc=idx<n?'done':idx===n?'active':'';
    return`<div class="step-item ${cls}"><div class="step-dot ${dc}">${idx<n?'✓':idx}</div>${s.l}</div>${i<2?`<div class="step-line${idx<n?' done':''}"></div>`:''}`
  }).join('');
  if(n===3)renderKonfirmasi();
}

function rxAddObat(){
  const sel=document.getElementById('rxObatSel');
  const idx=sel.value;if(idx==='')return;
  const o=obatList[parseInt(idx)];
  buatObat.push({...o,jml:1,aturan:''});
  sel.value='';
  renderRxObatList();
}

function renderRxObatList(){
  const el=document.getElementById('rxObatList');
  if(!buatObat.length){el.innerHTML=`<div style="font-size:13px;color:var(--text3);text-align:center;padding:1rem;background:var(--cream2);border-radius:8px">Belum ada obat ditambahkan</div>`;return;}
  el.innerHTML=buatObat.map((o,i)=>`
    <div class="rx-obat-item">
      <div class="rx-obat-top">
        <span class="rx-obat-nama">${o.nama} ${tipeBadge(o.tipe)}</span>
        <button class="btn-remove" onclick="buatObat.splice(${i},1);renderRxObatList()">Hapus</button>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        <div>
          <div style="font-size:10px;color:var(--text2);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em">Jumlah (${o.satuan})</div>
          <input type="number" min="1" value="${o.jml}" style="width:100%;padding:7px 10px;border-radius:7px;border:1px solid var(--cream3);background:var(--white);font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none" onchange="buatObat[${i}].jml=Math.max(1,parseInt(this.value)||1)">
        </div>
        <div>
          <div style="font-size:10px;color:var(--text2);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em">Aturan Pakai</div>
          <input type="text" value="${o.aturan}" placeholder="cth. 3x sehari 1 tablet" style="width:100%;padding:7px 10px;border-radius:7px;border:1px solid var(--cream3);background:var(--white);font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none" onchange="buatObat[${i}].aturan=this.value">
        </div>
      </div>
    </div>`).join('');
}

function renderKonfirmasi(){
  const pasienIdx=document.getElementById('rxPasien').value;
  const nama=document.getElementById('rxNamaPasien').value||'—';
  const rm=document.getElementById('rxRM').value||'—';
  const poli=document.getElementById('rxPoli').value;
  const tgl=document.getElementById('rxTanggal').value;
  const diag=document.getElementById('rxDiagnosis').value||'—';
  const catatan=document.getElementById('rxCatatanApoteker').value;
  const total=buatObat.reduce((s,o)=>s+o.harga*o.jml,0);
  document.getElementById('rxKonfirmasiContent').innerHTML=`
    <div style="background:var(--cream2);border-radius:10px;padding:14px;margin-bottom:14px">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px">
        <div><span style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.04em">Pasien</span><div style="font-weight:500">${nama}</div></div>
        <div><span style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.04em">No. RM</span><div style="font-weight:500">${rm}</div></div>
        <div><span style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.04em">Poli</span><div style="font-weight:500">${poli}</div></div>
        <div><span style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.04em">Tanggal</span><div style="font-weight:500">${fmtDate(tgl)}</div></div>
      </div>
      <div style="margin-top:8px;font-size:12px"><span style="color:var(--text3)">Diagnosis:</span> ${diag}</div>
    </div>
    <div style="font-size:11px;font-weight:500;text-transform:uppercase;letter-spacing:.05em;color:var(--text2);margin-bottom:8px">Daftar Obat</div>
    ${buatObat.map(o=>`
      <div class="detail-row">
        <div><div class="detail-obat-nama">${o.nama}</div><div class="detail-obat-info">${o.aturan||'—'} · ${fmtRp(o.harga)}/satuan</div></div>
        <div style="text-align:right"><div style="font-weight:500">${o.jml} ${o.satuan}</div><div style="font-size:11px;color:var(--text3)">${fmtRp(o.harga*o.jml)}</div></div>
      </div>`).join('')}
    <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:500;margin-top:10px;padding-top:10px;border-top:1px solid var(--cream3)">
      <span>Estimasi total</span><span>${fmtRp(total)}</span>
    </div>
    ${catatan?`<div style="margin-top:10px;font-size:12px;color:var(--text2);background:var(--amber-light);border-radius:8px;padding:8px 10px">Catatan: ${catatan}</div>`:''}
  `;
}

function rxSubmit(){
  const pasienIdx=document.getElementById('rxPasien').value;
  if(!pasienIdx){toast('Pilih pasien terlebih dahulu.','danger');return;}
  if(!buatObat.length){toast('Tambahkan minimal 1 obat.','danger');return;}
  const p=pasienDB[parseInt(pasienIdx)];
  const total=buatObat.reduce((s,o)=>s+o.harga*o.jml,0);
  const newId='RX-20260402-0'+(38+resepDB.length);
  resepDB.unshift({
    id:newId,pasienId:parseInt(pasienIdx),pasien:p.nama,rm:p.rm,
    poli:document.getElementById('rxPoli').value,
    diagnosis:document.getElementById('rxDiagnosis').value||'—',
    status:'baru',tanggal:document.getElementById('rxTanggal').value,
    obat:buatObat.map(o=>({nama:o.nama,tipe:o.tipe,jml:o.jml,aturan:o.aturan,satuan:o.satuan})),
    catatan:document.getElementById('rxCatatanApoteker').value,total
  });
  p.totalResep++;
  updatePendingBadge();
  buatObat=[];
  ['rxPasien','rxRM','rxNamaPasien','rxTglLahir','rxDiagnosis','rxCatatanApoteker'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('pasienInfoCard').style.display='none';
  document.getElementById('riwayatPasienCard').style.display='none';
  renderRxObatList();
  toast(`Resep ${newId} berhasil dikirim ke apoteker!`,'success');
  setTimeout(()=>showPage('riwayat',document.querySelector('[onclick*=riwayat]')),1000);
}

function updatePendingBadge(){
  const pending=resepDB.filter(r=>r.status==='baru'||r.status==='diproses').length;
  document.getElementById('navBadgePending').textContent=pending;
}

// ── RIWAYAT ──────────────────────────────────────────────
function renderRiwayat(){
  const q=document.getElementById('riwayatSearch').value.toLowerCase();
  const fs=document.getElementById('riwayatFilterStatus').value;
  const list=resepDB.filter(r=>{
    if(q&&!r.pasien.toLowerCase().includes(q)&&!r.id.toLowerCase().includes(q))return false;
    if(fs&&r.status!==fs)return false;
    return true;
  });
  document.getElementById('riwayatList').innerHTML=list.length?list.map(r=>`
    <div class="resep-list-item${selRiwayat&&selRiwayat.id===r.id?' selected':''}" onclick="selRiwayatFn('${r.id}')">
      <div class="rli-top">
        <div><div class="rli-no">${r.id}</div><div class="rli-meta">${r.pasien} · ${r.poli}</div></div>
        ${statusBadge(r.status)}
      </div>
      <div class="rli-bottom">${r.obat.length} obat · ${fmtDate(r.tanggal)} · ${fmtRp(r.total)}</div>
    </div>`).join(''):`<div style="text-align:center;padding:2rem;color:var(--text3);font-size:13px">Tidak ada resep ditemukan</div>`;
}

function selRiwayatFn(id){
  selRiwayat=resepDB.find(r=>r.id===id);
  renderRiwayat();
  renderRiwayatDetail();
}

function renderRiwayatDetail(){
  if(!selRiwayat){document.getElementById('riwayatDetail').innerHTML=`<div class="card"><div style="text-align:center;padding:2rem;color:var(--text3);font-size:13px">Pilih resep untuk melihat detail</div></div>`;return;}
  const r=selRiwayat;
  const canBatal=r.status==='baru';
  document.getElementById('riwayatDetail').innerHTML=`
    <div class="card">
      <div style="font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:600;margin-bottom:3px">${r.id}</div>
      <div style="font-size:12px;color:var(--text2);margin-bottom:8px">${r.pasien} · ${r.rm} · ${fmtDate(r.tanggal)}</div>
      <div style="margin-bottom:12px">${statusBadge(r.status)}</div>
      <div style="background:var(--cream2);border-radius:8px;padding:10px 12px;font-size:12px;margin-bottom:12px">
        <span style="color:var(--text3)">Poli:</span> ${r.poli}<br>
        <span style="color:var(--text3)">Diagnosis:</span> ${r.diagnosis}
        ${r.catatan?`<br><span style="color:var(--text3)">Catatan:</span> ${r.catatan}`:''}
      </div>
      <div style="font-size:11px;font-weight:500;text-transform:uppercase;letter-spacing:.05em;color:var(--text2);margin-bottom:8px">Obat diresepkan</div>
      ${r.obat.map(o=>`
        <div class="detail-row">
          <div><div class="detail-obat-nama">${o.nama} ${tipeBadge(o.tipe)}</div><div class="detail-obat-info">${o.aturan||'—'}</div></div>
          <div style="font-weight:500;white-space:nowrap">${o.jml} ${o.satuan}</div>
        </div>`).join('')}
      <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:500;margin-top:10px;padding-top:8px;border-top:1px solid var(--cream3)">
        <span>Total</span><span>${fmtRp(r.total)}</span>
      </div>
      ${canBatal?`<div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--cream3)">
        <button class="btn btn-sm" style="color:var(--red);border-color:#F09595" onclick="batalResep('${r.id}')">Batalkan Resep</button>
      </div>`:''}
    </div>`;
}

function batalResep(id){
  if(!confirm('Batalkan resep ini?'))return;
  const r=resepDB.find(x=>x.id===id);
  r.status='batal';
  selRiwayat=r;
  renderRiwayat();
  renderRiwayatDetail();
  updatePendingBadge();
  toast('Resep dibatalkan.','info');
}

// ── PASIEN TABLE ─────────────────────────────────────────
function renderPasienTable(){
  const q=document.getElementById('pasienSearch').value.toLowerCase();
  const list=pasienDB.filter(p=>!q||p.nama.toLowerCase().includes(q)||p.rm.toLowerCase().includes(q));
  document.getElementById('pasienTable').innerHTML=list.map(p=>`
    <tr>
      <td style="color:var(--text2);font-family:'Cormorant Garamond',serif">${p.rm}</td>
      <td><div style="font-weight:500">${p.nama}</div></td>
      <td>${fmtDate(p.tgl)}</td>
      <td>${p.jk==='P'?'Perempuan':'Laki-laki'}</td>
      <td>${p.bpjs||'<span style="color:var(--text3)">—</span>'}</td>
      <td><span style="font-weight:500">${p.totalResep}</span> resep</td>
      <td style="display:flex;gap:6px">
        <button class="btn btn-xs btn-primary" onclick="goTulisResep(${p.id})">Resepkan</button>
        <button class="btn btn-xs" onclick="lihatRiwayatPasien(${p.id})">Riwayat</button>
      </td>
    </tr>`).join('');
}

function lihatRiwayatPasien(id){
  showPage('riwayat',document.querySelector('[onclick*=riwayat]'));
  setTimeout(()=>{
    document.getElementById('riwayatSearch').value=pasienDB[id].nama;
    renderRiwayat();
  },100);
}

function openPasienModal(){document.getElementById('pasienModal').classList.add('open');}
function savePasien(){
  const nama=document.getElementById('pmNama').value.trim();
  const rm=document.getElementById('pmRM').value.trim();
  if(!nama||!rm){toast('Nama dan No. RM wajib diisi.','danger');return;}
  pasienDB.push({id:pasienDB.length,nama,rm,tgl:document.getElementById('pmTgl').value,jk:document.getElementById('pmJK').value,bpjs:document.getElementById('pmBPJS').value||null,telp:document.getElementById('pmTelp').value,totalResep:0});
  closeModal('pasienModal');
  renderPasienTable();
  toast('Pasien berhasil ditambahkan.','success');
}

// ── TOAST ────────────────────────────────────────────────
function toast(msg,type){
  const el=document.createElement('div');
  el.className=`toast toast-${type}`;
  el.textContent=msg;
  document.body.appendChild(el);
  setTimeout(()=>{el.style.opacity='0';el.style.transition='opacity .3s';setTimeout(()=>el.remove(),300);},2800);
}

// ── INIT ─────────────────────────────────────────────────
renderRxObatList();
renderPasienTable();
updatePendingBadge();
</script>
</body>
</html>
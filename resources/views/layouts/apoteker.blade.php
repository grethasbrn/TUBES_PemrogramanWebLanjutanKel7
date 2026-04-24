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
    
    @include('components.sidebarA')

    <div class="main">

        <div class="content">
            @yield('content')
        </div>
    </div>

</div>

<script>
// ===================== DATA =====================
const today = new Date();

let stockData = [
  {nama:'Paracetamol 500mg',tipe:'Tablet',batch:'BT-2024-001',jumlah:0,harga:1500,exp:'2025-03-15',masuk:'2024-01-10',supplier:'PT Kimia Farma'},
  {nama:'Amoxicillin 500mg',tipe:'Kapsul',batch:'BT-2025-012',jumlah:45,harga:3200,exp:'2026-08-20',masuk:'2025-02-01',supplier:'PT Sanbe'},
  {nama:'Metformin 500mg',tipe:'Tablet',batch:'BT-2025-018',jumlah:8,harga:2100,exp:'2026-11-30',masuk:'2025-03-10',supplier:'PT Dexa Medica'},
  {nama:'Insulin Novorapid',tipe:'Injeksi',batch:'BT-2025-022',jumlah:14,harga:185000,exp:'2026-05-10',masuk:'2025-01-20',supplier:'Novo Nordisk'},
  {nama:'OBH Combi Sirup',tipe:'Sirup',batch:'BT-2025-033',jumlah:8,harga:18500,exp:'2026-06-30',masuk:'2025-04-01',supplier:'PT Combiphar'},
  {nama:'Salep Betametason',tipe:'Salep',batch:'BT-2025-041',jumlah:22,harga:28000,exp:'2026-06-09',masuk:'2025-01-05',supplier:'PT Actavis'},
  {nama:'Antasida DOEN',tipe:'Tablet',batch:'BT-2026-001',jumlah:120,harga:800,exp:'2028-01-15',masuk:'2026-01-15',supplier:'PT Indofarma'},
  {nama:'Cetirizine 10mg',tipe:'Tablet',batch:'BT-2026-005',jumlah:85,harga:2800,exp:'2028-03-20',masuk:'2026-02-10',supplier:'PT Kimia Farma'},
  {nama:'Omeprazole 20mg',tipe:'Kapsul',batch:'BT-2026-008',jumlah:6,harga:4500,exp:'2027-07-10',masuk:'2026-03-01',supplier:'PT Sanbe'},
  {nama:'Captopril 25mg',tipe:'Tablet',batch:'BT-2026-010',jumlah:55,harga:1200,exp:'2027-09-30',masuk:'2026-02-20',supplier:'PT Dexa Medica'},
  {nama:'Clopidogrel 75mg',tipe:'Tablet',batch:'BT-2026-012',jumlah:3,harga:12000,exp:'2027-12-01',masuk:'2026-03-15',supplier:'PT Kalbe'},
  {nama:'Ibuprofen 400mg',tipe:'Tablet',batch:'BT-2026-015',jumlah:92,harga:2500,exp:'2028-06-15',masuk:'2026-04-01',supplier:'PT Kimia Farma'},
];

let resepData = [
  {id:'RX-2026-001',pasien:'Budi Santoso',rm:'RM-2026-0001',dokter:'Dr. Hendra Wijaya',bayar:'BPJS',tanggal:'2026-04-24',status:'baru',catatan:'Alergi penisilin',
   obat:[
    {nama:'Paracetamol 500mg',dosis:'3x1',tersedia:false,pengganti:null,alasan:null},
    {nama:'Antasida DOEN',dosis:'2x1 sebelum makan',tersedia:true,pengganti:null,alasan:null},
   ]},
  {id:'RX-2026-002',pasien:'Sari Dewi',rm:'RM-2026-0042',dokter:'Dr. Lisa Permata',bayar:'Mandiri',tanggal:'2026-04-24',status:'validasi',catatan:'',
   obat:[
    {nama:'Amoxicillin 500mg',dosis:'3x1 sesudah makan',tersedia:true,pengganti:null,alasan:null},
    {nama:'Cetirizine 10mg',dosis:'1x1 malam',tersedia:true,pengganti:null,alasan:null},
   ]},
  {id:'RX-2026-003',pasien:'Ahmad Ridwan',rm:'RM-2026-0078',dokter:'Dr. Hendra Wijaya',bayar:'BPJS',tanggal:'2026-04-23',status:'siap',catatan:'',
   obat:[
    {nama:'Metformin 500mg',dosis:'2x1 sesudah makan',tersedia:true,pengganti:null,alasan:null},
    {nama:'Captopril 25mg',dosis:'1x1 pagi',tersedia:true,pengganti:null,alasan:null},
   ]},
  {id:'RX-2026-004',pasien:'Nur Halimah',rm:'RM-2026-0091',dokter:'Dr. Lisa Permata',bayar:'Mandiri',tanggal:'2026-04-23',status:'selesai',catatan:'',
   obat:[
    {nama:'Ibuprofen 400mg',dosis:'3x1 sesudah makan',tersedia:true,pengganti:null,alasan:null},
   ]},
  {id:'RX-2026-005',pasien:'Hadi Susanto',rm:'RM-2026-0102',dokter:'Dr. Bima Saputra',bayar:'BPJS',tanggal:'2026-04-24',status:'baru',catatan:'Pasien lansia 70thn',
   obat:[
    {nama:'Omeprazole 20mg',dosis:'1x1 sebelum makan',tersedia:true,pengganti:null,alasan:null},
    {nama:'Clopidogrel 75mg',dosis:'1x1',tersedia:true,pengganti:null,alasan:null},
   ]},
];

let invoiceData = [
  {id:'INV-2026-001',pasien:'Sari Dewi',resep:'RX-2026-002',bayar:'Mandiri',status:'Terkirim',tanggal:'2026-04-24',items:[
    {nama:'Amoxicillin 500mg',qty:21,harga:3200},
    {nama:'Cetirizine 10mg',qty:10,harga:2800}
  ]},
  {id:'INV-2026-002',pasien:'Ahmad Ridwan',resep:'RX-2026-003',bayar:'BPJS',status:'Lunas',tanggal:'2026-04-23',items:[
    {nama:'Metformin 500mg',qty:60,harga:2100},
    {nama:'Captopril 25mg',qty:30,harga:1200}
  ]},
  {id:'INV-2026-003',pasien:'Nur Halimah',resep:'RX-2026-004',bayar:'Mandiri',status:'Lunas',tanggal:'2026-04-23',items:[
    {nama:'Ibuprofen 400mg',qty:21,harga:2500}
  ]},
];

let currentResep = null;
let currentSubsResepId = null;
let currentSubsObatIdx = null;

// ===================== HELPERS =====================
function formatRp(n){return 'Rp '+n.toLocaleString('id-ID')}
function formatDate(d){
  if(!d) return '-';
  const dt=new Date(d);
  return dt.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});
}
function daysUntilExp(d){
  const dt=new Date(d);
  const diff=Math.floor((dt-today)/(1000*60*60*24));
  return diff;
}
function badgeTipe(t){
  const m={Tablet:'b-tablet',Sirup:'b-sirup',Kapsul:'b-kapsul',Injeksi:'b-injeksi',Salep:'b-salep'};
  return `<span class="badge ${m[t]||''}">${t}</span>`;
}
function badgeStatus(s){
  const m={baru:'b-validasi',validasi:'b-validasi',siap:'b-siap',selesai:'b-selesai',ditolak:'b-ditolak'};
  const l={baru:'Baru',validasi:'Divalidasi',siap:'Siap Ambil',selesai:'Selesai',ditolak:'Ditolak'};
  return `<span class="badge ${m[s]||''}">${l[s]||s}</span>`;
}
function badgeBayar(b){
  return b==='BPJS'?`<span class="badge b-bpjs">BPJS</span>`:`<span class="badge b-mandiri">Mandiri</span>`;
}

function showToast(msg, type='success'){
  const c=document.getElementById('toastContainer');
  const t=document.createElement('div');
  const ico={success:'✅',danger:'❌',warn:'⚠️'}[type]||'ℹ️';
  t.className=`toast ${type}`;
  t.innerHTML=`<span>${ico}</span><span>${msg}</span>`;
  c.appendChild(t);
  setTimeout(()=>{t.style.animation='fadeOut .3s ease forwards';setTimeout(()=>t.remove(),300)},3000);
}

function openModal(id){document.getElementById(id).classList.add('open')}
function closeModal(id){document.getElementById(id).classList.remove('open')}
document.querySelectorAll('.modal-overlay').forEach(o=>{
  o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open')});
});

// ===================== NAVIGATION =====================
function showSection(name){
  document.querySelectorAll('.page-section').forEach(s=>s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('sec-'+name).classList.add('active');
  document.getElementById('nav-'+name).classList.add('active');
  if(name==='stock') renderStockTable();
  if(name==='alerts') renderAlerts();
  if(name==='resep') renderResepList('semua');
  if(name==='invoice'){renderInvoiceTable();}
  if(name==='report') renderReport();
}

// ===================== DATE =====================
function setDate(){
  const d=today.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
  document.getElementById('dateChip').textContent=d;
}

// ===================== STOCK TABLE =====================
function getStockStatus(item){
  const d=daysUntilExp(item.exp);
  if(d<=0) return 'expired';
  if(item.jumlah<10) return 'kritis';
  if(d<=90) return 'exp-soon';
  return 'aman';
}
function stockStatusBadge(item){
  const s=getStockStatus(item);
  const m={expired:`<span class="badge b-expired">Expired</span>`,kritis:`<span class="badge b-danger">Kritis</span>`,
    'exp-soon':`<span class="badge b-warn">Exp Segera</span>`,aman:`<span class="badge b-aman">Aman</span>`};
  return m[s];
}
function renderStockTable(){
  const q=(document.getElementById('stockSearch').value||'').toLowerCase();
  const ft=document.getElementById('stockFilterTipe').value;
  const fs=document.getElementById('stockFilterStatus').value;
  let data=stockData.filter(i=>{
    if(q && !i.nama.toLowerCase().includes(q) && !i.batch.toLowerCase().includes(q)) return false;
    if(ft && i.tipe!==ft) return false;
    if(fs && getStockStatus(i)!==fs) return false;
    return true;
  });
  const tbody=document.getElementById('stockTableBody');
  tbody.innerHTML=data.map((item,idx)=>{
    const realIdx=stockData.indexOf(item);
    const d=daysUntilExp(item.exp);
    const expText=d<=0?`<span style="color:var(--red);font-weight:600">${formatDate(item.exp)}</span>`:
      d<=90?`<span style="color:var(--orange)">${formatDate(item.exp)}</span>`:formatDate(item.exp);
    const qtyText=item.jumlah<10?`<span style="color:var(--red);font-weight:600">${item.jumlah}</span>`:item.jumlah;
    return `<tr>
      <td><strong>${item.nama}</strong><div style="font-size:11px;color:var(--text3)">${item.supplier||''}</div></td>
      <td>${badgeTipe(item.tipe)}</td>
      <td style="font-family:monospace;font-size:12px">${item.batch}</td>
      <td>${qtyText}</td>
      <td>${formatRp(item.harga)}</td>
      <td>${expText}</td>
      <td>${stockStatusBadge(item)}</td>
      <td>
        <button class="btn btn-sm" onclick="editBatch(${realIdx})">✏️</button>
        <button class="btn btn-sm btn-danger" onclick="hapusBatch(${realIdx})" style="margin-left:4px">🗑</button>
      </td>
    </tr>`;
  }).join('');
  if(!data.length) tbody.innerHTML=`<tr><td colspan="8" style="text-align:center;color:var(--text3);padding:30px">Tidak ada data</td></tr>`;
}

function tambahBatch(){
  const nama=document.getElementById('bNama').value.trim();
  const tipe=document.getElementById('bTipe').value;
  const batch=document.getElementById('bBatch').value.trim();
  const jumlah=parseInt(document.getElementById('bJumlah').value)||0;
  const harga=parseInt(document.getElementById('bHarga').value)||0;
  const exp=document.getElementById('bExp').value;
  const masuk=document.getElementById('bMasuk').value;
  const supplier=document.getElementById('bSupplier').value.trim();
  if(!nama||!batch||!exp){showToast('Nama obat, no. batch, dan tgl expired wajib diisi!','danger');return;}
  stockData.push({nama,tipe,batch,jumlah,harga,exp,masuk,supplier});
  closeModal('modalTambahBatch');
  ['bNama','bBatch','bJumlah','bHarga','bExp','bMasuk','bSupplier'].forEach(id=>document.getElementById(id).value='');
  renderStockTable();
  updateDashboard();
  showToast(`Batch ${nama} berhasil ditambahkan!`);
}

function editBatch(idx){
  const i=stockData[idx];
  document.getElementById('editIdx').value=idx;
  document.getElementById('editNama').value=i.nama;
  document.getElementById('editTipe').value=i.tipe;
  document.getElementById('editBatch').value=i.batch;
  document.getElementById('editJumlah').value=i.jumlah;
  document.getElementById('editHarga').value=i.harga;
  document.getElementById('editExp').value=i.exp;
  openModal('modalEditBatch');
}

function simpanEdit(){
  const idx=parseInt(document.getElementById('editIdx').value);
  stockData[idx]={
    ...stockData[idx],
    nama:document.getElementById('editNama').value,
    tipe:document.getElementById('editTipe').value,
    batch:document.getElementById('editBatch').value,
    jumlah:parseInt(document.getElementById('editJumlah').value)||0,
    harga:parseInt(document.getElementById('editHarga').value)||0,
    exp:document.getElementById('editExp').value,
  };
  closeModal('modalEditBatch');
  renderStockTable();
  updateDashboard();
  showToast('Data batch berhasil diperbarui!');
}

function hapusBatch(idx){
  if(!confirm(`Hapus batch "${stockData[idx].nama}"?`)) return;
  stockData.splice(idx,1);
  renderStockTable();
  updateDashboard();
  showToast('Batch dihapus','warn');
}

// ===================== ALERTS =====================
function renderAlerts(){
  const expired=stockData.filter(i=>daysUntilExp(i.exp)<=0);
  const kritis=stockData.filter(i=>i.jumlah<10 && daysUntilExp(i.exp)>0);
  const soon=stockData.filter(i=>daysUntilExp(i.exp)>0 && daysUntilExp(i.exp)<=90 && i.jumlah>=10);

  const makeCard=(item,type)=>{
    const d=daysUntilExp(item.exp);
    const info=type==='expired'?`Expired ${formatDate(item.exp)}`:
      type==='kritis'?`Sisa ${item.jumlah} unit`:
      `Expired ${formatDate(item.exp)} (${d} hari lagi)`;
    const tagCls=type==='expired'?'tag-expired':type==='kritis'?'tag-kritis':'tag-soon';
    const tagTxt=type==='expired'?'EXPIRED':type==='kritis'?'KRITIS':'EXP SOON';
    const cardCls=type==='expired'?'expired':type==='kritis'?'kritis':'exp-soon';
    return `<div class="alert-card ${cardCls}">
      <div class="alert-card-left">
        <span class="alert-tag ${tagCls}">${tagTxt}</span>
        <div>
          <div style="font-weight:500">${item.nama}</div>
          <div style="font-size:11px;opacity:.8">${info} · Batch ${item.batch}</div>
        </div>
      </div>
      <button class="btn btn-sm" onclick="showSection('stock')">Lihat Stok</button>
    </div>`;
  };

  document.getElementById('alertExpiredList').innerHTML=expired.length?expired.map(i=>makeCard(i,'expired')).join(''):`<div style="color:var(--text3);font-size:13px;padding:10px 0">Tidak ada obat expired ✅</div>`;
  document.getElementById('alertKritisList').innerHTML=kritis.length?kritis.map(i=>makeCard(i,'kritis')).join(''):`<div style="color:var(--text3);font-size:13px;padding:10px 0">Tidak ada stok kritis ✅</div>`;
  document.getElementById('alertExpSoonList').innerHTML=soon.length?soon.map(i=>makeCard(i,'exp-soon')).join(''):`<div style="color:var(--text3);font-size:13px;padding:10px 0">Tidak ada obat mendekati expired ✅</div>`;
}

// ===================== DASHBOARD =====================
function updateDashboard(){
  const expired=stockData.filter(i=>daysUntilExp(i.exp)<=0);
  const kritis=stockData.filter(i=>i.jumlah<10 && daysUntilExp(i.exp)>0);
  const soon=stockData.filter(i=>daysUntilExp(i.exp)>0 && daysUntilExp(i.exp)<=90 && i.jumlah>=10);

  document.getElementById('m-total').textContent=stockData.length;
  document.getElementById('m-kritis').textContent=kritis.length||'0';
  document.getElementById('m-exp').textContent=soon.length||'0';

  // Badge notif
  const totalAlert=expired.length+kritis.length+soon.length;
  document.getElementById('notifBadge').style.display=totalAlert?'block':'none';

// Alert banner
const msgs=[];
expired.forEach(i=>msgs.push(`🚨 ${i.nama} sudah EXPIRED`));
kritis.forEach(i=>msgs.push(`⚠️ ${i.nama} stok kritis (${i.jumlah} unit)`));
soon.forEach(i=>msgs.push(`🕐 ${i.nama} exp dalam ${daysUntilExp(i.exp)} hari`));

const banner = document.getElementById('alertBanner');
const track = document.getElementById('alertTrack'); // ⬅️ ini baru

if(msgs.length){
  banner.style.display = 'flex';

  const content = msgs.map(m => 
    `<span class="alert-item">${m}</span>`
  ).join('');

  // ⬅️ INI YANG PENTING (loop tanpa kosong)
  track.innerHTML = content + content;

} else {
  banner.style.display = 'none';
}

  // Alert list di dashboard
  const alertItems=[
    ...expired.map(i=>({type:'expired',text:`Expired: ${i.nama} (${formatDate(i.exp)})`,bg:'var(--red-light)',col:'var(--red)'})),
    ...kritis.map(i=>({type:'kritis',text:`Kritis: ${i.nama} — ${i.jumlah} unit`,bg:'var(--amber-light)',col:'#7A4A0A'})),
    ...soon.slice(0,3).map(i=>({type:'soon',text:`Exp ${daysUntilExp(i.exp)} hari: ${i.nama}`,bg:'var(--orange-light)',col:'var(--orange)'})),
  ].slice(0,6);

  document.getElementById('alertList').innerHTML=alertItems.length?alertItems.map(a=>`
    <div style="background:${a.bg};border-radius:8px;padding:9px 12px;margin-bottom:8px;font-size:13px;color:${a.col};display:flex;align-items:center;justify-content:space-between">
      <span>${a.text}</span>
    </div>`).join(''):`<div style="color:var(--text3);font-size:13px;padding:20px 0;text-align:center">Semua stok dalam kondisi baik ✅</div>`;
}

// ===================== CHARTS =====================
let chartResep, chartTipe, chartPend;
function initCharts(){
  // Resep chart
  const ctx1=document.getElementById('chartResep').getContext('2d');
  chartResep=new Chart(ctx1,{
    type:'line',
    data:{
      labels:['Sen','Sel','Rab','Kam','Jum','Sab','Hari ini'],
      datasets:[
        {label:'Resep masuk',data:[26,35,30,42,33,25,37],borderColor:'#8B7DB8',backgroundColor:'rgba(139,125,184,.1)',tension:.4,pointRadius:4,pointBackgroundColor:'#8B7DB8',fill:true},
        {label:'Resep selesai',data:[22,31,26,38,29,22,33],borderColor:'#2A9D8F',backgroundColor:'rgba(42,157,143,.08)',tension:.4,pointRadius:4,pointBackgroundColor:'#2A9D8F',fill:true},
      ]
    },
    options:{responsive:true,plugins:{legend:{labels:{font:{family:'DM Sans'},boxWidth:10}}},scales:{y:{beginAtZero:false,grid:{color:'rgba(0,0,0,.05)'}},x:{grid:{display:false}}}}
  });

  // Tipe chart
  const ctx2=document.getElementById('chartTipe').getContext('2d');
  chartTipe=new Chart(ctx2,{
    type:'doughnut',
    data:{
      labels:['Tablet','Kapsul','Sirup','Injeksi','Salep'],
      datasets:[{data:[48,22,15,8,7],backgroundColor:['#8B7DB8','#2A9D8F','#E76F51','#F4A261','#52B788'],borderWidth:0}]
    },
    options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{family:'DM Sans'},padding:10,boxWidth:10}}},cutout:'65%'}
  });
}

function renderReport(){
  // Top drugs bar chart
  const tops=[
    {nama:'Paracetamol',qty:412},
    {nama:'Amoxicillin',qty:338},
    {nama:'Antasida',qty:289},
    {nama:'Metformin',qty:241},
    {nama:'Cetirizine',qty:198},
    {nama:'Captopril',qty:175},
    {nama:'Ibuprofen',qty:156},
    {nama:'Omeprazole',qty:134},
  ];
  const maxQ=tops[0].qty;
  document.getElementById('topDrugsChart').innerHTML=tops.map(t=>`
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:9px;font-size:12px">
      <div style="width:90px;color:var(--text2);text-align:right;flex-shrink:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${t.nama}</div>
      <div style="flex:1;background:var(--cream2);border-radius:4px;height:14px;overflow:hidden">
        <div style="width:${(t.qty/maxQ*100).toFixed(1)}%;height:100%;background:var(--purple);border-radius:4px"></div>
      </div>
      <div style="width:40px;color:var(--text);font-size:11px;text-align:right">${t.qty}</div>
    </div>`).join('');

  // Revenue chart
  const ctxP=document.getElementById('chartPendapatan').getContext('2d');
  if(chartPend) chartPend.destroy();
  chartPend=new Chart(ctxP,{
    type:'bar',
    data:{
      labels:['W1','W2','W3','W4'],
      datasets:[{label:'Pendapatan (Rp jt)',data:[5.2,6.8,7.1,5.7],backgroundColor:'rgba(139,125,184,.7)',borderRadius:6}]
    },
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.05)'}},x:{grid:{display:false}}}}
  });
}

// ===================== ACTIVITY =====================
function renderActivity(){
  const acts=[
    {dot:'var(--green)',text:'Batch Amoxicillin 500mg ditambahkan',time:'10:42'},
    {dot:'var(--purple)',text:'Resep RX-2026-003 divalidasi',time:'10:15'},
    {dot:'var(--teal)',text:'Invoice INV-2026-002 dikirim ke admin',time:'09:58'},
    {dot:'var(--amber)',text:'Alert: OBH Combi stok kritis (8 botol)',time:'09:30'},
    {dot:'var(--red)',text:'Alert: Paracetamol 500mg sudah expired',time:'08:00'},
  ];
  document.getElementById('activityList').innerHTML=acts.map(a=>`
    <div class="activity-item">
      <div class="activity-dot" style="background:${a.dot}"></div>
      <div style="flex:1">${a.text}</div>
      <div style="font-size:11px;color:var(--text3)">${a.time}</div>
    </div>`).join('');
}

// ===================== RESEP =====================
let resepFilter='semua';
function filterResep(f, el){
  resepFilter=f;
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  el.classList.add('active');
  renderResepList(f);
}
function renderResepList(filter){
  let data=resepData;
  if(filter!=='semua') data=resepData.filter(r=>r.status===filter);
  document.getElementById('resepList').innerHTML=data.map(r=>`
    <div class="rx-item ${currentResep&&currentResep.id===r.id?'selected':''}" onclick="selectResep('${r.id}')">
      <div class="rx-header">
        <span class="rx-no">${r.id}</span>
        ${badgeStatus(r.status)}
      </div>
      <div class="rx-meta">${r.pasien} · ${r.dokter}</div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px">
        ${badgeBayar(r.bayar)}
        <span style="font-size:11px;color:var(--text3)">${formatDate(r.tanggal)}</span>
      </div>
    </div>`).join('');
}

function selectResep(id){
  currentResep=resepData.find(r=>r.id===id);
  renderResepDetail();
  renderResepList(resepFilter);
}

function renderResepDetail(){
  const r=currentResep;
  if(!r){document.getElementById('resepDetail').innerHTML=`<div class="card" style="text-align:center;padding:40px 20px;color:var(--text3)"><div style="font-size:32px;margin-bottom:10px">📋</div><div style="font-family:'Cormorant Garamond',serif;font-size:16px">Pilih resep untuk detail</div></div>`;return;}

  const canValidate=r.status==='baru';
  const canSelesai=r.status==='siap';
  const canInvoice=r.status==='validasi'||r.status==='siap';

  const obatRows=r.obat.map((o,idx)=>{
    const stockItem=stockData.find(s=>s.nama===o.nama&&daysUntilExp(s.exp)>0&&s.jumlah>0);
    const avail=!!stockItem;
    const isSubstituted=!!o.pengganti;

    let cls=isSubstituted?'substituted':(!avail?'unavail':'');
    let statusLabel=isSubstituted?`<span class="badge b-diganti" style="margin-left:6px">Diganti: ${o.pengganti}</span>`:
      (!avail?`<span class="badge b-danger" style="margin-left:6px">Tidak tersedia</span>`:
      `<span class="badge b-aman" style="margin-left:6px">Tersedia</span>`);

    return `<div class="drug-row ${cls}">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:4px">
        <div>
          <div class="drug-name">${o.nama} ${statusLabel}</div>
          <div class="drug-info">Dosis: ${o.dosis}${o.alasan?` · Alasan: ${o.alasan}`:''}</div>
        </div>
      </div>
      ${canValidate&&!isSubstituted?`
      <div class="drug-actions">
        ${!avail?`<span style="font-size:11px;color:var(--red)">⚠ Stok tidak ada</span>`:''}
        <button class="btn btn-sm btn-amber" onclick="openSubstitusi('${r.id}',${idx})">🔄 Substitusi</button>
      </div>`:''}
    </div>`;
  }).join('');

  document.getElementById('resepDetail').innerHTML=`
    <div class="card">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px">
        <div>
          <div style="font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:600">${r.id}</div>
          <div style="font-size:12px;color:var(--text3)">${formatDate(r.tanggal)}</div>
        </div>
        ${badgeStatus(r.status)}
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px;font-size:13px">
        <div><span style="color:var(--text3)">Pasien:</span> ${r.pasien}</div>
        <div><span style="color:var(--text3)">No. RM:</span> ${r.rm}</div>
        <div><span style="color:var(--text3)">Dokter:</span> ${r.dokter}</div>
        <div><span style="color:var(--text3)">Pembayaran:</span> ${badgeBayar(r.bayar)}</div>
      </div>
      ${r.catatan?`<div style="background:var(--amber-light);border-radius:8px;padding:9px 12px;font-size:12px;color:#7A4A0A;margin-bottom:14px">📌 ${r.catatan}</div>`:''}
      <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Daftar Obat</div>
      ${obatRows}
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--cream3)">
        ${canValidate?`<button class="btn btn-primary btn-sm" onclick="validasiResep('${r.id}')">✅ Validasi Resep</button>`:''}
        ${r.status==='validasi'?`<button class="btn btn-teal btn-sm" onclick="ubahStatus('${r.id}','siap')">📦 Siap Ambil</button>`:''}
        ${canSelesai?`<button class="btn btn-sm" style="background:var(--green);color:white;border-color:var(--green)" onclick="ubahStatus('${r.id}','selesai')">✔ Selesaikan</button>`:''}
        ${canInvoice?`<button class="btn btn-sm" onclick="buatInvoice('${r.id}')">🧾 Buat Invoice</button>`:''}
      </div>
    </div>`;
}

function openSubstitusi(resepId, obatIdx){
  currentSubsResepId=resepId;
  currentSubsObatIdx=obatIdx;
  const r=resepData.find(x=>x.id===resepId);
  const o=r.obat[obatIdx];
  document.getElementById('subsInfo').innerHTML=`Mengganti obat <strong>${o.nama}</strong> pada resep <strong>${resepId}</strong>`;
  // Populate options dari stok tersedia
  const avail=stockData.filter(s=>daysUntilExp(s.exp)>0&&s.jumlah>0&&s.nama!==o.nama);
  document.getElementById('subsObat').innerHTML=avail.map(s=>`<option value="${s.nama}">${s.nama} (Stok: ${s.jumlah})</option>`).join('');
  openModal('modalSubstitusi');
}

function simpanSubstitusi(){
  const pengganti=document.getElementById('subsObat').value;
  const alasan=document.getElementById('subsAlasan').value;
  const catatan=document.getElementById('subsCatatan').value;
  const r=resepData.find(x=>x.id===currentSubsResepId);
  r.obat[currentSubsObatIdx].pengganti=pengganti;
  r.obat[currentSubsObatIdx].alasan=alasan;
  r.obat[currentSubsObatIdx].tersedia=true;
  closeModal('modalSubstitusi');
  renderResepDetail();
  showToast(`Substitusi ke ${pengganti} berhasil disimpan`,'warn');
}

function validasiResep(id){
  const r=resepData.find(x=>x.id===id);
  r.status='validasi';
  renderResepDetail();
  renderResepList(resepFilter);
  showToast(`Resep ${id} berhasil divalidasi!`);
}

function ubahStatus(id,status){
  const r=resepData.find(x=>x.id===id);
  r.status=status;
  renderResepDetail();
  renderResepList(resepFilter);
  const msg={siap:`Resep ${id} ditandai Siap Ambil`,selesai:`Resep ${id} selesai!`};
  showToast(msg[status]||'Status diperbarui');
}

function tambahResep(){
  const pasien=document.getElementById('rPasien').value.trim();
  const rm=document.getElementById('rRM').value.trim();
  const dokter=document.getElementById('rDokter').value.trim();
  const bayar=document.getElementById('rBayar').value;
  const obatRaw=document.getElementById('rObat').value.trim();
  const catatan=document.getElementById('rCatatan').value.trim();
  if(!pasien||!rm||!dokter||!obatRaw){showToast('Lengkapi semua field wajib!','danger');return;}
  const obatList=obatRaw.split(',').map(o=>{
    const parts=o.trim().split(/\s+(?=\d)/);
    const nama=parts[0].trim();
    const dosis=parts.slice(1).join(' ')||'Sesuai petunjuk';
    const stockItem=stockData.find(s=>s.nama===nama&&daysUntilExp(s.exp)>0&&s.jumlah>0);
    return {nama,dosis,tersedia:!!stockItem,pengganti:null,alasan:null};
  });
  const newId='RX-'+new Date().getFullYear()+'-'+String(resepData.length+1).padStart(3,'0');
  resepData.unshift({id:newId,pasien,rm,dokter,bayar,tanggal:today.toISOString().split('T')[0],status:'baru',catatan,obat:obatList});
  closeModal('modalTambahResep');
  renderResepList(resepFilter);
  showToast(`Resep ${newId} berhasil ditambahkan!`);
  document.getElementById('m-resep').textContent=parseInt(document.getElementById('m-resep').textContent)+1;
}

// ===================== INVOICE =====================
function renderInvoiceTable(){
  const q=(document.getElementById('invSearch').value||'').toLowerCase();
  const fs=document.getElementById('invFilterStatus').value;
  let data=invoiceData.filter(i=>{
    if(q && !i.id.toLowerCase().includes(q) && !i.pasien.toLowerCase().includes(q)) return false;
    if(fs && i.status!==fs) return false;
    return true;
  });
  const statusBadge={Terkirim:'b-validasi',Lunas:'b-selesai',Draft:'b-warn'};
  document.getElementById('invoiceTableBody').innerHTML=data.map(inv=>{
    const total=inv.items.reduce((s,i)=>s+i.qty*i.harga,0);
    return `<tr style="cursor:pointer" onclick="previewInvoice('${inv.id}')">
      <td style="font-family:monospace;font-size:12px">${inv.id}</td>
      <td>${inv.pasien}</td>
      <td style="font-size:12px;color:var(--text2)">${inv.resep}</td>
      <td>${formatRp(total)}</td>
      <td><span class="badge ${statusBadge[inv.status]||''}">${inv.status}</span></td>
      <td>
        <button class="btn btn-sm btn-teal" onclick="event.stopPropagation();kirimInvoice('${inv.id}')">📤 Kirim</button>
      </td>
    </tr>`;
  }).join('');
  if(!data.length) document.getElementById('invoiceTableBody').innerHTML=`<tr><td colspan="6" style="text-align:center;color:var(--text3);padding:30px">Tidak ada invoice</td></tr>`;
}

function previewInvoice(id){
  const inv=invoiceData.find(i=>i.id===id);
  const total=inv.items.reduce((s,i)=>s+i.qty*i.harga,0);
  const subtotal=inv.items.reduce((s,i)=>s+i.qty*i.harga,0);
  const pajak=Math.round(subtotal*0.11);
  const totalAkhir=subtotal+pajak;
  const statusBadge={Terkirim:'b-validasi',Lunas:'b-selesai',Draft:'b-warn'};

  document.getElementById('invoicePreview').innerHTML=`
    <div class="invoice-card">
      <div class="inv-header">
        <div>
          <div class="inv-title">Invoice</div>
          <div class="inv-no">${inv.id} · ${formatDate(inv.tanggal)}</div>
        </div>
        <span class="badge ${statusBadge[inv.status]||''}">${inv.status}</span>
      </div>
      <div style="font-size:12px;margin-bottom:14px">
        <div style="color:var(--text3)">Kepada:</div>
        <div style="font-weight:500">${inv.pasien}</div>
        <div style="color:var(--text3)">Resep: ${inv.resep} · ${badgeBayar(inv.bayar)}</div>
      </div>
      <div class="inv-section-title">Rincian Obat</div>
      ${inv.items.map(i=>`
        <div class="inv-row">
          <div>
            <div style="font-weight:500">${i.nama}</div>
            <div style="font-size:11px;color:var(--text3)">${i.qty} × ${formatRp(i.harga)}</div>
          </div>
          <div>${formatRp(i.qty*i.harga)}</div>
        </div>`).join('')}
      <div class="inv-row" style="color:var(--text2)"><span>Subtotal</span><span>${formatRp(subtotal)}</span></div>
      <div class="inv-row" style="color:var(--text2)"><span>PPN 11%</span><span>${formatRp(pajak)}</span></div>
      <div class="inv-total-row"><span>TOTAL</span><span style="color:var(--purple2)">${formatRp(totalAkhir)}</span></div>
      <div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap">
        <button class="btn btn-teal btn-sm" onclick="kirimInvoice('${inv.id}')">📤 Kirim ke Admin</button>
        <button class="btn btn-sm" onclick="window.print()">🖨 Print</button>
        ${inv.status==='Terkirim'||inv.status==='Draft'?`<button class="btn btn-sm btn-primary" onclick="tandaiLunas('${inv.id}')">✅ Tandai Lunas</button>`:''}
      </div>
    </div>`;
}

function buatInvoice(resepId){
  const r=resepData.find(x=>x.id===resepId);
  const items=r.obat.map(o=>{
    const namaObat=o.pengganti||o.nama;
    const s=stockData.find(st=>st.nama===namaObat);
    return {nama:namaObat,qty:10,harga:s?s.harga:5000};
  });
  const newInv={
    id:'INV-'+new Date().getFullYear()+'-'+String(invoiceData.length+1).padStart(3,'0'),
    pasien:r.pasien,resep:r.id,bayar:r.bayar,status:'Draft',
    tanggal:today.toISOString().split('T')[0],items
  };
  invoiceData.unshift(newInv);
  showSection('invoice');
  renderInvoiceTable();
  previewInvoice(newInv.id);
  showToast(`Invoice ${newInv.id} dibuat untuk ${r.pasien}!`);
}

function kirimInvoice(id){
  const inv=invoiceData.find(i=>i.id===id);
  if(inv.status==='Lunas'){showToast('Invoice sudah lunas','warn');return;}
  inv.status='Terkirim';
  renderInvoiceTable();
  previewInvoice(id);
  showToast(`Invoice ${id} berhasil dikirim ke Admin! 📤`);
}

function tandaiLunas(id){
  const inv=invoiceData.find(i=>i.id===id);
  inv.status='Lunas';
  renderInvoiceTable();
  previewInvoice(id);
  showToast(`Invoice ${id} ditandai Lunas ✅`);
}

// ===================== INIT =====================
setDate();
updateDashboard();
renderActivity();
renderStockTable();
renderResepList('semua');
renderInvoiceTable();
setTimeout(initCharts, 100);
</script>

</body>
</html>
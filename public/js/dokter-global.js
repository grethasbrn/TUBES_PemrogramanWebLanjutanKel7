// ===================== KATALOG OBAT =====================
const obatBPJS = [
  {nama:'Paracetamol 500mg',kategori:'Analgetik',tipe:'Tablet',keterangan:'Analgetik-antipiretik standar', harga:'10.000'},
  {nama:'Amoxicillin 500mg',kategori:'Antibiotik',tipe:'Kapsul',keterangan:'Antibiotik broad-spectrum lini pertama'},
  {nama:'Metformin 500mg',kategori:'Antidiabetik',tipe:'Tablet',keterangan:'Antidiabetik oral lini pertama'},
  {nama:'Captopril 25mg',kategori:'Antihipertensi',tipe:'Tablet',keterangan:'ACE inhibitor lini pertama'},
  {nama:'Antasida DOEN',kategori:'GI',tipe:'Tablet',keterangan:'Antasida untuk dispepsia'},
  {nama:'Cetirizine 10mg',kategori:'Antihistamin',tipe:'Tablet',keterangan:'Antihistamin generasi 2'},
  {nama:'Omeprazole 20mg',kategori:'GI',tipe:'Kapsul',keterangan:'PPI untuk GERD/ulkus peptik'},
  {nama:'Salep Betametason',kategori:'Dermatologi',tipe:'Salep',keterangan:'Kortikosteroid topikal'},
  {nama:'OBH Combi Sirup',kategori:'Respirasi',tipe:'Sirup',keterangan:'Ekspektoran untuk batuk'},
  {nama:'Ibuprofen 400mg',kategori:'NSAID',tipe:'Tablet',keterangan:'Antiinflamasi NSAID'},
  {nama:'Clopidogrel 75mg',kategori:'Antiplatelet',tipe:'Tablet',keterangan:'Antiplatelet kardiovaskuler'},
  {nama:'Glibenclamide 5mg',kategori:'Antidiabetik',tipe:'Tablet',keterangan:'Sulfonilurea untuk DM tipe 2'},
  {nama:'Amlodipine 5mg',kategori:'Antihipertensi',tipe:'Tablet',keterangan:'Calcium channel blocker'},
  {nama:'Furosemide 40mg',kategori:'Diuretik',tipe:'Tablet',keterangan:'Diuretik kuat untuk edema'},
];

const obatMandiri = [
  ...obatBPJS,
  {nama:'Paracetamol 500mg (Panadol)',kategori:'Analgetik',tipe:'Tablet',keterangan:'Branded',premium:true},
  {nama:'Levofloxacin 500mg',kategori:'Antibiotik',tipe:'Tablet',keterangan:'Fluoroquinolon generasi 3',premium:true},
  {nama:'Metformin XR 500mg',kategori:'Antidiabetik',tipe:'Tablet',keterangan:'Extended release',premium:true},
  {nama:'Candesartan 8mg',kategori:'Antihipertensi',tipe:'Tablet',keterangan:'ARB lini pertama',premium:true},
  {nama:'Esomeprazole 20mg (Nexium)',kategori:'GI',tipe:'Tablet',keterangan:'PPI branded',premium:true},
  {nama:'Fexofenadine 120mg',kategori:'Antihistamin',tipe:'Tablet',keterangan:'Non-sedatif premium',premium:true},
  {nama:'Insulin Novorapid',kategori:'Antidiabetik',tipe:'Injeksi',keterangan:'Rapid-acting analog',premium:true},
  {nama:'Atorvastatin 20mg',kategori:'Lipid-lowering',tipe:'Tablet',keterangan:'Statin dislipidemia',premium:true},
  {nama:'Salbutamol Inhaler',kategori:'Respirasi',tipe:'Inhaler',keterangan:'Bronkodilator inhalasi',premium:true},
];

// ===================== STATE =====================
// pasienData & resepData diinject dari masing-masing blade view
// via: let pasienData = @json($pasienJson);
let selectedPasien = null;
let currentObatList = [];
let selectedCatalogObat = null;
let currentStatusResep = null;
let statusFilter = 'semua';
let currentRiwayatPasien = null;

// ===================== HELPERS =====================
function formatDate(d) {
  if (!d) return '-';
  return new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
}

function getCsrfToken() {
  return document.querySelector('meta[name=csrf-token]')?.content || '';
}

function showToast(msg, type='success') {
  let c = document.getElementById('toastContainer');
  if (!c) {
    c = document.createElement('div');
    c.id = 'toastContainer';
    c.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px';
    document.body.appendChild(c);
  }
  const t = document.createElement('div');
  const ico = {success:'✅', danger:'❌', warn:'⚠️', info:'ℹ️'}[type] || 'ℹ️';
  t.className = `toast ${type}`;
  t.style.cssText = 'background:#fff;border:1px solid #e5e5e5;border-radius:10px;padding:10px 16px;font-size:13px;display:flex;gap:8px;align-items:center;box-shadow:0 4px 12px rgba(0,0,0,.1);max-width:320px';
  t.innerHTML = `<span>${ico}</span><span>${msg}</span>`;
  c.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(() => t.remove(), 300); }, 3200);
}

function openModal(id) { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }

function badgeBayar(b) {
  return b === 'BPJS'
    ? `<span class="badge b-bpjs">BPJS</span>`
    : `<span class="badge b-mandiri">Mandiri</span>`;
}

function badgeStatus(s) {
  const m = {baru:'b-baru', validasi:'b-validasi', siap:'b-siap', selesai:'b-selesai', ditolak:'b-ditolak', draft:'b-warn'};
  const l = {baru:'Baru', validasi:'Divalidasi', siap:'Siap Ambil', selesai:'Selesai', ditolak:'Ditolak', draft:'Draft'};
  return `<span class="badge ${m[s]||''}">${l[s]||s}</span>`;
}

// ===================== NAV (single-page style di setiap halaman) =====================
function showSection(name) {
  // Kalau section ada di halaman ini
  const target = document.getElementById('sec-' + name);
  if (target) {
    document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
    target.classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    const navEl = document.getElementById('nav-' + name);
    if (navEl) navEl.classList.add('active');
    if (name === 'pasien') renderPasienList();
    if (name === 'buat-resep') { renderResepPasienList(); }
  } else {
    // Navigasi ke halaman lain
    const routes = {
      'dashboard': '/dokter/dashboard',
      'pasien': '/dokter/data',
      'buat-resep': '/dokter/prescription',
      'resep-status': '/dokter/status',
      'riwayat': '/dokter/history',
    };
    if (routes[name]) window.location.href = routes[name];
  }
}

// ===================== DATA PASIEN — LIST =====================
function renderPasienList() {
  const q = (document.getElementById('pasienSearch')?.value || '').toLowerCase();
  const fb = document.getElementById('pasienFilterBayar')?.value || '';
  let data = (typeof pasienData !== 'undefined' ? pasienData : []).filter(p => {
    if (q && !p.nama.toLowerCase().includes(q) && !(p.rm||'').toLowerCase().includes(q)) return false;
    if (fb && p.bayar !== fb) return false;
    return true;
  });

  const container = document.getElementById('pasienList');
  if (!container) return;

  if (!data.length) {
    container.innerHTML = `<div style="color:var(--text3,#aaa);font-size:13px;padding:24px;text-align:center">
      Belum ada pasien hari ini
    </div>`;
    return;
  }

  container.innerHTML = data.map(p => `
    <div class="pasien-card ${selectedPasien && selectedPasien.id === p.id ? 'selected' : ''}"
         onclick="selectPasien('${p.id}', 'pasien')"
         style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:1px solid var(--cream3,#eee);margin-bottom:8px;cursor:pointer;background:${selectedPasien && selectedPasien.id === p.id ? 'var(--blue-light,#EBF4FF)' : '#fff'}">
      <div style="width:38px;height:38px;border-radius:50%;background:var(--purple-light,#EEEDFE);color:var(--purple2,#534AB7);font-weight:600;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        ${p.nama.charAt(0)}
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:500;font-size:14px">${p.nama}</div>
        <div style="font-size:12px;color:var(--text2,#888);margin-top:1px">${p.rm} · ${p.usia ? p.usia+' thn' : ''} · ${p.jk === 'L' ? 'Laki-laki' : 'Perempuan'}</div>
        <div style="margin-top:5px;display:flex;gap:6px;flex-wrap:wrap">
          ${badgeBayar(p.bayar)}
          <span class="badge ${p.status==='Menunggu'?'b-baru':p.status==='Diperiksa'?'b-validasi':'b-selesai'}">${p.status}</span>
        </div>
      </div>
    </div>
  `).join('');
}

function selectPasien(id, context='pasien') {
  selectedPasien = (typeof pasienData !== 'undefined' ? pasienData : []).find(p => p.id === id);
  if (context === 'pasien') renderPasienDetail();
  renderPasienList();
}

// ===================== DATA PASIEN — DETAIL =====================
function renderPasienDetail() {
  const container = document.getElementById('pasienDetail');
  if (!container) return;
  const p = selectedPasien;
  if (!p) {
    container.innerHTML = `<div class="empty-state card" style="text-align:center;padding:40px 20px;color:var(--text3,#aaa)">
      <div style="font-size:32px;margin-bottom:10px">👤</div>
      <div>Pilih pasien untuk melihat detail</div>
    </div>`;
    return;
  }

  const resepPasien = (typeof resepData !== 'undefined' ? resepData : []).filter(r => r.pasienId === p.id);

  // Update status ke Diperiksa di server
  fetch(`/dokter/api/pasien/${p.id}/status`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
    body: JSON.stringify({ status: 'Diperiksa' })
  }).catch(() => {});

  container.innerHTML = `
    <div style="background:#fff;border-radius:14px;border:1px solid var(--cream3,#eee);overflow:hidden">
      <!-- Header -->
      <div style="padding:16px 18px;border-bottom:1px solid var(--cream3,#eee);background:var(--cream,#faf9f7)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
          <div style="font-size:17px;font-weight:600;font-family:'Cormorant Garamond',serif">${p.nama}</div>
          ${badgeBayar(p.bayar)}
        </div>
        <div style="font-size:12px;color:var(--text2,#888)">${p.rm} · Dikirim oleh Admin</div>
      </div>

      <!-- Body -->
      <div style="padding:16px 18px">

        <!-- Info Grid -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px">
          ${infoItem('Usia', (p.usia ? p.usia+' tahun' : '-'))}
          ${infoItem('Jenis Kelamin', p.jk === 'L' ? 'Laki-laki' : 'Perempuan')}
          ${infoItem('Poliklinik', p.poli || '-')}
          ${infoItem('Tekanan Darah', p.td || '-')}
          ${infoItem('Berat Badan', p.bb ? p.bb+' kg' : '-')}
          ${infoItem('Tinggi Badan', p.tb ? p.tb+' cm' : '-')}
        </div>

        <!-- Keluhan -->
        <div style="background:var(--cream,#faf9f7);border-radius:8px;padding:10px 12px;margin-bottom:10px;font-size:13px">
          <div style="font-size:11px;color:var(--text3,#aaa);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Keluhan</div>
          <div>${p.keluhan || '-'}</div>
        </div>

        <!-- Alergi (tampil hanya kalau ada) -->
        ${p.alergi && p.alergi !== '-' ? `
        <div style="background:#FFF3F3;border:1px solid #f7c1c1;border-radius:8px;padding:10px 12px;margin-bottom:10px;font-size:13px;color:#A32D2D">
          ⚠️ <strong>Alergi:</strong> ${p.alergi}
        </div>` : ''}

        <!-- Riwayat Penyakit -->
        ${p.riwayat && p.riwayat !== '-' ? `
        <div style="background:#EBF4FF;border-radius:8px;padding:10px 12px;margin-bottom:10px;font-size:13px">
          <div style="font-size:11px;color:var(--text3,#aaa);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Riwayat Penyakit</div>
          <div>${p.riwayat}</div>
        </div>` : ''}

        <!-- Resep Sebelumnya -->
        <div style="font-size:11px;color:var(--text3,#aaa);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
          Resep Sebelumnya
        </div>
        ${resepPasien.length
          ? resepPasien.slice(0,3).map(r => `
            <div style="border:1px solid var(--cream3,#eee);border-radius:8px;padding:9px 12px;margin-bottom:6px;font-size:13px;display:flex;justify-content:space-between;align-items:center">
              <div>
                <div style="font-weight:500">${r.id||r.no_resep||'-'}</div>
                <div style="font-size:11px;color:var(--text3,#aaa)">${r.diagnosa} · ${formatDate(r.tanggal||r.created_at)}</div>
              </div>
              ${badgeStatus(r.status)}
            </div>`).join('')
          : `<div style="color:var(--text3,#aaa);font-size:13px;padding:8px 0">Belum ada resep</div>`
        }

        <!-- Tombol Buat Resep -->
        <button onclick="startResepForPasien('${p.id}')"
          style="width:100%;margin-top:12px;padding:10px;background:var(--purple,#534AB7);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:500;cursor:pointer;letter-spacing:.02em">
          ✍️ Buat Resep untuk Pasien Ini
        </button>
      </div>
    </div>
  `;
}

function infoItem(label, value) {
  return `<div style="background:var(--cream,#faf9f7);border-radius:8px;padding:9px 11px">
    <div style="font-size:10px;color:var(--text3,#aaa);text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">${label}</div>
    <div style="font-size:13px;font-weight:500">${value}</div>
  </div>`;
}

function startResepForPasien(id) {
  selectedPasien = (typeof pasienData !== 'undefined' ? pasienData : []).find(p => p.id === id);
  // Kalau halaman prescription sudah terbuka
  if (document.getElementById('sec-buat-resep')) {
    showSection('buat-resep');
    // Langsung ke step 2
    setTimeout(() => { if (selectedPasien) { pilihPasienResep(id); } }, 50);
  } else {
    // Simpan ID di sessionStorage dan pindah ke halaman prescription
    sessionStorage.setItem('selectedPasienId', id);
    window.location.href = '/dokter/prescription';
  }
}

// ===================== BUAT RESEP =====================
function renderResepPasienList() {
  const q = (document.getElementById('resepPasienSearch')?.value || '').toLowerCase();
  let data = (typeof pasienData !== 'undefined' ? pasienData : []).filter(p => p.status !== 'Selesai');
  if (q) data = data.filter(p => p.nama.toLowerCase().includes(q) || (p.rm||'').toLowerCase().includes(q));

  const container = document.getElementById('resepPasienList');
  if (!container) return;

  container.innerHTML = data.map(p => `
    <div class="pasien-card ${selectedPasien && selectedPasien.id === p.id ? 'selected' : ''}"
         onclick="pilihPasienResep('${p.id}')"
         style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:1px solid var(--cream3,#eee);margin-bottom:8px;cursor:pointer">
      <div style="width:36px;height:36px;border-radius:50%;background:var(--purple-light,#EEEDFE);color:var(--purple2,#534AB7);font-weight:600;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        ${p.nama.charAt(0)}
      </div>
      <div style="flex:1">
        <div style="font-weight:500;font-size:14px">${p.nama}</div>
        <div style="font-size:12px;color:var(--text2,#888)">${p.rm} · ${p.usia ? p.usia+' thn' : ''}</div>
        <div style="margin-top:4px;display:flex;gap:6px">
          ${badgeBayar(p.bayar)}
          <span class="badge b-baru">${p.status}</span>
        </div>
      </div>
      ${p.alergi && p.alergi !== '-' ? `<span style="font-size:10px;color:#A32D2D;background:#FFF3F3;padding:2px 7px;border-radius:4px;flex-shrink:0">⚠ Alergi</span>` : ''}
    </div>
  `).join('') || `<div style="color:var(--text3,#aaa);font-size:13px;padding:20px;text-align:center">Semua pasien selesai diperiksa</div>`;
}

function pilihPasienResep(id) {
  selectedPasien = (typeof pasienData !== 'undefined' ? pasienData : []).find(p => p.id === id);
  currentObatList = [];
  activateStep2();
}

function activateStep2() {
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  if (!step1 || !step2 || !selectedPasien) return;
  step1.style.display = 'none';
  step2.style.display = 'block';
  const p = selectedPasien;

  const infoEl = document.getElementById('selectedPasienInfo');
  if (infoEl) {
    infoEl.innerHTML = `
      <div style="display:flex;align-items:center;gap:12px">
        <div style="width:36px;height:36px;border-radius:50%;background:var(--purple-light,#EEEDFE);color:var(--purple2,#534AB7);font-weight:600;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          ${p.nama.charAt(0)}
        </div>
        <div>
          <div style="font-weight:600;font-size:14px">${p.nama}</div>
          <div style="font-size:12px;color:var(--text2,#888)">${p.rm} · ${p.usia ? p.usia+' thn' : ''} · ${p.td || '-'}</div>
          ${p.alergi && p.alergi !== '-' ? `<div style="font-size:11px;color:#A32D2D;margin-top:3px">⚠️ Alergi: ${p.alergi}</div>` : ''}
        </div>
        <div style="margin-left:auto">${badgeBayar(p.bayar)}</div>
      </div>`;
  }

  const bayarEl = document.getElementById('bayarInfo');
  if (bayarEl) {
    const isBPJS = p.bayar === 'BPJS';
    bayarEl.innerHTML = isBPJS
      ? `<div style="background:#E1F5EE;border:1px solid #9FE1CB;border-radius:8px;padding:9px 12px;font-size:12px;color:#0F6E56">🏥 <strong>BPJS:</strong> Resep menggunakan obat standar formularium nasional (FORNAS)</div>`
      : `<div style="background:#EEEDFE;border:1px solid #c5bde8;border-radius:8px;padding:9px 12px;font-size:12px;color:#534AB7">💳 <strong>Mandiri:</strong> Dapat memilih obat standar atau obat premium (branded)</div>`;
  }
  renderObatForm();
  renderResepPreview();
}

function resetResepForm() {
  selectedPasien = null;
  currentObatList = [];
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  if (step1) step1.style.display = 'block';
  if (step2) step2.style.display = 'none';
  const preview = document.getElementById('resepPreviewPanel');
  if (preview) preview.innerHTML = `<div class="card" style="text-align:center;padding:40px 20px;color:var(--text3,#aaa)"><div style="font-size:32px;margin-bottom:10px">✍️</div><div style="font-family:'Cormorant Garamond',serif;font-size:16px">Pilih pasien untuk mulai menulis resep</div></div>`;
  renderResepPasienList();
}

function renderObatForm() {
  const container = document.getElementById('obatListForm');
  if (!container) return;
  if (!currentObatList.length) {
    container.innerHTML = `<div style="color:var(--text3,#aaa);font-size:13px;text-align:center;padding:16px 0;border:1px dashed #ddd;border-radius:8px;margin-bottom:8px">Belum ada obat. Klik tombol di bawah untuk menambah.</div>`;
    return;
  }
  container.innerHTML = currentObatList.map((o, idx) => `
    <div style="border:1px solid ${o.tipe==='premium'?'#c5bde8':'#a0ddd7'};background:${o.tipe==='premium'?'#EEEDFE':'#E1F5EE'};border-radius:10px;padding:12px 14px;margin-bottom:8px;position:relative">
      <button onclick="removeObat(${idx})" style="position:absolute;top:8px;right:8px;border:none;background:transparent;cursor:pointer;color:var(--text3,#aaa);font-size:14px">✕</button>
      <span style="display:inline-block;font-size:10px;padding:2px 8px;border-radius:4px;margin-bottom:6px;background:${o.tipe==='premium'?'#534AB7':'#0F6E56'};color:#fff">
        ${o.tipe==='premium'?'⭐ Premium':'✓ Standar'}
      </span>
      <div style="font-weight:600;font-size:14px;font-family:'Cormorant Garamond',serif">${o.nama}</div>
      <div style="font-size:12px;color:var(--text2,#888);margin-top:3px">${o.dosis} · ${o.durasi} · ${o.instruksi}</div>
      ${o.catatan ? `<div style="font-size:11px;color:var(--text3,#aaa);margin-top:2px">📌 ${o.catatan}</div>` : ''}
    </div>
  `).join('');
  renderResepPreview();
}

function removeObat(idx) {
  currentObatList.splice(idx, 1);
  renderObatForm();
}

function openCatalog() {
  if (!selectedPasien) return;
  const isBPJS = selectedPasien.bayar === 'BPJS';
  const titleEl = document.getElementById('catalogTitle');
  const descEl = document.getElementById('catalogDesc');
  if (titleEl) titleEl.textContent = isBPJS ? 'Katalog Obat BPJS (Formularium)' : 'Katalog Obat';
  if (descEl) descEl.innerHTML = isBPJS
    ? `<div style="background:#E1F5EE;border-radius:6px;padding:8px 10px;font-size:12px;color:#0F6E56">🏥 Hanya obat sesuai <strong>Formularium Nasional (FORNAS)</strong></div>`
    : `<div style="background:#EEEDFE;border-radius:6px;padding:8px 10px;font-size:12px;color:#534AB7">💳 Pasien mandiri dapat pilih <strong>obat standar</strong> atau <strong>premium (branded)</strong></div>`;
  const searchEl = document.getElementById('catalogSearch');
  if (searchEl) searchEl.value = '';
  selectedCatalogObat = null;
  const form = document.getElementById('selectedObatForm');
  const btn = document.getElementById('catalogAddBtn');
  if (form) form.style.display = 'none';
  if (btn) btn.disabled = true;
  renderCatalog();
  openModal('modalCatalog');
}

function renderCatalog() {
  const q = (document.getElementById('catalogSearch')?.value || '').toLowerCase();
  const isBPJS = selectedPasien?.bayar === 'BPJS';
  const catalog = isBPJS ? obatBPJS : obatMandiri;
  const filtered = catalog.filter(o => !q || o.nama.toLowerCase().includes(q) || o.kategori.toLowerCase().includes(q));
  const listEl = document.getElementById('catalogList');
  if (!listEl) return;
  listEl.innerHTML = filtered.map(o => `
    <div class="catalog-item" onclick="selectCatalogObat('${o.nama.replace(/'/g,"\\'")}')">
      <div>
        <div style="font-weight:500">
          ${o.nama}
          ${o.premium ? `<span style="font-size:10px;background:#EEEDFE;color:#534AB7;padding:1px 6px;border-radius:4px;margin-left:4px">Premium</span>` : ''}
        </div>
        <div style="font-size:11px;color:var(--text3,#aaa)">${o.kategori} · ${o.tipe} — ${o.keterangan}</div>
      </div>
      <span style="font-size:11px;color:var(--text3,#aaa)">→</span>
    </div>
  `).join('') || `<div style="padding:20px;text-align:center;color:var(--text3,#aaa);font-size:13px">Tidak ditemukan</div>`;
}

function selectCatalogObat(nama) {
  const isBPJS = selectedPasien?.bayar === 'BPJS';
  const catalog = isBPJS ? obatBPJS : obatMandiri;
  selectedCatalogObat = catalog.find(o => o.nama === nama);
  const nameEl = document.getElementById('selectedObatName');
  const formEl = document.getElementById('selectedObatForm');
  const btn = document.getElementById('catalogAddBtn');
  if (nameEl) nameEl.textContent = nama;
  if (formEl) formEl.style.display = 'block';
  if (btn) btn.disabled = false;
  document.querySelectorAll('.catalog-item').forEach(el => {
    el.style.background = el.textContent.includes(nama) ? 'var(--blue-light,#EBF4FF)' : '';
  });
}

function addObatFromCatalog() {
  if (!selectedCatalogObat) return;
  const dosis = document.getElementById('catalogDosis')?.value.trim() || 'Sesuai petunjuk';
  const durasi = document.getElementById('catalogDurasi')?.value.trim() || 'Sesuai kebutuhan';
  const instruksi = document.getElementById('catalogInstruksi')?.value || 'Sesudah makan';
  const catatan = document.getElementById('catalogCatatan')?.value.trim() || '';
  const isBPJS = selectedPasien?.bayar === 'BPJS';
  const tipe = selectedCatalogObat.premium ? 'premium' : 'standar';
  if (isBPJS && tipe === 'premium') {
    showToast('Obat premium tidak bisa diresepkan untuk pasien BPJS', 'danger');
    return;
  }
  currentObatList.push({ nama: selectedCatalogObat.nama, dosis, durasi, instruksi, tipe, catatan });
  closeModal('modalCatalog');
  renderObatForm();
  showToast(`${selectedCatalogObat.nama} ditambahkan ke resep`);
  if (document.getElementById('catalogDosis')) document.getElementById('catalogDosis').value = '';
  if (document.getElementById('catalogDurasi')) document.getElementById('catalogDurasi').value = '';
  if (document.getElementById('catalogCatatan')) document.getElementById('catalogCatatan').value = '';
}

function renderResepPreview() {
  const container = document.getElementById('resepPreviewPanel');
  if (!container || !selectedPasien) return;
  const p = selectedPasien;
  const diagnosa = document.getElementById('rDiagnosa')?.value || '—';
  container.innerHTML = `
    <div class="card" style="position:sticky;top:0">
      <div class="card-title">Preview Resep</div>
      <div style="background:var(--cream,#faf9f7);border-radius:8px;padding:12px;margin-bottom:12px">
        <div style="font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:600">${p.nama}</div>
        <div style="font-size:12px;color:var(--text2,#888)">${p.rm} · ${p.usia ? p.usia+' thn' : ''} · ${p.td || '-'}</div>
        <div style="margin-top:6px">${badgeBayar(p.bayar)}</div>
      </div>
      <div style="font-size:11px;color:var(--text3,#aaa);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
        Obat (${currentObatList.length})
      </div>
      ${currentObatList.length
        ? currentObatList.map(o => `
          <div style="border-left:3px solid ${o.tipe==='premium'?'#534AB7':'#0F6E56'};padding:8px 10px;margin-bottom:6px;background:var(--cream,#faf9f7);border-radius:0 8px 8px 0">
            <div style="font-weight:500;font-size:13px">${o.nama}</div>
            <div style="font-size:11px;color:var(--text2,#888)">${o.dosis} · ${o.durasi} · ${o.instruksi}</div>
          </div>`).join('')
        : `<div style="color:var(--text3,#aaa);font-size:13px;padding:8px 0">Belum ada obat</div>`
      }
      <div style="border-top:1px solid var(--cream3,#eee);margin-top:12px;padding-top:10px;font-size:12px;color:var(--text3,#aaa)">
        ${document.querySelector('meta[name="dokter-name"]')?.content || 'Dokter'} · ${new Date().toLocaleDateString('id-ID')}
      </div>
    </div>`;
}

function simpanDraft() {
  if (!selectedPasien || !currentObatList.length) {
    showToast('Pilih pasien dan minimal 1 obat', 'danger');
    return;
  }
  const payload = buildResepPayload('draft');
  saveResep(payload);
}

function kirimResep() {
  if (!selectedPasien) { showToast('Pilih pasien terlebih dahulu', 'danger'); return; }
  if (!currentObatList.length) { showToast('Tambahkan minimal 1 obat', 'danger'); return; }
  const diagnosa = document.getElementById('rDiagnosa')?.value.trim();
  if (!diagnosa) { showToast('Isi diagnosa terlebih dahulu', 'danger'); return; }
  const payload = buildResepPayload('baru');
  saveResep(payload);
}

function buildResepPayload(status) {
  return {
    pasien_id:       selectedPasien.id,
    diagnosa:        document.getElementById('rDiagnosa')?.value.trim() || '-',
    catatan_dokter:  document.getElementById('rCatatanApoteker')?.value.trim() || '',
    tanggal_kontrol: document.getElementById('rKontrol')?.value || null,
    status:          status,
    obat_list:       currentObatList,
  };
}

function saveResep(payload) {
  fetch('/dokter/api/resep/store', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
    body: JSON.stringify(payload)
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showToast(
        payload.status === 'baru'
          ? `Resep ${data.no_resep} berhasil dikirim ke Apoteker! 📤`
          : `Draft ${data.no_resep} disimpan`,
        'success'
      );
      resetResepForm();
    } else {
      showToast('Gagal menyimpan resep: ' + (data.message || 'Unknown error'), 'danger');
    }
  })
  .catch(err => showToast('Koneksi error: ' + err.message, 'danger'));
}

// ===================== INIT =====================
document.addEventListener('DOMContentLoaded', () => {
  // Modal overlay dismiss
  document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
  });

  // Auto-init halaman
  if (document.getElementById('pasienList')) renderPasienList();
  if (document.getElementById('resepPasienList')) {
    renderResepPasienList();
    // Kalau dari halaman data pasien (bawa selectedPasienId)
    const savedId = sessionStorage.getItem('selectedPasienId');
    if (savedId && typeof pasienData !== 'undefined') {
      sessionStorage.removeItem('selectedPasienId');
      selectedPasien = pasienData.find(p => p.id === savedId);
      if (selectedPasien) pilihPasienResep(savedId);
    }
  }
});

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pharmabee</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --cream:#F7F2EA;--cream2:#EEE7D8;--cream3:#E4DDD0;--cream4:#D5CBBA;
  --purple:#8B7DB8;--purple2:#6B5E9A;--purple3:#4A3F7A;--purple-light:#EDEBF8;--purple-pale:#F5F3FD;
  --teal:#2A9D8F;--teal2:#1F7A6F;--teal-light:#E0F5F2;
  --gold:#C9972A;--gold2:#A07820;--gold-light:#FDF3E0;
  --text:#2C2416;--text2:#6B5E4E;--text3:#A8998A;--text4:#C8BEB4;
  --white:#FDFAF5;--white2:#FAF6EF;
  --green:#52B788;--red:#E63946;--amber:#F4A261;
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--cream);color:var(--text);overflow-x:hidden;min-height:100vh}

/* ── TOPNAV ── */
.topnav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(253,250,245,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--cream3);height:60px;display:flex;align-items:center;padding:0 48px;justify-content:space-between}
.logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.logo-text .l1{font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:600;letter-spacing:.2em;color:var(--text);line-height:1.1}
.logo-text .l2{font-family:'Cormorant Garamond',serif;font-size:13px;font-weight:400;letter-spacing:.2em;color:var(--text2);line-height:1.1}
.nav-links{display:flex;align-items:center;gap:28px}
.nav-link{font-size:14px;color:var(--text2);text-decoration:none;transition:color .15s}
.nav-link:hover{color:var(--text)}

/* ── HERO ── */
.hero{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:80px 48px 60px;position:relative;overflow:hidden;flex-direction:column;gap:0}
.hero-bg-circle1{position:absolute;width:700px;height:700px;border-radius:50%;background:var(--purple-light);opacity:.4;top:-200px;right:-200px;z-index:0}
.hero-bg-circle2{position:absolute;width:400px;height:400px;border-radius:50%;background:var(--teal-light);opacity:.45;bottom:-100px;left:-120px;z-index:0}
.hero-bg-grain{position:absolute;inset:0;z-index:0;opacity:.025;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E")}

.hero-top{position:relative;z-index:1;text-align:center;max-width:680px;margin-bottom:64px}
.hero-tag{display:inline-flex;align-items:center;gap:7px;background:var(--purple-light);color:var(--purple2);padding:5px 16px;border-radius:20px;font-size:12px;font-weight:500;margin-bottom:22px;letter-spacing:.04em}
.tag-dot{width:6px;height:6px;border-radius:50%;background:var(--purple);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
.hero-title{font-family:'Cormorant Garamond',serif;font-size:64px;font-weight:600;line-height:1.05;color:var(--text);margin-bottom:18px}
.hero-title em{font-style:italic;color:var(--purple2)}
.hero-sub{font-size:16px;color:var(--text2);line-height:1.75;max-width:500px;margin:0 auto}

/* ── ROLE CARDS ── */
.cards-wrap{position:relative;z-index:1;display:flex;gap:24px;width:100%;max-width:900px;align-items:flex-start}

.role-card{flex:1;background:var(--white);border:1px solid var(--cream3);border-radius:20px;overflow:hidden;transition:all .35s cubic-bezier(.4,0,.2,1);cursor:pointer;position:relative;padding-bottom:10px;}
.role-card:hover{border-color:var(--cream4)}
.role-card.active{flex:1.8;cursor:default}
.role-card.other{flex:.55;opacity:.7}
.role-card.other:hover{opacity:.9}

/* card accent bar */
.role-card::after{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:20px 20px 0 0;opacity:0;transition:opacity .3s}
.role-card.active::after{opacity:1}
.role-card.dokter::after{background:var(--purple)}
.role-card.apoteker::after{background:var(--teal)}
.role-card.admin::after{background:var(--gold)}

/* card header — always visible */
.card-head{padding:26px 26px 20px;display:flex;align-items:flex-start;gap:14px;transition:padding .35s}
.card-icon{width:46px;height:46px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .3s}
.ci-purple{background:var(--purple-light)}
.ci-teal{background:var(--teal-light)}
.ci-gold{background:var(--gold-light)}
.card-head-text{flex:1;min-width:0}
.card-role-label{font-size:11px;font-weight:500;letter-spacing:.08em;text-transform:uppercase;margin-bottom:5px;transition:color .3s}
.cl-purple{color:var(--purple2)}
.cl-teal{color:var(--teal2)}
.cl-gold{color:var(--gold2)}
.card-title{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:600;color:var(--text);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.card-sub{font-size:12px;color:var(--text3);line-height:1.6;max-height:0;overflow:hidden;transition:max-height .35s ease,opacity .3s}
.role-card.active .card-sub{max-height:70px;opacity:1}
.role-card:not(.active) .card-sub{opacity:0}

/* perms strip — visible when inactive */
.card-perms{padding:0 26px 20px;display:flex;flex-direction:column;gap:5px;max-height:120px;overflow:hidden;transition:max-height .35s,opacity .35s}
.role-card.active .card-perms{max-height:0;opacity:0;padding-bottom:0}
.perm-row{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--text2); margin-bottom:15px}
.perm-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0}

/* divider */
.card-divider{height:1px;background:var(--cream3);margin:0 26px;max-height:1px;overflow:hidden;transition:max-height .35s,opacity .35s}
.role-card:not(.active) .card-divider{max-height:0;opacity:0}

/* form body */
.card-form{padding:0 26px;max-height:0;overflow:hidden;transition:max-height .5s cubic-bezier(.4,0,.2,1),padding .35s;}
.role-card.active .card-form{max-height:600px;padding:20px 26px 28px}

.fg{margin-bottom:14px}
.fg label{display:block;font-size:10px;color:var(--text2);margin-bottom:5px;font-weight:500;text-transform:uppercase;letter-spacing:.06em}
.fg input{width:100%;padding:10px 14px;border-radius:9px;border:1px solid var(--cream3);background:var(--cream);font-size:14px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;transition:border-color .15s,background .15s}
.fg input:focus{border-color:var(--purple);background:var(--white)}
.role-card.apoteker .fg input:focus{border-color:var(--teal)}
.input-wrap{position:relative}
.input-wrap input{padding-right:42px}
.eye-btn{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text3);font-size:13px;padding:0;line-height:1}
.eye-btn:hover{color:var(--text2)}
.form-footer-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.remember-row{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text2);cursor:pointer}
.remember-row input[type=checkbox]{width:14px;height:14px;cursor:pointer}
.forgot-link{font-size:12px;text-decoration:none;transition:color .15s}
.fl-purple{color:var(--purple2)}
.fl-purple:hover{color:var(--purple3)}
.fl-teal{color:var(--teal2)}
.fl-teal:hover{color:var(--teal2)}
.fl-gold{color:var(--gold2)}
.fl-gold:hover{color:var(--gold2)}
.submit-btn{width:100%;padding:12px;border-radius:10px;font-size:15px;font-family:'DM Sans',sans-serif;font-weight:500;cursor:pointer;border:none;transition:all .2s;letter-spacing:.01em}
.sb-purple{background:var(--purple);color:white}
.sb-purple:hover{background:var(--purple2)}
.sb-teal{background:var(--teal);color:white}
.sb-teal:hover{background:var(--teal2)}
.sb-gold{background:var(--gold);color:white}
.sb-gold:hover{background:var(--gold2)}
.form-switch{text-align:center;font-size:12px;color:var(--text3);margin-top:14px}
.form-switch a{font-weight:500;cursor:pointer;text-decoration:none;transition:color .15s}
.fsa-purple{color:var(--purple2)}
.fsa-purple:hover{color:var(--purple3)}
.fsa-teal{color:var(--teal2)}
.fsa-teal:hover{color:var(--teal2)}
.fsa-gold{color:var(--gold2)}
.fsa-gold:hover{color:var(--gold2)}

/* click hint */
.click-hint{position:absolute;bottom:14px;right:18px;font-size:12px;color:var(--text3);display:flex;align-items:center;gap:6px;opacity:.75;pointer-events:none;transition:all .25s ease;}

/* hover */
.role-card:hover .click-hint{opacity:1;transform:translateX(4px);}

/* hilang pas active */
.role-card.active .click-hint{opacity:0;transform:translateX(8px);}
.hint-arrow{font-size:10px}

/* ── FEATURES ── */
.features{padding:80px 48px;background:var(--white2)}
.feat-header{text-align:center;margin-bottom:48px}
.feat-tag{font-size:11px;font-weight:500;color:var(--purple2);text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px}
.feat-title{font-family:'Cormorant Garamond',serif;font-size:38px;font-weight:600;color:var(--text);line-height:1.15}
.feat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;max-width:900px;margin:0 auto}
.feat-item{background:var(--white);border:1px solid var(--cream3);border-radius:14px;padding:22px}
.feat-icon-wrap{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px}
.feat-name{font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:600;margin-bottom:6px}
.feat-desc{font-size:12px;color:var(--text2);line-height:1.7}

/* ── FOOTER ── */
.footer{background:var(--text);padding:32px 48px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.footer-logo .fl1{font-family:'Cormorant Garamond',serif;font-size:14px;font-weight:600;letter-spacing:.2em;color:var(--cream2)}
.footer-logo .fl2{font-family:'Cormorant Garamond',serif;font-size:12px;letter-spacing:.15em;color:var(--text3)}
.footer-copy{font-size:12px;color:var(--text3)}

/* ── TOAST ── */
.toast{position:fixed;bottom:24px;right:24px;z-index:999;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif;max-width:300px;color:white;transition:opacity .3s;animation:slideToast .2s ease}
@keyframes slideToast{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.toast-success{background:var(--green)}
.toast-danger{background:var(--red)}

@media(max-width:800px){.hero-title{font-size:44px}.cards-wrap{flex-direction:column}.role-card.other{flex:1;opacity:1}.topnav{padding:0 24px}
  .nav-links{display:none}.hero{padding:80px 24px 48px}.features{padding:60px 24px}.feat-grid{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>

<!-- TOPNAV -->
<nav class="topnav">
  <a href="#" class="logo">
    <svg width="34" height="34" viewBox="0 0 34 34" fill="none">
      <path d="M17 3C17 3 10 10 10 18C10 22.4 13.1 26 17 26C20.9 26 24 22.4 24 18C24 10 17 3 17 3Z" fill="#8B7DB8" opacity=".75"/>
      <path d="M12 14C12 14 8.5 18 8.5 22C8.5 24.8 10.7 27 13.5 27" stroke="#6B5E9A" stroke-width="1.8" stroke-linecap="round" fill="none"/>
    </svg>
    <div class="logo-text">
      <div class="l1">PHARMBEE</div>
    </div>
  </a>
  <div class="nav-links">
    <a class="nav-link" href="#features">Fitur</a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg-circle1"></div>
  <div class="hero-bg-circle2"></div>
  <div class="hero-bg-grain"></div>

  <div class="hero-top">
    <div class="hero-tag"><div class="tag-dot"></div>Sistem Manajemen Farmasi RS</div>
    <h1 class="hero-title">Farmasi lebih <em>rapi,</em><br>lebih aman.</h1>
    <p class="hero-sub">Platform terintegrasi untuk apoteker dan dokter — stok FEFO, resep digital, pembayaran Mandiri & BPJS.</p>
  </div>

  <div class="cards-wrap">

    <!-- ── DOKTER CARD ── -->
    <div class="role-card dokter active" id="cardDokter" onclick="activateCard('dokter')">
      <div class="card-head">
        <div class="card-icon ci-purple">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <circle cx="11" cy="8" r="4" stroke="#8B7DB8" stroke-width="1.4"/>
            <path d="M4 19c0-3.3 3.1-6 7-6s7 2.7 7 6" stroke="#8B7DB8" stroke-width="1.4" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="card-head-text">
          <div class="card-role-label cl-purple">Role</div>
          <div class="card-title">Dokter</div>
          <div class="card-sub">Tulis & kirim resep digital langsung ke apoteker tanpa kertas.</div>
        </div>
      </div>
      <div class="card-perms">
        <div class="perm-row"><div class="perm-dot" style="background:var(--purple)"></div>Tulis resep digital</div>
        <div class="perm-row"><div class="perm-dot" style="background:var(--purple)"></div>Lihat riwayat pasien</div>
        <div class="perm-row" style="opacity:.45"><div class="perm-dot" style="background:var(--text3)"></div>Stok & laporan</div>
      </div>
      <div class="card-divider"></div>
      <div class="card-form" id="formDokter">
        <!-- LOGIN -->
        <form method="POST" action="/login">
          @csrf
          <input type="hidden" name="role" value="dokter">

          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" placeholder="dokter@rs.id">
          </div>

          <div class="fg">
            <label>Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="dPass" placeholder="••••••••">
              <button class="eye-btn" onclick="togglePass('dPass',this)" type="button">👁</button>
            </div>
          </div>

          <div class="form-footer-row">
            <label class="remember-row">
              <input type="checkbox" name="remember"> Ingat saya
            </label>
            <a class="forgot-link fl-purple" href="#">Lupa password?</a>
          </div>

          <button type="submit" class="submit-btn sb-purple">
            Masuk sebagai Dokter
          </button>
        </form>

      </div>
      <div class="click-hint">Klik untuk masuk <span class="hint-arrow">→</span></div>
    </div>

    <!-- ── APOTEKER CARD ── -->
    <div class="role-card apoteker" id="cardApoteker" onclick="activateCard('apoteker')">
      <div class="card-head">
        <div class="card-icon ci-teal">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <path d="M11 2C11 2 6 6.5 6 11C6 13.8 8.2 16 11 16C13.8 16 16 13.8 16 11C16 6.5 11 2 11 2Z" fill="#2A9D8F" opacity=".7"/>
            <path d="M8 9C8 9 6 11 6 13.5" stroke="#1F7A6F" stroke-width="1.4" stroke-linecap="round" fill="none"/>
            <path d="M8.5 18.5h5M11 16v2.5" stroke="#2A9D8F" stroke-width="1.3" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="card-head-text">
          <div class="card-role-label cl-teal">Role</div>
          <div class="card-title">Apoteker</div>
          <div class="card-sub">Kelola stok, validasi resep, dan proses pembayaran Mandiri & BPJS.</div>
        </div>
      </div>
      <div class="card-perms">
        <div class="perm-row"><div class="perm-dot" style="background:var(--teal)"></div>Stok, batch & expired</div>
        <div class="perm-row"><div class="perm-dot" style="background:var(--teal)"></div>Validasi resep (FEFO)</div>
        <div class="perm-row"><div class="perm-dot" style="background:var(--teal)"></div>Pembayaran & laporan</div>
      </div>
      <div class="card-divider"></div>
      <div class="card-form" id="formApoteker">
        <!-- LOGIN -->
        <form method="POST" action="/login">
          @csrf
          <input type="hidden" name="role" value="apoteker">

          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" placeholder="apoteker@rs.id">
          </div>
          
          <div class="fg">
            <label>Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="dPass" placeholder="••••••••">
              <button class="eye-btn" onclick="togglePass('dPass',this)" type="button">👁</button>
            </div>
          </div>

          <button type="submit" type="submit" class="submit-btn sb-teal">Masuk sebagai Apoteker</button>
        </form>

      </div>
      <div class="click-hint">Klik untuk masuk <span class="hint-arrow">→</span></div>
    </div>

    <!-- ── ADMIN CARD ── -->
    <div class="role-card admin" id="cardAdmin" onclick="activateCard('admin')">
      <div class="card-head">
        <div class="card-icon ci-gold">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
            <path d="M12 8.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z" stroke="#A07820" stroke-width="1.5"/>
            <path d="M19.4 15a7.97 7.97 0 0 0 .1-2l2-1.5-2-3.5-2.4 1a8.2 8.2 0 0 0-1.7-1L15 3h-6l-.4 2.9c-.6.3-1.2.6-1.7 1l-2.4-1-2 3.5 2 1.5a7.97 7.97 0 0 0 0 2l-2 1.5 2 3.5 2.4-1c.5.4 1.1.7 1.7 1L9 21h6l.4-2.9c.6-.3 1.2-.6 1.7-1l2.4 1 2-3.5-2-1.5Z" stroke="#A07820" stroke-width="1.3" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-head-text">
          <div class="card-role-label cl-gold">Role</div>
          <div class="card-title">Admin</div>
          <div class="card-sub">Proses pendataan dan pembayaran denganMandiri/BPJS.</div>
        </div>
      </div>
      <div class="card-perms">
        <div class="perm-row"><div class="perm-dot" style="background:var(--gold)"></div>Data Pasien</div>
        <div class="perm-row"><div class="perm-dot" style="background:var(--gold)"></div>Pembayaran</div>
        <div class="perm-row"><div class="perm-dot" style="background:var(--gold)"></div>Laporan Keuangan</div>
      </div>
      <div class="card-divider"></div>
      <div class="card-form" id="formAdmin">
        <!-- LOGIN -->
        <form method="POST" action="/login">
          @csrf
          <input type="hidden" name="role" value="admin">

          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" placeholder="admin@rs.id">
          </div>
          
          <div class="fg">
            <label>Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="dPass" placeholder="••••••••">
              <button class="eye-btn" onclick="togglePass('dPass',this)" type="button">👁</button>
            </div>
          </div>

          <button type="submit" type="submit" class="submit-btn sb-gold">Masuk sebagai Admin</button>
        </form>

      </div>
      <div class="click-hint">Klik untuk masuk <span class="hint-arrow">→</span></div>
    </div>

  </div>
</section>

<!-- FEATURES -->
<section class="features" id="features">
  <div class="feat-header">
    <div class="feat-tag">Fitur unggulan</div>
    <div class="feat-title">Semua yang kamu butuhkan</div>
  </div>
  <div class="feat-grid">
    <div class="feat-item">
      <div class="feat-icon-wrap" style="background:var(--purple-light)">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M9 2C9 2 4.5 6 4.5 10C4.5 12.5 6.5 14.5 9 14.5C11.5 14.5 13.5 12.5 13.5 10C13.5 6 9 2 9 2Z" fill="#8B7DB8" opacity=".7"/></svg>
      </div>
      <div class="feat-name">FEFO Otomatis</div>
      <div class="feat-desc">Batch expired paling awal selalu diambil duluan tanpa pilih manual.</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon-wrap" style="background:var(--teal-light)">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M9 2L16 14H2L9 2Z" stroke="#2A9D8F" stroke-width="1.2" fill="none" stroke-linejoin="round"/><path d="M9 7v3" stroke="#2A9D8F" stroke-width="1.2" stroke-linecap="round"/><circle cx="9" cy="11.5" r=".6" fill="#2A9D8F"/></svg>
      </div>
      <div class="feat-name">Alert Expired</div>
      <div class="feat-desc">Notifikasi real-time untuk obat mendekati atau sudah expired.</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon-wrap" style="background:#EAF3DE">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="2.5" y="3" width="13" height="12" rx="2" stroke="#52B788" stroke-width="1.2"/><path d="M6 9l2.5 2.5L12 6.5" stroke="#52B788" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div class="feat-name">Validasi Resep</div>
      <div class="feat-desc">Alur validasi apoteker jelas. Stok otomatis berkurang saat divalidasi.</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon-wrap" style="background:#FEF3E8">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M2 14L5 9l3.5 3L12 5l2.5 2.5" stroke="#F4A261" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
      </div>
      <div class="feat-name">Reporting</div>
      <div class="feat-desc">Laporan konsumsi, pendapatan, dan tren pemakaian. Ekspor PDF.</div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer" id="footer">
  <div class="footer-logo">
    <div class="fl1">PHARMBEE</div>
    <div class="fl2">Sistem Manajemen Farmasi RS</div>
  </div>
  <div class="footer-copy" style="color:var(--text3);font-size:12px">© 2026 Pharmbee. All rights reserved.</div>
</footer>

<script>
// ── active card ──────────────────────────────────────────
let activeCard = 'dokter';

function activateCard(role) {
  if (activeCard === role) return;
  activeCard = role;
  const cards = ['dokter','apoteker','admin'];
  cards.forEach(r => {
    const el = document.getElementById('card' + cap(r));
    el.classList.remove('active','other');
    if (r === role) el.classList.add('active');
    else el.classList.add('other');
  });
}

function cap(s){ return s.charAt(0).toUpperCase()+s.slice(1); }

// ── eye toggle ───────────────────────────────────────────
function togglePass(id, btn) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
  btn.textContent = el.type === 'password' ? '👁' : '🙈';
  event.stopPropagation();
}

// ── toast ────────────────────────────────────────────────
function toast(msg, type) {
  const el = document.createElement('div');
  el.className = 'toast toast-' + type;
  el.textContent = msg;
  document.body.appendChild(el);
  setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 2600);
}

// ── init: dokter card active by default ──────────────────
document.getElementById('cardApoteker').classList.add('other');
</script>
</body>
</html>

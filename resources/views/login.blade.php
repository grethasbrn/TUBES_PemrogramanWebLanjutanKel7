<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pharmabee — Login</title>
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
  --green:#52B788;--red:#E63946;
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--cream);color:var(--text);min-height:100vh;display:flex;flex-direction:column}

/* ── BG DECOR ── */
.bg-decor{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.bg-circle1{position:absolute;width:600px;height:600px;border-radius:50%;background:var(--purple-light);opacity:.5;top:-180px;right:-150px}
.bg-circle2{position:absolute;width:380px;height:380px;border-radius:50%;background:var(--teal-light);opacity:.5;bottom:-80px;left:-100px}
.bg-grain{position:absolute;inset:0;opacity:.03;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E")}

/* ── TOPNAV ── */
.topnav{position:relative;z-index:10;background:rgba(253,250,245,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--cream3);height:60px;display:flex;align-items:center;padding:0 48px;justify-content:space-between}
.logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.logo-text{font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:600;letter-spacing:.2em;color:var(--text)}
.nav-link{font-size:13px;color:var(--text2);text-decoration:none}
.nav-link:hover{color:var(--text)}

/* ── MAIN ── */
.main{position:relative;z-index:1;flex:1;display:flex;align-items:center;justify-content:center;padding:48px 24px}

.login-box{width:100%;max-width:460px}

.login-header{text-align:center;margin-bottom:36px}
.login-tag{display:inline-flex;align-items:center;gap:7px;background:var(--purple-light);color:var(--purple2);padding:5px 16px;border-radius:20px;font-size:11px;font-weight:500;margin-bottom:16px;letter-spacing:.05em}
.tag-dot{width:6px;height:6px;border-radius:50%;background:var(--purple);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
.login-title{font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:600;color:var(--text);margin-bottom:6px}
.login-sub{font-size:13px;color:var(--text3)}

/* ── ROLE SELECTOR ── */
.role-label{font-size:11px;font-weight:500;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;text-align:center}

.role-buttons{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:28px}

.role-btn{position:relative;background:var(--white);border:2px solid var(--cream3);border-radius:16px;padding:18px 12px 16px;cursor:pointer;transition:all .2s cubic-bezier(.4,0,.2,1);text-align:center;outline:none;width:100%}
.role-btn:hover{border-color:var(--cream4);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.06)}

.role-btn.active-dokter{border-color:var(--purple);background:var(--purple-pale);box-shadow:0 0 0 4px rgba(139,125,184,.12)}
.role-btn.active-apoteker{border-color:var(--teal);background:var(--teal-light);box-shadow:0 0 0 4px rgba(42,157,143,.12)}
.role-btn.active-admin{border-color:var(--gold);background:var(--gold-light);box-shadow:0 0 0 4px rgba(201,151,42,.12)}

.rb-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 10px}
.rb-icon-purple{background:var(--purple-light)}
.rb-icon-teal{background:var(--teal-light)}
.rb-icon-gold{background:var(--gold-light)}

.rb-label{font-size:10px;font-weight:500;letter-spacing:.07em;text-transform:uppercase;margin-bottom:3px}
.lc-purple{color:var(--purple2)}
.lc-teal{color:var(--teal2)}
.lc-gold{color:var(--gold2)}

.rb-name{font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:600;color:var(--text)}

/* active indicator dot */
.rb-dot{width:7px;height:7px;border-radius:50%;margin:8px auto 0;opacity:0;transition:opacity .2s}
.role-btn.active-dokter .rb-dot{background:var(--purple);opacity:1}
.role-btn.active-apoteker .rb-dot{background:var(--teal);opacity:1}
.role-btn.active-admin .rb-dot{background:var(--gold);opacity:1}

/* ── FORM CARD ── */
.form-card{background:var(--white);border:1px solid var(--cream3);border-radius:20px;padding:28px;box-shadow:0 4px 24px rgba(0,0,0,.05);transition:all .3s}
.form-card.border-dokter{border-top:3px solid var(--purple)}
.form-card.border-apoteker{border-top:3px solid var(--teal)}
.form-card.border-admin{border-top:3px solid var(--gold)}

.form-title{font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:600;color:var(--text);margin-bottom:20px;display:flex;align-items:center;gap:8px}
.form-title-dot{width:8px;height:8px;border-radius:50%}
.dot-purple{background:var(--purple)}
.dot-teal{background:var(--teal)}
.dot-gold{background:var(--gold)}

.fg{margin-bottom:16px}
.fg label{display:block;font-size:10px;color:var(--text2);margin-bottom:6px;font-weight:500;text-transform:uppercase;letter-spacing:.06em}
.fg input{width:100%;padding:11px 14px;border-radius:10px;border:1px solid var(--cream3);background:var(--cream);font-size:14px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;transition:border-color .15s,background .15s}
.fg input:focus{background:var(--white)}
.fg input:focus.fi-purple{border-color:var(--purple)}
.fg input:focus.fi-teal{border-color:var(--teal)}
.fg input:focus.fi-gold{border-color:var(--gold)}
/* use JS to add focus class */
.input-wrap{position:relative}
.input-wrap input{padding-right:42px}
.eye-btn{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text3);font-size:13px;padding:0;line-height:1}
.eye-btn:hover{color:var(--text2)}

.submit-btn{width:100%;padding:13px;border-radius:11px;font-size:15px;font-family:'DM Sans',sans-serif;font-weight:500;cursor:pointer;border:none;transition:all .2s;letter-spacing:.01em;margin-top:4px}
.sb-purple{background:var(--purple);color:white}
.sb-purple:hover{background:var(--purple2)}
.sb-teal{background:var(--teal);color:white}
.sb-teal:hover{background:var(--teal2)}
.sb-gold{background:var(--gold);color:white}
.sb-gold:hover{background:var(--gold2)}

/* ── TOAST ── */
.toast{position:fixed;bottom:24px;right:24px;z-index:999;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif;max-width:300px;color:white;transition:opacity .3s;animation:slideToast .2s ease}
@keyframes slideToast{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.toast-success{background:var(--green)}
.toast-danger{background:var(--red)}

/* ── FOOTER ── */
.footer{position:relative;z-index:1;background:var(--text);padding:20px 48px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.footer-logo .fl1{font-family:'Cormorant Garamond',serif;font-size:14px;font-weight:600;letter-spacing:.2em;color:var(--cream2)}
.footer-copy{font-size:12px;color:var(--text3)}

@media(max-width:500px){
  .role-buttons{grid-template-columns:repeat(3,1fr);gap:8px}
  .rb-name{font-size:15px}
  .main{padding:32px 16px}
  .topnav{padding:0 20px}
  .footer{padding:16px 20px}
}
</style>
</head>
<body>

<div class="bg-decor">
  <div class="bg-circle1"></div>
  <div class="bg-circle2"></div>
  <div class="bg-grain"></div>
</div>

<!-- TOPNAV -->
<nav class="topnav">
  <a href="#" class="logo">
    <svg width="30" height="30" viewBox="0 0 34 34" fill="none">
      <path d="M17 3C17 3 10 10 10 18C10 22.4 13.1 26 17 26C20.9 26 24 22.4 24 18C24 10 17 3 17 3Z" fill="#8B7DB8" opacity=".75"/>
      <path d="M12 14C12 14 8.5 18 8.5 22C8.5 24.8 10.7 27 13.5 27" stroke="#6B5E9A" stroke-width="1.8" stroke-linecap="round" fill="none"/>
    </svg>
    <span class="logo-text">PHARMBEE</span>
  </a>
  <a href="#" class="nav-link">Fitur</a>
</nav>

<!-- MAIN -->
<main class="main">
  <div class="login-box">

    <div class="login-header">
      <div class="login-tag"><div class="tag-dot"></div>Sistem Manajemen Farmasi RS</div>
      <div class="login-title">Masuk ke Pharmbee</div>
      <div class="login-sub">Pilih role untuk melanjutkan</div>
    </div>

    <!-- ROLE BUTTONS -->
    <div class="role-label">Pilih Role</div>
    <div class="role-buttons">

      <button class="role-btn active-dokter" id="btnDokter" onclick="selectRole('dokter')">
        <div class="rb-icon rb-icon-purple">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <circle cx="11" cy="8" r="4" stroke="#8B7DB8" stroke-width="1.4"/>
            <path d="M4 19c0-3.3 3.1-6 7-6s7 2.7 7 6" stroke="#8B7DB8" stroke-width="1.4" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="rb-label lc-purple">Role</div>
        <div class="rb-name">Dokter</div>
        <div class="rb-dot"></div>
      </button>

      <button class="role-btn" id="btnApoteker" onclick="selectRole('apoteker')">
        <div class="rb-icon rb-icon-teal">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <path d="M11 2C11 2 6 6.5 6 11C6 13.8 8.2 16 11 16C13.8 16 16 13.8 16 11C16 6.5 11 2 11 2Z" fill="#2A9D8F" opacity=".7"/>
            <path d="M8 9C8 9 6 11 6 13.5" stroke="#1F7A6F" stroke-width="1.4" stroke-linecap="round" fill="none"/>
            <path d="M8.5 18.5h5M11 16v2.5" stroke="#2A9D8F" stroke-width="1.3" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="rb-label lc-teal">Role</div>
        <div class="rb-name">Apoteker</div>
        <div class="rb-dot"></div>
      </button>

      <button class="role-btn" id="btnAdmin" onclick="selectRole('admin')">
        <div class="rb-icon rb-icon-gold">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
            <path d="M12 8.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z" stroke="#A07820" stroke-width="1.5"/>
            <path d="M19.4 15a7.97 7.97 0 0 0 .1-2l2-1.5-2-3.5-2.4 1a8.2 8.2 0 0 0-1.7-1L15 3h-6l-.4 2.9c-.6.3-1.2.6-1.7 1l-2.4-1-2 3.5 2 1.5a7.97 7.97 0 0 0 0 2l-2 1.5 2 3.5 2.4-1c.5.4 1.1.7 1.7 1L9 21h6l.4-2.9c.6-.3 1.2-.6 1.7-1l2.4 1 2-3.5-2-1.5Z" stroke="#A07820" stroke-width="1.3" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="rb-label lc-gold">Role</div>
        <div class="rb-name">Admin</div>
        <div class="rb-dot"></div>
      </button>

    </div>

    <!-- FORM CARD -->
    <div class="form-card border-dokter" id="formCard">

      <!-- DOKTER FORM -->
      <div id="formDokter">
        <div class="form-title">
          <div class="form-title-dot dot-purple"></div>
          Masuk sebagai Dokter
        </div>
        <form method="POST" action="/login">
          @csrf
          <input type="hidden" name="role" value="dokter">
          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" placeholder="dokter@pharmbee.com" id="emailDokter">
          </div>
          <div class="fg">
            <label>Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="passDokter" placeholder="••••••••">
              <button class="eye-btn" onclick="togglePass('passDokter',this,event)" type="button">👁</button>
            </div>
          </div>
          <button type="submit" class="submit-btn sb-purple">Masuk sebagai Dokter</button>
        </form>
      </div>

      <!-- APOTEKER FORM -->
      <div id="formApoteker" style="display:none">
        <div class="form-title">
          <div class="form-title-dot dot-teal"></div>
          Masuk sebagai Apoteker
        </div>
        <form method="POST" action="/login">
          @csrf
          <input type="hidden" name="role" value="apoteker">
          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" placeholder="apoteker@pharmbee.com">
          </div>
          <div class="fg">
            <label>Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="passApoteker" placeholder="••••••••">
              <button class="eye-btn" onclick="togglePass('passApoteker',this,event)" type="button">👁</button>
            </div>
          </div>
          <button type="submit" class="submit-btn sb-teal">Masuk sebagai Apoteker</button>
        </form>
      </div>

      <!-- ADMIN FORM -->
      <div id="formAdmin" style="display:none">
        <div class="form-title">
          <div class="form-title-dot dot-gold"></div>
          Masuk sebagai Admin
        </div>
        <form method="POST" action="/login">
          @csrf
          <input type="hidden" name="role" value="admin">
          <div class="fg">
            <label>Email</label>
            <input type="email" name="email" placeholder="admin@pharmbee.com">
          </div>
          <div class="fg">
            <label>Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="passAdmin" placeholder="••••••••">
              <button class="eye-btn" onclick="togglePass('passAdmin',this,event)" type="button">👁</button>
            </div>
          </div>
          <button type="submit" class="submit-btn sb-gold">Masuk sebagai Admin</button>
        </form>
      </div>

    </div>
  </div>
</main>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-logo">
    <div class="fl1">PHARMBEE</div>
  </div>
  <div class="footer-copy">© 2026 Pharmbee. All rights reserved.</div>
</footer>

<script>
let activeRole = 'dokter';

function selectRole(role) {
  if (activeRole === role) return;
  activeRole = role;

  // reset all buttons
  ['dokter','apoteker','admin'].forEach(r => {
    const btn = document.getElementById('btn' + cap(r));
    btn.className = 'role-btn';
  });

  // activate selected
  const btn = document.getElementById('btn' + cap(role));
  btn.classList.add('active-' + role);

  // show correct form
  ['formDokter','formApoteker','formAdmin'].forEach(id => {
    document.getElementById(id).style.display = 'none';
  });
  document.getElementById('form' + cap(role)).style.display = 'block';

  // update card border
  const card = document.getElementById('formCard');
  card.className = 'form-card border-' + role;
}

function cap(s){ return s.charAt(0).toUpperCase()+s.slice(1); }

function togglePass(id, btn, e) {
  e.stopPropagation();
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
  btn.textContent = el.type === 'password' ? '👁' : '🙈';
}

function toast(msg, type) {
  const el = document.createElement('div');
  el.className = 'toast toast-' + type;
  el.textContent = msg;
  document.body.appendChild(el);
  setTimeout(() => { el.style.opacity='0'; setTimeout(()=>el.remove(),300); }, 2600);
}

document.querySelectorAll("form").forEach(form => {
  form.addEventListener("submit", function(e) {
    const email = form.querySelector("input[name='email']").value.trim();
    const password = form.querySelector("input[name='password']").value.trim();
    if (!email || !password) { e.preventDefault(); toast("Email dan password wajib diisi!", "danger"); return; }
    if (!email.includes("@")) { e.preventDefault(); toast("Format email tidak valid!", "danger"); return; }
  });
});

@if(session('error'))
  toast("{{ session('error') }}", "danger");
@endif
@if(session('success'))
  toast("{{ session('success') }}", "success");
@endif
</script>
</body>
</html>
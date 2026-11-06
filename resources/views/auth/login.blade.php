<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>Login | Sistem Absensi PTPN4 Regional 7</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Fraunces:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;width:100%;}

:root{
  --forest:#003d22;
  --emerald:#005c34;
  --jade:#00844d;
  --gold:#f5c842;
  --cream:#f8f9f5;
  --white:#ffffff;
  --ink:#0f1a0f;
  --muted:#718875;
  --border:#dce8e0;
  --ok:#22a86e;
  --err:#d94040;
  --ease:cubic-bezier(0.4,0,0.2,1);
}

body{
  font-family:'Plus Jakarta Sans',sans-serif;
  min-height:100dvh;
  background:linear-gradient(145deg,#002616 0%,#004d2c 45%,#006b3f 100%);
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
  position:relative;
  overflow-x:hidden;
}

/* Background effects */
.bg-orb{
  position:fixed;border-radius:50%;pointer-events:none;will-change:transform;z-index:0;
}
.bg-orb-1{
  width:600px;height:600px;top:-200px;right:-200px;
  background:radial-gradient(circle,rgba(245,200,66,.10) 0%,transparent 65%);
  animation:drift 14s ease-in-out infinite;
}
.bg-orb-2{
  width:450px;height:450px;bottom:-180px;left:-150px;
  background:radial-gradient(circle,rgba(0,160,96,.15) 0%,transparent 65%);
  animation:drift 18s ease-in-out infinite reverse;
}
.bg-grid{
  position:fixed;inset:0;pointer-events:none;z-index:0;
  background-image:
    linear-gradient(rgba(255,255,255,.022) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.022) 1px,transparent 1px);
  background-size:48px 48px;
}
@keyframes drift{
  0%,100%{transform:translate(0,0) scale(1);}
  50%{transform:translate(-22px,26px) scale(1.07);}
}

/* ── CARD ── */
.card{
  position:relative;z-index:1;
  width:100%;max-width:560px;
  background:var(--cream);
  border-radius:20px;
  overflow:hidden;
  box-shadow:0 28px 70px rgba(0,0,0,.36),0 0 0 1px rgba(255,255,255,.06);
  display:grid;
  grid-template-columns:1fr 1fr;
  animation:cardIn .65s var(--ease) both;
}
@keyframes cardIn{
  from{opacity:0;transform:translateY(24px) scale(.97);}
  to{opacity:1;transform:translateY(0) scale(1);}
}

/* ── LEFT PANEL ── */
.left{
  background:linear-gradient(155deg,#002616 0%,#004d2c 55%,#005e38 100%);
  padding:22px 20px;
  display:flex;flex-direction:column;justify-content:center;
  position:relative;overflow:hidden;
}
.left-orb-1{
  position:absolute;width:280px;height:280px;top:-90px;right:-90px;
  background:radial-gradient(circle,rgba(245,200,66,.12) 0%,transparent 70%);
  border-radius:50%;pointer-events:none;
}
.left-orb-2{
  position:absolute;width:210px;height:210px;bottom:-70px;left:-50px;
  background:radial-gradient(circle,rgba(0,160,96,.18) 0%,transparent 70%);
  border-radius:50%;pointer-events:none;
}
.left-grid{
  position:absolute;inset:0;pointer-events:none;
  background-image:
    linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
  background-size:36px 36px;
}

.left-content{
  position:relative;z-index:1;
  text-align:center;
  display:flex;
  flex-direction:column;
  align-items:center;
}

/* Logo */
.logo-wrap{
  display:inline-flex;justify-content:center;position:relative;margin-bottom:12px;
  animation:fadeDown .6s var(--ease) both;
}
.logo-ring{
  width:48px;height:48px;
  background:var(--white);border-radius:15px;
  display:flex;align-items:center;justify-content:center;padding:7px;
  box-shadow:0 0 0 1.5px rgba(245,200,66,.4),0 0 0 5px rgba(245,200,66,.08),0 12px 22px rgba(0,0,0,.28);
  transition:transform .35s var(--ease),box-shadow .35s;
}
.logo-ring:hover{
  transform:translateY(-3px) scale(1.04);
  box-shadow:0 0 0 1.5px rgba(245,200,66,.6),0 0 0 10px rgba(245,200,66,.10),0 22px 40px rgba(0,0,0,.34);
}
.logo-ring img{width:100%;height:100%;object-fit:contain;}
.logo-dot{
  position:absolute;bottom:-2px;right:-2px;
  width:11px;height:11px;background:var(--gold);border-radius:50%;
  border:2.5px solid #003d22;box-shadow:0 0 8px rgba(245,200,66,.5);
  animation:pulseDot 2.4s ease-in-out infinite;
}
@keyframes pulseDot{0%,100%{transform:scale(1);opacity:1;}50%{transform:scale(1.4);opacity:.6;}}

.hero-title{
  font-family:'Fraunces',serif;font-size:clamp(13px,1.3vw,17px);font-weight:700;
  color:var(--white);line-height:1.2;margin-bottom:4px;
  text-align:center;
  animation:fadeDown .6s .08s var(--ease) both;
}
.hero-title em{font-style:italic;color:var(--gold);}

.hero-sub{
  font-size:9.5px;color:rgba(255,255,255,.52);line-height:1.55;margin-bottom:16px;
  text-align:center;
  animation:fadeDown .6s .14s var(--ease) both;
}

/* Features */
.features{display:flex;flex-direction:column;gap:6px;width:100%;animation:fadeDown .6s .20s var(--ease) both;}
.feat{
  display:flex;align-items:center;justify-content:center;gap:9px;
  padding:7px 10px;
  background:rgba(255,255,255,.055);
  border:1px solid rgba(255,255,255,.09);
  border-radius:11px;cursor:default;
  text-align:left;
  transition:background .3s,border-color .3s,transform .3s var(--ease);
}
.feat:hover{background:rgba(255,255,255,.10);border-color:rgba(245,200,66,.28);transform:translateX(4px);}
.feat-icon{
  width:24px;height:24px;flex-shrink:0;
  background:rgba(245,200,66,.13);
  border:1px solid rgba(245,200,66,.20);
  border-radius:8px;display:flex;align-items:center;justify-content:center;
  color:var(--gold);transition:transform .3s var(--ease);
}
.feat-icon svg{width:10px;height:10px;}
.feat:hover .feat-icon{transform:scale(1.12) rotate(4deg);}
.feat-name{font-size:10px;font-weight:600;color:var(--white);margin-bottom:1px;}
.feat-desc{font-size:8.5px;color:rgba(255,255,255,.48);}

.left-foot{
  margin-top:16px;font-size:8.5px;color:rgba(255,255,255,.20);
  text-align:center;
  animation:fadeDown .6s .26s var(--ease) both;
}

/* ── RIGHT PANEL ── */
.right{
  padding:22px 24px;
  display:flex;flex-direction:column;justify-content:center;align-items:center;
  background:var(--cream);
  position:relative;overflow:hidden;
  z-index:1;
  text-align:center;
}
.right::before{
  content:'';position:absolute;top:-130px;right:-130px;
  width:330px;height:330px;
  background:radial-gradient(circle,rgba(0,107,63,.05) 0%,transparent 65%);
  border-radius:50%;pointer-events:none;
  z-index:0;
}

/* Header */
.right-header{width:100%;display:flex;flex-direction:column;align-items:center;}

.login-tag{
  display:inline-flex;align-items:center;justify-content:center;gap:5px;
  background:rgba(0,107,63,.07);border:1px solid rgba(0,107,63,.16);
  color:var(--emerald);border-radius:999px;
  padding:3px 10px;font-size:9px;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;margin-bottom:7px;
  position:relative;z-index:2;
}
.login-tag svg{width:8px;height:8px;}

.login-title{
  font-family:'Fraunces',serif;
  font-size:clamp(17px,1.8vw,22px);font-weight:700;
  color:var(--ink);line-height:1.15;margin-bottom:3px;
  text-align:center;
  position:relative;z-index:2;
}
.login-sub{font-size:11px;color:var(--muted);margin-bottom:14px;text-align:center;position:relative;z-index:2;}

/* Alerts */
.alert{
  display:flex;align-items:flex-start;justify-content:center;gap:9px;
  padding:10px 13px;border-radius:11px;
  font-size:11.5px;font-weight:500;margin-bottom:14px;line-height:1.5;
  text-align:left;width:100%;
}
.alert svg{width:13px;height:13px;flex-shrink:0;margin-top:1px;}
.alert-error{background:rgba(217,64,64,.07);border:1.5px solid rgba(217,64,64,.20);color:var(--err);animation:shake .45s var(--ease),fadeDown .35s;}
.alert-success{background:rgba(34,168,110,.07);border:1.5px solid rgba(34,168,110,.20);color:var(--ok);animation:fadeDown .35s;}

/* Form */
.login-form{width:100%;display:flex;flex-direction:column;align-items:center;}

.form-group{margin-bottom:9px;position:relative;z-index:2;width:100%;}

.field-label{
  display:flex;align-items:center;justify-content:center;gap:6px;
  font-size:10.5px;font-weight:600;color:#2d4a38;margin-bottom:4px;
  text-align:center;
}
.field-badge{
  display:inline-flex;align-items:center;justify-content:center;gap:3px;
  background:rgba(0,107,63,.07);border:1px solid rgba(0,107,63,.14);
  color:var(--emerald);border-radius:6px;
  padding:1px 6px;font-size:9px;font-weight:700;letter-spacing:.3px;
}
.field-badge svg{width:7px;height:7px;}

.input-wrap{position:relative;z-index:2;width:100%;}
.input-ico{
  position:absolute;left:13px;top:50%;transform:translateY(-50%);
  color:#b8cac0;pointer-events:none;display:flex;align-items:center;
  transition:color .25s;z-index:3;
}
.input-ico svg{width:13px;height:13px;}
.input-wrap:focus-within .input-ico{color:var(--jade);}
.field-input:focus{background:#fbfffc;}

.field-input{
  width:100%;
  padding:11px 38px 11px 38px;
  border:1.5px solid var(--border);border-radius:12px;
  font-family:'Plus Jakarta Sans',sans-serif;
  font-size:13px;font-weight:500;color:var(--ink);
  letter-spacing:.2px;
  background:var(--white);outline:none;
  -webkit-appearance:none;appearance:none;
  position:relative;z-index:2;
  text-align:center;
  transition:border-color .25s,box-shadow .25s,background .25s;
}
.field-input::placeholder{color:#c0cfcb;}
.field-input{box-shadow:0 1px 2px rgba(15,26,15,.03);}
.field-input:hover{border-color:#b4cac0;}
.field-input:focus{border-color:var(--jade);box-shadow:0 0 0 3px rgba(0,132,77,.10);}

.focus-bar{
  position:absolute;bottom:0;left:11px;right:11px;
  height:2px;
  background:linear-gradient(90deg,var(--jade),var(--gold));
  border-radius:2px;
  transform:scaleX(0);transform-origin:center;
  transition:transform .3s var(--ease);
}
.input-wrap:focus-within .focus-bar{transform:scaleX(1);}

.toggle-pw{
  position:absolute;right:10px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;
  color:#94a8a0;padding:5px;display:flex;align-items:center;justify-content:center;
  transition:color .2s;z-index:4;line-height:0;
}
.toggle-pw:hover{color:var(--jade);}
.toggle-pw svg{width:16px;height:16px;display:block;flex-shrink:0;}

/* Remember row */
.remember-row{display:flex;align-items:center;justify-content:center;margin-bottom:13px;position:relative;z-index:2;width:100%;}
.check-label{
  display:flex;align-items:center;justify-content:center;gap:7px;
  cursor:pointer;font-size:10.5px;color:var(--muted);
  font-weight:500;user-select:none;
}
.check-label input[type="checkbox"]{
  -webkit-appearance:none;-moz-appearance:none;appearance:none;
  width:16px;height:16px;
  border:1.5px solid #b9cdc3;border-radius:5px;
  background:var(--white);
  cursor:pointer;transition:all .2s;position:relative;flex-shrink:0;
  margin:0;
}
.check-label input[type="checkbox"]:hover{border-color:var(--jade);}
.check-label input[type="checkbox"]:checked{background:var(--jade);border-color:var(--jade);}
.check-label input[type="checkbox"]:checked::after{
  content:'';position:absolute;left:4.5px;top:1.5px;
  width:4px;height:8px;
  border:2px solid var(--white);border-top:none;border-left:none;
  transform:rotate(45deg);
}

/* Submit */
.submit-btn{
  width:100%;padding:10.5px;
  background:linear-gradient(135deg,var(--jade) 0%,var(--forest) 100%);
  color:var(--white);border:none;border-radius:11px;
  font-family:'Plus Jakarta Sans',sans-serif;
  font-size:12px;font-weight:700;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:7px;
  position:relative;overflow:hidden;
  box-shadow:0 7px 18px rgba(0,77,44,.28);letter-spacing:.2px;
  transition:transform .25s var(--ease),box-shadow .25s;
  position:relative;z-index:2;
}
.submit-btn::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,#00a060 0%,var(--emerald) 100%);
  opacity:0;transition:opacity .3s;
}
.submit-btn:hover::before{opacity:1;}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(0,77,44,.32);}
.submit-btn:active{transform:scale(.98);}
.submit-btn span,.submit-btn svg{position:relative;z-index:1;}
.submit-btn svg{width:14px;height:14px;transition:transform .3s var(--ease);}
.submit-btn:hover svg{transform:translateX(4px);}
.submit-btn.loading{pointer-events:none;opacity:.8;}

/* Divider */
.divider{
  display:flex;align-items:center;justify-content:center;gap:10px;margin:12px 0;width:100%;
}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}
.divider span{font-size:9px;font-weight:600;color:#b8cac0;letter-spacing:.5px;text-transform:uppercase;white-space:nowrap;}

/* Info box */
.info-box{
  display:flex;justify-content:center;gap:8px;
  padding:9px 11px;
  background:rgba(0,107,63,.04);
  border:1.5px solid rgba(0,107,63,.10);
  border-left:3px solid var(--jade);
  border-radius:11px;
  text-align:left;width:100%;
  transition:background .3s,border-color .3s;
}
.info-box:hover{background:rgba(0,107,63,.07);border-color:rgba(0,107,63,.18);border-left-color:var(--emerald);}
.info-box svg{width:11px;height:11px;flex-shrink:0;margin-top:2px;color:var(--jade);}
.info-text{font-size:9.5px;color:var(--muted);line-height:1.65;text-align:center;}
.info-text strong{color:#2d4a38;font-weight:600;}

/* WhatsApp contact button */
.wa-btn{
  display:flex;align-items:center;justify-content:center;gap:8px;
  width:100%;margin-top:10px;
  padding:11px;
  background:#fff;
  border:1.5px solid #c9e6d2;
  border-radius:12px;
  color:#1f8a4c;
  font-family:'Plus Jakarta Sans',sans-serif;
  font-size:12.5px;font-weight:700;
  text-decoration:none;
  cursor:pointer;
  text-align:center;
  transition:background .25s,border-color .25s,transform .25s var(--ease),box-shadow .25s;
  position:relative;z-index:2;
}
.wa-btn svg{width:16px;height:16px;flex-shrink:0;color:#25D366;}
.wa-btn:hover{
  background:#f0fbf4;
  border-color:#25D366;
  transform:translateY(-2px);
  box-shadow:0 8px 18px rgba(37,211,102,.18);
}
.wa-btn:active{transform:scale(.98);}

/* ── KEYFRAMES ── */
@keyframes fadeDown{
  from{opacity:0;transform:translateY(-12px);}
  to{opacity:1;transform:translateY(0);}
}
@keyframes shake{
  0%,100%{transform:translateX(0);}
  20%{transform:translateX(-8px);}
  60%{transform:translateX(8px);}
}

/* ── RESPONSIVE ── */

/* Tablet: stack vertically */
@media(max-width:768px){
  body{padding:16px;align-items:flex-start;min-height:100dvh;}
  .card{
    grid-template-columns:1fr;
    max-width:360px;margin:0 auto;
    border-radius:18px;
  }
  .left{
    padding:24px 22px 22px;
    border-radius:0;
  }
  .left-content{display:flex;flex-direction:column;align-items:center;text-align:center;}
  .logo-wrap{margin-bottom:14px;}
  .hero-title{font-size:16px;}
  .hero-sub{font-size:10px;margin-bottom:16px;}
  .features{max-width:300px;width:100%;}
  .left-foot{text-align:center;margin-top:14px;}
  .right{padding:22px 22px 26px;}
}

/* Mobile */
@media(max-width:480px){
  body{padding:0;align-items:stretch;}
  .card{
    border-radius:0;
    max-width:100%;
    min-height:100dvh;
    max-height:100dvh;
    overflow-y:auto;
    -webkit-overflow-scrolling:touch;
  }

  /* Panel branding dipadatkan jadi header ringkas */
  .left{padding:16px 18px 12px;}
  .logo-wrap{margin-bottom:8px;}
  .logo-ring{width:38px;height:38px;padding:5px;border-radius:11px;}
  .logo-dot{width:9px;height:9px;}
  .hero-title{font-size:13.5px;margin-bottom:2px;}
  .hero-sub{font-size:8.5px;line-height:1.4;margin-bottom:10px;}
  .features{display:none;}
  .left-foot{display:none;}

  .right{padding:16px 18px 18px;}
  .right-header{margin-bottom:10px !important;}
  .login-tag{margin-bottom:5px;padding:2.5px 9px;font-size:8.5px;}
  .login-title{font-size:16px;margin-bottom:2px;}
  .login-sub{font-size:10px;margin-bottom:0;}

  .alert{padding:8px 10px;margin-bottom:10px;font-size:10.5px;gap:7px;}

  .form-group{margin-bottom:7px;}
  .field-label{margin-bottom:3px;font-size:9.5px;}
  .field-badge{padding:1px 5px;font-size:8px;}
  .field-input{font-size:12px;padding:9px 32px 9px 32px;border-radius:10px;}
  .input-ico{left:11px;}
  .input-ico svg{width:11px;height:11px;}
  .toggle-pw{right:8px;}
  .toggle-pw svg{width:14px;height:14px;}

  .remember-row{margin-bottom:10px;}
  .check-label{font-size:10px;}

  .submit-btn{font-size:11.5px;padding:9.5px;border-radius:10px;}

  .divider{margin:10px 0;}
  .divider span{font-size:8px;}

  .info-box{padding:7px 9px;border-radius:10px;}
  .info-text{font-size:8.5px;line-height:1.5;}

  .wa-btn{margin-top:8px;padding:8.5px;font-size:11px;border-radius:10px;}
  .wa-btn svg{width:14px;height:14px;}
}

/* Large screens — wider card */
@media(min-width:1200px){
  .card{max-width:700px;}
  .left{padding:34px 30px;}
  .right{padding:34px 32px;}
}

@media(prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important;}
}
</style>
</head>
<body>

<div class="bg-orb bg-orb-1"></div>
<div class="bg-orb bg-orb-2"></div>
<div class="bg-grid"></div>

<div class="card">

  <!-- LEFT: Branding -->
  <div class="left">
    <div class="left-orb-1"></div>
    <div class="left-orb-2"></div>
    <div class="left-grid"></div>

    <div class="left-content">
      <div class="logo-wrap">
        <div class="logo-ring">
          <img src="{{ asset('images/foto1.png') }}" alt="Logo PTPN4 Regional 7">
        </div>
        <div class="logo-dot"></div>
      </div>

      <h1 class="hero-title">
        Sistem Absensi<br>
        <em>PTPN4</em> Regional 7
      </h1>
      <p class="hero-sub">
        Platform manajemen kehadiran pegawai<br>
        terintegrasi dengan teknologi GPS terkini
      </p>

      <div class="features">
        <div class="feat">
          <div class="feat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
          </div>
          <div>
            <div class="feat-name">Absensi Berbasis Lokasi</div>
            <div class="feat-desc">Check-in & check-out dengan verifikasi GPS</div>
          </div>
        </div>
        <div class="feat">
          <div class="feat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
          </div>
          <div>
            <div class="feat-name">Dashboard Real-Time</div>
            <div class="feat-desc">Pantau statistik kehadiran secara langsung</div>
          </div>
        </div>
        <div class="feat">
          <div class="feat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          </div>
          <div>
            <div class="feat-name">Manajemen Pegawai</div>
            <div class="feat-desc">Kelola data dan jabatan seluruh pegawai</div>
          </div>
        </div>
      </div>

      <p class="left-foot">© 2026 PTPN4 Regional 7. All rights reserved.</p>
    </div>
  </div>

  <!-- RIGHT: Login form -->
  <div class="right">

    <div class="right-header" style="margin-bottom:18px;position:relative;z-index:2;">
      <div class="login-tag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Portal Pegawai
      </div>
      <h2 class="login-title">Selamat<br>Datang</h2>
      <p class="login-sub">Masukkan NIK SAP dan password untuk masuk</p>
    </div>

    @if($errors->any())
    <div class="alert alert-error">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
      <span>{{ $errors->first() }}</span>
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
      <span>{{ session('success') }}</span>
    </div>
    @endif

    <form id="loginForm" class="login-form" method="POST" action="{{ route('login.post') }}">
      @csrf

      <div class="form-group">
        <label class="field-label">
          NIK SAP
          <span class="field-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/></svg>
            Pegawai
          </span>
        </label>
        <div class="input-wrap">
          <span class="input-ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 10h2M16 14h2M6 10c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zM6 14h4"/></svg>
          </span>
          <input
            type="text"
            name="nip"
            class="field-input"
            placeholder="Masukkan NIK SAP"
            required autocomplete="username"
            autofocus inputmode="text"
          >
          <div class="focus-bar"></div>
        </div>
      </div>

      <div class="form-group">
        <label class="field-label">Password</label>
        <div class="input-wrap">
          <span class="input-ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
          </span>
          <input
            type="password"
            name="password"
            id="pwInput"
            class="field-input"
            placeholder="Masukkan password"
            required autocomplete="current-password"
          >
          <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Tampilkan/sembunyikan password">
            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
          <div class="focus-bar"></div>
        </div>
      </div>

      <div class="remember-row">
        <label class="check-label">
          <input type="checkbox" name="remember" id="rememberCheck">
          <span>Ingat saya</span>
        </label>
      </div>

      <button type="submit" class="submit-btn" id="submitBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        <span>Masuk ke Sistem</span>
      </button>
    </form>

    <div class="divider"><span>Informasi</span></div>

    <div class="info-box">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/></svg>
      <p class="info-text">
        Login menggunakan <strong>NIK SAP</strong> yang tertera pada kartu pegawai.
        Password diberikan oleh <strong>Admin sistem</strong>.
        Hubungi admin jika belum memiliki akses atau mengalami kendala login.
      </p>
    </div>

    <a href="https://wa.me/6285269568212?text=Halo%20Admin%2C%20saya%20membutuhkan%20bantuan%20terkait%20akses%20login%20Sistem%20Absensi%20PTPN4%20Regional%207." target="_blank" rel="noopener noreferrer" class="wa-btn">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.47 14.38c-.29-.15-1.7-.84-1.97-.93-.26-.1-.46-.15-.65.14-.2.3-.75.93-.92 1.12-.17.2-.34.22-.63.07-.29-.15-1.22-.45-2.32-1.43-.86-.76-1.44-1.71-1.6-2-.18-.3-.02-.46.13-.6.13-.13.29-.34.43-.51.15-.17.2-.3.29-.49.1-.2.05-.37-.02-.52-.07-.15-.65-1.56-.89-2.14-.23-.56-.47-.48-.65-.49-.17-.01-.36-.01-.56-.01-.2 0-.51.07-.78.37-.27.3-1.02 1-1.02 2.43 0 1.43 1.05 2.81 1.2 3 .15.2 2.06 3.14 4.99 4.4.7.3 1.24.48 1.67.61.7.22 1.34.19 1.84.12.56-.08 1.7-.7 1.94-1.37.24-.68.24-1.26.17-1.38-.07-.12-.26-.2-.55-.34z"/><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.36 5.07L2 22l5.06-1.33A9.94 9.94 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm0 18.2c-1.66 0-3.21-.46-4.54-1.27l-.33-.2-3.37.89.9-3.29-.22-.34A8.18 8.18 0 013.8 12c0-4.53 3.68-8.2 8.2-8.2s8.2 3.68 8.2 8.2-3.67 8.2-8.2 8.2z"/></svg>
      <span>Hubungi Admin via WhatsApp</span>
    </a>

  </div>
</div>

<script>
(function(){
  function togglePw(){
    var inp=document.getElementById('pwInput');
    var ico=document.getElementById('eyeIcon');
    var show=inp.type==='password';
    inp.type=show?'text':'password';
    ico.innerHTML=show
      ?'<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 01-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
      :'<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>';
  }
  window.togglePw=togglePw;

  // ── Fitur "Ingat saya" ──
  // Saat halaman dimuat: jika ada NIK SAP tersimpan, isi otomatis & centang checkbox
  var nipInput   = document.querySelector('input[name="nip"]');
  var rememberCb = document.getElementById('rememberCheck');
  var savedNip   = localStorage.getItem('remember_nik_sap');

  if (savedNip) {
    nipInput.value = savedNip;
    rememberCb.checked = true;
  }

  document.getElementById('loginForm').addEventListener('submit',function(){
    // Simpan atau hapus NIK SAP sesuai status checkbox sebelum form dikirim
    if (rememberCb.checked) {
      localStorage.setItem('remember_nik_sap', nipInput.value);
    } else {
      localStorage.removeItem('remember_nik_sap');
    }

    var btn=document.getElementById('submitBtn');
    btn.classList.add('loading');
    btn.disabled=true;
    btn.querySelector('span').textContent='Memproses...';
  });
})();
</script>
</body>
</html>
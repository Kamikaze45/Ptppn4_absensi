<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PTPN4 Regional 7') | Sistem Absensi</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/foto1.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        :root {
            --primary:        #006B3F;
            --primary-dark:   #004D2C;
            --primary-light:  #00A060;
            --accent:         #FFD700;
            --accent-dark:    #E6C200;
            --bg-body:        #F0F4F0;
            --bg-card:        #FFFFFF;
            --bg-sidebar:     #003D24;
            --sidebar-width:  260px;
            --text-primary:   #1A2E1A;
            --text-secondary: #5A7A5A;
            --text-muted:     #8FAA8F;
            --border:         #D5E8D5;
            --success:        #22C55E;
            --warning:        #F59E0B;
            --danger:         #EF4444;
            --info:           #3B82F6;
            --shadow-sm:  0 1px 3px  rgba(0,107,63,0.08);
            --shadow-md:  0 4px 16px rgba(0,107,63,0.12);
            --shadow-lg:  0 8px 32px rgba(0,107,63,0.16);
            --radius:     14px;
            --radius-sm:  8px;
            --topbar-height: 64px;
            /* safe area untuk notch / gesture bar */
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-top:    env(safe-area-inset-top,    0px);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            min-height: 100dvh; /* dynamic viewport untuk mobile */
            overflow-x: hidden;
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeInDown    { from { opacity:0; transform:translateY(-20px);  } to { opacity:1; transform:translateY(0);    } }
        @keyframes fadeInUp      { from { opacity:0; transform:translateY(24px);   } to { opacity:1; transform:translateY(0);    } }
        @keyframes fadeInLeft    { from { opacity:0; transform:translateX(-24px);  } to { opacity:1; transform:translateX(0);    } }
        @keyframes scaleIn       { from { opacity:0; transform:scale(0.92);        } to { opacity:1; transform:scale(1);         } }
        @keyframes slideInSidebar{ from { transform:translateX(-100%); opacity:0;  } to { transform:translateX(0);  opacity:1;  } }
        @keyframes pulseGlow     { 0%,100%{box-shadow:0 0 0 0 rgba(0,160,96,.4);} 50%{box-shadow:0 0 0 8px rgba(0,160,96,0);} }
        @keyframes shimmer       { 0%{background-position:-200% center;} 100%{background-position:200% center;} }
        @keyframes floatOrb      { 0%,100%{transform:translateY(0) scale(1);} 50%{transform:translateY(-12px) scale(1.05);} }
        @keyframes spin          { to { transform:rotate(360deg); } }
        @keyframes navItemIn     { from{opacity:0;transform:translateX(-16px);} to{opacity:1;transform:translateX(0);} }
        @keyframes toastBounce   { 0%{transform:translateX(120%) scale(.8);} 60%{transform:translateX(-8px) scale(1.02);} 100%{transform:translateX(0) scale(1);} }
        @keyframes progressBar   { from{width:0%;} to{width:100%;} }
        @keyframes subMenuOpen   { from{opacity:0;transform:translateY(-8px);} to{opacity:1;transform:translateY(0);} }
        @keyframes scaleInModal  { from{transform:scale(.92);opacity:0;} to{transform:scale(1);opacity:1;} }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            height: 100dvh;
            background: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.35s cubic-bezier(0.4,0,0.2,1), box-shadow 0.35s ease;
            overflow: hidden;
            animation: fadeInLeft 0.5s ease both;
            /* padding untuk status bar di atas pada mobile */
            padding-top: var(--safe-top);
        }
        .sidebar::before {
            content:''; position:absolute; top:-80px; right:-80px;
            width:220px; height:220px;
            background:radial-gradient(circle,rgba(0,160,96,.22),transparent 70%);
            border-radius:50%; pointer-events:none;
            animation:floatOrb 6s ease-in-out infinite;
        }
        .sidebar::after {
            content:''; position:absolute; bottom:40px; left:-60px;
            width:180px; height:180px;
            background:radial-gradient(circle,rgba(255,215,0,.1),transparent 70%);
            border-radius:50%; pointer-events:none;
            animation:floatOrb 8s ease-in-out infinite reverse;
        }

        /* ── BRAND ── */
        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            animation: fadeInDown 0.5s 0.1s ease both;
            flex-shrink: 0;
        }
        .brand-logo { display:flex; align-items:center; gap:12px; text-decoration:none; transition:transform .25s ease; }
        .brand-logo:hover { transform:translateX(4px); }
        .brand-icon {
            width:44px; height:44px; border-radius:12px;
            display:flex; align-items:center; justify-content:center;
            flex-shrink:0; overflow:hidden; background:#fff;
            box-shadow:0 4px 12px rgba(255,215,0,.35);
            transition:transform .35s cubic-bezier(.34,1.56,.64,1),box-shadow .3s ease;
        }
        .brand-logo:hover .brand-icon { transform:rotate(8deg) scale(1.1); box-shadow:0 8px 20px rgba(255,215,0,.5); }
        .brand-icon img { width:100%; height:100%; object-fit:contain; }
        .brand-text h1 { font-family:'Sora',sans-serif; font-size:13px; font-weight:700; color:#fff; letter-spacing:.5px; line-height:1.2; }
        .brand-text span { font-size:10px; color:rgba(255,255,255,.5); font-weight:400; }

        /* ── USER ── */
        .sidebar-user {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: 12px;
            animation: fadeInDown 0.5s 0.2s ease both;
            flex-shrink: 0;
        }
        .user-avatar {
            width:40px; height:40px; border-radius:50%;
            border:2px solid rgba(255,215,0,.4); background:rgba(255,255,255,.1);
            display:flex; align-items:center; justify-content:center;
            color:var(--accent); font-weight:700; font-size:15px;
            overflow:hidden; flex-shrink:0;
            transition:border-color .3s, transform .3s;
            animation:pulseGlow 3s ease-in-out infinite;
        }
        .user-avatar:hover { border-color:var(--accent); transform:scale(1.08); }
        .user-avatar img { width:100%; height:100%; object-fit:cover; display:block; }
        .user-info h4 { font-size:12.5px; font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px; }
        .user-info span { font-size:10.5px; color:rgba(255,255,255,.45); }
        .user-badge { font-size:9px; padding:2px 7px; border-radius:20px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; margin-left:4px; }
        .badge-admin   { background:var(--accent); color:var(--primary-dark); }
        .badge-manajer { background:rgba(59,130,246,.3); color:#93c5fd; }
        .badge-user    { background:rgba(255,255,255,.15); color:rgba(255,255,255,.8); }

        /* ── NAV ── */
        .sidebar-nav {
            flex: 1;
            padding: 10px 10px 6px;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: none;
            min-height: 0; /* krusial agar flex bisa shrink & footer tetap terlihat */
            -webkit-overflow-scrolling: touch;
        }
        .sidebar-nav::-webkit-scrollbar { display:none; }
        .nav-section-title {
            font-size:9.5px; font-weight:700; letter-spacing:1.2px;
            text-transform:uppercase; color:rgba(255,255,255,.3);
            padding:8px 8px 4px; margin-top:6px;
        }

        .nav-item {
            display:flex; align-items:center; gap:12px;
            padding:10px 12px; border-radius:10px; text-decoration:none;
            color:rgba(255,255,255,.65); font-size:13px; font-weight:500;
            transition:all .25s cubic-bezier(.4,0,.2,1);
            margin-bottom:2px; position:relative; overflow:hidden;
            animation:navItemIn .4s ease both;
            -webkit-tap-highlight-color: transparent;
        }
        .nav-item:hover  { background:rgba(255,255,255,.09); color:#fff; transform:translateX(4px); }
        .nav-item.active { background:linear-gradient(135deg,var(--primary-light),var(--primary)); color:#fff; box-shadow:0 4px 16px rgba(0,160,96,.4); }
        .nav-item.active::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:3px; height:60%; background:var(--accent); border-radius:0 4px 4px 0; }
        .nav-item .nav-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.08); font-size:14px; flex-shrink:0; transition:all .25s; }
        .nav-item:hover .nav-icon  { background:rgba(255,255,255,.14); transform:scale(1.1); }
        .nav-item.active .nav-icon { background:rgba(255,255,255,.2); }

        .nav-item-parent {
            display:flex; align-items:center; justify-content:space-between; gap:12px;
            padding:10px 12px; border-radius:10px; text-decoration:none;
            color:rgba(255,255,255,.65); font-size:13px; font-weight:500;
            transition:all .25s cubic-bezier(.4,0,.2,1);
            margin-bottom:2px; position:relative; overflow:hidden;
            cursor:pointer; background:none; border:none; width:100%; font-family:inherit;
            animation:navItemIn .4s ease both;
            -webkit-tap-highlight-color: transparent;
        }
        .nav-item-parent:hover { background:rgba(255,255,255,.09); color:#fff; transform:translateX(4px); }
        .nav-item-parent.has-active { background:rgba(0,160,96,.15); color:#fff; }
        .nav-item-parent .nav-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.08); font-size:14px; flex-shrink:0; transition:all .25s; }
        .nav-item-parent:hover .nav-icon       { background:rgba(255,255,255,.14); transform:scale(1.1); }
        .nav-item-parent.has-active .nav-icon  { background:rgba(0,160,96,.25); }
        .nav-parent-label { flex:1; text-align:left; }
        .nav-chevron { font-size:10px; color:rgba(255,255,255,.35); transition:transform .3s cubic-bezier(.4,0,.2,1), color .2s; flex-shrink:0; }
        .nav-item-parent.open .nav-chevron { transform:rotate(180deg); color:rgba(255,255,255,.65); }

        .nav-submenu { overflow:hidden; max-height:0; transition:max-height .35s cubic-bezier(.4,0,.2,1), opacity .3s ease; opacity:0; margin:0 0 2px 14px; padding-left:10px; border-left:2px solid rgba(255,255,255,.1); }
        .nav-submenu.open { max-height:300px; opacity:1; }
        .nav-subitem { display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:8px; text-decoration:none; color:rgba(255,255,255,.55); font-size:12.5px; font-weight:500; transition:all .22s ease; margin:2px 0; position:relative; animation:subMenuOpen .3s ease both; -webkit-tap-highlight-color:transparent; }
        .nav-subitem:hover { background:rgba(255,255,255,.08); color:#fff; transform:translateX(4px); }
        .nav-subitem.active         { color:#fff; font-weight:700; }
        .nav-subitem.active-cuti    { color:#4ade80; }
        .nav-subitem.active-izin    { color:#60a5fa; }
        .nav-subitem.active-sakit   { color:#f87171; }
        .nav-subitem .sub-icon { width:28px; height:28px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; transition:transform .22s; }
        .nav-subitem:hover .sub-icon { transform:scale(1.15); }
        .sub-icon-cuti  { background:rgba(74,222,128,.15);  color:#4ade80; }
        .sub-icon-izin  { background:rgba(96,165,250,.15);  color:#60a5fa; }
        .sub-icon-sakit { background:rgba(248,113,113,.15); color:#f87171; }

        /* ── SIDEBAR FOOTER (tombol Keluar) ── */
        .sidebar-footer {
            padding: 12px 12px;
            /* tambahan padding bawah untuk gesture bar Android/iPhone */
            padding-bottom: calc(12px + var(--safe-bottom));
            border-top: 1px solid rgba(255,255,255,.08);
            animation: fadeInUp 0.5s 0.5s ease both;
            flex-shrink: 0; /* TIDAK boleh diperas oleh flex */
            position: relative;
            z-index: 2;
        }
        .logout-btn {
            width:100%; display:flex; align-items:center; gap:12px;
            padding:12px 14px;
            border:1.5px solid rgba(239,68,68,.4);
            background:rgba(239,68,68,.18);
            color:#ff9b9b; border-radius:10px;
            font-size:13.5px; font-weight:700;
            cursor:pointer; transition:all .25s; font-family:inherit;
            position:relative; overflow:hidden;
            box-shadow:0 2px 10px rgba(239,68,68,.15);
            -webkit-tap-highlight-color:transparent;
            touch-action: manipulation;
        }
        .logout-btn:hover  { background:rgba(239,68,68,.28); border-color:rgba(239,68,68,.6); color:#ffb3b3; transform:translateX(4px); box-shadow:0 4px 16px rgba(239,68,68,.25); }
        .logout-btn:active { transform:scale(.97); }

        /* ── MAIN WRAPPER ── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.35s cubic-bezier(0.4,0,0.2,1);
        }

        /* ── TOPBAR ── */
        .topbar {
            background:var(--bg-card);
            padding:0 24px;
            height:var(--topbar-height);
            display:flex; align-items:center; justify-content:space-between;
            border-bottom:1px solid var(--border);
            position:sticky; top:0; z-index:100;
            box-shadow:var(--shadow-sm);
            animation:fadeInDown .5s .15s ease both;
            backdrop-filter:blur(8px);
        }
        .topbar::after {
            content:''; position:absolute; bottom:0; left:0; height:2px;
            background:linear-gradient(90deg,var(--primary-light),var(--accent),var(--primary));
            background-size:200% 100%; animation:shimmer 2.5s linear infinite; width:100%; opacity:.4;
        }
        .topbar-left  { display:flex; align-items:center; gap:16px; }
        .menu-toggle  { display:none; background:none; border:none; font-size:20px; color:var(--primary); cursor:pointer; padding:8px; border-radius:8px; transition:background .2s, transform .2s; -webkit-tap-highlight-color:transparent; touch-action:manipulation; }
        .menu-toggle:hover  { background:rgba(0,107,63,.08); transform:scale(1.1); }
        .menu-toggle.active { transform:rotate(90deg); }
        .page-title   { font-family:'Sora',sans-serif; font-size:17px; font-weight:700; color:var(--text-primary); }
        .topbar-right { display:flex; align-items:center; gap:12px; }
        .topbar-date  { font-size:12px; color:var(--text-muted); background:var(--bg-body); padding:6px 14px; border-radius:20px; border:1px solid transparent; transition:all .25s; white-space:nowrap; }
        .topbar-date:hover { border-color:var(--border); color:var(--text-secondary); }
        .topbar-avatar { width:36px; height:36px; border-radius:50%; border:2px solid var(--primary); background:var(--primary); display:flex; align-items:center; justify-content:center; color:white; font-size:13px; font-weight:700; overflow:hidden; cursor:pointer; transition:all .3s cubic-bezier(.34,1.56,.64,1); text-decoration:none; flex-shrink:0; }
        .topbar-avatar:hover { transform:scale(1.15); border-color:var(--accent); box-shadow:0 0 0 4px rgba(255,215,0,.2); }
        .topbar-avatar img { width:100%; height:100%; object-fit:cover; display:block; border-radius:50%; }

        /* ── TOPBAR USER DROPDOWN ── */
        .topbar-user-wrap { position:relative; display:flex; align-items:center; }
        .topbar-user-btn {
            display:flex; align-items:center; gap:9px;
            background:none; border:none; cursor:pointer;
            padding:5px 8px 5px 5px; border-radius:40px;
            transition:background .2s; font-family:inherit;
            -webkit-tap-highlight-color:transparent; touch-action:manipulation;
        }
        .topbar-user-btn:hover { background:rgba(0,107,63,.07); }
        .topbar-user-btn .tb-avatar {
            width:36px; height:36px; border-radius:50%;
            border:2px solid var(--primary); background:var(--primary);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:13px; font-weight:700;
            overflow:hidden; flex-shrink:0;
            transition:all .3s cubic-bezier(.34,1.56,.64,1);
        }
        .topbar-user-btn:hover .tb-avatar { border-color:var(--accent); box-shadow:0 0 0 3px rgba(255,215,0,.2); }
        .topbar-user-btn .tb-avatar img  { width:100%; height:100%; object-fit:cover; display:block; border-radius:50%; }
        .topbar-user-btn .tb-name {
            font-size:12.5px; font-weight:600; color:var(--text-primary);
            max-width:110px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .topbar-user-btn .tb-chevron {
            font-size:10px; color:var(--text-muted);
            transition:transform .25s; flex-shrink:0;
        }
        .topbar-user-wrap.open .tb-chevron { transform:rotate(180deg); }

        /* dropdown panel */
        .topbar-dropdown {
            position:absolute; top:calc(100% + 10px); right:0;
            background:#fff; border-radius:14px;
            box-shadow:0 12px 40px rgba(0,0,0,.14);
            border:1px solid var(--border);
            min-width:210px; z-index:500;
            overflow:hidden;
            opacity:0; transform:translateY(-8px) scale(.97);
            pointer-events:none;
            transition:opacity .22s ease, transform .22s cubic-bezier(.34,1.3,.64,1);
        }
        .topbar-user-wrap.open .topbar-dropdown {
            opacity:1; transform:translateY(0) scale(1);
            pointer-events:auto;
        }

        /* dropdown header */
        .td-header {
            padding:14px 16px 10px;
            border-bottom:1px solid var(--border);
            display:flex; align-items:center; gap:10px;
        }
        .td-header .td-av {
            width:38px; height:38px; border-radius:50%;
            border:2px solid var(--primary); background:var(--primary);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:14px; font-weight:700;
            overflow:hidden; flex-shrink:0;
        }
        .td-header .td-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
        .td-header-info h5  { font-size:12.5px; font-weight:700; color:var(--text-primary); line-height:1.3; }
        .td-header-info span{ font-size:11px; color:var(--text-muted); }

        /* dropdown items */
        .td-item {
            display:flex; align-items:center; gap:10px;
            padding:10px 16px; font-size:13px; font-weight:500;
            color:var(--text-secondary); text-decoration:none;
            transition:background .18s, color .18s;
            cursor:pointer; background:none; border:none;
            width:100%; font-family:inherit; text-align:left;
            -webkit-tap-highlight-color:transparent;
        }
        .td-item:hover { background:rgba(0,107,63,.06); color:var(--primary); }
        .td-item .td-icon {
            width:30px; height:30px; border-radius:8px;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; flex-shrink:0;
            background:rgba(0,107,63,.08); color:var(--primary);
            transition:transform .2s;
        }
        .td-item:hover .td-icon { transform:scale(1.1); }

        /* logout item dalam dropdown */
        .td-item-logout { border-top:1px solid var(--border); margin-top:2px; }
        .td-item-logout,
        .td-item-logout .td-icon { color:#ef4444; background:rgba(239,68,68,.08); }
        .td-item-logout:hover { background:rgba(239,68,68,.07); color:#dc2626; }

        /* Sembunyikan nama di layar sangat kecil */
        @media (max-width: 400px) { .tb-name, .tb-chevron { display:none; } }

        /* ── CONTENT ── */
        .content { flex:1; padding:24px; }

        /* ── CARDS ── */
        .card { background:var(--bg-card); border-radius:var(--radius); box-shadow:var(--shadow-sm); border:1px solid var(--border); overflow:hidden; transition:box-shadow .3s, transform .3s; }
        .card:hover { box-shadow:var(--shadow-md); }
        .card-header { padding:18px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
        .card-title  { font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
        .card-body   { padding:20px; }

        /* ── STAT CARDS ── */
        .stat-card { background:var(--bg-card); border-radius:var(--radius); padding:20px; border:1px solid var(--border); display:flex; align-items:center; gap:16px; transition:all .35s cubic-bezier(.34,1.2,.64,1); box-shadow:var(--shadow-sm); position:relative; overflow:hidden; }
        .stat-card:hover { transform:translateY(-4px) scale(1.01); box-shadow:var(--shadow-lg); border-color:rgba(0,107,63,.2); }
        .stat-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0; transition:transform .3s cubic-bezier(.34,1.56,.64,1); }
        .stat-card:hover .stat-icon { transform:scale(1.15) rotate(-5deg); }
        .stat-icon.green  { background:rgba(0,107,63,.1);   color:var(--primary); }
        .stat-icon.yellow { background:rgba(245,158,11,.12); color:var(--warning); }
        .stat-icon.red    { background:rgba(239,68,68,.1);   color:var(--danger);  }
        .stat-icon.blue   { background:rgba(59,130,246,.1);  color:var(--info);    }
        .stat-info h3 { font-family:'Sora',sans-serif; font-size:26px; font-weight:700; color:var(--text-primary); line-height:1; }
        .stat-info p  { font-size:12px; color:var(--text-muted); margin-top:4px; }

        /* ── BADGES ── */
        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-success   { background:rgba(34,197,94,.12);   color:#16a34a; }
        .badge-warning   { background:rgba(245,158,11,.12);  color:#d97706; }
        .badge-danger    { background:rgba(239,68,68,.12);   color:#dc2626; }
        .badge-info      { background:rgba(59,130,246,.12);  color:#2563eb; }
        .badge-secondary { background:rgba(100,116,139,.12); color:#475569; }

        /* ── BUTTONS ── */
        .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; border:none; cursor:pointer; text-decoration:none; transition:all .25s cubic-bezier(.34,1.2,.64,1); font-family:inherit; position:relative; overflow:hidden; touch-action:manipulation; }
        .btn-primary { background:linear-gradient(135deg,var(--primary-light),var(--primary)); color:white; box-shadow:0 3px 10px rgba(0,107,63,.25); }
        .btn-primary:hover { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); transform:translateY(-2px) scale(1.02); box-shadow:0 6px 20px rgba(0,107,63,.4); }
        .btn-warning { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:#78350f; box-shadow:0 3px 10px rgba(245,158,11,.25); }
        .btn-warning:hover { transform:translateY(-2px) scale(1.02); box-shadow:0 6px 20px rgba(245,158,11,.35); }
        .btn-danger  { background:linear-gradient(135deg,#f87171,#ef4444); color:white; box-shadow:0 3px 10px rgba(239,68,68,.25); }
        .btn-danger:hover  { transform:translateY(-2px) scale(1.02); box-shadow:0 6px 20px rgba(239,68,68,.35); }
        .btn-outline { background:transparent; color:var(--primary); border:1.5px solid var(--primary); }
        .btn-outline:hover { background:rgba(0,107,63,.06); transform:translateY(-2px); }
        .btn-sm { padding:6px 12px; font-size:12px; }
        .btn-lg { padding:13px 28px; font-size:15px; }

        /* ── FORMS ── */
        .form-group   { margin-bottom:16px; }
        .form-label   { display:block; font-size:12.5px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; }
        .form-control { width:100%; padding:10px 14px; border:1.5px solid var(--border); border-radius:var(--radius-sm); font-size:13px; font-family:inherit; color:var(--text-primary); background:#fff; transition:all .25s; outline:none; -webkit-appearance:none; appearance:none; }
        .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 4px rgba(0,107,63,.1); transform:translateY(-1px); }
        select.form-control { cursor:pointer; }

        /* ── TABLES ── */
        .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
        table { width:100%; border-collapse:collapse; min-width:480px; }
        thead th { background:var(--bg-body); padding:12px 16px; font-size:11.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; text-align:left; border-bottom:1px solid var(--border); white-space:nowrap; }
        tbody tr { border-bottom:1px solid var(--border); transition:background .2s, transform .2s; }
        tbody tr:hover { background:rgba(0,107,63,.04); }
        tbody tr:last-child { border-bottom:none; }
        tbody td { padding:13px 16px; font-size:13px; color:var(--text-primary); }

        /* ── ALERTS ── */
        .alert { padding:12px 16px; border-radius:var(--radius-sm); font-size:13px; font-weight:500; display:flex; align-items:center; gap:10px; margin-bottom:16px; animation:fadeInDown .4s ease both; }
        .alert-success { background:rgba(34,197,94,.1);  color:#16a34a; border:1px solid rgba(34,197,94,.2); }
        .alert-danger  { background:rgba(239,68,68,.1);  color:#dc2626; border:1px solid rgba(239,68,68,.2); }
        .alert-warning { background:rgba(245,158,11,.1); color:#d97706; border:1px solid rgba(245,158,11,.2); }

        /* ── GRID ── */
        .grid   { display:grid; gap:20px; }
        .grid-2 { grid-template-columns:repeat(2,1fr); }
        .grid-3 { grid-template-columns:repeat(3,1fr); }
        .grid-4 { grid-template-columns:repeat(4,1fr); }

        /* ── OVERLAY ── */
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0); z-index:999; backdrop-filter:blur(0px); transition:background .35s, backdrop-filter .35s; }
        .sidebar-overlay.open { display:block; background:rgba(0,0,0,.45); backdrop-filter:blur(2px); }

        /* ── LOADING ── */
        .loading-spinner { display:inline-block; width:18px; height:18px; border:2px solid rgba(255,255,255,.4); border-top-color:white; border-radius:50%; animation:spin .7s linear infinite; }

        /* ── PAGINATION ── */
        .pagination { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:20px; flex-wrap:wrap; }
        .pagination a, .pagination span { padding:6px 12px; border-radius:8px; font-size:13px; font-weight:500; text-decoration:none; color:var(--text-secondary); border:1px solid var(--border); background:var(--bg-card); transition:all .2s; }
        .pagination a:hover { background:rgba(0,107,63,.07); border-color:var(--primary); color:var(--primary); transform:translateY(-1px); }
        .pagination .active span { background:var(--primary); color:white; border-color:var(--primary); }

        /* ── FADE IN ── */
        .fade-in { animation:fadeInUp .5s .2s ease both; }

        /* ── TOAST ── */
        .toast { position:fixed; bottom:24px; right:24px; background:#fff; border-radius:14px; padding:14px 18px; box-shadow:0 12px 32px rgba(0,0,0,.15); display:flex; align-items:center; gap:12px; font-size:13.5px; font-weight:500; z-index:9999; transform:translateX(120%) scale(.8); transition:transform .5s cubic-bezier(.34,1.3,.64,1); max-width:320px; border-left:4px solid var(--primary); }
        .toast.show    { transform:translateX(0) scale(1); }
        .toast.success { border-left-color:var(--success); }
        .toast.error   { border-left-color:var(--danger);  }
        .toast-progress { position:absolute; bottom:0; left:4px; height:3px; background:var(--primary); border-radius:0 0 0 14px; animation:progressBar 4s linear forwards; }
        .toast.success .toast-progress { background:var(--success); }
        .toast.error   .toast-progress { background:var(--danger);  }

        /* ── LOGOUT MODAL ── */
        .logout-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(3px); }
        .logout-modal-overlay.show { display:flex; }
        .logout-modal-box { background:#fff; border-radius:18px; padding:26px 24px; width:100%; max-width:360px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.3); animation:scaleInModal .25s ease both; }
        .logout-modal-icon { width:56px; height:56px; border-radius:50%; background:rgba(239,68,68,.1); display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:22px; color:#ef4444; }
        .logout-modal-box h3 { font-size:16px; font-weight:700; margin-bottom:6px; color:var(--text-primary); }
        .logout-modal-box p  { font-size:13px; color:var(--text-muted); margin-bottom:22px; }
        .logout-modal-btns   { display:flex; gap:10px; }
        .logout-modal-cancel  { flex:1; padding:11px; border:1.5px solid #e0e0e0; border-radius:10px; background:white; font-size:13px; font-weight:600; color:#666; cursor:pointer; font-family:inherit; transition:background .2s; touch-action:manipulation; }
        .logout-modal-cancel:hover { background:#f5f5f5; }
        .logout-modal-confirm { flex:1; padding:11px; border:none; border-radius:10px; background:linear-gradient(135deg,#f87171,#ef4444); color:white; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; box-shadow:0 4px 14px rgba(239,68,68,.35); transition:transform .2s, box-shadow .2s; touch-action:manipulation; }
        .logout-modal-confirm:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(239,68,68,.45); }

        /* ════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ════════════════════════════════════ */

        /* Tablet landscape / small desktop */
        @media (max-width: 1024px) {
            :root { --sidebar-width: 240px; }
            .grid-4 { grid-template-columns:repeat(2,1fr); }
            .grid-3 { grid-template-columns:repeat(2,1fr); }
        }

        /* Tablet portrait / large phone landscape */
        @media (max-width: 768px) {
            :root { --sidebar-width: 280px; --topbar-height: 60px; }

            /* Sidebar slide-in dari kiri */
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
                animation: none;
                /* tombol Keluar tidak boleh ketutup gesture bar */
                padding-bottom: var(--safe-bottom);
            }
            .sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0,0,0,.25);
                animation: slideInSidebar .35s cubic-bezier(.4,0,.2,1) both;
            }

            .main-wrapper { margin-left: 0; }
            .menu-toggle  { display:flex; align-items:center; justify-content:center; }
            .topbar       { padding:0 16px; }
            .topbar-date  { display:none; }
            .content      { padding:16px; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns:1fr; }
            .toast { bottom:16px; right:16px; left:16px; max-width:100%; }

            /* Naikkan tombol Keluar sedikit supaya tidak terpotong */
            .sidebar-footer {
                padding: 14px 12px;
                padding-bottom: calc(16px + var(--safe-bottom));
            }
        }

        /* Phone portrait */
        @media (max-width: 480px) {
            :root { --topbar-height: 56px; --sidebar-width: 100vw; }

            /* Sidebar full-screen di HP kecil */
            .sidebar {
                width: 100vw;
                max-width: 320px;
            }

            .stat-card    { flex-direction:column; text-align:center; padding:16px; }
            .page-title   { font-size:15px; }
            .content      { padding:12px; }
            .card-body    { padding:14px; }
            .card-header  { padding:14px 16px; }
            .logout-btn   { font-size:13px; padding:13px 14px; }

            /* Footer lebih tinggi agar tombol "Keluar" jelas terlihat */
            .sidebar-footer {
                padding: 16px 12px;
                padding-bottom: calc(20px + var(--safe-bottom));
            }

            /* Nav sedikit lebih compact di layar kecil */
            .nav-item, .nav-item-parent { padding:11px 10px; font-size:13px; }
            .nav-section-title { font-size:9px; padding:10px 8px 3px; }
        }

        /* Extra small (320px - iPhone SE lama, dll) */
        @media (max-width: 360px) {
            :root { --topbar-height: 52px; }
            .brand-text h1  { font-size:12px; }
            .brand-icon     { width:38px; height:38px; }
            .user-avatar    { width:36px; height:36px; font-size:13px; }
            .page-title     { font-size:14px; }
            .sidebar-footer { padding:14px 10px calc(18px + var(--safe-bottom)); }
            .logout-btn     { padding:12px 12px; font-size:12.5px; }
        }

        /* Landscape phone — tinggi layar sangat terbatas */
        @media (max-height: 500px) and (orientation: landscape) {
            .sidebar-brand { padding:12px 20px; }
            .sidebar-user  { padding:10px 20px; }
            .nav-item, .nav-item-parent { padding:8px 12px; }
            .nav-section-title { padding:6px 8px 2px; margin-top:4px; }
            .sidebar-footer { padding:10px 12px calc(10px + var(--safe-bottom)); }
            .logout-btn { padding:10px 14px; font-size:12.5px; }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration:.01ms !important; transition-duration:.01ms !important; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-logo">
            <div class="brand-icon">
                <img src="{{ asset('images/foto1.png') }}" alt="Logo PTPN4">
            </div>
            <div class="brand-text">
                <h1>PTPN4 Regional 7</h1>
                <span>Sistem Absensi Digital</span>
            </div>
        </a>
    </div>

    {{-- User Info --}}
    {{--
        $jabatanTampil: admin selalu tampil "Staff SDM", role lain pakai data asli.

        CATATAN PERBAIKAN FOTO PROFIL:
        Semua avatar foto profil (sidebar, topbar, dropdown) di layout ini sekarang
        memakai asset(Auth::user()->photo) — SAMA seperti yang dipakai di halaman
        profil (avatar besar) yang sudah terbukti berhasil menampilkan gambar.
        Sebelumnya bagian ini memakai route('storage.local', ...) yang menghasilkan
        URL salah, sehingga gambar tidak muncul (hanya tampil teks alt "foto").
    --}}
    @php
        $role          = Auth::user()->role;
        $jabatanTampil = ($role === 'admin') ? 'Staff SDM' : Auth::user()->jabatan;
    @endphp
    <div class="sidebar-user">
        <div class="user-avatar">
            @if(Auth::user()->photo)
                <img src="{{ asset(Auth::user()->photo) }}?v={{ time() }}" alt="foto">
            @else
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            @endif
        </div>
        <div class="user-info" style="flex:1;overflow:hidden;">
            <h4>{{ Auth::user()->name }}</h4>
            <span>
                {{ $jabatanTampil }}
                <span class="user-badge {{ $role === 'admin' ? 'badge-admin' : ($role === 'manajer' ? 'badge-manajer' : 'badge-user') }}">
                    {{ ucfirst($role) }}
                </span>
            </span>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="sidebar-nav">

        <div class="nav-section-title">Menu Utama</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
            Dashboard
        </a>

        {{-- ══ MENU USER (Pegawai) ══ --}}
        @if(Auth::user()->role === 'user')

        <a href="{{ route('absensi.index') }}" class="nav-item {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-fingerprint"></i></span>
            Absensi
        </a>

        <div class="nav-section-title">Pengajuan</div>

        @php $isPengajuanActive = request()->routeIs('izin.*'); @endphp

        <button class="nav-item-parent {{ $isPengajuanActive ? 'has-active open' : '' }}"
                id="btnPengajuan" onclick="toggleSubMenu('subPengajuan', this)">
            <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
            <span class="nav-parent-label">Pengajuan Surat</span>
            <i class="fas fa-chevron-down nav-chevron"></i>
        </button>

        <div class="nav-submenu {{ $isPengajuanActive ? 'open' : '' }}" id="subPengajuan">
            <a href="{{ route('izin.cuti') }}"
               class="nav-subitem {{ request()->routeIs('izin.cuti') ? 'active active-cuti' : '' }}">
                <span class="sub-icon sub-icon-cuti"><i class="fas fa-umbrella-beach"></i></span>
                Surat Cuti
            </a>
            <a href="{{ route('izin.izin') }}"
               class="nav-subitem {{ request()->routeIs('izin.izin') ? 'active active-izin' : '' }}">
                <span class="sub-icon sub-icon-izin"><i class="fas fa-id-card"></i></span>
                Surat Izin
            </a>
            <a href="{{ route('izin.sakit') }}"
               class="nav-subitem {{ request()->routeIs('izin.sakit') ? 'active active-sakit' : '' }}">
                <span class="sub-icon sub-icon-sakit"><i class="fas fa-clinic-medical"></i></span>
                Surat Sakit
            </a>
            <a href="{{ route('izin.riwayat') }}"
               class="nav-subitem {{ request()->routeIs('izin.riwayat') ? 'active' : '' }}">
                <span class="sub-icon" style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.5);">
                    <i class="fas fa-history"></i>
                </span>
                Riwayat Saya
            </a>
        </div>

        @endif
        {{-- ══ AKHIR MENU USER ══ --}}

        {{-- ══ MENU MANAJER ══ --}}
        @if(Auth::user()->role === 'manajer')

        <div class="nav-section-title">Persetujuan</div>

        <a href="{{ route('manajer.izin.index') }}"
           class="nav-item {{ request()->routeIs('manajer.izin.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-clipboard-check"></i></span>
            Pengajuan Izin
        </a>

        @endif
        {{-- ══ AKHIR MENU MANAJER ══ --}}

        {{-- ══ MENU ADMIN ══ --}}
        @if(Auth::user()->role === 'admin')

        <div class="nav-section-title">Manajemen</div>

        <a href="{{ route('admin.pegawai.index') }}"
           class="nav-item {{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-users"></i></span>
            Data Pegawai
        </a>

        <a href="{{ route('admin.laporan.absensi') }}"
           class="nav-item {{ request()->routeIs('admin.laporan.absensi') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-fingerprint"></i></span>
            Laporan Absensi
        </a>

        <a href="{{ route('admin.laporan.index') }}"
           class="nav-item {{ (request()->routeIs('admin.laporan.*') && !request()->routeIs('admin.laporan.absensi')) ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
            Data Pengajuan Izin
        </a>

        @endif
        {{-- ══ AKHIR MENU ADMIN ══ --}}

        <div class="nav-section-title">Akun</div>

        <a href="{{ route('profile.index') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-user-circle"></i></span>
            Profil Saya
        </a>

    </nav>

    {{-- Logout — selalu terlihat, tidak terpotong --}}
    <div class="sidebar-footer">
        <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">
            @csrf
        </form>
        <button type="button" class="logout-btn" onclick="konfirmasiLogout()">
            <span style="width:34px;height:34px;border-radius:8px;background:rgba(239,68,68,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-sign-out-alt"></i>
            </span>
            Keluar
        </button>
    </div>

</aside>

<!-- ═══════════════ MAIN ═══════════════ -->
<div class="main-wrapper">

    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggleBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">@yield('page-title', 'Dashboard')</h2>
        </div>
        <div class="topbar-right">
            <span class="topbar-date">
                <i class="fas fa-calendar-alt" style="color:var(--primary);margin-right:5px;"></i>
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </span>

            {{-- Avatar + Dropdown --}}
            <div class="topbar-user-wrap" id="topbarUserWrap">
                <button class="topbar-user-btn" onclick="toggleTopbarDropdown()" aria-label="Menu pengguna">
                    <div class="tb-avatar">
                        @if(Auth::user()->photo)
                            <img src="{{ asset(Auth::user()->photo) }}?v={{ time() }}" alt="foto">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <span class="tb-name">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down tb-chevron"></i>
                </button>

                <div class="topbar-dropdown" id="topbarDropdown">
                    {{-- Info user --}}
                    <div class="td-header">
                        <div class="td-av">
                            @if(Auth::user()->photo)
                                <img src="{{ asset(Auth::user()->photo) }}?v={{ time() }}" alt="foto">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="td-header-info">
                            <h5>{{ Auth::user()->name }}</h5>
                            <span>{{ $jabatanTampil }}</span>
                        </div>
                    </div>

                    {{-- Profil --}}
                    <a href="{{ route('profile.index') }}" class="td-item">
                        <span class="td-icon"><i class="fas fa-user-circle"></i></span>
                        Profil Saya
                    </a>

                    {{-- Keluar --}}
                    <button type="button" class="td-item td-item-logout" onclick="konfirmasiLogout()">
                        <span class="td-icon"><i class="fas fa-sign-out-alt"></i></span>
                        Keluar
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="content fade-in">

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')

    </main>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <i class="fas fa-check-circle" style="color:var(--success);font-size:18px;" id="toastIcon"></i>
    <span id="toastMsg"></span>
    <div class="toast-progress" id="toastProgress"></div>
</div>

<!-- Modal Konfirmasi Logout -->
<div class="logout-modal-overlay" id="logoutModal">
    <div class="logout-modal-box">
        <div class="logout-modal-icon"><i class="fas fa-sign-out-alt"></i></div>
        <h3>Keluar dari Akun?</h3>
        <p>Anda perlu login kembali untuk mengakses sistem.</p>
        <div class="logout-modal-btns">
            <button type="button" class="logout-modal-cancel"  onclick="batalLogout()">Batal</button>
            <button type="button" class="logout-modal-confirm" onclick="document.getElementById('logoutForm').submit()">Ya, Keluar</button>
        </div>
    </div>
</div>

<script>
    /* ── Topbar Dropdown ── */
    function toggleTopbarDropdown() {
        document.getElementById('topbarUserWrap').classList.toggle('open');
    }
    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('topbarUserWrap');
        if (wrap && !wrap.contains(e.target)) {
            wrap.classList.remove('open');
        }
    });

    /* ── Toggle Sidebar ── */
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const btn     = document.getElementById('menuToggleBtn');
        const isOpen  = sidebar.classList.toggle('open');
        overlay.classList.toggle('open', isOpen);
        btn.classList.toggle('active', isOpen);
        // cegah scroll body saat sidebar terbuka di mobile
        document.body.style.overflow = isOpen ? 'hidden' : '';
    }

    /* Tutup sidebar kalau resize ke desktop */
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
            document.getElementById('menuToggleBtn').classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    /* ── Sub Menu ── */
    function toggleSubMenu(subId, btn) {
        const sub = document.getElementById(subId);
        sub.classList.toggle('open');
        btn.classList.toggle('open');
    }

    /* Tutup sidebar saat nav-item diklik di mobile */
    document.addEventListener('DOMContentLoaded', () => {
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.nav-item, .nav-subitem').forEach(el => {
                el.addEventListener('click', () => {
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar.classList.contains('open')) toggleSidebar();
                });
            });
        }

        // animasi delay nav item
        document.querySelectorAll('.nav-item').forEach((el, i) => {
            el.style.animationDelay = (0.25 + i * 0.06) + 's';
        });
    });

    /* ── Toast ── */
    function showToast(msg, type = 'success') {
        const toast    = document.getElementById('toast');
        const icon     = document.getElementById('toastIcon');
        const progress = document.getElementById('toastProgress');
        document.getElementById('toastMsg').textContent = msg;
        toast.className = 'toast ' + type;
        icon.className  = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        icon.style.color = type === 'success' ? 'var(--success)' : 'var(--danger)';
        progress.style.animation = 'none';
        void progress.offsetHeight; // reflow
        progress.style.animation  = '';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    }

    /* ── Logout Modal ── */
    function konfirmasiLogout() {
        document.getElementById('logoutModal').classList.add('show');
    }
    function batalLogout() {
        document.getElementById('logoutModal').classList.remove('show');
    }
    document.getElementById('logoutModal').addEventListener('click', function(e) {
        if (e.target === this) batalLogout();
    });
</script>

@stack('scripts')
</body>
</html>
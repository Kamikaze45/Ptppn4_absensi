@extends('layouts.app')

@section('title', 'Absensi')

@section('content')

{{-- ── Leaflet CSS (diletakkan di atas agar peta tidak berkedip) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
    /* ── Token sistem warna ───────────────────────────────────────── */
    :root {
        --green-primary : #16a34a;
        --green-light   : #dcfce7;
        --green-mid     : #86efac;
        --red-primary   : #dc2626;
        --red-light     : #fee2e2;
        --blue-primary  : #2563eb;
        --blue-light    : #dbeafe;
        --surface       : #ffffff;
        --bg-page       : #f0fdf4;
        --border        : #e7f3eb;
        --text-main     : #1a2e1f;
        --text-sub      : #6b7280;
        --text-muted    : #9ca3af;
        --radius-card   : 20px;
        --radius-btn    : 14px;
        --shadow-card   : 0 1px 4px rgba(22,163,74,.07), 0 4px 16px rgba(22,163,74,.05);
    }

    /* ── Reset & base ─────────────────────────────────────────────── */
    .absensi-page { background: var(--bg-page); min-height: 100vh; padding: 1.25rem 1rem 2.5rem; }
    @media (min-width: 768px) { .absensi-page { padding: 2rem 2rem 3rem; } }

    /* ── Header ───────────────────────────────────────────────────── */
    .abs-header { margin-bottom: 1.5rem; }
    .abs-header h1 {
        font-size: 1.6rem; font-weight: 800;
        color: var(--text-main); letter-spacing: -.5px; line-height: 1.2;
    }
    .abs-header .date-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 999px; padding: .3rem .85rem;
        font-size: .78rem; color: var(--green-primary); font-weight: 600;
        margin-top: .45rem; box-shadow: var(--shadow-card);
    }
    .abs-header .date-pill svg { width: 13px; height: 13px; }

    /* ── Info bar ─────────────────────────────────────────────────── */
    #info-waktu {
        border-radius: 14px; padding: .75rem 1rem;
        font-size: .85rem; font-weight: 500;
        margin-bottom: 1.25rem;
    }

    /* ── Grid kartu check-in / check-out ──────────────────────────── */
    .abs-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media (min-width: 640px) { .abs-grid { grid-template-columns: 1fr 1fr; } }

    /* ── Kartu dasar ─────────────────────────────────────────────── */
    .abs-card {
        background: var(--surface);
        border-radius: var(--radius-card);
        border: 1.5px solid var(--border);
        box-shadow: var(--shadow-card);
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        transition: box-shadow .2s;
    }
    .abs-card:hover { box-shadow: 0 4px 20px rgba(22,163,74,.12); }
    .abs-card::before {
        content: ''; position: absolute;
        left: 0; top: 16px; bottom: 16px; width: 3px;
        border-radius: 0 3px 3px 0;
    }
    .abs-card.card-in::before  { background: var(--green-primary); }
    .abs-card.card-out::before { background: var(--red-primary); }

    /* ── Header kartu ─────────────────────────────────────────────── */
    .card-head { display: flex; align-items: center; gap: .6rem; margin-bottom: 1rem; }
    .card-icon {
        width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .card-icon.ci { background: var(--green-light); }
    .card-icon.co { background: var(--red-light); }
    .card-icon svg { width: 17px; height: 17px; }
    .card-icon.ci svg { color: var(--green-primary); }
    .card-icon.co svg { color: var(--red-primary); }
    .card-head h2 { font-size: .9rem; font-weight: 700; color: var(--text-main); }

    /* ── Status row ──────────────────────────────────────────────── */
    .status-row { display: flex; align-items: center; gap: .75rem; margin-bottom: .75rem; }
    .status-circle {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .status-circle.sc-green { background: var(--green-light); }
    .status-circle.sc-red   { background: var(--red-light); }
    .status-circle svg { width: 18px; height: 18px; stroke-width: 2.5; }
    .status-circle.sc-green svg { color: var(--green-primary); }
    .status-circle.sc-red   svg { color: var(--red-primary); }
    .status-label { font-size: .82rem; font-weight: 700; }
    .status-label.sl-green { color: var(--green-primary); }
    .status-label.sl-red   { color: var(--red-primary); }
    .status-meta  { font-size: .72rem; color: var(--text-muted); margin-top: .1rem; }

    .card-hint { font-size: .83rem; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.5; }

    /* ── Tombol utama ─────────────────────────────────────────────── */
    .btn-main {
        width: 100%; padding: .8rem 1rem;
        border-radius: var(--radius-btn);
        font-size: .88rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        border: none; cursor: pointer;
        transition: transform .12s, box-shadow .15s, filter .15s;
        box-shadow: 0 2px 8px rgba(0,0,0,.12);
    }
    .btn-main:hover  { filter: brightness(1.05); box-shadow: 0 4px 14px rgba(0,0,0,.18); }
    .btn-main:active { transform: scale(.97); }
    .btn-main svg { width: 16px; height: 16px; }
    .btn-checkin  { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; }
    .btn-checkout { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }

    .empty-checkout {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: .75rem 0 .5rem;
        color: var(--text-muted);
    }
    .empty-checkout svg { width: 36px; height: 36px; margin-bottom: .5rem; opacity: .4; }
    .empty-checkout p { font-size: .82rem; text-align: center; }

    .tipe-badge {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .25rem .75rem; border-radius: 999px;
        font-size: .72rem; font-weight: 700;
    }

    /* ── Ringkasan ────────────────────────────────────────────────── */
    .ringkasan-card {
        background: var(--surface); border-radius: var(--radius-card);
        border: 1.5px solid var(--border); box-shadow: var(--shadow-card);
        padding: 1.25rem; margin-bottom: 1.25rem;
    }
    .ringkasan-card h2 { font-size: .82rem; font-weight: 700; color: var(--text-main); margin-bottom: .85rem; }
    .ringkasan-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .6rem; }
    @media (min-width: 480px) { .ringkasan-grid { grid-template-columns: repeat(4, 1fr); } }
    .rg-item { background: var(--bg-page); border-radius: 12px; padding: .7rem .5rem; text-align: center; border: 1px solid var(--border); }
    .rg-label { font-size: .68rem; color: var(--text-muted); margin-bottom: .25rem; text-transform: uppercase; letter-spacing: .4px; }
    .rg-val   { font-size: .88rem; font-weight: 700; color: var(--text-main); }

    /* ── Kartu peta ───────────────────────────────────────────────── */
    .map-card { background: var(--surface); border-radius: var(--radius-card); border: 1.5px solid var(--border); box-shadow: var(--shadow-card); padding: 1.25rem; }
    .map-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: .85rem; flex-wrap: wrap; gap: .5rem; }
    .map-title { display: flex; align-items: center; gap: .5rem; }
    .map-title svg { width: 15px; height: 15px; color: var(--green-primary); }
    .map-title h2 { font-size: .88rem; font-weight: 700; color: var(--text-main); }
    .btn-refresh {
        display: flex; align-items: center; gap: .4rem;
        font-size: .75rem; font-weight: 600; color: var(--green-primary);
        background: var(--green-light); border: 1px solid var(--green-mid);
        border-radius: 999px; padding: .35rem .85rem;
        cursor: pointer; transition: background .15s;
    }
    .btn-refresh:hover { background: #bbf7d0; }
    .btn-refresh svg { width: 12px; height: 12px; }
    #map { width: 100%; height: 240px; border-radius: 14px; overflow: hidden; background: #e9f5ec; position: relative; z-index: 0; }
    @media (min-width: 640px) { #map { height: 300px; } }
    #map-loading { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: #f0fdf4; border-radius: 14px; z-index: 10; }
    #map-loading svg { animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    #koordinat-info { margin-top: .6rem; font-size: .73rem; color: var(--text-muted); text-align: center; font-family: monospace; background: var(--bg-page); border-radius: 8px; padding: .35rem .6rem; border: 1px solid var(--border); }

    /* ── Peringatan akurasi GPS (baru) ─────────────────────────────── */
    #akurasi-warning {
        margin-top: .6rem; padding: .55rem .8rem; border-radius: 10px;
        font-size: .74rem; font-weight: 600; line-height: 1.4;
        display: none; align-items: flex-start; gap: .45rem;
    }
    #akurasi-warning.ak-buruk  { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
    #akurasi-warning.ak-sedang { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
    #akurasi-warning.ak-baik   { background: #f0fdf4; color: #166534; border: 1px solid #86efac; }
    #akurasi-warning svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }

    /* ── Riwayat Absensi ─────────────────────────────────────────── */
    .riwayat-card { background: var(--surface); border-radius: var(--radius-card); border: 1.5px solid var(--border); box-shadow: var(--shadow-card); padding: 1.25rem; margin-top: 1.25rem; }
    .riwayat-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: .5rem; }
    .riwayat-title { display: flex; align-items: center; gap: .5rem; }
    .riwayat-title svg { width: 15px; height: 15px; color: var(--green-primary); }
    .riwayat-title h2 { font-size: .88rem; font-weight: 700; color: var(--text-main); }
    .btn-riwayat-lengkap { font-size: .75rem; font-weight: 600; color: var(--green-primary); border: 1.5px solid var(--green-primary); border-radius: 999px; padding: .35rem .9rem; text-decoration: none; background: transparent; transition: background .15s, color .15s; white-space: nowrap; }
    .btn-riwayat-lengkap:hover { background: var(--green-primary); color: #fff; }
    .riwayat-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .riwayat-table { width: 100%; border-collapse: collapse; font-size: .8rem; min-width: 640px; }
    .riwayat-table thead tr { border-bottom: 1.5px solid var(--border); }
    .riwayat-table thead th { text-align: left; padding: .5rem .75rem; font-size: .68rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    .riwayat-table tbody tr { border-bottom: 1px solid #f3f4f6; transition: background .1s; }
    .riwayat-table tbody tr:last-child { border-bottom: none; }
    .riwayat-table tbody tr:hover { background: var(--bg-page); }
    .riwayat-table td { padding: .65rem .75rem; vertical-align: middle; color: var(--text-main); }
    .td-tanggal-main { font-weight: 600; font-size: .82rem; }
    .td-tanggal-hari { font-size: .7rem; color: var(--text-muted); margin-top: .1rem; }
    .td-jam          { font-family: monospace; font-weight: 700; color: var(--green-primary); }
    .td-jam.terlambat { color: #f97316; }
    .td-jam.kosong    { color: var(--text-muted); }
    .td-lokasi a { display: inline-flex; align-items: center; gap: .3rem; font-size: .75rem; color: var(--green-primary); font-weight: 600; text-decoration: none; }
    .td-lokasi a:hover { text-decoration: underline; }
    .td-lokasi a svg { width: 11px; height: 11px; }
    .badge-status { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .65rem; border-radius: 999px; font-size: .7rem; font-weight: 700; white-space: nowrap; }
    .badge-hadir     { background: #dcfce7; color: #15803d; }
    .badge-terlambat { background: #ffedd5; color: #c2410c; }
    .badge-tepat     { background: #dbeafe; color: #1d4ed8; }
    .badge-default   { background: #f3f4f6; color: #6b7280; }
    .riwayat-empty { text-align: center; padding: 2rem 1rem; color: var(--text-muted); }
    .riwayat-empty svg { width: 36px; height: 36px; margin: 0 auto .75rem; display: block; opacity: .35; }
    .riwayat-empty p { font-size: .82rem; }

    /* ── Kartu mobile untuk Riwayat Absensi (baru) ─────────────────
       Tabel butuh scroll horizontal di layar kecil, jadi di mobile
       kita ganti jadi kartu vertikal supaya semua info (termasuk
       tombol Lihat Peta) langsung kelihatan tanpa geser. ── */
    .riwayat-mc-list { display: none; }
    .riwayat-mc-item {
        border: 1px solid var(--border); border-radius: 14px;
        padding: .85rem .95rem; margin-bottom: .7rem;
    }
    .riwayat-mc-item:last-child { margin-bottom: 0; }
    .riwayat-mc-top {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: .6rem; padding-bottom: .55rem; border-bottom: 1px dashed var(--border);
    }
    .riwayat-mc-tgl-main { font-weight: 700; font-size: .85rem; color: var(--text-main); }
    .riwayat-mc-tgl-hari { font-size: .7rem; color: var(--text-muted); }
    .riwayat-mc-rows { display: flex; flex-direction: column; gap: .5rem; }
    .riwayat-mc-row { display: flex; align-items: center; justify-content: space-between; gap: .6rem; }
    .riwayat-mc-row .rmc-k {
        font-size: .68rem; font-weight: 700; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .4px; flex-shrink: 0;
    }
    .riwayat-mc-row .rmc-v { font-size: .82rem; font-weight: 600; color: var(--text-main); text-align: right; }

    @media (max-width: 767px) {
        .riwayat-table-wrap { display: none; }
        .riwayat-mc-list { display: block; }
    }

    /* ════════════════════════════════════════════════════════
       MODAL CHECK-IN — lebih kompak
    ════════════════════════════════════════════════════════ */
    #modal-checkin {
        position: fixed; inset: 0; z-index: 9000;
        display: none;
    }
    #modal-backdrop {
        position: absolute; inset: 0;
        background: rgba(0,0,0,.55);
        backdrop-filter: blur(4px);
    }
    .modal-wrapper {
        position: absolute; inset: 0;
        display: flex; align-items: flex-end; justify-content: center;
        padding: 0;
        overflow-y: auto;
        pointer-events: none;
    }
    @media (min-width: 640px) {
        .modal-wrapper { align-items: center; padding: 1.5rem; }
    }

    /* ── #modal-box — diperkecil ─────────────────────────────────── */
    #modal-box {
        background: var(--surface);
        width: 100%; max-width: 400px;
        border-radius: 24px 24px 0 0;
        overflow: hidden;
        box-shadow: 0 -4px 40px rgba(0,0,0,.18);
        /* ↓ PERUBAHAN: dikurangi dari 92vh → 78vh agar tidak terlalu tinggi */
        max-height: min(78vh, calc(100dvh - 1.5rem));
        display: flex; flex-direction: column;
        margin: auto 0;
        transition: transform .25s cubic-bezier(.22,1,.36,1);
        will-change: transform;
        pointer-events: all;
    }
    #modal-box.dragging  { transition: none; }
    #modal-box.free-mode { border-radius: 22px !important; box-shadow: 0 12px 48px rgba(0,0,0,.28); }

    @media (min-width: 640px) {
        #modal-box { border-radius: 20px; max-width: 400px; }
    }
    @media (min-width: 1024px) {
        #modal-box { max-width: 440px; }
    }
    /* Layar sangat pendek — pastikan modal muat */
    @media (max-height: 600px) {
        #modal-box { max-height: min(96vh, calc(100dvh - .5rem)); }
    }

    /* ── Drag handle ─────────────────────────────────────────────── */
    /* ↓ PERUBAHAN: padding dikurangi agar handle lebih tipis */
    .modal-drag {
        padding: .4rem 0;
        display: flex; justify-content: center; flex-shrink: 0;
        cursor: grab; user-select: none; touch-action: none;
    }
    .modal-drag:active { cursor: grabbing; }
    .modal-drag span {
        width: 36px; height: 4px;
        background: rgba(255,255,255,.5); border-radius: 999px;
        display: block; transition: background .15s, width .15s;
        pointer-events: none;
    }
    .modal-drag.dragging span { background: rgba(255,255,255,.9); width: 44px; }

    /* ── Modal header ─────────────────────────────────────────────── */
    /* ↓ PERUBAHAN: padding dikurangi dari "0 1.15rem 1rem" → "0 1.1rem .7rem" */
    .modal-head {
        background: linear-gradient(135deg, #15803d, #16a34a 60%, #22c55e);
        padding: 0 1.1rem .7rem;
        flex-shrink: 0;
        cursor: grab; user-select: none; touch-action: none;
    }
    .modal-head:active { cursor: grabbing; }
    .modal-head-top { display: flex; align-items: flex-start; justify-content: space-between; gap: .6rem; }
    /* ↓ PERUBAHAN: font dikurangi sedikit */
    .modal-head h3 { color: #fff; font-weight: 800; font-size: .95rem; line-height: 1.25; }
    .modal-head .modal-date { color: rgba(255,255,255,.75); font-size: .66rem; margin-top: .1rem; }
    #modal-jam { color: #fff; font-family: monospace; font-weight: 800; font-size: .95rem; text-align: right; white-space: nowrap; }
    .modal-jam-label { color: rgba(255,255,255,.65); font-size: .58rem; text-align: right; }

    .modal-drag, .modal-head { cursor: move; }
    .modal-drag:active, .modal-head:active { cursor: grabbing; }

    /* ── Body wrapper + scroll fade ──────────────────────────────── */
    .modal-body-wrap { position: relative; flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
    .modal-body-wrap .modal-body { flex: 1; }

    #modal-scroll-fade {
        position: absolute; left: 0; right: 6px; bottom: 0; height: 32px;
        background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,.92));
        pointer-events: none; opacity: 0; transition: opacity .2s;
    }
    #modal-scroll-fade.show { opacity: 1; }

    /* ↓ PERUBAHAN: padding modal-body dikurangi */
    .modal-body {
        padding: .75rem .9rem 1rem 1.1rem;
        overflow-y: auto; flex: 1;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y;
        scrollbar-width: thin;
        scrollbar-color: var(--green-mid) transparent;
    }
    .modal-body::-webkit-scrollbar { width: 6px; }
    .modal-body::-webkit-scrollbar-track { background: #f0fdf4; border-radius: 999px; }
    .modal-body::-webkit-scrollbar-thumb { background: var(--green-primary); border-radius: 999px; }
    .modal-body::-webkit-scrollbar-thumb:hover { background: #15803d; }

    /* ↓ PERUBAHAN: margin alert dikurangi */
    #modal-alert { border-radius: 11px; padding: .5rem .75rem; font-size: .74rem; border: 1px solid; margin-bottom: .65rem; display: none; }
    #lokasi-loading { display: flex; align-items: center; gap: .5rem; font-size: .74rem; color: var(--text-sub); margin-bottom: .65rem; }
    #lokasi-loading svg { animation: spin 1s linear infinite; width: 13px; height: 13px; color: var(--green-primary); flex-shrink: 0; }

    /* ↓ PERUBAHAN: info lokasi lebih compact */
    #lokasi-info { margin-bottom: .65rem; padding: .45rem .7rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 11px; display: none; }
    .lokasi-title { font-size: .68rem; font-weight: 700; color: var(--green-primary); margin-bottom: .1rem; }
    #lokasi-koordinat { font-size: .66rem; color: var(--text-muted); font-family: monospace; word-break: break-all; }
    #lokasi-akurasi { font-size: .66rem; margin-top: .15rem; font-weight: 700; }

    /* ↓ PERUBAHAN: label tipe lebih kecil */
    .tipe-label { font-size: .74rem; font-weight: 700; color: var(--text-main); margin-bottom: .5rem; }

    /* ── Grid tipe absensi — lebih kompak ─────────────────────────── */
    /* ↓ PERUBAHAN: gap & margin dikurangi, padding tombol lebih kecil */
    .tipe-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: .45rem; margin-bottom: .75rem; }
    .tipe-btn {
        display: flex; flex-direction: column; align-items: center; gap: .22rem;
        padding: .55rem .35rem;
        border-radius: 13px; border: 2px solid #e5e7eb;
        background: #fff; cursor: pointer;
        transition: border-color .15s, background .15s, transform .1s;
        font-family: inherit; min-width: 0;
    }
    .tipe-btn:hover  { border-color: #6ee7b7; background: #f0fdf4; }
    .tipe-btn:active { transform: scale(.97); }
    /* ↓ PERUBAHAN: emoji lebih kecil */
    .tipe-btn .tipe-emoji { font-size: 1.25rem; line-height: 1; }
    .tipe-btn .tipe-name  { font-size: .68rem; font-weight: 800; color: var(--text-main); }
    .tipe-btn .tipe-desc  { font-size: .58rem; color: var(--text-muted); text-align: center; line-height: 1.2; }
    @media (max-width: 360px) {
        .tipe-grid { gap: .3rem; }
        .tipe-btn { padding: .45rem .25rem; }
        .tipe-btn .tipe-emoji { font-size: 1.1rem; }
        .tipe-btn .tipe-name  { font-size: .62rem; }
        .tipe-btn .tipe-desc  { font-size: .54rem; }
    }

    /* ── Ketentuan — lebih kompak ─────────────────────────────────── */
    /* ↓ PERUBAHAN: padding & margin dikurangi */
    .ketentuan-box {
        background: #fffbeb; border: 1px solid #fde68a;
        border-radius: 11px; padding: .55rem .75rem; margin-bottom: .75rem;
    }
    .ketentuan-box .ket-title { font-size: .7rem; font-weight: 700; color: #92400e; margin-bottom: .4rem; }
    .ketentuan-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: .25rem; }
    /* ↓ PERUBAHAN: font list lebih kecil */
    .ketentuan-list li { display: flex; align-items: flex-start; gap: .4rem; font-size: .67rem; color: #78350f; }
    .ketentuan-list li span.ket-icon { flex-shrink: 0; margin-top: .02rem; }

    /* ── Tombol konfirmasi ────────────────────────────────────────── */
    /* ↓ PERUBAHAN: padding tombol dikurangi sedikit */
    #btn-konfirmasi-checkin {
        width: 100%; padding: .65rem 1rem;
        border-radius: var(--radius-btn);
        color: #fff; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: .5rem;
        transition: filter .15s, transform .1s, box-shadow .15s;
        box-shadow: 0 2px 10px rgba(0,0,0,.12);
        font-size: max(15px, .82rem); font-weight: 700;
        min-height: 40px;
    }
    #btn-konfirmasi-checkin:not(:disabled):hover  { filter: brightness(1.06); }
    #btn-konfirmasi-checkin:not(:disabled):active { transform: scale(.98); }
    #btn-konfirmasi-checkin:disabled { background: #d1d5db !important; cursor: not-allowed; box-shadow: none; }
    #btn-konfirmasi-checkin svg { width: 14px; height: 14px; flex-shrink: 0; }

    /* ↓ PERUBAHAN: tombol batal lebih ramping */
    #modal-close-btn {
        width: 100%; margin-top: .3rem; padding: .5rem 1rem;
        border-radius: var(--radius-btn); font-size: .78rem; font-weight: 600;
        color: var(--text-sub); background: transparent; border: none;
        cursor: pointer; transition: background .15s; min-height: 34px;
    }
    #modal-close-btn:hover { background: #f3f4f6; }

    @supports (padding: max(0px)) {
        .modal-body { padding-bottom: max(1.25rem, calc(env(safe-area-inset-bottom) + .75rem)); }
    }

    /* ════════════════════════════════════════════════════════
       MODAL SUKSES
    ════════════════════════════════════════════════════════ */
    #modal-sukses { position: fixed; inset: 0; z-index: 9001; display: none; }
    .sukses-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(4px); }
    .sukses-inner { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1.25rem; overflow-y: auto; }
    .sukses-box { background: var(--surface); border-radius: 24px; box-shadow: 0 8px 40px rgba(0,0,0,.18); padding: 2rem 1.75rem; text-align: center; width: 100%; max-width: 300px; margin: auto; }
    #sukses-icon-bg { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.1rem; }
    #sukses-icon { width: 30px; height: 30px; }
    #sukses-title { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-bottom: .25rem; }
    #sukses-jam   { font-size: .82rem; color: var(--text-muted); }
    #sukses-status { font-size: .82rem; margin-top: .35rem; color: var(--text-sub); }
    .sukses-ok-btn { margin-top: 1.4rem; width: 100%; padding: .8rem; border-radius: var(--radius-btn); background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; font-size: .88rem; font-weight: 700; border: none; cursor: pointer; box-shadow: 0 2px 10px rgba(22,163,74,.3); transition: filter .15s; min-height: 44px; }
    .sukses-ok-btn:hover { filter: brightness(1.07); }
    @media (max-width: 360px) {
        .sukses-box { padding: 1.5rem 1.25rem; max-width: 100%; }
    }
</style>

<div class="absensi-page">

    {{-- ── Header ───────────────────────────────────────────────── --}}
    <div class="abs-header">
        <h1>Absensi</h1>
        <div class="date-pill">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    {{-- ── Info Bar Waktu ────────────────────────────────────────── --}}
    <div id="info-waktu" class="hidden"></div>

    {{-- ── Grid Check-In & Check-Out ───────────────────────────── --}}
    <div class="abs-grid">

        {{-- Check-In Card --}}
        <div class="abs-card card-in">
            <div class="card-head">
                <div class="card-icon ci">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h2>Check-In</h2>
            </div>

            @if($absensi && $absensi->check_in)
                <div class="status-row">
                    <div class="status-circle sc-green">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="status-label sl-green">Check-in tercatat</p>
                        <p class="status-meta">
                            {{ $absensi->jam_masuk }} WIB &bull;
                            <span style="font-weight:600;color:{{ $absensi->status_masuk === 'Terlambat' ? '#dc2626' : '#16a34a' }}">
                                {{ $absensi->status_masuk ?? '-' }}
                            </span>
                        </p>
                    </div>
                </div>
                <span class="tipe-badge {{ $absensi->tipe_badge }}">
                    {{ $absensi->tipe_absensi_label }}
                </span>
            @else
                <p class="card-hint">Anda belum melakukan check-in hari ini. Pastikan GPS aktif.</p>
                <button id="btn-checkin" class="btn-main btn-checkin">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Check-In Sekarang
                </button>
            @endif
        </div>

        {{-- Check-Out Card --}}
        <div class="abs-card card-out">
            <div class="card-head">
                <div class="card-icon co">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h2>Check-Out</h2>
            </div>

            @if($absensi && $absensi->check_out)
                <div class="status-row">
                    <div class="status-circle sc-red">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="status-label sl-red">Check-out tercatat</p>
                        <p class="status-meta">{{ $absensi->jam_keluar }} WIB</p>
                    </div>
                </div>

            @elseif($absensi && $absensi->check_in)
                <p class="card-hint">Jangan lupa check-out saat pulang.</p>
                <button id="btn-checkout" class="btn-main btn-checkout">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Check-Out Sekarang
                </button>

            @else
                <div class="empty-checkout">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <p>Lakukan check-in terlebih dahulu</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Ringkasan ─────────────────────────────────────────────── --}}
    @if($absensi && $absensi->check_in)
    <div class="ringkasan-card">
        <h2>📋 Ringkasan Absensi Hari Ini</h2>
        <div class="ringkasan-grid">
            <div class="rg-item">
                <p class="rg-label">Tipe</p>
                <p class="rg-val">{{ $absensi->tipe_absensi ?? '-' }}</p>
            </div>
            <div class="rg-item">
                <p class="rg-label">Jam Masuk</p>
                <p class="rg-val">{{ $absensi->jam_masuk ?? '-' }}</p>
            </div>
            <div class="rg-item">
                <p class="rg-label">Jam Keluar</p>
                <p class="rg-val">{{ $absensi->jam_keluar ?? '-' }}</p>
            </div>
            <div class="rg-item">
                <p class="rg-label">Status</p>
                <span class="tipe-badge {{ $absensi->status_masuk_badge }}">
                    {{ $absensi->status_masuk ?? '-' }}
                </span>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Peta Lokasi ───────────────────────────────────────────── --}}
    <div class="map-card">
        <div class="map-head">
            <div class="map-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <h2>Lokasi Absensi</h2>
            </div>
            <button id="btn-refresh-lokasi" class="btn-refresh">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Perbarui Lokasi
            </button>
        </div>

        <div id="map" style="position:relative;">
            <div id="map-loading">
                <div style="text-align:center;color:#6b7280;">
                    <svg style="width:24px;height:24px;margin:0 auto .5rem;display:block;color:#16a34a;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <p style="font-size:.75rem;">Mendeteksi lokasi GPS...</p>
                </div>
            </div>
        </div>

        <div id="koordinat-info">Mendeteksi lokasi...</div>
        <div id="akurasi-warning"></div>
    </div>

    {{-- ── Riwayat Absensi ──────────────────────────────────────── --}}
    <div class="riwayat-card">
        <div class="riwayat-head">
            <div class="riwayat-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2>Riwayat Absensi</h2>
            </div>
            <a href="{{ route('absensi.riwayat') }}" class="btn-riwayat-lengkap">Riwayat Lengkap →</a>
        </div>

        @if($riwayat->count() > 0)
        <div class="riwayat-table-wrap">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Check-In</th>
                        <th>Lokasi Masuk</th>
                        <th>Check-Out</th>
                        <th>Lokasi Keluar</th>
                        <th>Durasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $r)
                    @php
                        $durasi = '-';
                        if ($r->check_in && $r->check_out) {
                            $tgl    = \Carbon\Carbon::parse($r->tanggal)->toDateString();
                            $masuk  = \Carbon\Carbon::parse($tgl . ' ' . $r->check_in);
                            $keluar = \Carbon\Carbon::parse($tgl . ' ' . $r->check_out);
                            $menit  = $masuk->diffInMinutes($keluar);
                            $jam    = intdiv($menit, 60);
                            $sisa   = $menit % 60;
                            $durasi = $jam > 0 ? "{$jam}j {$sisa}m" : "{$sisa}m";
                        }
                        $badgeClass = match(true) {
                            $r->status_masuk === 'Terlambat'   => 'badge-terlambat',
                            $r->status_masuk === 'Tepat Waktu' => 'badge-tepat',
                            $r->status === 'Hadir'             => 'badge-hadir',
                            default                            => 'badge-default',
                        };
                        $badgeLabel = $r->status_masuk ?? $r->status ?? '-';
                    @endphp
                    <tr>
                        <td>
                            <div class="td-tanggal-main">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</div>
                            <div class="td-tanggal-hari">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            @if($r->check_in)
                                <span class="td-jam {{ $r->status_masuk === 'Terlambat' ? 'terlambat' : '' }}">
                                    {{ \Carbon\Carbon::parse($r->check_in)->format('H:i:s') }}
                                </span>
                            @else
                                <span class="td-jam kosong">-</span>
                            @endif
                        </td>
                        <td class="td-lokasi">
                            @if($r->check_in_lat && $r->check_in_lng)
                                <a href="https://maps.google.com/?q={{ $r->check_in_lat }},{{ $r->check_in_lng }}" target="_blank" rel="noopener">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lihat Peta
                                </a>
                            @else
                                <span style="color:var(--text-muted)">-</span>
                            @endif
                        </td>
                        <td>
                            @if($r->check_out)
                                <span class="td-jam" style="color:var(--red-primary)">
                                    {{ \Carbon\Carbon::parse($r->check_out)->format('H:i:s') }}
                                </span>
                            @else
                                <span class="td-jam kosong">-</span>
                            @endif
                        </td>
                        <td class="td-lokasi">
                            @if($r->check_out_lat && $r->check_out_lng)
                                <a href="https://maps.google.com/?q={{ $r->check_out_lat }},{{ $r->check_out_lng }}" target="_blank" rel="noopener">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lihat Peta
                                </a>
                            @else
                                <span style="color:var(--text-muted)">-</span>
                            @endif
                        </td>
                        <td style="font-size:.8rem;color:var(--text-sub);font-weight:600;">{{ $durasi }}</td>
                        <td><span class="badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Kartu mobile (baru) — semua info langsung kelihatan tanpa scroll ── --}}
        <div class="riwayat-mc-list">
            @foreach($riwayat as $r)
            @php
                $durasi = '-';
                if ($r->check_in && $r->check_out) {
                    $tgl    = \Carbon\Carbon::parse($r->tanggal)->toDateString();
                    $masuk  = \Carbon\Carbon::parse($tgl . ' ' . $r->check_in);
                    $keluar = \Carbon\Carbon::parse($tgl . ' ' . $r->check_out);
                    $menit  = $masuk->diffInMinutes($keluar);
                    $jam    = intdiv($menit, 60);
                    $sisa   = $menit % 60;
                    $durasi = $jam > 0 ? "{$jam}j {$sisa}m" : "{$sisa}m";
                }
                $badgeClass = match(true) {
                    $r->status_masuk === 'Terlambat'   => 'badge-terlambat',
                    $r->status_masuk === 'Tepat Waktu' => 'badge-tepat',
                    $r->status === 'Hadir'             => 'badge-hadir',
                    default                            => 'badge-default',
                };
                $badgeLabel = $r->status_masuk ?? $r->status ?? '-';
            @endphp
            <div class="riwayat-mc-item">
                <div class="riwayat-mc-top">
                    <div>
                        <div class="riwayat-mc-tgl-main">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</div>
                        <div class="riwayat-mc-tgl-hari">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('l') }}</div>
                    </div>
                    <span class="badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span>
                </div>
                <div class="riwayat-mc-rows">
                    <div class="riwayat-mc-row">
                        <span class="rmc-k">Check-In</span>
                        <span class="rmc-v">
                            @if($r->check_in)
                                <span class="td-jam {{ $r->status_masuk === 'Terlambat' ? 'terlambat' : '' }}">
                                    {{ \Carbon\Carbon::parse($r->check_in)->format('H:i:s') }}
                                </span>
                            @else
                                <span class="td-jam kosong">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="riwayat-mc-row">
                        <span class="rmc-k">Lokasi Masuk</span>
                        <span class="rmc-v">
                            @if($r->check_in_lat && $r->check_in_lng)
                                <a href="https://maps.google.com/?q={{ $r->check_in_lat }},{{ $r->check_in_lng }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:.3rem;color:var(--green-primary);text-decoration:none;font-weight:700;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lihat Peta
                                </a>
                            @else
                                <span style="color:var(--text-muted)">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="riwayat-mc-row">
                        <span class="rmc-k">Check-Out</span>
                        <span class="rmc-v">
                            @if($r->check_out)
                                <span class="td-jam" style="color:var(--red-primary)">
                                    {{ \Carbon\Carbon::parse($r->check_out)->format('H:i:s') }}
                                </span>
                            @else
                                <span class="td-jam kosong">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="riwayat-mc-row">
                        <span class="rmc-k">Lokasi Keluar</span>
                        <span class="rmc-v">
                            @if($r->check_out_lat && $r->check_out_lng)
                                <a href="https://maps.google.com/?q={{ $r->check_out_lat }},{{ $r->check_out_lng }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:.3rem;color:var(--green-primary);text-decoration:none;font-weight:700;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lihat Peta
                                </a>
                            @else
                                <span style="color:var(--text-muted)">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="riwayat-mc-row">
                        <span class="rmc-k">Durasi</span>
                        <span class="rmc-v">{{ $durasi }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="riwayat-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p>Belum ada riwayat absensi.</p>
        </div>
        @endif
    </div>

</div>{{-- end .absensi-page --}}


{{-- ════════════════════════════════════════════════════════════
     MODAL: PILIH TIPE ABSENSI
════════════════════════════════════════════════════════════ --}}
<div id="modal-checkin" role="dialog" aria-modal="true">
    <div id="modal-backdrop"></div>
    <div class="modal-wrapper">
        <div id="modal-box">
            <div class="modal-drag" id="modal-drag-handle"><span></span></div>

            {{-- Header --}}
            <div class="modal-head">
                <div class="modal-head-top">
                    <div>
                        <h3>Pilih Tipe Absensi</h3>
                        <p class="modal-date">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div>
                        <p id="modal-jam">--:--:--</p>
                        <p class="modal-jam-label">WIB</p>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="modal-body-wrap">
            <div class="modal-body" id="modal-body-scroll">

                <div id="modal-alert"></div>

                <div id="lokasi-loading">
                    <svg fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Mendeteksi lokasi GPS...
                </div>
                <div id="lokasi-info">
                    <p class="lokasi-title">📍 Lokasi terdeteksi</p>
                    <p id="lokasi-koordinat"></p>
                    <p id="lokasi-akurasi"></p>
                </div>

                <p class="tipe-label">Pilih kondisi kerja Anda hari ini:</p>

                <div class="tipe-grid">
                    <button onclick="pilihTipe('WFO')" data-tipe="WFO" class="tipe-btn">
                        <span class="tipe-emoji">🏢</span>
                        <span class="tipe-name">WFO</span>
                        <span class="tipe-desc">Work From<br>Office</span>
                    </button>
                    <button onclick="pilihTipe('WFH')" data-tipe="WFH" class="tipe-btn">
                        <span class="tipe-emoji">🏠</span>
                        <span class="tipe-name">WFH</span>
                        <span class="tipe-desc">Work From<br>Home</span>
                    </button>
                    <button onclick="pilihTipe('Dinas Luar')" data-tipe="Dinas Luar" class="tipe-btn">
                        <span class="tipe-emoji">🚗</span>
                        <span class="tipe-name">Dinas</span>
                        <span class="tipe-desc">Dinas<br>Luar</span>
                    </button>
                </div>

                <div class="ketentuan-box">
                    <p class="ket-title">ℹ️ Ketentuan Check-In</p>
                    <ul class="ketentuan-list">
                        <li><span class="ket-icon">🏢</span><span><b>WFO</b> — wajib dalam radius <b>100 meter</b> dari kantor</span></li>
                        <li><span class="ket-icon">🏠</span><span><b>WFH</b> — bisa dari lokasi mana saja</span></li>
                        <li><span class="ket-icon">🚗</span><span><b>Dinas Luar</b> — bisa dari lokasi mana saja</span></li>
                        <li><span class="ket-icon">⏰</span><span>Check-in dibuka mulai <b>06:00 WIB</b>. Setelah 08:30 tetap bisa, namun tercatat <b>Terlambat</b></span></li>
                    </ul>
                </div>

                <button id="btn-konfirmasi-checkin" disabled>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="btn-konfirmasi-text">Pilih tipe absensi dulu</span>
                </button>

                <button id="modal-close-btn">Batal</button>

            </div>
            <div id="modal-scroll-fade"></div>
            </div>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════════
     MODAL: SUKSES
════════════════════════════════════════════════════════════ --}}
<div id="modal-sukses">
    <div class="sukses-backdrop"></div>
    <div class="sukses-inner">
        <div class="sukses-box">
            <div id="sukses-icon-bg">
                <svg id="sukses-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 id="sukses-title">Berhasil!</h3>
            <p id="sukses-jam"></p>
            <div id="sukses-tipe-wrap" class="hidden" style="margin:.5rem 0;">
                <span id="sukses-tipe" class="tipe-badge"></span>
            </div>
            <p id="sukses-status"></p>
            <button onclick="location.reload()" class="sukses-ok-btn">OK</button>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════════
     LEAFLET & JAVASCRIPT
════════════════════════════════════════════════════════════ --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ── Config ───────────────────────────────────────────────────────────
const KANTOR_LAT   = {{ \App\Http\Controllers\AbsensiController::KANTOR_LAT }};
const KANTOR_LNG   = {{ \App\Http\Controllers\AbsensiController::KANTOR_LNG }};
const RADIUS_M     = {{ \App\Http\Controllers\AbsensiController::RADIUS_METER }};
const CSRF_TOKEN   = '{{ csrf_token() }}';
const URL_CHECKIN  = '{{ route("absensi.checkin") }}';
const URL_CHECKOUT = '{{ route("absensi.checkout") }}';

// ── State ────────────────────────────────────────────────────────────
let currentLat  = null;
let currentLng  = null;
let currentAcc  = null;
let tipeDipilih = null;
let mapInstance = null;
let userMarker  = null;
let accuracyCircle = null;
let jamInterval = null;

// ── PETA ─────────────────────────────────────────────────────────────
function initMap(lat, lng, acc) {
    document.getElementById('map-loading').style.display = 'none';
    if (mapInstance) {
        mapInstance.setView([lat, lng], 17);
        userMarker.setLatLng([lat, lng]);
        updateAccuracyCircle(lat, lng, acc);
        return;
    }
    mapInstance = L.map('map', { zoomControl: true }).setView([lat, lng], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>'
    }).addTo(mapInstance);
    userMarker = L.marker([lat, lng], {
        icon: L.divIcon({
            html: `<div style="width:16px;height:16px;background:#16a34a;border-radius:50%;border:3px solid #fff;box-shadow:0 0 0 2px #16a34a,0 2px 6px rgba(0,0,0,.3)"></div>`,
            className: '', iconSize: [16,16], iconAnchor: [8,8]
        })
    }).addTo(mapInstance).bindPopup('<b>📍 Lokasi Anda</b>');
    L.marker([KANTOR_LAT, KANTOR_LNG], {
        icon: L.divIcon({
            html: `<div style="width:16px;height:16px;background:#2563eb;border-radius:50%;border:3px solid #fff;box-shadow:0 0 0 2px #2563eb,0 2px 6px rgba(0,0,0,.3)"></div>`,
            className: '', iconSize: [16,16], iconAnchor: [8,8]
        })
    }).addTo(mapInstance).bindPopup('<b>🏢 Kantor PTPN4 Bekri</b>');
    L.circle([KANTOR_LAT, KANTOR_LNG], {
        radius: RADIUS_M, color: '#2563eb', weight: 1.5,
        fillColor: '#3b82f6', fillOpacity: 0.1
    }).addTo(mapInstance);
    updateAccuracyCircle(lat, lng, acc);
}

// ── LINGKARAN AKURASI GPS (baru) ───────────────────────────────────
// Menggambar lingkaran radius akurasi di sekitar marker pengguna,
// supaya admin/pegawai bisa lihat seberapa "yakin" browser dengan
// koordinat yang didapat. Radius besar = akurasi jelek.
function updateAccuracyCircle(lat, lng, acc) {
    if (!mapInstance || !acc) return;
    const warna = acc <= 30 ? '#16a34a' : (acc <= 100 ? '#f59e0b' : '#dc2626');
    if (accuracyCircle) {
        accuracyCircle.setLatLng([lat, lng]);
        accuracyCircle.setRadius(acc);
        accuracyCircle.setStyle({ color: warna, fillColor: warna });
    } else {
        accuracyCircle = L.circle([lat, lng], {
            radius: acc, color: warna, weight: 1,
            fillColor: warna, fillOpacity: 0.08, dashArray: '4,4'
        }).addTo(mapInstance);
    }
}

// ── TAMPILKAN PERINGATAN AKURASI (baru) ────────────────────────────
function tampilkanAkurasi(acc) {
    const info = document.getElementById('koordinat-info');
    const warn = document.getElementById('akurasi-warning');
    if (!acc) {
        warn.style.display = 'none';
        return;
    }

    const accBulat = Math.round(acc);
    info.textContent += ` (± ${accBulat} m)`;

    warn.classList.remove('ak-baik', 'ak-sedang', 'ak-buruk');
    warn.style.display = 'flex';

    if (acc <= 30) {
        warn.classList.add('ak-baik');
        warn.innerHTML = `
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Akurasi GPS baik (± ${accBulat} m). Lokasi cukup bisa diandalkan.</span>`;
    } else if (acc <= 100) {
        warn.classList.add('ak-sedang');
        warn.innerHTML = `
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            <span>Akurasi GPS sedang (± ${accBulat} m). Kalau tolak WFO, coba pindah ke tempat terbuka atau nyalakan GPS HP.</span>`;
    } else {
        warn.classList.add('ak-buruk');
        warn.innerHTML = `
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            <span><b>Akurasi GPS buruk (± ${accBulat} m).</b> Lokasi kemungkinan tidak sesuai posisi asli. Gunakan HP dengan GPS aktif untuk hasil lebih akurat, terutama untuk check-in WFO.</span>`;
    }
}

function tampilkanAkurasiModal(acc) {
    const el = document.getElementById('lokasi-akurasi');
    if (!acc) { el.textContent = ''; return; }
    const accBulat = Math.round(acc);
    const warna = acc <= 30 ? '#16a34a' : (acc <= 100 ? '#d97706' : '#dc2626');
    const label = acc <= 30 ? 'Baik' : (acc <= 100 ? 'Sedang' : 'Buruk — gunakan HP dengan GPS aktif');
    el.style.color = warna;
    el.textContent = `Akurasi: ± ${accBulat} m (${label})`;
}

// ── AMBIL GPS ────────────────────────────────────────────────────────
function getLocation(onSuccess, onError) {
    if (!navigator.geolocation) {
        const msg = 'Browser tidak mendukung GPS.';
        setInfoWaktu('error', '⚠️ ' + msg);
        if (onError) onError(msg);
        return;
    }
    navigator.geolocation.getCurrentPosition(
        pos => {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;
            currentAcc = pos.coords.accuracy; // dalam meter
            initMap(currentLat, currentLng, currentAcc);
            document.getElementById('koordinat-info').textContent =
                `📍 Lat: ${currentLat.toFixed(6)}, Lng: ${currentLng.toFixed(6)}`;
            tampilkanAkurasi(currentAcc);
            if (onSuccess) onSuccess(currentLat, currentLng, currentAcc);
        },
        err => {
            document.getElementById('map-loading').innerHTML = `
                <div style="text-align:center;color:#9ca3af;">
                    <p style="font-size:.75rem;">⚠️ Gagal mendapatkan lokasi GPS.<br>Aktifkan GPS lalu refresh.</p>
                </div>`;
            document.getElementById('koordinat-info').textContent = 'Gagal mendapatkan lokasi GPS.';
            document.getElementById('akurasi-warning').style.display = 'none';
            if (onError) onError(err.message);
        },
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
    );
}

// ── TINGGI MODAL PRESISI ─────────────────────────────────────────────
function adjustModalMaxHeight() {
    const box = document.getElementById('modal-box');
    if (!box || box.style.position === 'fixed') return;
    const vh   = window.innerHeight;
    const maxH = Math.min(Math.round(vh * 0.78), vh - 16);
    box.style.maxHeight = maxH + 'px';
}

window.addEventListener('resize', adjustModalMaxHeight);
window.addEventListener('orientationchange', () => setTimeout(adjustModalMaxHeight, 150));

// ── FADE INDIKATOR SCROLL ────────────────────────────────────────────
function updateModalScrollFade() {
    const body = document.getElementById('modal-body-scroll');
    const fade = document.getElementById('modal-scroll-fade');
    if (!body || !fade) return;
    const sisaScroll = body.scrollHeight - body.scrollTop - body.clientHeight;
    fade.classList.toggle('show', sisaScroll > 4);
}

// ── INFO BAR ─────────────────────────────────────────────────────────
function setInfoWaktu(type, msg) {
    const el = document.getElementById('info-waktu');
    const map = {
        warning: 'background:#fefce8;color:#854d0e;border:1px solid #fde047;',
        error:   'background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;',
        success: 'background:#f0fdf4;color:#166534;border:1px solid #86efac;',
    };
    el.style.cssText = (map[type] || map.warning) + 'display:block;';
    el.textContent = msg;
    el.classList.remove('hidden');
}

function cekJamCheckin() {
    const now   = new Date();
    const total = now.getHours() * 60 + now.getMinutes();
    if (total < 6 * 60) {
        setInfoWaktu('warning', '⏰ Belum waktunya. Check-in dibuka mulai pukul 06:00 WIB.');
        return false;
    }
    if (total > 8 * 60 + 30) {
        setInfoWaktu('error', '⚠️ Anda check-in di luar jadwal. Absensi akan tercatat sebagai Terlambat.');
    }
    return true;
}

// ── MODAL CHECK-IN ───────────────────────────────────────────────────
function bukaModalCheckin() {
    tipeDipilih = null;
    resetPilihTipe();
    sembunyikanAlert();
    setKonfirmasiDisabled();

    adjustModalMaxHeight();
    resetModalPosition();

    document.getElementById('lokasi-loading').style.display = 'flex';
    document.getElementById('lokasi-info').style.display = 'none';
    document.getElementById('modal-checkin').style.display = 'block';
    document.body.style.overflow = 'hidden';

    clearInterval(jamInterval);
    updateJamModal();
    jamInterval = setInterval(updateJamModal, 1000);

    requestAnimationFrame(updateModalScrollFade);

    if (currentLat && currentLng) {
        tampilkanLokasiModal(currentLat, currentLng, currentAcc);
    } else {
        getLocation(
            (lat, lng, acc) => tampilkanLokasiModal(lat, lng, acc),
            (err)      => tampilkanAlertModal('error', 'Gagal mendapatkan lokasi GPS. Pastikan GPS aktif.')
        );
    }
}

function tutupModalCheckin() {
    document.getElementById('modal-checkin').style.display = 'none';
    document.body.style.overflow = '';
    clearInterval(jamInterval);
    resetModalPosition();
}

function resetModalPosition() {
    const box = document.getElementById('modal-box');
    if (!box) return;
    box.style.position  = '';
    box.style.left      = '';
    box.style.top       = '';
    box.style.right     = '';
    box.style.bottom    = '';
    box.style.margin    = '';
    box.style.transform = '';
    box.style.maxHeight = '';
    box.classList.remove('free-mode', 'dragging');
}

function updateJamModal() {
    const now = new Date();
    document.getElementById('modal-jam').textContent =
        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
}

function tampilkanLokasiModal(lat, lng, acc) {
    document.getElementById('lokasi-loading').style.display = 'none';
    document.getElementById('lokasi-koordinat').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    tampilkanAkurasiModal(acc);
    document.getElementById('lokasi-info').style.display = 'block';
}

function resetPilihTipe() {
    document.querySelectorAll('.tipe-btn').forEach(btn => {
        btn.style.borderColor = '#e5e7eb';
        btn.style.background  = '#fff';
    });
}

function pilihTipe(tipe) {
    tipeDipilih = tipe;
    resetPilihTipe();
    sembunyikanAlert();

    const btn = document.querySelector(`[data-tipe="${tipe}"]`);
    const warna = {
        WFO:          { border: '#3b82f6', bg: '#eff6ff' },
        WFH:          { border: '#a855f7', bg: '#faf5ff' },
        'Dinas Luar': { border: '#f97316', bg: '#fff7ed' },
    };
    const w = warna[tipe] || { border: '#16a34a', bg: '#f0fdf4' };
    btn.style.borderColor = w.border;
    btn.style.background  = w.bg;

    const konfirmasi = document.getElementById('btn-konfirmasi-checkin');
    konfirmasi.disabled = false;
    const bgGradient = {
        WFO:          'linear-gradient(135deg,#2563eb,#1d4ed8)',
        WFH:          'linear-gradient(135deg,#9333ea,#7e22ce)',
        'Dinas Luar': 'linear-gradient(135deg,#f97316,#ea580c)',
    };
    konfirmasi.style.background = bgGradient[tipe] || 'linear-gradient(135deg,#16a34a,#15803d)';
    document.getElementById('btn-konfirmasi-text').textContent = `Konfirmasi Check-In ${tipe}`;
}

function setKonfirmasiDisabled() {
    const btn = document.getElementById('btn-konfirmasi-checkin');
    btn.disabled = true;
    btn.style.background = '';
    document.getElementById('btn-konfirmasi-text').textContent = 'Pilih tipe absensi dulu';
}

function tampilkanAlertModal(type, msg) {
    const el = document.getElementById('modal-alert');
    const styles = {
        error:   'background:#fef2f2;color:#991b1b;border-color:#fca5a5;',
        success: 'background:#f0fdf4;color:#166534;border-color:#86efac;',
    };
    el.style.cssText = (styles[type] || styles.error) + 'display:block;';
    el.textContent = msg;
}

function sembunyikanAlert() {
    document.getElementById('modal-alert').style.display = 'none';
}

// ── SUBMIT CHECK-IN ──────────────────────────────────────────────────
async function submitCheckIn() {
    if (!tipeDipilih) return;
    if (!currentLat || !currentLng) {
        tampilkanAlertModal('error', 'Lokasi GPS belum terdeteksi. Coba perbarui lokasi terlebih dahulu.');
        return;
    }
    const btn = document.getElementById('btn-konfirmasi-checkin');
    btn.disabled = true;
    document.getElementById('btn-konfirmasi-text').textContent = 'Memproses...';
    try {
        const res  = await fetch(URL_CHECKIN, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body:    JSON.stringify({ tipe_absensi: tipeDipilih, latitude: currentLat, longitude: currentLng, accuracy: currentAcc }),
        });
        const data = await res.json();
        if (data.success) {
            tutupModalCheckin();
            tampilkanModalSukses('checkin', data);
        } else {
            tampilkanAlertModal('error', data.message || 'Gagal melakukan check-in.');
            btn.disabled = false;
            document.getElementById('btn-konfirmasi-text').textContent = `Konfirmasi Check-In ${tipeDipilih}`;
        }
    } catch (e) {
        tampilkanAlertModal('error', 'Terjadi kesalahan jaringan. Coba lagi.');
        btn.disabled = false;
        document.getElementById('btn-konfirmasi-text').textContent = `Konfirmasi Check-In ${tipeDipilih}`;
    }
}

// ── SUBMIT CHECK-OUT ─────────────────────────────────────────────────
async function submitCheckOut() {
    if (!currentLat || !currentLng) {
        alert('Lokasi GPS belum terdeteksi. Aktifkan GPS dan coba lagi.');
        return;
    }
    const btn = document.getElementById('btn-checkout');
    if (btn) { btn.disabled = true; btn.textContent = 'Memproses...'; }
    try {
        const res  = await fetch(URL_CHECKOUT, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body:    JSON.stringify({ latitude: currentLat, longitude: currentLng, accuracy: currentAcc }),
        });
        const data = await res.json();
        if (data.success) {
            tampilkanModalSukses('checkout', data);
        } else {
            alert(data.message || 'Gagal melakukan check-out.');
            if (btn) { btn.disabled = false; btn.textContent = 'Check-Out Sekarang'; }
        }
    } catch (e) {
        alert('Terjadi kesalahan jaringan. Coba lagi.');
        if (btn) { btn.disabled = false; btn.textContent = 'Check-Out Sekarang'; }
    }
}

// ── MODAL SUKSES ─────────────────────────────────────────────────────
function tampilkanModalSukses(jenis, data) {
    const iconBg = document.getElementById('sukses-icon-bg');
    const icon   = document.getElementById('sukses-icon');
    if (jenis === 'checkin') {
        iconBg.style.background = '#dcfce7';
        icon.style.color        = '#16a34a';
        document.getElementById('sukses-title').textContent   = 'Check-In Berhasil! 🎉';
        document.getElementById('sukses-jam').textContent     = `Jam masuk: ${data.jam_masuk} WIB`;
        const tipeWrap = document.getElementById('sukses-tipe-wrap');
        const tipeEl   = document.getElementById('sukses-tipe');
        tipeWrap.classList.remove('hidden');
        const badgeMap = {
            WFO:          'background:#dbeafe;color:#1d4ed8;',
            WFH:          'background:#f3e8ff;color:#7e22ce;',
            'Dinas Luar': 'background:#ffedd5;color:#c2410c;',
        };
        tipeEl.style.cssText = badgeMap[data.tipe_absensi] || '';
        tipeEl.textContent   = data.tipe_absensi;
        document.getElementById('sukses-status').innerHTML =
            `Status: <span style="font-weight:700;color:${data.status_masuk === 'Terlambat' ? '#dc2626' : '#16a34a'}">${data.status_masuk}</span>`;
    } else {
        iconBg.style.background = '#dbeafe';
        icon.style.color        = '#2563eb';
        document.getElementById('sukses-title').textContent = 'Check-Out Berhasil! 👋';
        document.getElementById('sukses-jam').textContent   = `Jam keluar: ${data.jam_keluar} WIB`;
        document.getElementById('sukses-tipe-wrap').classList.add('hidden');
        document.getElementById('sukses-status').textContent = '';
    }
    document.getElementById('modal-sukses').style.display = 'block';
}

// ── EVENT LISTENERS ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

    getLocation();

    document.getElementById('btn-checkin')?.addEventListener('click', () => {
        if (cekJamCheckin()) bukaModalCheckin();
    });
    document.getElementById('btn-checkout')?.addEventListener('click', submitCheckOut);
    document.getElementById('modal-backdrop')?.addEventListener('click', tutupModalCheckin);
    document.getElementById('modal-close-btn')?.addEventListener('click', tutupModalCheckin);
    document.getElementById('btn-konfirmasi-checkin')?.addEventListener('click', submitCheckIn);
    document.getElementById('btn-refresh-lokasi')?.addEventListener('click', () => {
        currentLat = null; currentLng = null; currentAcc = null;
        document.getElementById('koordinat-info').textContent = 'Memperbarui lokasi...';
        document.getElementById('akurasi-warning').style.display = 'none';
        document.getElementById('map-loading').style.display = 'flex';
        getLocation();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') tutupModalCheckin();
    });
    document.getElementById('modal-body-scroll')?.addEventListener('scroll', updateModalScrollFade);
    window.addEventListener('resize', updateModalScrollFade);

    // ── DRAG MODAL ─────────────────────────────────────────────────
    (function initDragModal() {
        const modalBox   = document.getElementById('modal-box');
        const dragHandle = document.getElementById('modal-drag-handle');
        const modalHead  = document.querySelector('.modal-head');
        if (!modalBox || !dragHandle) return;

        const EDGE_VISIBLE = 56;
        let isDragging = false;
        let offsetX    = 0;
        let offsetY    = 0;

        function clampPosition(left, top) {
            const w  = modalBox.offsetWidth;
            const h  = modalBox.offsetHeight;
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            return {
                left: Math.min(Math.max(left, EDGE_VISIBLE - w), vw - EDGE_VISIBLE),
                top:  Math.min(Math.max(top,  0),                 vh - EDGE_VISIBLE),
            };
        }

        function onPointerDown(e) {
            if (e.button !== undefined && e.button !== 0) return;
            if (e.target.closest('button, a, input, select, textarea')) return;

            isDragging = true;
            modalBox.classList.add('dragging', 'free-mode');
            document.body.style.userSelect = 'none';

            const rect = modalBox.getBoundingClientRect();
            modalBox.style.position  = 'fixed';
            modalBox.style.margin    = '0';
            modalBox.style.left      = rect.left + 'px';
            modalBox.style.top       = rect.top  + 'px';
            modalBox.style.right     = 'auto';
            modalBox.style.bottom    = 'auto';
            modalBox.style.transform = 'none';
            modalBox.style.maxHeight = rect.height + 'px';

            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;

            if (e.target.setPointerCapture && e.pointerId !== undefined) {
                try { e.target.setPointerCapture(e.pointerId); } catch (_) {}
            }

            dragHandle.classList.add('dragging');
            document.addEventListener('pointermove', onPointerMove, { passive: true });
            document.addEventListener('pointerup',   onPointerUp);
            document.addEventListener('pointercancel', onPointerUp);
        }

        function onPointerMove(e) {
            if (!isDragging) return;
            const pos = clampPosition(e.clientX - offsetX, e.clientY - offsetY);
            modalBox.style.left = pos.left + 'px';
            modalBox.style.top  = pos.top  + 'px';
        }

        function onPointerUp() {
            if (!isDragging) return;
            isDragging = false;
            modalBox.classList.remove('dragging');
            dragHandle.classList.remove('dragging');
            document.body.style.userSelect = '';
            document.removeEventListener('pointermove', onPointerMove);
            document.removeEventListener('pointerup',   onPointerUp);
            document.removeEventListener('pointercancel', onPointerUp);
            const pos = clampPosition(
                parseFloat(modalBox.style.left) || 0,
                parseFloat(modalBox.style.top)  || 0,
            );
            modalBox.style.left = pos.left + 'px';
            modalBox.style.top  = pos.top  + 'px';
        }

        window.addEventListener('resize', () => {
            if (modalBox.style.position !== 'fixed') return;
            const pos = clampPosition(
                parseFloat(modalBox.style.left) || 0,
                parseFloat(modalBox.style.top)  || 0,
            );
            modalBox.style.left = pos.left + 'px';
            modalBox.style.top  = pos.top  + 'px';
        });

        dragHandle.addEventListener('pointerdown', onPointerDown);
        modalHead?.addEventListener('pointerdown', onPointerDown);
    })();
});
</script>
@endsection
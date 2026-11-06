@extends('layouts.app')
@section('title', 'Data Pegawai')

@push('styles')
<style>
:root {
    --green-dark:  #006B3F;
    --green-mid:   #00A060;
    --green-light: #f0fdf4;
    --green-border:#bbf7d0;
    --text-primary:#111827;
    --text-muted:  #6b7280;
    --text-light:  #9ca3af;
    --border:      #e5e7eb;
    --surface:     #ffffff;
    --bg:          #f9fafb;
    --radius-sm:   8px;
    --radius-md:   12px;
    --radius-lg:   16px;
    --shadow-sm:   0 1px 4px rgba(0,0,0,.06);
    --shadow-md:   0 4px 16px rgba(0,0,0,.08);
}

/* ── FLASH ── */
.flash-alert {
    display: flex; align-items: flex-start; gap: .6rem;
    border-radius: var(--radius-md); padding: .7rem .9rem;
    font-size: .78rem; margin-bottom: .85rem;
    border: 1px solid transparent; line-height: 1.5;
}
.flash-success { background: var(--green-light); color: #15803d; border-color: var(--green-border); }
.flash-error   { background: #fff5f5; color: #dc2626; border-color: #fecaca; }
.flash-alert .btn-close { margin-left: auto; font-size: .6rem; }

/* ── POPUP NOTIFIKASI IMPORT ── */
.notif-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.45);
    align-items: center; justify-content: center;
}
.notif-overlay.show { display: flex; }
.notif-box {
    background: var(--surface); border-radius: var(--radius-lg);
    box-shadow: 0 24px 64px rgba(0,0,0,.18);
    width: 100%; max-width: 440px; margin: 1rem;
    overflow: hidden; animation: popIn .3s cubic-bezier(.34,1.56,.64,1);
}
@keyframes popIn {
    from { transform: scale(.8); opacity: 0; }
    to   { transform: scale(1);  opacity: 1; }
}
.notif-head {
    display: flex; align-items: center; gap: .6rem;
    padding: 1rem 1.2rem .8rem; border-bottom: 1px solid #f3f4f6;
}
.notif-head-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
}
.notif-head-icon.success { background: #dcfce7; color: #16a34a; }
.notif-head-icon.error   { background: #fee2e2; color: #dc2626; }
.notif-head h6 { font-size: .86rem; font-weight: 800; color: var(--text-primary); margin: 0; }
.notif-head .notif-close {
    margin-left: auto; background: #f3f4f6; border: none;
    border-radius: 7px; width: 27px; height: 27px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--text-muted); font-size: .8rem;
}
.notif-head .notif-close:hover { background: var(--border); }
.notif-body { padding: 1rem 1.2rem; }
.notif-stats {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: .5rem; margin-bottom: .85rem;
}
.notif-stat {
    border-radius: var(--radius-sm); padding: .65rem .8rem; text-align: center;
}
.notif-stat.imported { background: #dcfce7; border: 1px solid #bbf7d0; }
.notif-stat.skipped  { background: #fef9c3; border: 1px solid #fde68a; }
.notif-stat .ns-num {
    font-size: 1.4rem; font-weight: 800; line-height: 1;
    margin-bottom: .15rem;
}
.notif-stat.imported .ns-num { color: #15803d; }
.notif-stat.skipped  .ns-num { color: #b45309; }
.notif-stat .ns-lbl { font-size: .65rem; font-weight: 600; color: var(--text-muted); }
.notif-errors {
    background: #fff5f5; border: 1px solid #fecaca;
    border-radius: var(--radius-sm); padding: .65rem .8rem;
    margin-bottom: .85rem; max-height: 140px; overflow-y: auto;
}
.notif-errors-title {
    font-size: .69rem; font-weight: 700; color: #dc2626;
    margin-bottom: .4rem; display: flex; align-items: center; gap: .3rem;
}
.notif-error-item {
    font-size: .67rem; color: #7f1d1d;
    padding: .2rem 0; border-bottom: 1px solid #fecaca; line-height: 1.4;
}
.notif-error-item:last-child { border-bottom: none; }
.notif-footer { display: flex; justify-content: flex-end; }
.notif-btn-close {
    padding: .48rem 1.2rem; border-radius: var(--radius-sm); border: none;
    font-size: .76rem; font-weight: 700; cursor: pointer; color: #fff;
}
.notif-btn-close.success { background: linear-gradient(135deg, var(--green-dark), var(--green-mid)); }
.notif-btn-close.error   { background: linear-gradient(135deg, #dc2626, #ef4444); }

/* ── HEADER ── */
.pg-header {
    display: flex; align-items: center;
    justify-content: space-between;
    flex-wrap: wrap; gap: .75rem;
    margin-bottom: 1rem;
}
.pg-header-left h5 {
    font-size: 1rem; font-weight: 800;
    color: var(--text-primary); margin: 0 0 .1rem;
}
.pg-header-left p { font-size: .72rem; color: var(--text-light); margin: 0; }
.header-actions { display: flex; flex-wrap: wrap; gap: .4rem; align-items: center; }
.hbtn {
    display: inline-flex; align-items: center; gap: .35rem;
    border-radius: var(--radius-sm); padding: .5rem .9rem;
    font-size: .76rem; font-weight: 700;
    text-decoration: none; cursor: pointer; border: 1.5px solid;
    white-space: nowrap; transition: all .18s;
}
.hbtn i { font-size: .7rem; }
.hbtn-primary {
    background: linear-gradient(135deg, var(--green-dark), var(--green-mid));
    color: #fff; border-color: transparent;
    box-shadow: 0 2px 8px rgba(0,107,63,.2);
}
.hbtn-primary:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
.hbtn-green  { background: var(--surface); color: #15803d; border-color: var(--green-border); }
.hbtn-green:hover  { background: var(--green-light); color: #15803d; }
.hbtn-amber  { background: var(--surface); color: #b45309; border-color: #fde68a; }
.hbtn-amber:hover  { background: #fffbeb; color: #b45309; }
.hbtn-gray   { background: var(--surface); color: var(--text-muted); border-color: var(--border); }
.hbtn-gray:hover   { background: var(--bg); color: var(--text-primary); }

/* ── MODAL IMPORT ── */
.modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 999;
    background: rgba(0,0,0,.4);
    align-items: center; justify-content: center;
}
.modal-overlay.show { display: flex; }
.modal-box {
    background: var(--surface); border-radius: var(--radius-lg);
    box-shadow: 0 24px 64px rgba(0,0,0,.14);
    width: 100%; max-width: 420px; margin: 1rem; overflow: hidden;
}
.modal-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.2rem .8rem; border-bottom: 1px solid #f3f4f6;
}
.modal-head h6 {
    font-weight: 800; font-size: .86rem;
    color: var(--text-primary); margin: 0;
    display: flex; align-items: center; gap: .4rem;
}
.modal-close {
    background: #f3f4f6; border: none; border-radius: 7px;
    width: 27px; height: 27px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--text-muted); font-size: .85rem;
}
.modal-close:hover { background: var(--border); }
.modal-body { padding: 1rem 1.2rem 1.2rem; }
.modal-info {
    background: var(--green-light); border: 1px solid var(--green-border);
    border-radius: var(--radius-sm); padding: .6rem .8rem;
    margin-bottom: .85rem; font-size: .72rem; color: #15803d; line-height: 1.65;
}
.modal-info ol { margin: .3rem 0 0 .9rem; padding: 0; }
.modal-info li { margin-bottom: .12rem; }
.modal-info a { color: var(--green-dark); font-weight: 700; }
.modal-warn {
    margin-top: .4rem; padding-top: .4rem;
    border-top: 1px solid var(--green-border);
    font-size: .69rem; color: #166534;
}
.file-upload-area {
    border: 2px dashed #d1fae5; border-radius: var(--radius-md);
    padding: 1.1rem; text-align: center; margin-bottom: .85rem;
    background: #fafffe; cursor: pointer; position: relative;
    transition: border-color .2s, background .2s;
}
.file-upload-area:hover, .file-upload-area.dragover {
    border-color: var(--green-dark); background: var(--green-light);
}
.file-upload-area input[type="file"] {
    position: absolute; inset: 0; opacity: 0;
    cursor: pointer; width: 100%; height: 100%;
}
.fu-icon {
    width: 36px; height: 36px; background: #dcfce7; border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .5rem; color: #15803d; font-size: .95rem;
}
.file-upload-area p { margin: 0; font-size: .74rem; color: var(--text-muted); }
.fu-name { font-size: .71rem; color: var(--green-dark); font-weight: 700; margin-top: .3rem; display: none; }
.modal-actions { display: flex; gap: .45rem; justify-content: flex-end; }
.btn-cancel {
    padding: .48rem .9rem; border-radius: var(--radius-sm);
    border: 1.5px solid var(--border); background: var(--surface);
    color: var(--text-muted); font-size: .76rem; font-weight: 600; cursor: pointer;
}
.btn-cancel:hover { background: var(--bg); }
.btn-submit {
    padding: .48rem 1rem; border-radius: var(--radius-sm); border: none;
    background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff;
    font-size: .76rem; font-weight: 700; cursor: pointer;
    box-shadow: 0 2px 8px rgba(217,119,6,.22);
    display: inline-flex; align-items: center; gap: .35rem;
}
.btn-submit:hover { opacity: .9; }

/* ── FILTER ── */
.filter-box {
    background: var(--surface); border-radius: var(--radius-md);
    padding: .85rem 1rem; border: 1px solid var(--border);
    box-shadow: var(--shadow-sm); margin-bottom: 1rem;
}
.filter-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto auto;
    gap: .5rem; align-items: end;
}
.flbl {
    font-size: .64rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--text-light); margin-bottom: .22rem; display: block;
}
.finput, .fselect {
    width: 100%; border: 1.5px solid var(--border);
    border-radius: var(--radius-sm); padding: .46rem .7rem;
    font-size: .79rem; background: var(--bg); color: var(--text-primary);
    outline: none; transition: border-color .2s, box-shadow .2s;
}
.finput:focus, .fselect:focus {
    border-color: var(--green-dark);
    box-shadow: 0 0 0 3px rgba(0,107,63,.08);
    background: var(--surface);
}
.finput-wrap { position: relative; }
.finput-wrap .fi-ic {
    position: absolute; left: .65rem; top: 50%;
    transform: translateY(-50%); color: var(--text-light);
    font-size: .68rem; pointer-events: none;
}
.finput-wrap .finput { padding-left: 1.9rem; }
.btn-cari {
    display: inline-flex; align-items: center; gap: .3rem;
    background: var(--green-dark); color: #fff; border: none;
    border-radius: var(--radius-sm); padding: .48rem .9rem;
    font-size: .76rem; font-weight: 700; cursor: pointer; white-space: nowrap;
}
.btn-cari:hover { background: #005533; }
.btn-reset {
    display: inline-flex; align-items: center; gap: .28rem;
    background: var(--bg); color: var(--text-muted);
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    padding: .48rem .8rem; font-size: .76rem; font-weight: 600;
    text-decoration: none; white-space: nowrap;
}
.btn-reset:hover { background: var(--border); color: var(--text-primary); }

/* ── SUMMARY ── */
.summary-bar {
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap;
    gap: .5rem; margin-bottom: .7rem;
}
.sb-info { font-size: .72rem; color: var(--text-muted); }
.sb-info strong { color: var(--text-primary); font-weight: 700; }
.sb-total {
    background: var(--green-light); color: #15803d;
    border: 1px solid var(--green-border); border-radius: 20px;
    padding: .18rem .6rem; font-size: .67rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: .25rem;
}

/* ── CARD GRID ── */
.pgw-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: .65rem;
}
.pgw-card {
    background: var(--surface); border-radius: var(--radius-md);
    border: 1px solid var(--border); box-shadow: var(--shadow-sm);
    overflow: hidden; transition: box-shadow .2s, transform .2s;
}
.pgw-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.pgw-strip { height: 3px; background: linear-gradient(90deg, var(--green-dark), var(--green-mid)); }
.pgw-strip.off { background: var(--border); }
.pgw-body { padding: .85rem .9rem .75rem; }
.pgw-top { display: flex; align-items: center; gap: .6rem; margin-bottom: .6rem; }
.pgw-av {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, var(--green-dark), var(--green-mid));
    color: #fff; font-weight: 800; font-size: .88rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.pgw-av.off { background: linear-gradient(135deg, #9ca3af, #d1d5db); }
.pgw-nm { font-size: .81rem; font-weight: 700; color: var(--text-primary); line-height: 1.2; margin-bottom: .08rem; }
.pgw-nip { font-size: .64rem; color: var(--text-light); }
.pgw-badges { display: flex; flex-wrap: wrap; gap: .28rem; margin-bottom: .6rem; }
.badge-jbt {
    background: var(--green-light); color: #15803d;
    border: 1px solid var(--green-border); border-radius: 20px;
    padding: .15rem .5rem; font-size: .61rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: .18rem;
}
.badge-div {
    background: var(--bg); color: var(--text-muted);
    border: 1px solid var(--border); border-radius: 20px;
    padding: .15rem .5rem; font-size: .61rem; font-weight: 500;
}
.pgw-info { margin-bottom: .6rem; }
.pgw-info-row {
    display: flex; align-items: center; gap: .38rem;
    font-size: .69rem; color: var(--text-muted); margin-bottom: .18rem;
}
.pgw-info-row i { width: 12px; color: var(--text-light); font-size: .63rem; flex-shrink: 0; }
.pgw-info-row span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pgw-divider { border: none; border-top: 1px solid #f3f4f6; margin: 0 0 .6rem; }
.pgw-actions { display: flex; align-items: center; justify-content: space-between; }
.badge-aktif {
    background: var(--green-light); color: #15803d;
    border: 1px solid var(--green-border); border-radius: 20px;
    padding: .15rem .5rem; font-size: .6rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: .18rem;
}
.badge-nonaktif {
    background: var(--bg); color: var(--text-light);
    border: 1px solid var(--border); border-radius: 20px;
    padding: .15rem .5rem; font-size: .6rem; font-weight: 600;
}
.pgw-btns { display: flex; gap: .3rem; }
.act-btn {
    width: 29px; height: 29px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: .68rem; text-decoration: none;
    border: 1px solid; cursor: pointer; transition: all .15s;
}
.act-view   { background: var(--green-light); color: #15803d; border-color: var(--green-border); }
.act-view:hover   { background: #dcfce7; }
.act-edit   { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.act-edit:hover   { background: #dbeafe; }
.act-delete { background: #fff5f5; color: #dc2626; border-color: #fecaca; }
.act-delete:hover { background: #fee2e2; }

/* ── EMPTY STATE ── */
.empty-state {
    background: var(--surface); border-radius: var(--radius-md);
    border: 1px solid var(--border); padding: 2.5rem 1.5rem; text-align: center;
}
.empty-icon {
    width: 52px; height: 52px; background: var(--bg); border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem; margin: 0 auto .75rem;
}

/* ── PAGINATION ── */
.pgn-wrap {
    margin-top: 1.1rem; display: flex;
    align-items: center; justify-content: center;
    gap: .75rem; flex-wrap: wrap;
}
.pgn-info { font-size: .7rem; color: var(--text-light); }
.pgn-pages { display: flex; align-items: center; gap: .25rem; }
.pgn-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: var(--radius-sm);
    border: 1.5px solid var(--border); background: var(--surface);
    color: var(--text-primary); font-size: .72rem; font-weight: 600;
    text-decoration: none; transition: all .15s; cursor: pointer; line-height: 1;
}
.pgn-btn:hover { background: var(--green-light); border-color: var(--green-dark); color: var(--green-dark); }
.pgn-btn.active { background: var(--green-dark); border-color: var(--green-dark); color: #fff; }
.pgn-btn.disabled { opacity: .35; pointer-events: none; background: var(--bg); }
.pgn-btn i { font-size: .6rem; }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
    .filter-row { grid-template-columns: 1fr 1fr; }
    .filter-row > :nth-child(1) { grid-column: 1 / -1; }
    .pg-header { flex-direction: column; align-items: flex-start; }
    .header-actions { width: 100%; }
    .hbtn { flex: 1; justify-content: center; }
}
@media (max-width: 480px) {
    .filter-row { grid-template-columns: 1fr; }
    .btn-cari, .btn-reset { width: 100%; justify-content: center; }
    .hbtn { font-size: .72rem; padding: .46rem .7rem; }
    .pgw-av { width: 34px; height: 34px; font-size: .82rem; }
    .pgn-wrap { gap: .4rem; }
}
</style>
@endpush

@section('content')

{{-- ── FLASH biasa (non-import) ── --}}
@if(session('error'))
<div class="flash-alert flash-error">
    <i class="fas fa-exclamation-circle" style="flex-shrink:0;margin-top:.1rem;"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success') && !str_contains(session('success'), 'mengimpor'))
<div class="flash-alert flash-success">
    <i class="fas fa-check-circle" style="flex-shrink:0;margin-top:.1rem;"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── POPUP NOTIFIKASI IMPORT ── --}}
@if(session('success') && str_contains(session('success'), 'mengimpor'))
@php
    $msg      = session('success');
    $parts    = explode('. Error:', $msg);
    $mainMsg  = trim($parts[0]);
    $errRaw   = isset($parts[1]) ? trim($parts[1]) : null;
    $errItems = $errRaw ? array_filter(array_map('trim', explode('|', $errRaw))) : [];

    preg_match('/(\d+) pegawai/', $mainMsg, $mImp);
    preg_match('/(\d+) dilewati/', $mainMsg, $mSkip);
    $importedNum = $mImp[1]  ?? 0;
    $skippedNum  = $mSkip[1] ?? 0;
    $isSuccess   = $importedNum > 0;
@endphp
<div id="notifImport" class="notif-overlay show">
    <div class="notif-box">
        <div class="notif-head">
            <div class="notif-head-icon {{ $isSuccess ? 'success' : 'error' }}">
                <i class="fas fa-{{ $isSuccess ? 'check' : 'exclamation-triangle' }}"></i>
            </div>
            <h6>{{ $isSuccess ? 'Import Berhasil' : 'Import Selesai' }}</h6>
            <button class="notif-close" onclick="closeNotif()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="notif-body">
            {{-- Statistik --}}
            <div class="notif-stats">
                <div class="notif-stat imported">
                    <div class="ns-num">{{ $importedNum }}</div>
                    <div class="ns-lbl">✅ Berhasil Diimpor</div>
                </div>
                <div class="notif-stat skipped">
                    <div class="ns-num">{{ $skippedNum }}</div>
                    <div class="ns-lbl">⏭️ Dilewati</div>
                </div>
            </div>

            {{-- Detail Error --}}
            @if(count($errItems) > 0)
            <div class="notif-errors">
                <div class="notif-errors-title">
                    <i class="fas fa-exclamation-circle"></i>
                    Detail Error ({{ count($errItems) }} item)
                </div>
                @foreach($errItems as $err)
                    <div class="notif-error-item">• {{ $err }}</div>
                @endforeach
            </div>
            @endif

            <div class="notif-footer">
                <button class="notif-btn-close {{ $isSuccess ? 'success' : 'error' }}"
                        onclick="closeNotif()">
                    <i class="fas fa-check"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── HEADER ── --}}
<div class="pg-header">
    <div class="pg-header-left">
        <h5>Data Pegawai</h5>
        <p>Kelola seluruh data pegawai aktif</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.pegawai.template') }}" class="hbtn hbtn-gray">
            <i class="fas fa-file-excel"></i> Template
        </a>
        <button type="button" class="hbtn hbtn-amber"
                onclick="document.getElementById('modalImport').classList.add('show')">
            <i class="fas fa-file-upload"></i> Import
        </button>
        <a href="{{ route('admin.pegawai.export') }}" class="hbtn hbtn-green">
            <i class="fas fa-file-download"></i> Export
        </a>
        <a href="{{ route('admin.pegawai.create') }}" class="hbtn hbtn-primary">
            <i class="fas fa-user-plus"></i> Tambah Pegawai
        </a>
    </div>
</div>

{{-- ── MODAL IMPORT ── --}}
<div id="modalImport" class="modal-overlay"
     onclick="if(event.target===this)this.classList.remove('show')">
    <div class="modal-box">
        <div class="modal-head">
            <h6><i class="fas fa-file-upload" style="color:#d97706;"></i> Import Data Pegawai</h6>
            <button class="modal-close"
                    onclick="document.getElementById('modalImport').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-info">
                <strong>📋 Petunjuk:</strong>
                <ol>
                    <li>Download <a href="{{ route('admin.pegawai.template') }}">template Excel</a> terlebih dahulu</li>
                    <li>Isi data pegawai sesuai format (jangan ubah nama kolom)</li>
                    <li>Upload file yang sudah diisi di bawah ini</li>
                </ol>
                <div class="modal-warn">
                    ⚠️ Data dengan NIP/email yang sudah ada akan <strong>dilewati</strong>.
                </div>
            </div>
            <form id="formImport" action="{{ route('admin.pegawai.import') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                <div class="file-upload-area" id="uploadArea">
                    <input type="file" name="file" id="fileInput" accept=".xlsx,.xls"
                           required onchange="showFileName(this)">
                    <div class="fu-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <p><strong>Klik untuk pilih file</strong> atau drag & drop</p>
                    <p style="font-size:.68rem;margin-top:.2rem;color:#9ca3af;">
                        Format: .xlsx / .xls &nbsp;·&nbsp; Maks 5MB
                    </p>
                    <p class="fu-name" id="fileName"></p>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel"
                            onclick="document.getElementById('modalImport').classList.remove('show')">
                        Batal
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-upload"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── FILTER ── --}}
<div class="filter-box">
    <form method="GET" action="{{ route('admin.pegawai.index') }}">
        <div class="filter-row">
            <div>
                <label class="flbl">Cari Pegawai</label>
                <div class="finput-wrap">
                    <i class="fas fa-search fi-ic"></i>
                    <input type="text" name="search" class="finput"
                           placeholder="Nama, SAP, email..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div>
                <label class="flbl">Jabatan</label>
                <select name="jabatan" class="fselect">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatanList ?? [] as $jbt)
                        <option value="{{ $jbt }}" {{ request('jabatan') == $jbt ? 'selected' : '' }}>
                            {{ $jbt }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="flbl">&nbsp;</label>
                <button type="submit" class="btn-cari">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
            <div>
                <label class="flbl">&nbsp;</label>
                <a href="{{ route('admin.pegawai.index') }}" class="btn-reset">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ── SUMMARY ── --}}
<div class="summary-bar">
    <div class="sb-info">
        Menampilkan <strong>{{ $pegawai->count() }}</strong>
        dari <strong>{{ $pegawai->total() }}</strong> pegawai
        @if(request('search') || request('jabatan'))
            <span style="color:var(--green-dark);font-weight:600;"> · hasil filter</span>
        @endif
    </div>
    <span class="sb-total">
        <i class="fas fa-users" style="font-size:.58rem;"></i>
        {{ $pegawai->total() }} Total
    </span>
</div>

{{-- ── GRID ── --}}
@if($pegawai->isEmpty())
<div class="empty-state">
    <div class="empty-icon">👥</div>
    <p style="font-weight:700;color:var(--text-primary);font-size:.85rem;margin-bottom:.25rem;">
        {{ request('search') || request('jabatan') ? 'Pegawai tidak ditemukan' : 'Belum ada pegawai' }}
    </p>
    <p style="font-size:.72rem;color:var(--text-light);margin-bottom:1rem;">
        {{ request('search') || request('jabatan') ? 'Coba ubah kata kunci pencarian' : 'Tambah pegawai baru untuk memulai' }}
    </p>
    @if(request('search') || request('jabatan'))
        <a href="{{ route('admin.pegawai.index') }}" class="btn-reset">
            <i class="fas fa-undo"></i> Reset Filter
        </a>
    @else
        <a href="{{ route('admin.pegawai.create') }}" class="hbtn hbtn-primary">
            <i class="fas fa-user-plus"></i> Tambah Pegawai
        </a>
    @endif
</div>
@else
<div class="pgw-grid">
    @foreach($pegawai as $p)
    @php
        $isAktif = $p->is_active ?? true;
        $inisial = strtoupper(substr($p->name ?? 'U', 0, 1));
    @endphp
    <div class="pgw-card">
        <div class="pgw-strip {{ $isAktif ? '' : 'off' }}"></div>
        <div class="pgw-body">
            <div class="pgw-top">
                <div class="pgw-av {{ $isAktif ? '' : 'off' }}">{{ $inisial }}</div>
                <div style="min-width:0;">
                    <div class="pgw-nm">{{ $p->name ?? '-' }}</div>
                    <div class="pgw-nip">SAP: {{ $p->nip ?? '-' }}</div>
                </div>
            </div>
            <div class="pgw-badges">
                @if($p->jabatan)
                    <span class="badge-jbt">
                        <i class="fas fa-briefcase" style="font-size:.52rem;"></i> {{ $p->jabatan }}
                    </span>
                @endif
                @if($p->divisi)
                    <span class="badge-div">{{ $p->divisi }}</span>
                @endif
            </div>
            <div class="pgw-info">
                @if($p->email)
                <div class="pgw-info-row">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $p->email }}</span>
                </div>
                @endif
                @if($p->phone)
                <div class="pgw-info-row">
                    <i class="fas fa-phone"></i>
                    <span>{{ $p->phone }}</span>
                </div>
                @endif
            </div>
            <hr class="pgw-divider">
            <div class="pgw-actions">
                <div>
                    @if($isAktif)
                        <span class="badge-aktif">
                            <i class="fas fa-circle" style="font-size:.42rem;"></i> Aktif
                        </span>
                    @else
                        <span class="badge-nonaktif">Nonaktif</span>
                    @endif
                </div>
                <div class="pgw-btns">
                    <a href="{{ route('admin.pegawai.show', $p->id) }}"
                       class="act-btn act-view" title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.pegawai.edit', $p->id) }}"
                       class="act-btn act-edit" title="Edit">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.pegawai.destroy', $p->id) }}" method="POST"
                          onsubmit="return confirm('Hapus pegawai {{ addslashes($p->name ?? '') }}?')"
                          style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="act-btn act-delete" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── PAGINATION ── --}}
@if($pegawai->hasPages())
<div class="pgn-wrap">
    <span class="pgn-info">
        Hal {{ $pegawai->currentPage() }} dari {{ $pegawai->lastPage() }}
    </span>
    <div class="pgn-pages">
        @if($pegawai->onFirstPage())
            <span class="pgn-btn disabled"><i class="fas fa-chevron-left"></i></span>
        @else
            <a href="{{ $pegawai->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
               class="pgn-btn"><i class="fas fa-chevron-left"></i></a>
        @endif

        @php
            $current = $pegawai->currentPage();
            $last    = $pegawai->lastPage();
            $start   = max(1, $current - 2);
            $end     = min($last, $current + 2);
        @endphp

        @if($start > 1)
            <a href="{{ $pegawai->url(1) }}" class="pgn-btn">1</a>
            @if($start > 2)
                <span class="pgn-btn disabled" style="border:none;background:none;color:var(--text-light);">…</span>
            @endif
        @endif

        @for($i = $start; $i <= $end; $i++)
            @if($i == $current)
                <span class="pgn-btn active">{{ $i }}</span>
            @else
                <a href="{{ $pegawai->url($i) }}" class="pgn-btn">{{ $i }}</a>
            @endif
        @endfor

        @if($end < $last)
            @if($end < $last - 1)
                <span class="pgn-btn disabled" style="border:none;background:none;color:var(--text-light);">…</span>
            @endif
            <a href="{{ $pegawai->url($last) }}" class="pgn-btn">{{ $last }}</a>
        @endif

        @if($pegawai->hasMorePages())
            <a href="{{ $pegawai->nextPageUrl() }}" class="pgn-btn">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span class="pgn-btn disabled"><i class="fas fa-chevron-right"></i></span>
        @endif
    </div>
</div>
@endif
@endif

@endsection

@push('scripts')
<script>
function closeNotif() {
    const el = document.getElementById('notifImport');
    if (el) {
        el.style.animation = 'fadeOut .2s ease forwards';
        setTimeout(() => el.remove(), 200);
    }
}

function showFileName(input) {
    const el = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        el.textContent = '📎 ' + input.files[0].name;
        el.style.display = 'block';
    }
}

const ua = document.getElementById('uploadArea');
if (ua) {
    ua.addEventListener('dragover',  e => { e.preventDefault(); ua.classList.add('dragover'); });
    ua.addEventListener('dragleave', () => ua.classList.remove('dragover'));
    ua.addEventListener('drop', e => {
        e.preventDefault(); ua.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            document.getElementById('fileInput').files = e.dataTransfer.files;
            showFileName(document.getElementById('fileInput'));
        }
    });
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('modalImport')?.classList.remove('show');
        closeNotif();
    }
});
</script>
<style>
@keyframes fadeOut {
    from { opacity: 1; }
    to   { opacity: 0; }
}
</style>
@endpush
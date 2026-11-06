@extends('layouts.app')

@section('title', 'Laporan Surat Pegawai')

@push('styles')
<style>
/* ══ HERO ══ */
.admin-hero {
    background: linear-gradient(135deg, #0f5132 0%, #157347 55%, #20c997 100%);
    border-radius: 16px; padding: 1.5rem 1.75rem;
    margin-bottom: 1.25rem; position: relative; overflow: hidden; color: #fff;
}
.admin-hero::before { content:'';position:absolute;top:-50%;right:-5%;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none; }
.admin-hero .hero-label {
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(8px); border-radius: 20px;
    padding: .22rem .7rem; font-size: .7rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: .3rem; margin-bottom: .5rem;
}
.admin-hero h2  { font-size: 1.25rem; font-weight: 800; margin-bottom: .2rem; }
.admin-hero .sub { color: rgba(255,255,255,.75); font-size: .78rem; }
.hero-date-badge {
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.2);
    border-radius: 8px; padding: .3rem .75rem;
    font-size: .72rem; font-weight: 600; color: #fff;
    display: inline-flex; align-items: center; gap: .3rem; white-space: nowrap;
}

/* ══ STAT CARDS ══ */
.stat-card {
    background: #fff; border-radius: 12px; padding: .7rem .8rem;
    border: 1px solid #f0f0f0; box-shadow: 0 2px 8px rgba(0,0,0,.04);
    position: relative; overflow: hidden;
    display: flex; align-items: center; gap: .55rem;
    transition: transform .2s, box-shadow .2s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 5px 14px rgba(0,0,0,.08); }
.stat-card .sc-icon {
    width:32px; height:32px; border-radius:9px;
    display:flex; align-items:center; justify-content:center;
    font-size:.82rem; flex-shrink:0;
}
.stat-card .sc-val   { font-size: 1.2rem; font-weight: 800; line-height: 1; color: #1a1a2e; }
.stat-card .sc-label { font-size: .62rem; color: #6b7280; font-weight: 500; margin-top:.1rem; white-space:nowrap; }
.stat-card .sc-bar   { position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 12px 12px; }

/* ══ TAB ══ */
.tab-row { display:flex; gap:.35rem; flex-wrap:wrap; margin-bottom:.9rem; }
.tab-btn {
    padding: .38rem .85rem; border-radius: 8px; font-size: .75rem; font-weight: 600;
    cursor: pointer; border: 1.5px solid #e4e7ec; background: #fff; color: #6b7280;
    transition: all .2s; display: inline-flex; align-items: center; gap: .3rem;
}
.tab-btn:hover { border-color: #157347; color: #157347; background: #f0fdf4; }
.tab-btn.active { background: #157347; color: #fff; border-color: #157347; }
.tab-btn .cnt { background: rgba(255,255,255,.25); border-radius: 20px; padding: .02rem .38rem; font-size: .62rem; }
.tab-btn:not(.active) .cnt { background: #f3f4f6; color: #374151; }

/* ══ FILTER ══ */
.filter-wrap {
    background: #fff; border-radius: 13px; padding: .9rem 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.04); border: 1px solid #f0f0f0; margin-bottom: .9rem;
}
.form-label-mini { font-size:.67rem; font-weight:700; color:#374151; margin-bottom:.25rem; text-transform:uppercase; letter-spacing:.4px; display:block; }
.filter-wrap .form-select,
.filter-wrap .form-control {
    border: 1.5px solid #e4e7ec; border-radius: 8px; font-size: .8rem; padding: .42rem .75rem; background: #fafafa;
}
.filter-wrap .form-select:focus,
.filter-wrap .form-control:focus { border-color:#157347; box-shadow:0 0 0 3px rgba(21,115,71,.1); background:#fff; }
.btn-terapkan {
    background: linear-gradient(135deg,#157347,#0f5132); color:#fff; border:none;
    border-radius:8px; padding:.45rem 1rem; font-weight:700; font-size:.8rem;
    display:inline-flex; align-items:center; gap:.3rem; cursor:pointer; white-space:nowrap; transition:opacity .2s;
}
.btn-terapkan:hover { opacity:.88; color:#fff; }
.btn-reset {
    background:#f8f9fa; color:#6b7280; border:1.5px solid #e4e7ec; border-radius:8px;
    padding:.45rem .8rem; font-size:.8rem; font-weight:600; display:inline-flex; align-items:center; gap:.3rem;
    text-decoration:none; white-space:nowrap; transition:background .2s;
}
.btn-reset:hover { background:#e9ecef; color:#374151; }

/* ══ TABLE CARD ══ */
.tbl-card { background:#fff; border-radius:16px; box-shadow:0 2px 10px rgba(0,0,0,.05); border:1px solid #f0f0f0; overflow:hidden; }
.tbl-head {
    padding:.9rem 1.1rem; border-bottom:1px solid #f4f4f5;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.4rem;
}
.tbl-head h6 { font-weight:800; color:#1a1a2e; margin:0; font-size:.875rem; }
.table > :not(caption) > * > * { padding:.7rem .95rem; vertical-align:middle; }
.table thead th { background:#f8f9fb; font-size:.66rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; border-bottom:1px solid #eaecf0; white-space:nowrap; }
.table tbody td { border-bottom:1px solid #f4f4f5; font-size:.8rem; }
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover { background:#fafbfc; }

/* ══ MOBILE CARD LIST ══ */
.laporan-list { display:none; padding:.75rem; }
.laporan-item {
    background:#fff; border-radius:13px; border:1px solid #eef0f2;
    box-shadow:0 1px 6px rgba(0,0,0,.05); padding:.9rem; margin-bottom:.6rem;
    transition:box-shadow .2s;
}
.laporan-item:last-child { margin-bottom:0; }
.laporan-item:hover { box-shadow:0 4px 12px rgba(0,0,0,.08); }
.li-top { display:flex; align-items:flex-start; justify-content:space-between; gap:.4rem; margin-bottom:.6rem; }
.li-user { display:flex; align-items:center; gap:.6rem; }
.av { width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#157347,#20c997);color:#fff;font-weight:800;font-size:.78rem;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.li-name { font-size:.82rem; font-weight:700; color:#1a1a2e; line-height:1.2; }
.li-jabatan { font-size:.67rem; color:#9ca3af; }
.li-body { display:grid; grid-template-columns:1fr 1fr; gap:.4rem .6rem; margin-bottom:.65rem; }
.li-field label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#9ca3af; display:block; margin-bottom:.1rem; }
.li-field span  { font-size:.75rem; color:#374151; font-weight:500; }
.li-actions { display:flex; gap:.35rem; flex-wrap:wrap; }

/* Badges */
.bs { padding:.22rem .6rem; border-radius:20px; font-size:.65rem; font-weight:700; display:inline-flex; align-items:center; gap:.25rem; white-space:nowrap; }
.bs-menunggu  { background:#fff7e6; color:#d97706; border:1px solid #fcd34d; }
.bs-disetujui { background:#f0fdf4; color:#15803d; border:1px solid #86efac; }
.bs-ditolak   { background:#fff5f5; color:#dc2626; border:1px solid #fca5a5; }
.bt { padding:.2rem .55rem; border-radius:6px; font-size:.65rem; font-weight:800; text-transform:uppercase; display:inline-flex; align-items:center; gap:.2rem; }
.bt-cuti  { background:#e0f2fe; color:#0369a1; }
.bt-izin  { background:#ede9fe; color:#7c3aed; }
.bt-sakit { background:#fee2e2; color:#dc2626; }

/* Action buttons */
.btn-lihat { background:#f0fdf4; color:#15803d; border:1px solid #86efac; border-radius:7px; padding:.25rem .7rem; font-size:.7rem; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; transition:background .15s; }
.btn-lihat:hover { background:#dcfce7; color:#15803d; }
.btn-lamp { background:#eff6ff; color:#2563eb; border:1px solid #93c5fd; border-radius:7px; padding:.24rem .6rem; font-size:.7rem; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:.22rem; }

/* Empty */
.empty-box { padding:2.5rem 1.5rem; text-align:center; }
.empty-box .em-icon { width:56px;height:56px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin:0 auto .75rem; }

/* ══ CHECKBOX & HAPUS MASSAL (BARU) ══ */
.checkbox-hapus {
    width: 16px; height: 16px; cursor: pointer; accent-color: #dc2626;
}
.checkbox-hapus:disabled { cursor: not-allowed; opacity: .3; }
.btn-hapus-massal {
    background: #dc2626; color: #fff; border: none; border-radius: 8px;
    padding: .38rem .9rem; font-size: .73rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: .35rem; cursor: pointer;
    transition: background .2s;
}
.btn-hapus-massal:hover { background: #b91c1c; color: #fff; }
.btn-hapus-massal:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
.li-check-wrap { display: flex; align-items: center; margin-bottom: .5rem; }
.li-check-wrap label { font-size: .68rem; color: #6b7280; font-weight: 600; margin-left: .4rem; }

/* ══ RESPONSIVE ══ */
@media(max-width:768px) {
    .admin-hero { padding:1rem 1.1rem; border-radius:13px; }
    .admin-hero h2 { font-size:1.05rem; }
    .admin-hero .sub { font-size:.72rem; }
    .stat-card { padding:.65rem .8rem; }
    .stat-card .sc-icon { width:32px; height:32px; font-size:.82rem; }
    .stat-card .sc-val { font-size:1.15rem; }
    .stat-card .sc-label { font-size:.62rem; }
    .filter-wrap { padding:.75rem .85rem; }
    .desktop-table { display: none !important; }
    .laporan-list  { display: block; }
    .tbl-head { padding:.75rem .9rem; }
}
@media(max-width:480px) {
    .admin-hero { padding:.9rem 1rem; }
    .admin-hero h2 { font-size:.95rem; }
    .tab-btn { padding:.3rem .65rem; font-size:.7rem; }
    .hero-date-badge { font-size:.65rem; }
}
</style>
@endpush

@section('content')

{{-- Deteksi role supaya link Detail & form Filter mengarah ke route yang
     benar, dan supaya fitur checkbox+hapus massal HANYA muncul untuk Admin
     (karena route hapusnya memang cuma didaftarkan di grup admin). --}}
@php
    $isManajer = auth()->user()->role === 'manajer';
    $routeIndex = $isManajer ? route('manajer.izin.index') : route('admin.izin.index');
@endphp

{{-- HERO --}}
<div class="admin-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div style="position:relative;z-index:1">
            <div class="hero-label">
                <i class="fas fa-chart-bar"></i>
                {{ $isManajer ? 'Panel Manajer' : 'Panel Admin' }}
            </div>
            <h2>📋 Laporan Surat Pegawai</h2>
            <div class="sub">Pantau & kelola semua laporan cuti, izin, dan sakit pegawai</div>
        </div>
        <div style="position:relative;z-index:1;align-self:flex-start;margin-top:.1rem;">
            <div class="hero-date-badge">
                <i class="fas fa-calendar-alt"></i>
                {{ \Carbon\Carbon::now()->isoFormat('ddd, D MMM Y') }}
            </div>
        </div>
    </div>
</div>

{{-- ALERTS --}}
@if(session('success'))
<div class="alert d-flex align-items-center gap-2 rounded-3 mb-3 border-0"
     style="background:#f0fdf4;color:#15803d;padding:.65rem .9rem;font-size:.8rem;">
    <i class="fas fa-check-circle"></i><div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size:.65rem;"></button>
</div>
@endif
@if(session('error'))
<div class="alert d-flex align-items-center gap-2 rounded-3 mb-3 border-0"
     style="background:#fff5f5;color:#dc2626;padding:.65rem .9rem;font-size:.8rem;">
    <i class="fas fa-exclamation-circle"></i><div>{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size:.65rem;"></button>
</div>
@endif

{{-- STATISTIK --}}
@php
    $total     = $laporan->count();
    $menunggu  = $laporan->where('status','menunggu')->count();
    $disetujui = $laporan->where('status','disetujui')->count();
    $ditolak   = $laporan->where('status','ditolak')->count();
    $jmlCuti   = $laporan->where('tipe','cuti')->count();
    $jmlIzin   = $laporan->where('tipe','izin')->count();
    $jmlSakit  = $laporan->where('tipe','sakit')->count();
@endphp

{{-- Baris 1: 3 kolom --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-bottom:.5rem;">
    <div class="stat-card">
        <div class="sc-icon" style="background:#f0f4ff;color:#3b82f6"><i class="fas fa-file-alt"></i></div>
        <div><div class="sc-val">{{ $total }}</div><div class="sc-label">Total</div></div>
        <div class="sc-bar" style="background:linear-gradient(90deg,#3b82f6,#60a5fa)"></div>
    </div>
    <div class="stat-card">
        <div class="sc-icon" style="background:#fffbeb;color:#d97706"><i class="fas fa-hourglass-half"></i></div>
        <div><div class="sc-val">{{ $menunggu }}</div><div class="sc-label">Menunggu</div></div>
        <div class="sc-bar" style="background:linear-gradient(90deg,#f59e0b,#fcd34d)"></div>
    </div>
    <div class="stat-card">
        <div class="sc-icon" style="background:#dcfce7;color:#15803d"><i class="fas fa-check-circle"></i></div>
        <div><div class="sc-val">{{ $disetujui }}</div><div class="sc-label">Disetujui</div></div>
        <div class="sc-bar" style="background:linear-gradient(90deg,#15803d,#4ade80)"></div>
    </div>
</div>
{{-- Baris 2: 3 kolom --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-bottom:1rem;">
    <div class="stat-card">
        <div class="sc-icon" style="background:#e0f2fe;color:#0369a1"><i class="fas fa-umbrella-beach"></i></div>
        <div><div class="sc-val">{{ $jmlCuti }}</div><div class="sc-label">Cuti</div></div>
        <div class="sc-bar" style="background:linear-gradient(90deg,#0369a1,#38bdf8)"></div>
    </div>
    <div class="stat-card">
        <div class="sc-icon" style="background:#ede9fe;color:#7c3aed"><i class="fas fa-file-signature"></i></div>
        <div><div class="sc-val">{{ $jmlIzin }}</div><div class="sc-label">Izin</div></div>
        <div class="sc-bar" style="background:linear-gradient(90deg,#7c3aed,#a78bfa)"></div>
    </div>
    <div class="stat-card">
        <div class="sc-icon" style="background:#fee2e2;color:#dc2626"><i class="fas fa-notes-medical"></i></div>
        <div><div class="sc-val">{{ $jmlSakit }}</div><div class="sc-label">Sakit</div></div>
        <div class="sc-bar" style="background:linear-gradient(90deg,#dc2626,#f87171)"></div>
    </div>
</div>

{{-- TAB --}}
<div class="tab-row">
    <button class="tab-btn active" onclick="filterTab('semua',this)">
        <i class="fas fa-list"></i> Semua <span class="cnt">{{ $total }}</span>
    </button>
    <button class="tab-btn" onclick="filterTab('menunggu',this)">
        <i class="fas fa-clock"></i> Menunggu <span class="cnt">{{ $menunggu }}</span>
    </button>
    <button class="tab-btn" onclick="filterTab('disetujui',this)">
        <i class="fas fa-check"></i> Disetujui <span class="cnt">{{ $disetujui }}</span>
    </button>
    <button class="tab-btn" onclick="filterTab('ditolak',this)">
        <i class="fas fa-times"></i> Ditolak <span class="cnt">{{ $ditolak }}</span>
    </button>
</div>

{{-- FILTER BAR --}}
<div class="filter-wrap">
    <form method="GET" action="{{ $routeIndex }}">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label-mini">Jenis Surat</label>
                <select name="tipe" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="cuti"  {{ request('tipe')=='cuti'  ?'selected':'' }}>🏖️ Cuti</option>
                    <option value="izin"  {{ request('tipe')=='izin'  ?'selected':'' }}>📋 Izin</option>
                    <option value="sakit" {{ request('tipe')=='sakit' ?'selected':'' }}>🏥 Sakit</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label-mini">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu"  {{ request('status')=='menunggu'  ?'selected':'' }}>⏳ Menunggu</option>
                    <option value="disetujui" {{ request('status')=='disetujui' ?'selected':'' }}>✅ Disetujui</option>
                    <option value="ditolak"   {{ request('status')=='ditolak'   ?'selected':'' }}>❌ Ditolak</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label-mini">Cari Pegawai</label>
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.7rem;pointer-events:none;"></i>
                    <input type="text" name="cari" class="form-control" style="padding-left:1.9rem;"
                        placeholder="Nama pegawai..." value="{{ request('cari') }}">
                </div>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn-terapkan flex-fill">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                <a href="{{ $routeIndex }}" class="btn-reset">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ✅ BARU — Form hapus massal (HANYA ADMIN). Membungkus seluruh tabel
     supaya checkbox yang tercentang (name="ids[]") ikut terkirim. --}}
@if(!$isManajer)
<form id="form-hapus-massal" action="{{ route('admin.izin.hapusTerverifikasi') }}" method="POST"
      onsubmit="return confirm('Yakin ingin menghapus data yang dipilih? Tindakan ini tidak bisa dibatalkan.');">
    @csrf
    @method('DELETE')
@endif

{{-- MAIN CARD --}}
<div class="tbl-card">
    <div class="tbl-head">
        <h6><i class="fas fa-clipboard-list me-2" style="color:#157347"></i>Data Laporan Pegawai</h6>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($menunggu > 0)
            <span style="background:#fff7e6;color:#d97706;border:1px solid #fcd34d;padding:.2rem .65rem;border-radius:20px;font-size:.65rem;font-weight:700;">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $menunggu }} perlu diproses
            </span>
            @endif
            <span id="jumlahData" style="background:#f0fdf4;color:#15803d;padding:.2rem .65rem;border-radius:20px;font-size:.65rem;font-weight:700;">
                {{ $laporan->count() }} data
            </span>

            {{-- ✅ BARU — tombol hapus massal, hanya untuk admin --}}
            @if(!$isManajer)
            <button type="submit" id="btnHapusMassal" class="btn-hapus-massal" disabled>
                <i class="fas fa-trash"></i> Hapus Terpilih (<span id="jumlahTerpilih">0</span>)
            </button>
            @endif
        </div>
    </div>

    @if($laporan->isEmpty())
    <div class="empty-box">
        <div class="em-icon">📭</div>
        <p class="fw-bold mb-1" style="color:#374151;font-size:.875rem;">Belum ada laporan</p>
        <p class="text-muted" style="font-size:.78rem;">Laporan muncul saat pegawai mengajukan surat</p>
    </div>
    @else

    {{-- TABEL DESKTOP --}}
    <div class="table-responsive desktop-table">
        <table class="table mb-0" id="tblLaporan">
            <thead>
                <tr>
                    {{-- ✅ BARU — checkbox "select all", hanya untuk admin --}}
                    @if(!$isManajer)
                    <th style="width:36px;">
                        <input type="checkbox" class="checkbox-hapus" id="checkAllDesktop">
                    </th>
                    @endif
                    <th>#</th><th>Pegawai</th><th>Jenis</th><th>Periode</th>
                    <th>Keterangan</th><th>Lampiran</th><th>Status</th><th>Tgl Ajuan</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporan as $i => $l)
                @php $sudahTuntas = in_array($l->status, ['disetujui', 'ditolak']); @endphp
                <tr data-status="{{ $l->status }}">
                    {{-- ✅ BARU — checkbox per baris, hanya untuk admin dan
                         hanya aktif kalau statusnya sudah tuntas --}}
                    @if(!$isManajer)
                    <td>
                        @if($sudahTuntas)
                            <input type="checkbox" name="ids[]" value="{{ $l->id }}" class="checkbox-hapus item-checkbox-desktop">
                        @else
                            <input type="checkbox" class="checkbox-hapus" disabled title="Hanya bisa dihapus jika sudah Disetujui/Ditolak">
                        @endif
                    </td>
                    @endif
                    <td class="text-muted fw-semibold" style="font-size:.7rem;">{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="av">{{ strtoupper(substr($l->user->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div style="font-size:.8rem;font-weight:700;color:#1a1a2e;">{{ $l->user->name ?? '-' }}</div>
                                <div style="font-size:.66rem;color:#9ca3af;">{{ $l->user->jabatan ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="bt bt-{{ $l->tipe }}">
                            @if($l->tipe=='cuti') 🏖️ @elseif($l->tipe=='izin') 📋 @else 🏥 @endif
                            {{ ucfirst($l->tipe) }}
                        </span>
                    </td>
                    <td>
                        <div style="font-size:.76rem;white-space:nowrap;">{{ \Carbon\Carbon::parse($l->tanggal_mulai)->format('d M Y') }}</div>
                        <div style="font-size:.68rem;color:#9ca3af;white-space:nowrap;">s/d {{ \Carbon\Carbon::parse($l->tanggal_selesai)->format('d M Y') }}</div>
                        <span style="background:#f3f4f6;color:#374151;font-size:.63rem;font-weight:700;padding:.08rem .38rem;border-radius:5px;">
                            {{ \Carbon\Carbon::parse($l->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($l->tanggal_selesai)) + 1 }} hari
                        </span>
                    </td>
                    <td style="max-width:130px;">
                        <span style="font-size:.76rem;color:#374151;" title="{{ $l->alasan }}">{{ Str::limit($l->alasan, 30) }}</span>
                    </td>
                    <td>
                        @if($l->lampiran_url)
                            <a href="{{ $l->lampiran_url }}" target="_blank" class="btn-lamp">
                                <i class="fas fa-file"></i> Lihat
                            </a>
                        @elseif($l->file_lampiran)
                            <span style="color:#dc2626;font-size:.68rem;" title="File tidak ditemukan di server">
                                <i class="fas fa-exclamation-triangle"></i> Error
                            </span>
                        @else
                            <span style="color:#d1d5db;font-size:.72rem;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($l->status=='menunggu')
                            <span class="bs bs-menunggu"><i class="fas fa-clock" style="font-size:.58rem;"></i> Menunggu</span>
                        @elseif($l->status=='disetujui')
                            <span class="bs bs-disetujui"><i class="fas fa-check" style="font-size:.58rem;"></i> Disetujui</span>
                        @else
                            <span class="bs bs-ditolak"><i class="fas fa-times" style="font-size:.58rem;"></i> Ditolak</span>
                        @endif
                    </td>
                    <td style="font-size:.7rem;color:#9ca3af;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($l->created_at)->format('d M Y') }}
                        <div style="font-size:.65rem;">{{ \Carbon\Carbon::parse($l->created_at)->format('H:i') }}</div>
                    </td>
                    <td>
                        <a href="{{ $isManajer ? route('manajer.izin.detail', $l->id) : route('admin.izin.detail', $l->id) }}" class="btn-lihat">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- CARD LIST MOBILE --}}
    <div class="laporan-list" id="laporanList">
        @foreach($laporan as $l)
        @php $sudahTuntas = in_array($l->status, ['disetujui', 'ditolak']); @endphp
        <div class="laporan-item" data-status="{{ $l->status }}">
            {{-- ✅ BARU — checkbox versi mobile, hanya untuk admin --}}
            @if(!$isManajer)
            <div class="li-check-wrap">
                @if($sudahTuntas)
                    <input type="checkbox" name="ids[]" value="{{ $l->id }}" class="checkbox-hapus item-checkbox-mobile">
                    <label>Pilih untuk dihapus</label>
                @else
                    <input type="checkbox" class="checkbox-hapus" disabled>
                    <label>Belum bisa dihapus (masih menunggu)</label>
                @endif
            </div>
            @endif
            <div class="li-top">
                <div class="li-user">
                    <div class="av">{{ strtoupper(substr($l->user->name ?? 'U', 0, 1)) }}</div>
                    <div>
                        <div class="li-name">{{ $l->user->name ?? '-' }}</div>
                        <div class="li-jabatan">{{ $l->user->jabatan ?? '-' }}</div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-end gap-1">
                    <span class="bt bt-{{ $l->tipe }}">
                        @if($l->tipe=='cuti') 🏖️ @elseif($l->tipe=='izin') 📋 @else 🏥 @endif
                        {{ ucfirst($l->tipe) }}
                    </span>
                    @if($l->status=='menunggu')
                        <span class="bs bs-menunggu"><i class="fas fa-clock" style="font-size:.55rem;"></i> Menunggu</span>
                    @elseif($l->status=='disetujui')
                        <span class="bs bs-disetujui"><i class="fas fa-check" style="font-size:.55rem;"></i> Disetujui</span>
                    @else
                        <span class="bs bs-ditolak"><i class="fas fa-times" style="font-size:.55rem;"></i> Ditolak</span>
                    @endif
                </div>
            </div>
            <div class="li-body">
                <div class="li-field">
                    <label>Tanggal Mulai</label>
                    <span>{{ \Carbon\Carbon::parse($l->tanggal_mulai)->format('d M Y') }}</span>
                </div>
                <div class="li-field">
                    <label>Tanggal Selesai</label>
                    <span>{{ \Carbon\Carbon::parse($l->tanggal_selesai)->format('d M Y') }}</span>
                </div>
                <div class="li-field">
                    <label>Durasi</label>
                    <span>{{ \Carbon\Carbon::parse($l->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($l->tanggal_selesai)) + 1 }} hari</span>
                </div>
                <div class="li-field">
                    <label>Tgl Ajuan</label>
                    <span>{{ \Carbon\Carbon::parse($l->created_at)->format('d M Y') }}</span>
                </div>
                <div class="li-field" style="grid-column:1/-1;">
                    <label>Keterangan</label>
                    <span>{{ Str::limit($l->alasan, 70) }}</span>
                </div>
            </div>
            <div class="li-actions">
                <a href="{{ $isManajer ? route('manajer.izin.detail', $l->id) : route('admin.izin.detail', $l->id) }}" class="btn-lihat">
                    <i class="fas fa-eye"></i> Detail
                </a>
                @if($l->lampiran_url)
                <a href="{{ $l->lampiran_url }}" target="_blank" class="btn-lamp">
                    <i class="fas fa-file"></i> Lampiran
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @endif
</div>

@if(!$isManajer)
</form>
@endif

@push('scripts')
<script>
function filterTab(status, el) {
    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
    el.classList.add('active');

    let visible = 0;

    document.querySelectorAll('#tblLaporan tbody tr').forEach(r => {
        const show = status === 'semua' || r.dataset.status === status;
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.querySelectorAll('#laporanList .laporan-item').forEach(c => {
        const show = status === 'semua' || c.dataset.status === status;
        c.style.display = show ? '' : 'none';
    });

    const el2 = document.getElementById('jumlahData');
    if (el2) el2.textContent = visible + ' data';
}

// ✅ BARU — Logika checkbox & tombol hapus massal (khusus Admin).
// Checkbox desktop dan mobile disinkronkan berdasarkan value yang sama,
// supaya user tetap bisa memilih walau berpindah antara tampilan
// desktop/mobile (resize browser).
document.addEventListener('DOMContentLoaded', function () {
    const btnHapus     = document.getElementById('btnHapusMassal');
    const labelJumlah  = document.getElementById('jumlahTerpilih');
    const checkAllDesk = document.getElementById('checkAllDesktop');

    // Kalau elemen-elemen ini tidak ada (berarti sedang login sebagai
    // Manajer, bukan Admin), hentikan script di sini.
    if (!btnHapus) return;

    const itemsDesktop = document.querySelectorAll('.item-checkbox-desktop');
    const itemsMobile  = document.querySelectorAll('.item-checkbox-mobile');

    function updateTombol() {
        const dicentang = document.querySelectorAll('input[name="ids[]"]:checked').length;
        labelJumlah.textContent = dicentang;
        btnHapus.disabled = dicentang === 0;
    }

    function setChecked(value, checked) {
        document.querySelectorAll('input[name="ids[]"][value="' + value + '"]')
            .forEach(cb => cb.checked = checked);
    }

    // Saat checkbox desktop diklik, samakan dengan checkbox mobile (value sama)
    itemsDesktop.forEach(cb => {
        cb.addEventListener('change', function () {
            setChecked(cb.value, cb.checked);
            if (!cb.checked && checkAllDesk) checkAllDesk.checked = false;
            updateTombol();
        });
    });

    itemsMobile.forEach(cb => {
        cb.addEventListener('change', function () {
            setChecked(cb.value, cb.checked);
            updateTombol();
        });
    });

    if (checkAllDesk) {
        checkAllDesk.addEventListener('change', function () {
            itemsDesktop.forEach(cb => {
                cb.checked = checkAllDesk.checked;
                setChecked(cb.value, checkAllDesk.checked);
            });
            updateTombol();
        });
    }

    updateTombol();
});
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'Laporan Absensi')

@push('styles')
<style>
/* ══ HERO — disederhanakan, tanpa dekorasi berlebih ══ */
.absen-hero {
    background: #157347;
    border-radius: 14px;
    padding: 1.1rem 1.25rem;
    margin-bottom: 1rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
}
.absen-hero h2 {
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0 0 .15rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.absen-hero .sub {
    color: rgba(255,255,255,.78);
    font-size: .75rem;
    margin: 0;
}
.absen-hero .hero-date {
    background: rgba(255,255,255,.15);
    border-radius: 8px;
    padding: .3rem .7rem;
    font-size: .72rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    white-space: nowrap;
}

/* ══ MODE TABS ══ */
.mode-tabs {
    display: flex;
    gap: .4rem;
    margin-bottom: .9rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.mode-tabs::-webkit-scrollbar { display: none; }
.mode-tab {
    padding: .42rem 1rem;
    border-radius: 8px;
    font-size: .78rem;
    font-weight: 700;
    border: 1px solid #e4e7ec;
    background: #fff;
    color: #6b7280;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    white-space: nowrap;
    flex-shrink: 0;
    transition: background .15s, color .15s, border-color .15s;
}
.mode-tab:hover  { border-color: #157347; color: #157347; }
.mode-tab.active { background: #157347; color: #fff; border-color: #157347; }

/* ══ FILTER CARD ══ */
.filter-card {
    background: #fff;
    border-radius: 12px;
    padding: .9rem 1rem;
    border: 1px solid #eef0f2;
    margin-bottom: .9rem;
}
.form-label-mini {
    font-size: .66rem;
    font-weight: 700;
    color: #6b7280;
    margin-bottom: .25rem;
    text-transform: uppercase;
    letter-spacing: .3px;
    display: block;
}
.filter-card .form-select,
.filter-card .form-control {
    border: 1px solid #e4e7ec;
    border-radius: 8px;
    font-size: .8rem;
    padding: .42rem .7rem;
}
.filter-card .form-select:focus,
.filter-card .form-control:focus {
    border-color: #157347;
    box-shadow: 0 0 0 3px rgba(21,115,71,.1);
}
.btn-filter {
    background: #157347;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .45rem 1rem;
    font-weight: 700;
    font-size: .8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    cursor: pointer;
    transition: background .15s;
}
.btn-filter:hover { background: #0f5132; }
.btn-reset-f {
    background: #f8f9fa;
    color: #6b7280;
    border: 1px solid #e4e7ec;
    border-radius: 8px;
    padding: .45rem .7rem;
    font-size: .8rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
    flex-shrink: 0;
}
.btn-reset-f:hover { background: #eef0f2; }

/* ══ FILTER GRID — CSS Grid sendiri, lebih reliable di semua ukuran layar ══ */
.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: .6rem;
    align-items: end;
}
.filter-grid .ff-actions {
    display: flex;
    gap: .5rem;
}
@media (max-width: 480px) {
    .filter-grid { grid-template-columns: repeat(2, 1fr); }
    .filter-grid .ff-actions { grid-column: span 2; }
}

/* ══ STAT CARDS — satu baris rapi, auto-wrap di mobile ══ */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: .6rem;
    margin-bottom: 1rem;
}
.stat-mini {
    background: #fff;
    border-radius: 10px;
    padding: .75rem .85rem;
    border: 1px solid #eef0f2;
    display: flex;
    align-items: center;
    gap: .6rem;
}
.stat-mini .sm-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
}
.stat-mini .sm-val   { font-size: 1.15rem; font-weight: 800; line-height: 1; color: #1a1a2e; }
.stat-mini .sm-label { font-size: .68rem; color: #6b7280; font-weight: 500; }

/* ══ TABLE CARD ══ */
.tbl-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eef0f2;
    overflow: hidden;
}
.tbl-head {
    padding: .8rem 1rem;
    border-bottom: 1px solid #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    flex-wrap: wrap;
}
.tbl-head h6 {
    font-weight: 700;
    color: #1a1a2e;
    margin: 0;
    font-size: .82rem;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.tbl-head .badge-count {
    background: #f0fdf4;
    color: #15803d;
    padding: .2rem .6rem;
    border-radius: 20px;
    font-size: .65rem;
    font-weight: 700;
    white-space: nowrap;
}
.table > :not(caption) > * > * { padding: .6rem .85rem; vertical-align: middle; }
.table thead th {
    background: #fafbfc;
    font-size: .64rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #9ca3af;
    border-bottom: 1px solid #eaecf0;
    white-space: nowrap;
}
.table-responsive { -webkit-overflow-scrolling: touch; }
.table tbody td { border-bottom: 1px solid #f6f6f7; font-size: .8rem; }
.table tbody tr:last-child td { border-bottom: none; }
.table tbody tr:hover { background: #fafbfc; }

/* ══ STATUS BADGES ══ */
.sb {
    padding: .2rem .55rem;
    border-radius: 20px;
    font-size: .65rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    white-space: nowrap;
}
.sb-hadir     { background: #f0fdf4; color: #15803d; }
.sb-terlambat { background: #fff7e6; color: #d97706; }
.sb-alpha     { background: #fef2f2; color: #dc2626; }
.sb-izin      { background: #f3effe; color: #7c3aed; }
.sb-cuti      { background: #eff8fe; color: #0369a1; }
.sb-sakit     { background: #fef2f2; color: #dc2626; }

/* Sel mingguan */
.cell-status {
    font-weight: 700;
    text-align: center;
    font-size: .68rem;
    border-radius: 6px;
    padding: .15rem .3rem;
    display: inline-block;
    min-width: 56px;
}
.cell-hadir     { background: #f0fdf4; color: #15803d; }
.cell-terlambat { background: #fff7e6; color: #d97706; }
.cell-alpha     { background: #fef2f2; color: #dc2626; }
.cell-izin      { background: #f3effe; color: #7c3aed; }
.cell-cuti      { background: #eff8fe; color: #0369a1; }
.cell-sakit     { background: #fef2f2; color: #dc2626; }
.cell-libur     { background: #f4f4f5; color: #9ca3af; font-weight: 600; }

/* Avatar kecil */
.av-sm {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    background: #157347;
    color: #fff;
    font-weight: 700;
    font-size: .65rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Progress kehadiran */
.pct-bar  { height: 4px; border-radius: 3px; background: #f0f1f3; overflow: hidden; margin-top: 3px; min-width: 56px; }
.pct-fill { height: 100%; border-radius: 3px; }

/* Empty state */
.empty-box { padding: 2.25rem 1.5rem; text-align: center; }
.empty-box .em-icon {
    width: 48px; height: 48px;
    background: #f4f4f5;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    margin: 0 auto .65rem;
}

/* Legend */
.legend-wrap { display: flex; flex-wrap: wrap; gap: .5rem 1rem; align-items: center; font-size: .7rem; color: #6b7280; }
.leg-dot { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; display: inline-block; margin-right: .3rem; }

/* ══ LOKASI ══ */
.loc-link {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .72rem;
    font-weight: 700;
    color: #157347;
    text-decoration: none;
    padding: .18rem .5rem;
    border-radius: 6px;
    background: #f0fdf4;
    white-space: nowrap;
    transition: background .15s;
}
.loc-link:hover { background: #dcfce7; color: #0f5132; }
.loc-empty { color: #d1d5db; font-size: .75rem; }
.tipe-kerja-badge {
    font-size: .62rem;
    font-weight: 700;
    padding: .12rem .45rem;
    border-radius: 5px;
    display: inline-block;
    margin-bottom: .2rem;
    white-space: nowrap;
}
.tk-wfo   { background: #eff8fe; color: #0369a1; }
.tk-wfh   { background: #f3effe; color: #7c3aed; }
.tk-dinas { background: #fff7e6; color: #d97706; }
.tk-default { background: #f4f4f5; color: #6b7280; }

/* ══ TOMBOL UNDUH EXCEL ══ */
.btn-unduh-excel {
    background: linear-gradient(135deg,#16a34a,#15803d);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .38rem .85rem;
    font-size: .72rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    text-decoration: none;
    white-space: nowrap;
    transition: opacity .15s;
}
.btn-unduh-excel:hover { opacity: .88; color: #fff; }
.tbl-head-actions { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }

/* ══ MOBILE CARD VIEW — pengganti tabel di layar kecil, semua data tampil tanpa scroll horizontal ══ */
.mc-list { display: flex; flex-direction: column; }
.mc-item { padding: .85rem 1rem; border-bottom: 1px solid #f4f4f5; }
.mc-item:last-child { border-bottom: none; }
.mc-top { display: flex; align-items: center; gap: .6rem; margin-bottom: .6rem; }
.mc-name { font-size: .82rem; font-weight: 700; color: #1a1a2e; }
.mc-jabatan { font-size: .68rem; color: #9ca3af; }
.mc-rows { display: flex; flex-direction: column; gap: .35rem; }
.mc-row { display: flex; align-items: center; justify-content: space-between; gap: .5rem; font-size: .76rem; }
.mc-row .mc-k { color: #9ca3af; font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; flex-shrink: 0; }
.mc-row .mc-v { color: #1a1a2e; font-weight: 600; text-align: right; }
.mc-chips { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .5rem; }
.mc-chip {
    font-size: .66rem; font-weight: 700; border-radius: 6px;
    padding: .25rem .5rem; display: flex; flex-direction: column; align-items: center;
    min-width: 44px;
}
.mc-chip .mc-chip-label { font-size: .58rem; font-weight: 600; opacity: .75; text-transform: uppercase; }
.mc-chip .mc-chip-val { font-size: .82rem; font-weight: 800; }
.mc-days { display: flex; flex-wrap: wrap; gap: .35rem; margin-bottom: .55rem; }
.mc-day { font-size: .65rem; border-radius: 6px; padding: .3rem .45rem; text-align: center; min-width: 56px; }
.mc-day .mc-day-label { font-weight: 700; opacity: .7; display: block; font-size: .6rem; }
.mc-day .mc-day-status { font-weight: 800; display: block; margin-top: 1px; }

@media (min-width: 768px) {
    .mc-list { display: none; }
}
@media (max-width: 767px) {
    .table-responsive { display: none; }
}

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
    .absen-hero { padding: 1rem; }
    .absen-hero h2 { font-size: .95rem; }
    .tbl-head { padding: .7rem .85rem; }
}
@media (max-width: 480px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
    .stat-mini .sm-val { font-size: 1rem; }
}
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<div class="absen-hero">
    <div>
        <h2>📊 Laporan Absensi Pegawai</h2>
        <p class="sub">Rekap harian, mingguan, dan bulanan kehadiran seluruh pegawai</p>
    </div>
    <div class="hero-date">
        <i class="fas fa-calendar-alt"></i>
        {{ \Carbon\Carbon::now()->isoFormat('ddd, D MMM Y') }}
    </div>
</div>

{{-- ── MODE TABS ── --}}
<div class="mode-tabs">
    <a href="{{ route('admin.laporan.absensi', ['mode'=>'harian']) }}"
       class="mode-tab {{ $mode==='harian' ? 'active' : '' }}">
        <i class="fas fa-calendar-day"></i> Harian
    </a>
    <a href="{{ route('admin.laporan.absensi', ['mode'=>'mingguan','tahun'=>$tahun,'bulan'=>$bulan]) }}"
       class="mode-tab {{ $mode==='mingguan' ? 'active' : '' }}">
        <i class="fas fa-calendar-week"></i> Mingguan
    </a>
    <a href="{{ route('admin.laporan.absensi', ['mode'=>'bulanan','tahun'=>$tahun,'bulan'=>$bulan]) }}"
       class="mode-tab {{ $mode==='bulanan' ? 'active' : '' }}">
        <i class="fas fa-calendar-alt"></i> Bulanan
    </a>
</div>

{{-- ══════════════════════════════ MODE HARIAN ══════════════════════════════ --}}
@if($mode === 'harian')

<div class="filter-card">
    <form method="GET" action="{{ route('admin.laporan.absensi') }}">
        <input type="hidden" name="mode" value="harian">
        <div class="filter-grid">
            <div>
                <label class="form-label-mini">Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       value="{{ $tanggal }}" max="{{ today()->toDateString() }}">
            </div>
            <div class="ff-actions">
                <button type="submit" class="btn-filter flex-fill">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                <a href="{{ route('admin.laporan.absensi', ['mode'=>'harian']) }}" class="btn-reset-f">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="stat-grid">
    <div class="stat-mini">
        <div class="sm-icon" style="background:#f0fdf4;color:#15803d"><i class="fas fa-user-check"></i></div>
        <div><div class="sm-val">{{ $ringkasan['hadir'] }}</div><div class="sm-label">Hadir</div></div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#fff7e6;color:#d97706"><i class="fas fa-clock"></i></div>
        <div><div class="sm-val">{{ $ringkasan['terlambat'] }}</div><div class="sm-label">Terlambat</div></div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#fef2f2;color:#dc2626"><i class="fas fa-user-times"></i></div>
        <div><div class="sm-val">{{ $ringkasan['alpha'] }}</div><div class="sm-label">Alpha</div></div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#f3effe;color:#7c3aed"><i class="fas fa-file-signature"></i></div>
        <div><div class="sm-val">{{ $ringkasan['izin'] }}</div><div class="sm-label">Izin</div></div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#eff8fe;color:#0369a1"><i class="fas fa-umbrella-beach"></i></div>
        <div><div class="sm-val">{{ $ringkasan['cuti'] }}</div><div class="sm-label">Cuti</div></div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#fef2f2;color:#dc2626"><i class="fas fa-notes-medical"></i></div>
        <div><div class="sm-val">{{ $ringkasan['sakit'] }}</div><div class="sm-label">Sakit</div></div>
    </div>
</div>

<div class="tbl-card">
    <div class="tbl-head">
        <h6><i class="fas fa-list-alt" style="color:#157347"></i> Kehadiran — {{ $tglCarbon->isoFormat('dddd, D MMMM Y') }}</h6>
        <div class="tbl-head-actions">
            <a href="{{ route('admin.laporan.absensi.export', ['mode' => 'harian', 'tanggal' => $tanggal]) }}" class="btn-unduh-excel">
                <i class="fas fa-file-excel"></i> Unduh Excel
            </a>
            <span class="badge-count">{{ $totalPegawai }} pegawai</span>
        </div>
    </div>

    @if($dataPegawai->isEmpty())
    <div class="empty-box">
        <div class="em-icon">👥</div>
        <p class="fw-bold mb-0" style="color:#374151;font-size:.85rem;">Belum ada data pegawai</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Pegawai</th><th>Status</th><th>Tipe</th>
                    <th>Check In</th><th>Lokasi Masuk</th>
                    <th>Check Out</th><th>Lokasi Keluar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataPegawai as $i => $row)
                @php
                    $st = strtolower($row['status']);
                    $badgeClass = match($st) {
                        'hadir' => 'sb-hadir', 'terlambat' => 'sb-terlambat', 'alpha' => 'sb-alpha',
                        'izin' => 'sb-izin', 'cuti' => 'sb-cuti', 'sakit' => 'sb-sakit', default => 'sb-alpha',
                    };
                    $badgeIcon = match($st) {
                        'hadir' => 'fas fa-check', 'terlambat' => 'fas fa-clock', 'alpha' => 'fas fa-times',
                        'izin' => 'fas fa-file-signature', 'cuti' => 'fas fa-umbrella-beach',
                        'sakit' => 'fas fa-notes-medical', default => 'fas fa-question',
                    };

                    // ── Badge tipe kerja (WFO/WFH/Dinas Luar) ──
                    $tk = strtolower($row['tipe_kerja'] ?? '-');
                    $tkClass = match(true) {
                        str_contains($tk, 'wfh')   => 'tk-wfh',
                        str_contains($tk, 'wfo')   => 'tk-wfo',
                        str_contains($tk, 'dinas') => 'tk-dinas',
                        default => 'tk-default',
                    };
                @endphp
                <tr>
                    <td class="text-muted" style="font-size:.7rem;">{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="av-sm">{{ strtoupper(substr($row['user']->name, 0, 1)) }}</div>
                            <div>
                                <div style="font-size:.8rem;font-weight:700;color:#1a1a2e;">{{ $row['user']->name }}</div>
                                <div style="font-size:.65rem;color:#9ca3af;">{{ $row['user']->jabatan ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="sb {{ $badgeClass }}"><i class="{{ $badgeIcon }}" style="font-size:.55rem;"></i> {{ $row['status'] }}</span>
                    </td>
                    <td>
                        @if($row['tipe'] !== '-')
                            <span style="font-size:.75rem;font-weight:600;color:#374151;">{{ $row['tipe'] }}</span>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td>{{ $row['check_in'] !== '-' ? $row['check_in'] : '—' }}</td>
                    <td>
                        @if(!empty($row['check_in_lat']) && !empty($row['check_in_lng']))
                            @if(!empty($row['tipe_kerja']) && $row['tipe_kerja'] !== '-')
                                <span class="tipe-kerja-badge {{ $tkClass }}">{{ $row['tipe_kerja'] }}</span><br>
                            @endif
                            <a href="https://maps.google.com/?q={{ $row['check_in_lat'] }},{{ $row['check_in_lng'] }}"
                               target="_blank" rel="noopener" class="loc-link" title="{{ $row['check_in_alamat'] ?? 'Lihat lokasi di peta' }}">
                                <i class="fas fa-map-marker-alt"></i> Lihat Peta
                            </a>
                        @else
                            <span class="loc-empty">—</span>
                        @endif
                    </td>
                    <td>{{ $row['check_out'] !== '-' ? $row['check_out'] : '—' }}</td>
                    <td>
                        @if(!empty($row['check_out_lat']) && !empty($row['check_out_lng']))
                            <a href="https://maps.google.com/?q={{ $row['check_out_lat'] }},{{ $row['check_out_lng'] }}"
                               target="_blank" rel="noopener" class="loc-link" title="{{ $row['check_out_alamat'] ?? 'Lihat lokasi di peta' }}">
                                <i class="fas fa-map-marker-alt"></i> Lihat Peta
                            </a>
                        @else
                            <span class="loc-empty">—</span>
                        @endif
                    </td>
                    <td style="max-width:150px;font-size:.75rem;color:#6b7280;" title="{{ $row['keterangan'] }}">
                        {{ $row['keterangan'] ? \Str::limit($row['keterangan'], 30) : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile card view --}}
    <div class="mc-list">
        @foreach($dataPegawai as $i => $row)
        @php
            $st = strtolower($row['status']);
            $badgeClass = match($st) {
                'hadir' => 'sb-hadir', 'terlambat' => 'sb-terlambat', 'alpha' => 'sb-alpha',
                'izin' => 'sb-izin', 'cuti' => 'sb-cuti', 'sakit' => 'sb-sakit', default => 'sb-alpha',
            };
            $badgeIcon = match($st) {
                'hadir' => 'fas fa-check', 'terlambat' => 'fas fa-clock', 'alpha' => 'fas fa-times',
                'izin' => 'fas fa-file-signature', 'cuti' => 'fas fa-umbrella-beach',
                'sakit' => 'fas fa-notes-medical', default => 'fas fa-question',
            };
            $tk = strtolower($row['tipe_kerja'] ?? '-');
            $tkClass = match(true) {
                str_contains($tk, 'wfh')   => 'tk-wfh',
                str_contains($tk, 'wfo')   => 'tk-wfo',
                str_contains($tk, 'dinas') => 'tk-dinas',
                default => 'tk-default',
            };
        @endphp
        <div class="mc-item">
            <div class="mc-top">
                <div class="av-sm">{{ strtoupper(substr($row['user']->name, 0, 1)) }}</div>
                <div>
                    <div class="mc-name">{{ $row['user']->name }}</div>
                    <div class="mc-jabatan">{{ $row['user']->jabatan ?? '-' }}</div>
                </div>
            </div>
            <div class="mc-rows">
                <div class="mc-row">
                    <span class="mc-k">Status</span>
                    <span class="sb {{ $badgeClass }}"><i class="{{ $badgeIcon }}" style="font-size:.55rem;"></i> {{ $row['status'] }}</span>
                </div>
                <div class="mc-row"><span class="mc-k">Tipe</span><span class="mc-v">{{ $row['tipe'] !== '-' ? $row['tipe'] : '—' }}</span></div>
                <div class="mc-row"><span class="mc-k">Check In</span><span class="mc-v">{{ $row['check_in'] !== '-' ? $row['check_in'] : '—' }}</span></div>
                <div class="mc-row">
                    <span class="mc-k">Lokasi Masuk</span>
                    <span class="mc-v">
                        @if(!empty($row['check_in_lat']) && !empty($row['check_in_lng']))
                            <a href="https://maps.google.com/?q={{ $row['check_in_lat'] }},{{ $row['check_in_lng'] }}"
                               target="_blank" rel="noopener" class="loc-link">
                                <i class="fas fa-map-marker-alt"></i> Lihat Peta
                            </a>
                        @else
                            <span class="loc-empty">—</span>
                        @endif
                    </span>
                </div>
                <div class="mc-row"><span class="mc-k">Check Out</span><span class="mc-v">{{ $row['check_out'] !== '-' ? $row['check_out'] : '—' }}</span></div>
                <div class="mc-row">
                    <span class="mc-k">Lokasi Keluar</span>
                    <span class="mc-v">
                        @if(!empty($row['check_out_lat']) && !empty($row['check_out_lng']))
                            <a href="https://maps.google.com/?q={{ $row['check_out_lat'] }},{{ $row['check_out_lng'] }}"
                               target="_blank" rel="noopener" class="loc-link">
                                <i class="fas fa-map-marker-alt"></i> Lihat Peta
                            </a>
                        @else
                            <span class="loc-empty">—</span>
                        @endif
                    </span>
                </div>
                @if(!empty($row['tipe_kerja']) && $row['tipe_kerja'] !== '-')
                <div class="mc-row">
                    <span class="mc-k">Tipe Kerja</span>
                    <span class="tipe-kerja-badge {{ $tkClass }}">{{ $row['tipe_kerja'] }}</span>
                </div>
                @endif
                @if($row['keterangan'])
                <div class="mc-row"><span class="mc-k">Ket.</span><span class="mc-v">{{ $row['keterangan'] }}</span></div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ══════════════════════════════ MODE MINGGUAN ══════════════════════════════ --}}
@elseif($mode === 'mingguan')

<div class="filter-card">
    <form method="GET" action="{{ route('admin.laporan.absensi') }}">
        <input type="hidden" name="mode" value="mingguan">
        <div class="filter-grid">
            <div>
                <label class="form-label-mini">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label-mini">Bulan</label>
                <select name="bulan" class="form-select">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bi => $bn)
                        <option value="{{ $bi+1 }}" {{ $bulan==$bi+1?'selected':'' }}>{{ $bn }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label-mini">Minggu</label>
                <select name="minggu" class="form-select">
                    @foreach($daftarMinggu as $m)
                        <option value="{{ $m['ke'] }}" {{ $minggu==$m['ke']?'selected':'' }}>{{ $m['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ff-actions">
                <button type="submit" class="btn-filter flex-fill">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                <a href="{{ route('admin.laporan.absensi', ['mode'=>'mingguan']) }}" class="btn-reset-f">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="legend-wrap mb-2">
    <span><span class="leg-dot" style="background:#dcfce7;"></span>Hadir</span>
    <span><span class="leg-dot" style="background:#fde7b8;"></span>Terlambat</span>
    <span><span class="leg-dot" style="background:#fecaca;"></span>Alpha</span>
    <span><span class="leg-dot" style="background:#e3d6fb;"></span>Izin</span>
    <span><span class="leg-dot" style="background:#bee6fd;"></span>Cuti</span>
    <span><span class="leg-dot" style="background:#f4f4f5;"></span>Libur</span>
</div>

<div class="tbl-card">
    <div class="tbl-head">
        <h6><i class="fas fa-calendar-week" style="color:#157347"></i>
            Minggu {{ $minggu }} — {{ $awalMinggu->format('d M') }} s/d {{ $akhirMinggu->format('d M Y') }}
        </h6>
        <div class="tbl-head-actions">
            <a href="{{ route('admin.laporan.absensi.export', ['mode'=>'mingguan','tahun'=>$tahun,'bulan'=>$bulan,'minggu'=>$minggu]) }}" class="btn-unduh-excel">
                <i class="fas fa-file-excel"></i> Unduh Excel
            </a>
            <span class="badge-count">{{ $totalPegawai }} pegawai</span>
        </div>
    </div>

    @if($rekapMinggu->isEmpty())
    <div class="empty-box"><div class="em-icon">📅</div><p class="fw-bold mb-0" style="color:#374151;font-size:.85rem;">Belum ada data</p></div>
    @else
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Pegawai</th>
                    @foreach($hariKerja as $hk)
                        <th class="text-center">{{ $hk->isoFormat('ddd') }}<br><span style="font-size:.6rem;">{{ $hk->format('d/m') }}</span></th>
                    @endforeach
                    <th class="text-center">Hadir</th><th class="text-center">Telat</th>
                    <th class="text-center">Alpha</th><th class="text-center">I/C/S</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapMinggu as $i => $row)
                <tr>
                    <td class="text-muted" style="font-size:.7rem;">{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="av-sm">{{ strtoupper(substr($row['user']->name, 0, 1)) }}</div>
                            <div>
                                <div style="font-size:.78rem;font-weight:700;color:#1a1a2e;">{{ $row['user']->name }}</div>
                                <div style="font-size:.63rem;color:#9ca3af;">{{ $row['user']->jabatan ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    @foreach($hariKerja as $hk)
                        @php
                            $tgl  = $hk->toDateString();
                            $det  = $row['detail'][$tgl] ?? null;
                            $st   = is_array($det) ? ($det['status'] ?? '—') : ($det ?? '—');
                            $stL  = strtolower($st);
                            $cellClass = match($stL) {
                                'hadir' => 'cell-hadir', 'terlambat' => 'cell-terlambat', 'alpha' => 'cell-alpha',
                                'izin' => 'cell-izin', 'cuti' => 'cell-cuti', 'sakit' => 'cell-sakit', default => 'cell-libur',
                            };
                            $hasLoc = is_array($det) && !empty($det['check_in_lat']) && !empty($det['check_in_lng']);
                        @endphp
                        <td class="text-center">
                            <span class="cell-status {{ $cellClass }}">{{ $st }}</span>
                            @if($hasLoc)
                                <br>
                                <a href="https://maps.google.com/?q={{ $det['check_in_lat'] }},{{ $det['check_in_lng'] }}"
                                   target="_blank" rel="noopener" title="Lokasi masuk"
                                   style="font-size:.6rem;color:#157347;text-decoration:none;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            @endif
                        </td>
                    @endforeach
                    <td class="text-center" style="font-weight:800;color:#15803d;">{{ $row['hadir'] }}</td>
                    <td class="text-center" style="font-weight:800;color:#d97706;">{{ $row['terlambat'] }}</td>
                    <td class="text-center" style="font-weight:800;color:#dc2626;">{{ $row['alpha'] }}</td>
                    <td class="text-center" style="font-weight:800;color:#7c3aed;">{{ $row['izin'] + $row['cuti'] + $row['sakit'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile card view --}}
    <div class="mc-list">
        @foreach($rekapMinggu as $i => $row)
        <div class="mc-item">
            <div class="mc-top">
                <div class="av-sm">{{ strtoupper(substr($row['user']->name, 0, 1)) }}</div>
                <div>
                    <div class="mc-name">{{ $row['user']->name }}</div>
                    <div class="mc-jabatan">{{ $row['user']->jabatan ?? '-' }}</div>
                </div>
            </div>
            <div class="mc-days">
                @foreach($hariKerja as $hk)
                    @php
                        $tgl = $hk->toDateString();
                        $det = $row['detail'][$tgl] ?? null;
                        $st  = is_array($det) ? ($det['status'] ?? '—') : ($det ?? '—');
                        $stL = strtolower($st);
                        $cellClass = match($stL) {
                            'hadir' => 'cell-hadir', 'terlambat' => 'cell-terlambat', 'alpha' => 'cell-alpha',
                            'izin' => 'cell-izin', 'cuti' => 'cell-cuti', 'sakit' => 'cell-sakit', default => 'cell-libur',
                        };
                    @endphp
                    <div class="mc-day {{ $cellClass }}">
                        <span class="mc-day-label">{{ $hk->isoFormat('dd') }} {{ $hk->format('d/m') }}</span>
                        <span class="mc-day-status">{{ $st }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mc-chips">
                <div class="mc-chip" style="background:#f0fdf4;color:#15803d;"><span class="mc-chip-label">Hadir</span><span class="mc-chip-val">{{ $row['hadir'] }}</span></div>
                <div class="mc-chip" style="background:#fff7e6;color:#d97706;"><span class="mc-chip-label">Telat</span><span class="mc-chip-val">{{ $row['terlambat'] }}</span></div>
                <div class="mc-chip" style="background:#fef2f2;color:#dc2626;"><span class="mc-chip-label">Alpha</span><span class="mc-chip-val">{{ $row['alpha'] }}</span></div>
                <div class="mc-chip" style="background:#f3effe;color:#7c3aed;"><span class="mc-chip-label">I/C/S</span><span class="mc-chip-val">{{ $row['izin'] + $row['cuti'] + $row['sakit'] }}</span></div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ══════════════════════════════ MODE BULANAN ══════════════════════════════ --}}
@else

<div class="filter-card">
    <form method="GET" action="{{ route('admin.laporan.absensi') }}">
        <input type="hidden" name="mode" value="bulanan">
        <div class="filter-grid">
            <div>
                <label class="form-label-mini">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label-mini">Bulan</label>
                <select name="bulan" class="form-select">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bi => $bn)
                        <option value="{{ $bi+1 }}" {{ $bulan==$bi+1?'selected':'' }}>{{ $bn }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ff-actions">
                <button type="submit" class="btn-filter flex-fill">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                <a href="{{ route('admin.laporan.absensi', ['mode'=>'bulanan']) }}" class="btn-reset-f">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
    <div class="stat-mini">
        <div class="sm-icon" style="background:#f0fdf4;color:#15803d"><i class="fas fa-user-check"></i></div>
        <div><div class="sm-val">{{ $ringkasanBulan['total_hadir'] }}</div><div class="sm-label">Total Hadir</div></div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#fff7e6;color:#d97706"><i class="fas fa-clock"></i></div>
        <div><div class="sm-val">{{ $ringkasanBulan['total_terlambat'] }}</div><div class="sm-label">Terlambat</div></div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#eff8fe;color:#0369a1"><i class="fas fa-chart-line"></i></div>
        <div><div class="sm-val">{{ number_format($ringkasanBulan['rata_kehadiran'], 1) }}%</div><div class="sm-label">Rata Kehadiran</div></div>
    </div>
</div>

<div class="tbl-card">
    <div class="tbl-head">
        <h6><i class="fas fa-calendar-alt" style="color:#157347"></i>
            {{ \Carbon\Carbon::create($tahun, $bulan, 1)->isoFormat('MMMM Y') }}
            <span style="font-size:.68rem;color:#9ca3af;font-weight:500;">({{ $totalHariKerja }} hari kerja)</span>
        </h6>
        <div class="tbl-head-actions">
            <a href="{{ route('admin.laporan.absensi.export', ['mode'=>'bulanan','tahun'=>$tahun,'bulan'=>$bulan]) }}" class="btn-unduh-excel">
                <i class="fas fa-file-excel"></i> Unduh Excel
            </a>
            <span class="badge-count">{{ $totalPegawai }} pegawai</span>
        </div>
    </div>

    @if($rekapBulanan->isEmpty())
    <div class="empty-box"><div class="em-icon">📅</div><p class="fw-bold mb-0" style="color:#374151;font-size:.85rem;">Belum ada data</p></div>
    @else
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th><th>Pegawai</th><th class="text-center">Hadir</th>
                    <th class="text-center">Telat</th><th class="text-center">Alpha</th>
                    <th class="text-center">Izin</th><th class="text-center">Cuti</th>
                    <th class="text-center">Sakit</th><th>% Kehadiran</th>
                    <th class="text-center">Riwayat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapBulanan->sortByDesc('hadir') as $i => $row)
                @php
                    $pct   = $row['persen_kehadiran'];
                    $color = $pct >= 90 ? '#15803d' : ($pct >= 75 ? '#d97706' : '#dc2626');
                @endphp
                <tr>
                    <td class="text-muted" style="font-size:.7rem;">{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="av-sm">{{ strtoupper(substr($row['user']->name, 0, 1)) }}</div>
                            <div>
                                <div style="font-size:.8rem;font-weight:700;color:#1a1a2e;">{{ $row['user']->name }}</div>
                                <div style="font-size:.65rem;color:#9ca3af;">{{ $row['user']->jabatan ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center" style="font-weight:800;color:#15803d;">{{ $row['hadir'] }}</td>
                    <td class="text-center" style="font-weight:800;color:#d97706;">{{ $row['terlambat'] }}</td>
                    <td class="text-center" style="font-weight:800;color:#dc2626;">{{ $row['alpha'] }}</td>
                    <td class="text-center" style="font-weight:800;color:#7c3aed;">{{ $row['izin'] }}</td>
                    <td class="text-center" style="font-weight:800;color:#0369a1;">{{ $row['cuti'] }}</td>
                    <td class="text-center" style="font-weight:800;color:#dc2626;">{{ $row['sakit'] }}</td>
                    <td style="min-width:90px;">
                        <div style="font-size:.78rem;font-weight:800;color:{{ $color }};">{{ $pct }}%</div>
                        <div class="pct-bar"><div class="pct-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div></div>
                    </td>
                    <td class="text-center">
                        @if(!empty($row['riwayat_lokasi']))
                            <span class="loc-link" style="cursor:default;" title="{{ count($row['riwayat_lokasi']) }} hari tercatat lokasinya. Lihat detail di Mode Harian.">
                                <i class="fas fa-map-marker-alt"></i> {{ count($row['riwayat_lokasi']) }} hari
                            </span>
                        @else
                            <span class="loc-empty">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile card view --}}
    <div class="mc-list">
        @foreach($rekapBulanan->sortByDesc('hadir') as $i => $row)
        @php
            $pct   = $row['persen_kehadiran'];
            $color = $pct >= 90 ? '#15803d' : ($pct >= 75 ? '#d97706' : '#dc2626');
        @endphp
        <div class="mc-item">
            <div class="mc-top">
                <div class="av-sm">{{ strtoupper(substr($row['user']->name, 0, 1)) }}</div>
                <div>
                    <div class="mc-name">{{ $row['user']->name }}</div>
                    <div class="mc-jabatan">{{ $row['user']->jabatan ?? '-' }}</div>
                </div>
            </div>
            <div class="mc-chips">
                <div class="mc-chip" style="background:#f0fdf4;color:#15803d;"><span class="mc-chip-label">Hadir</span><span class="mc-chip-val">{{ $row['hadir'] }}</span></div>
                <div class="mc-chip" style="background:#fff7e6;color:#d97706;"><span class="mc-chip-label">Telat</span><span class="mc-chip-val">{{ $row['terlambat'] }}</span></div>
                <div class="mc-chip" style="background:#fef2f2;color:#dc2626;"><span class="mc-chip-label">Alpha</span><span class="mc-chip-val">{{ $row['alpha'] }}</span></div>
                <div class="mc-chip" style="background:#f3effe;color:#7c3aed;"><span class="mc-chip-label">Izin</span><span class="mc-chip-val">{{ $row['izin'] }}</span></div>
                <div class="mc-chip" style="background:#eff8fe;color:#0369a1;"><span class="mc-chip-label">Cuti</span><span class="mc-chip-val">{{ $row['cuti'] }}</span></div>
                <div class="mc-chip" style="background:#fef2f2;color:#dc2626;"><span class="mc-chip-label">Sakit</span><span class="mc-chip-val">{{ $row['sakit'] }}</span></div>
            </div>
            <div class="mc-row" style="margin-top:.55rem;">
                <span class="mc-k">% Kehadiran</span>
                <span class="mc-v" style="color:{{ $color }};">{{ $pct }}%</span>
            </div>
            <div class="pct-bar"><div class="pct-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div></div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endif

@endsection
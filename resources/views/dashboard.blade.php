@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
/* ════════════════════════════════
   BASE — cegah overflow global
════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }
.db-wrap {
    max-width: 900px; margin: 0 auto;
    padding: 0 0 2rem;
    overflow-x: hidden;
    width: 100%;
}

/* ════════════════════════════════
   HERO
════════════════════════════════ */
.db-hero {
    background: linear-gradient(135deg, #0f5132, #157347 60%, #20c997);
    border-radius: 16px; padding: 1.25rem 1.5rem;
    color: #fff; margin-bottom: 1.25rem;
    position: relative; overflow: hidden;
}
.db-hero::before {
    content:''; position:absolute; top:-40px; right:-20px;
    width:150px; height:150px; border-radius:50%;
    background:rgba(255,255,255,.06); pointer-events:none;
}
.db-hero-top {
    display:flex; align-items:flex-start;
    justify-content:space-between; gap:.5rem; flex-wrap:wrap;
}
.db-hero h2 { font-size:1.15rem; font-weight:800; margin:0 0 .2rem; line-height:1.3; }
.db-hero p  { font-size:.78rem; color:rgba(255,255,255,.75); margin:0; }
.db-date {
    background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.22);
    border-radius:8px; padding:.28rem .7rem;
    font-size:.7rem; font-weight:600; white-space:nowrap;
    display:inline-flex; align-items:center; gap:.3rem; flex-shrink:0;
}

/* ════════════════════════════════
   STAT GRID  (2 kolom di HP, 4 di desktop)
════════════════════════════════ */
.db-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: .65rem;
    margin-bottom: 1.25rem;
    width: 100%;
}
@media(min-width:600px) { .db-stats { grid-template-columns: repeat(4, 1fr); } }

.db-stat {
    background: #fff; border-radius: 14px;
    padding: .9rem 1rem;
    border: 1px solid #f0f0f0;
    box-shadow: 0 1px 6px rgba(0,0,0,.04);
    display: flex; align-items: center; gap: .7rem;
    position: relative; overflow: hidden;
    transition: box-shadow .2s;
}
.db-stat:hover { box-shadow: 0 4px 14px rgba(0,0,0,.09); }
.db-stat .ds-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .95rem;
}
.db-stat .ds-val   { font-size: 1.5rem; font-weight: 800; color: #1a1a2e; line-height: 1; }
.db-stat .ds-lbl   { font-size: .65rem; color: #9ca3af; font-weight: 500; margin-top: .15rem; }
.db-stat .ds-bar   { position:absolute; bottom:0; left:0; right:0; height:3px; border-radius:0 0 14px 14px; }

/* ════════════════════════════════
   REKAP ROW (5 mini pills)
════════════════════════════════ */
.db-rekap {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .5rem;
    margin-bottom: 1.25rem;
    width: 100%;
}
@media(min-width: 480px) {
    .db-rekap { grid-template-columns: repeat(5, 1fr); }
}
.db-rekap-pill {
    background: #fff; border: 1px solid #f0f0f0;
    border-radius: 12px; padding: .55rem .4rem;
    text-align: center;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    min-width: 0; overflow: hidden;
}
.db-rekap-pill .rp-val { font-size: 1.1rem; font-weight: 800; line-height: 1; }
.db-rekap-pill .rp-lbl { font-size: .58rem; color: #9ca3af; margin-top: .15rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ════════════════════════════════
   CARD GENERIC
════════════════════════════════ */
.db-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #f0f0f0;
    box-shadow: 0 1px 8px rgba(0,0,0,.04);
    margin-bottom: 1.1rem; overflow: hidden;
}
.db-card-head {
    padding: .9rem 1.1rem;
    border-bottom: 1px solid #f4f4f5;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .4rem;
}
.db-card-head h6 {
    font-size: .875rem; font-weight: 800; color: #1a1a2e;
    margin: 0; display: flex; align-items: center; gap: .4rem;
}
.db-card-head h6 i { color: #157347; }
.db-card-body { padding: 1rem 1.1rem; }

/* ════════════════════════════════
   CHART
════════════════════════════════ */
.db-chart-wrap { position: relative; height: 220px; }
@media(min-width:600px) { .db-chart-wrap { height: 260px; } }

.db-legend {
    display: flex; flex-wrap: wrap; gap: .35rem .8rem;
    margin-bottom: .75rem;
}
.db-legend li {
    list-style: none; font-size: .7rem; font-weight: 600;
    color: #6b7280; display: flex; align-items: center; gap: .28rem;
}
.db-legend .dot { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; }

/* ════════════════════════════════
   EXPORT SECTION
════════════════════════════════ */
.db-export-row {
    display: flex; flex-wrap: wrap; gap: .65rem; align-items: flex-end;
}
.db-export-row .fe-group { display: flex; flex-direction: column; gap: .25rem; }
.db-export-row .fe-label {
    font-size: .65rem; font-weight: 700; color: #6b7280;
    text-transform: uppercase; letter-spacing: .4px;
}
.db-export-row select,
.db-export-row input[type="date"],
.db-export-row input[type="month"] {
    border: 1.5px solid #e4e7ec; border-radius: 9px;
    font-size: .8rem; padding: .45rem .8rem;
    background: #fafafa; color: #1a1a2e; outline: none;
    transition: border-color .18s;
}
.db-export-row select:focus,
.db-export-row input:focus {
    border-color: #157347;
    box-shadow: 0 0 0 3px rgba(21,115,71,.1);
    background: #fff;
}
.btn-exp-green {
    background: linear-gradient(135deg,#157347,#0f5132);
    color: #fff; border: none; border-radius: 9px;
    padding: .48rem 1rem; font-weight: 700; font-size: .8rem;
    display: inline-flex; align-items: center; gap: .35rem;
    cursor: pointer; text-decoration: none; transition: opacity .2s; white-space: nowrap;
}
.btn-exp-green:hover { opacity: .87; color: #fff; }
.btn-exp-blue {
    background: linear-gradient(135deg,#2563eb,#1d4ed8);
    color: #fff; border: none; border-radius: 9px;
    padding: .48rem 1rem; font-weight: 700; font-size: .8rem;
    display: inline-flex; align-items: center; gap: .35rem;
    cursor: pointer; text-decoration: none; transition: opacity .2s; white-space: nowrap;
}
.btn-exp-blue:hover { opacity: .87; color: #fff; }
.db-export-divider { width: 1px; height: 30px; background: #e4e7ec; align-self: center; flex-shrink: 0; }
@media(max-width:560px) { .db-export-divider { display: none; } }
.db-export-hint { font-size: .68rem; color: #9ca3af; margin-top: .5rem; }

/* ════════════════════════════════
   TABLE
════════════════════════════════ */
.db-table { width: 100%; border-collapse: collapse; }
.db-table thead th {
    background: #f8f9fb; font-size: .66rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px; color: #9ca3af;
    padding: .6rem .9rem; border-bottom: 1px solid #eaecf0; white-space: nowrap;
}
.db-table tbody td { padding: .65rem .9rem; border-bottom: 1px solid #f4f4f5; font-size: .82rem; }
.db-table tbody tr:last-child td { border-bottom: none; }
.db-table tbody tr:hover { background: #fafbfc; }
.db-table .col-no {
    width: 42px; text-align: center; color: #9ca3af; font-weight: 700;
}

/* ════════════════════════════════
   STATUS BADGE
════════════════════════════════ */
.sb {
    padding: .2rem .6rem; border-radius: 20px; font-size: .68rem;
    font-weight: 700; display: inline-flex; align-items: center; gap: .22rem; white-space: nowrap;
}
.sb-hadir     { background:#f0fdf4; color:#15803d; border:1px solid #86efac; }
.sb-terlambat { background:#fefce8; color:#a16207; border:1px solid #fde047; }
.sb-alpha     { background:#fff1f2; color:#be123c; border:1px solid #fca5a5; }
.sb-izin      { background:#eff6ff; color:#1d4ed8; border:1px solid #93c5fd; }
.sb-sakit     { background:#fdf4ff; color:#7e22ce; border:1px solid #d8b4fe; }

/* ════════════════════════════════
   TODAY BOX
════════════════════════════════ */
.today-box {
    display: grid; grid-template-columns: 1fr 1fr; gap: .6rem;
}
@media(min-width:480px) { .today-box { grid-template-columns: repeat(3, 1fr); } }
.today-item { text-align: center; }
.today-item .ti-lbl { font-size: .65rem; color: #9ca3af; font-weight: 700; text-transform: uppercase; margin-bottom: .2rem; }
.today-item .ti-val { font-size: 1.15rem; font-weight: 800; }

/* ════════════════════════════════
   PEMANTAUAN PEGAWAI (Manajer)
════════════════════════════════ */
.mon-live-dot {
    display:inline-block; width:7px; height:7px; border-radius:50%;
    background:#16a34a; margin-right:.35rem;
    animation: monPulse 1.6s ease-in-out infinite;
}
@keyframes monPulse {
    0%,100% { opacity:1; box-shadow:0 0 0 0 rgba(22,163,74,.4); }
    50%     { opacity:.6; box-shadow:0 0 0 4px rgba(22,163,74,0); }
}
.mon-avatar {
    width:26px; height:26px; border-radius:50%;
    background:#157347; color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:.68rem; font-weight:700; flex-shrink:0;
}
.mon-name-cell { display:flex; align-items:center; gap:.55rem; }
.mon-refresh-note {
    padding:.6rem 1.1rem; border-top:1px solid #f4f4f5;
    font-size:.68rem; color:#9ca3af;
    display:flex; align-items:center; gap:.3rem;
}

/* ════════════════════════════════
   LOKASI CHECK-IN / CHECK-OUT
════════════════════════════════ */
.loc-link {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.72rem; font-weight:700; color:#157347;
    text-decoration:none; white-space:nowrap;
}
.loc-link:hover { text-decoration:underline; }
.loc-alamat {
    display:block; font-size:.65rem; color:#9ca3af;
    max-width:160px; white-space:normal; margin-top:.1rem;
    overflow:hidden; text-overflow:ellipsis;
}
.loc-none { font-size:.75rem; color:#d1d5db; }

/* ════════════════════════════════
   TIPE ABSENSI (WFO / WFH / Dinas Luar)
════════════════════════════════ */
.tp {
    padding:.2rem .55rem; border-radius:8px; font-size:.66rem;
    font-weight:700; display:inline-flex; align-items:center; gap:.25rem; white-space:nowrap;
}
.tp-wfo        { background:#eff6ff; color:#1d4ed8; border:1px solid #93c5fd; }
.tp-wfh        { background:#f0fdf4; color:#15803d; border:1px solid #86efac; }
.tp-dinasluar  { background:#fff7ed; color:#c2410c; border:1px solid #fdba74; }
.tp-none       { font-size:.75rem; color:#d1d5db; }
</style>
@endpush

@section('content')
<div class="db-wrap">

{{-- ══ HERO ══════════════════════════════════════════════ --}}
<div class="db-hero">
    <div class="db-hero-top">
        <div>
            <h2>
                @if($user->isAdmin()) 👋 Halo, Admin {{ explode(' ', $user->name)[0] }}!
                @elseif($user->isManajer()) 👋 Halo, Manajer {{ explode(' ', $user->name)[0] }}!
                @else 👋 Halo, {{ explode(' ', $user->name)[0] }}!
                @endif
            </h2>
            <p>
                @if($user->isAdmin()) Pantau & kelola absensi pegawai PTPN4.
                @elseif($user->isManajer()) Pantau kehadiran pegawai secara real-time.
                @else Selamat datang di sistem absensi PTPN4.
                @endif
            </p>
        </div>
        <span class="db-date">
            <i class="fas fa-calendar-day"></i>
            {{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}
        </span>
    </div>
</div>

@if($user->isAdmin())
{{-- ══════════════════════════════════════════════════════
     ADMIN VIEW
══════════════════════════════════════════════════════ --}}

{{-- Stat Cards --}}
<div class="db-stats">
    <div class="db-stat">
        <div class="ds-icon" style="background:#f0fdf4;color:#157347;"><i class="fas fa-users"></i></div>
        <div><div class="ds-val">{{ $totalPegawai }}</div><div class="ds-lbl">Total Pegawai</div></div>
        <div class="ds-bar" style="background:#157347;"></div>
    </div>
    <div class="db-stat">
        <div class="ds-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-user-check"></i></div>
        <div><div class="ds-val">{{ $hadirHariIni }}</div><div class="ds-lbl">Hadir Hari Ini</div></div>
        <div class="ds-bar" style="background:#16a34a;"></div>
    </div>
    <div class="db-stat">
        <div class="ds-icon" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-user-times"></i></div>
        <div><div class="ds-val">{{ $belumAbsen }}</div><div class="ds-lbl">Belum Absen</div></div>
        <div class="ds-bar" style="background:#ea580c;"></div>
    </div>
    <div class="db-stat">
        <div class="ds-icon" style="background:#fff1f2;color:#e11d48;"><i class="fas fa-times-circle"></i></div>
        <div><div class="ds-val">{{ $rekap['alpha'] ?? 0 }}</div><div class="ds-lbl">Alpha Bulan Ini</div></div>
        <div class="ds-bar" style="background:#e11d48;"></div>
    </div>
</div>

{{-- Rekap Pills --}}
<div class="db-rekap">
    <div class="db-rekap-pill">
        <div class="rp-val" style="color:#7e22ce;">{{ $rekap['sakit'] ?? 0 }}</div>
        <div class="rp-lbl">🏥 Sakit</div>
    </div>
    <div class="db-rekap-pill">
        <div class="rp-val" style="color:#a16207;">{{ $rekap['terlambat'] ?? 0 }}</div>
        <div class="rp-lbl">⏰ Terlambat</div>
    </div>
    <div class="db-rekap-pill">
        <div class="rp-val" style="color:#be123c;">{{ $rekap['alpha'] ?? 0 }}</div>
        <div class="rp-lbl">❌ Alpha</div>
    </div>
    <div class="db-rekap-pill">
        <div class="rp-val" style="color:#1d4ed8;">{{ $rekap['izin'] ?? 0 }}</div>
        <div class="rp-lbl">📋 Izin</div>
    </div>
    <div class="db-rekap-pill">
        <div class="rp-val" style="color:#15803d;">{{ $rekap['hadir'] ?? 0 }}</div>
        <div class="rp-lbl">✅ Hadir</div>
    </div>
</div>

{{-- Grafik --}}
<div class="db-card">
    <div class="db-card-head">
        <h6><i class="fas fa-chart-bar"></i> Grafik Absensi Bulan Ini</h6>
        <span style="font-size:.72rem;color:#9ca3af;">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}</span>
    </div>
    <div class="db-card-body" style="padding-bottom:.75rem;">
        <ul class="db-legend">
            <li><span class="dot" style="background:#16a34a;"></span>Hadir</li>
            <li><span class="dot" style="background:#f59e0b;"></span>Terlambat</li>
            <li><span class="dot" style="background:#ef4444;"></span>Alpha</li>
            <li><span class="dot" style="background:#3b82f6;"></span>Izin</li>
            <li><span class="dot" style="background:#a855f7;"></span>Sakit</li>
        </ul>
        <div class="db-chart-wrap">
            <canvas id="absensiChart"></canvas>
        </div>
    </div>
</div>

{{-- Ekspor Data --}}
<div class="db-card">
    <div class="db-card-head">
        <h6><i class="fas fa-file-excel"></i> Ekspor Data</h6>
    </div>
    <div class="db-card-body">
        <div class="db-export-row">

            {{-- Pegawai --}}
            <div class="fe-group">
                <span class="fe-label">Data Pegawai</span>
                <a href="{{ route('admin.pegawai.export') }}" class="btn-exp-green">
                    <i class="fas fa-users"></i> Ekspor Pegawai
                </a>
            </div>

            <div class="db-export-divider"></div>

            {{-- Absensi --}}
            <form action="{{ route('admin.export.absensi') }}" method="GET"
                  style="display:contents;">
                <div class="fe-group">
                    <span class="fe-label">Periode</span>
                    <select name="periode" id="periodeSelect" onchange="toggleTanggal()" style="min-width:130px;">
                        <option value="harian">Harian</option>
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan" selected>Bulanan</option>
                    </select>
                </div>
                <div class="fe-group">
                    <span class="fe-label">Tanggal Referensi</span>
                    <input type="month" name="tanggal" id="tanggalInput"
                           value="{{ now()->format('Y-m') }}" style="min-width:140px;">
                </div>
                <button type="submit" class="btn-exp-blue">
                    <i class="fas fa-file-excel"></i> Ekspor Absensi
                </button>
            </form>

        </div>
        <p class="db-export-hint">
            <i class="fas fa-info-circle"></i>
            Harian = 1 hari · Mingguan = 7 hari · Bulanan = 1 bulan penuh
        </p>
    </div>
</div>

@elseif($user->isManajer())
{{-- ══════════════════════════════════════════════════════
     MANAJER VIEW — Pemantauan Real-Time Absensi Pegawai
══════════════════════════════════════════════════════ --}}

{{-- Stat Cards ringkas pegawai --}}
<div class="db-stats">
    <div class="db-stat">
        <div class="ds-icon" style="background:#f0fdf4;color:#157347;"><i class="fas fa-users"></i></div>
        <div><div class="ds-val">{{ $totalPegawai }}</div><div class="ds-lbl">Total Pegawai</div></div>
        <div class="ds-bar" style="background:#157347;"></div>
    </div>
    <div class="db-stat">
        <div class="ds-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-user-check"></i></div>
        <div><div class="ds-val">{{ $hadirHariIni }}</div><div class="ds-lbl">Hadir Hari Ini</div></div>
        <div class="ds-bar" style="background:#16a34a;"></div>
    </div>
    <div class="db-stat">
        <div class="ds-icon" style="background:#fefce8;color:#a16207;"><i class="fas fa-user-clock"></i></div>
        <div><div class="ds-val">{{ $terlambatHariIni ?? 0 }}</div><div class="ds-lbl">Terlambat</div></div>
        <div class="ds-bar" style="background:#f59e0b;"></div>
    </div>
    <div class="db-stat">
        <div class="ds-icon" style="background:#fff1f2;color:#e11d48;"><i class="fas fa-user-times"></i></div>
        <div><div class="ds-val">{{ $belumAbsen }}</div><div class="ds-lbl">Belum Absen</div></div>
        <div class="ds-bar" style="background:#e11d48;"></div>
    </div>
</div>

{{-- Tabel Pemantauan Real-Time --}}
<div class="db-card">
    <div class="db-card-head">
        <h6><span class="mon-live-dot"></span>Pemantauan Absensi Pegawai — Hari Ini</h6>
        <span style="font-size:.72rem;color:#9ca3af;">{{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="db-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Pegawai</th>
                    <th>NIK SAP</th>
                    <th>Jabatan</th>
                    <th>Tipe</th>
                    <th>Masuk</th>
                    <th>Lokasi Masuk</th>
                    <th>Keluar</th>
                    <th>Lokasi Keluar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemantauanPegawai as $p)
                <tr>
                    <td class="col-no">{{ $loop->iteration }}</td>
                    <td style="white-space:nowrap;">
                        <div class="mon-name-cell">
                            <div class="mon-avatar">{{ strtoupper(substr($p->user->name, 0, 1)) }}</div>
                            <span style="font-weight:600;">{{ $p->user->name }}</span>
                        </div>
                    </td>
                    <td style="color:#6b7280;white-space:nowrap;">{{ $p->user->nip ?? '-' }}</td>
                    <td style="color:#6b7280;white-space:nowrap;">{{ $p->user->jabatan ?? '-' }}</td>
                    <td>
                        @if($p->tipe_absensi === 'WFO')
                            <span class="tp tp-wfo"><i class="fas fa-building"></i> WFO</span>
                        @elseif($p->tipe_absensi === 'WFH')
                            <span class="tp tp-wfh"><i class="fas fa-house-laptop"></i> WFH</span>
                        @elseif($p->tipe_absensi === 'Dinas Luar')
                            <span class="tp tp-dinasluar"><i class="fas fa-route"></i> Dinas Luar</span>
                        @else
                            <span class="tp-none">-</span>
                        @endif
                    </td>
                    <td style="color:#157347;font-weight:600;white-space:nowrap;">
                        {{ $p->check_in ? \Carbon\Carbon::parse($p->check_in)->format('H:i') : '-' }}
                    </td>
                    <td>
                        @if($p->check_in_lat && $p->check_in_lng)
                            <a href="https://www.google.com/maps?q={{ $p->check_in_lat }},{{ $p->check_in_lng }}"
                               target="_blank" rel="noopener" class="loc-link">
                                <i class="fas fa-map-marker-alt"></i> Lihat Peta
                            </a>
                            @if($p->check_in_alamat)
                                <span class="loc-alamat">{{ $p->check_in_alamat }}</span>
                            @endif
                        @else
                            <span class="loc-none">-</span>
                        @endif
                    </td>
                    <td style="color:#2563eb;font-weight:600;white-space:nowrap;">
                        {{ $p->check_out ? \Carbon\Carbon::parse($p->check_out)->format('H:i') : '-' }}
                    </td>
                    <td>
                        @if($p->check_out_lat && $p->check_out_lng)
                            <a href="https://www.google.com/maps?q={{ $p->check_out_lat }},{{ $p->check_out_lng }}"
                               target="_blank" rel="noopener" class="loc-link">
                                <i class="fas fa-map-marker-alt"></i> Lihat Peta
                            </a>
                            @if($p->check_out_alamat)
                                <span class="loc-alamat">{{ $p->check_out_alamat }}</span>
                            @endif
                        @else
                            <span class="loc-none">-</span>
                        @endif
                    </td>
                    <td><span class="sb sb-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:1.5rem;color:#9ca3af;font-size:.8rem;">
                        Belum ada data pegawai aktif.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mon-refresh-note">
        <i class="fas fa-sync-alt"></i> Data diperbarui otomatis setiap 60 detik.
    </div>
</div>

{{-- Pengajuan Izin Menunggu --}}
<div class="db-card">
    <div class="db-card-head">
        <h6><i class="fas fa-inbox"></i> Ringkasan Pengajuan Izin</h6>
        @if(Route::has('manajer.izin.index'))
        <a href="{{ route('manajer.izin.index') }}" style="font-size:.75rem;color:#157347;font-weight:700;text-decoration:none;">
            Lihat Semua <i class="fas fa-chevron-right" style="font-size:.65rem;"></i>
        </a>
        @endif
    </div>
    <div class="db-card-body">
        <div class="today-box">
            <div class="today-item">
                <div class="ti-lbl">Menunggu</div>
                <div class="ti-val" style="color:#a16207;">{{ $izinMenunggu }}</div>
            </div>
            <div class="today-item">
                <div class="ti-lbl">Disetujui Bulan Ini</div>
                <div class="ti-val" style="color:#15803d;">{{ $izinDisetujuiBulanIni }}</div>
            </div>
        </div>
    </div>
</div>

@else
{{-- ══════════════════════════════════════════════════════
     USER VIEW
══════════════════════════════════════════════════════ --}}

{{-- Stat Cards --}}
<div class="db-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="db-stat" style="flex-direction:column;align-items:flex-start;gap:.3rem;">
        <div class="ds-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-check-circle"></i></div>
        <div><div class="ds-val">{{ $totalHadir }}</div><div class="ds-lbl">Hadir</div></div>
        <div class="ds-bar" style="background:#16a34a;"></div>
    </div>
    <div class="db-stat" style="flex-direction:column;align-items:flex-start;gap:.3rem;">
        <div class="ds-icon" style="background:#fefce8;color:#a16207;"><i class="fas fa-clock"></i></div>
        <div><div class="ds-val">{{ $totalTerlambat }}</div><div class="ds-lbl">Terlambat</div></div>
        <div class="ds-bar" style="background:#f59e0b;"></div>
    </div>
    <div class="db-stat" style="flex-direction:column;align-items:flex-start;gap:.3rem;">
        <div class="ds-icon" style="background:#fff1f2;color:#e11d48;"><i class="fas fa-times-circle"></i></div>
        <div><div class="ds-val">{{ $totalAlpha }}</div><div class="ds-lbl">Alpha</div></div>
        <div class="ds-bar" style="background:#e11d48;"></div>
    </div>
</div>

{{-- Absensi Hari Ini --}}
<div class="db-card">
    <div class="db-card-head">
        <h6><i class="fas fa-calendar-check"></i> Absensi Hari Ini</h6>
        <span style="font-size:.72rem;color:#9ca3af;">{{ now()->locale('id')->isoFormat('dddd, D MMM') }}</span>
    </div>
    <div class="db-card-body">
        @if($absensiHariIni)
        <div class="today-box">
            <div class="today-item">
                <div class="ti-lbl">Jam Masuk</div>
                <div class="ti-val" style="color:#157347;">{{ $absensiHariIni->check_in ?? '-' }}</div>
            </div>
            <div class="today-item">
                <div class="ti-lbl">Jam Keluar</div>
                <div class="ti-val" style="color:#2563eb;">{{ $absensiHariIni->check_out ?? '-' }}</div>
            </div>
            <div class="today-item">
                <div class="ti-lbl">Status</div>
                <div style="margin-top:.2rem;">
                    <span class="sb sb-{{ $absensiHariIni->status }}">
                        {{ ucfirst($absensiHariIni->status) }}
                    </span>
                </div>
            </div>
        </div>
        @else
        <div style="text-align:center;padding:1rem 0;color:#9ca3af;">
            <i class="fas fa-calendar-times" style="font-size:1.5rem;display:block;margin-bottom:.4rem;"></i>
            <span style="font-size:.82rem;">Belum ada absensi hari ini.</span>
        </div>
        @endif
    </div>
</div>

{{-- Riwayat --}}
<div class="db-card">
    <div class="db-card-head">
        <h6><i class="fas fa-history"></i> Riwayat Absensi</h6>
        <a href="{{ route('absensi.riwayat') }}"
           style="font-size:.75rem;color:#157347;font-weight:700;text-decoration:none;">
            Lihat Semua <i class="fas fa-chevron-right" style="font-size:.65rem;"></i>
        </a>
    </div>
    <div style="overflow-x:auto;">
        <table class="db-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatAbsensi as $item)
                <tr>
                    <td class="col-no">{{ $loop->iteration }}</td>
                    <td style="font-weight:600;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('D MMM YY') }}
                    </td>
                    <td style="color:#157347;font-weight:600;">{{ $item->check_in ?? '-' }}</td>
                    <td style="color:#2563eb;font-weight:600;">{{ $item->check_out ?? '-' }}</td>
                    <td><span class="sb sb-{{ $item->status }}">{{ ucfirst($item->status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:1.5rem;color:#9ca3af;font-size:.8rem;">
                        Belum ada data absensi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endif {{-- isAdmin / isManajer / user --}}

</div>{{-- db-wrap --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if($user->isAdmin())
// ── Chart ─────────────────────────────────────────────
const ctx = document.getElementById('absensiChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! $chartLabels !!},
            datasets: [
                { label:'Hadir',     data:{!! $chartHadir !!},     backgroundColor:'rgba(22,163,74,.7)',  borderColor:'#16a34a', borderWidth:1.5, borderRadius:4 },
                { label:'Terlambat', data:{!! $chartTerlambat !!},  backgroundColor:'rgba(245,158,11,.7)', borderColor:'#f59e0b', borderWidth:1.5, borderRadius:4 },
                { label:'Alpha',     data:{!! $chartAlpha !!},      backgroundColor:'rgba(239,68,68,.7)',  borderColor:'#ef4444', borderWidth:1.5, borderRadius:4 },
                { label:'Izin',      data:{!! $chartIzin !!},       backgroundColor:'rgba(59,130,246,.7)', borderColor:'#3b82f6', borderWidth:1.5, borderRadius:4 },
                { label:'Sakit',     data:{!! $chartSakit !!},      backgroundColor:'rgba(168,85,247,.7)', borderColor:'#a855f7', borderWidth:1.5, borderRadius:4 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode:'index', intersect:false },
            plugins: {
                legend: { display:false },
                tooltip: {
                    backgroundColor:'#1a1a2e', padding:10, cornerRadius:10,
                    titleFont:{ size:11, weight:'bold' }, bodyFont:{ size:11 }
                }
            },
            scales: {
                x: { grid:{ display:false }, ticks:{ font:{ size:10 }, color:'#9ca3af' } },
                y: {
                    beginAtZero:true, grid:{ color:'#f4f4f5' },
                    ticks:{
                        font:{ size:10 }, color:'#9ca3af', stepSize:1,
                        callback: v => Number.isInteger(v) ? v : null
                    }
                }
            }
        }
    });
}

// ── Toggle input periode ───────────────────────────────
function toggleTanggal() {
    const p = document.getElementById('periodeSelect').value;
    const el = document.getElementById('tanggalInput');
    if (p === 'bulanan') {
        el.type  = 'month';
        el.value = new Date().toISOString().slice(0,7);
    } else {
        el.type  = 'date';
        el.value = new Date().toISOString().slice(0,10);
    }
}
@endif

@if($user->isManajer())
// ── Auto-refresh pemantauan pegawai setiap 60 detik ────
setTimeout(function () {
    window.location.reload();
}, 60000);
@endif
</script>
@endpush
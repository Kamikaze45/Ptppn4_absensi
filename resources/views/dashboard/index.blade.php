@extends('layouts.app')
@section('page-title', 'Dashboard')
@section('title', 'Dashboard')

@push('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, var(--green-deep) 0%, #0d7a39 50%, var(--green-main) 100%);
        border-radius: var(--radius);
        padding: 28px 32px;
        color: white;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        right: -40px; top: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.07);
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        right: 80px; bottom: -60px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(201,162,39,0.12);
    }
    .welcome-content { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; }
    .welcome-text h2 { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700; margin-bottom: 6px; }
    .welcome-text p { font-size: 14px; opacity: 0.8; }
    .welcome-date { text-align: right; }
    .welcome-date .date-big { font-family: 'Sora', sans-serif; font-size: 28px; font-weight: 800; line-height: 1; }
    .welcome-date .date-sub { font-size: 13px; opacity: 0.7; margin-top: 4px; }

    .absen-status-card {
        background: white;
        border-radius: var(--radius);
        padding: 22px 24px;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
        border-left: 4px solid var(--green-main);
    }
    .absen-status-card.checked-out { border-left-color: #3b82f6; }
    .absen-status-card.not-checked { border-left-color: #e5e7eb; }

    .status-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .status-times { display: flex; gap: 24px; flex-wrap: wrap; }
    .time-block label { font-size: 11px; color: var(--gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
    .time-block .time-val { font-family: 'Sora', sans-serif; font-size: 20px; font-weight: 700; color: var(--dark); }
    .time-block .time-val.green { color: var(--green-main); }
    .time-block .time-val.blue { color: #3b82f6; }
    .time-block .time-val.gray { color: #9ca3af; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media(max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }

    .activity-item {
        display: flex; align-items: center; gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-dot {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .activity-dot.green { background: #dcfce7; color: #16a34a; }
    .activity-dot.gold { background: #fef9c3; color: #ca8a04; }
    .activity-dot.red { background: #fee2e2; color: #ef4444; }
    .activity-dot.blue { background: #dbeafe; color: #3b82f6; }
    .activity-info { flex: 1; min-width: 0; }
    .activity-name { font-size: 14px; font-weight: 600; color: var(--dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .activity-sub { font-size: 12px; color: var(--gray); margin-top: 1px; }
    .activity-time { font-size: 12px; color: var(--gray); text-align: right; flex-shrink: 0; }
</style>
@endpush

@section('content')

<!-- Welcome Banner -->
<div class="welcome-banner animate-in">
    <div class="welcome-content">
        <div class="welcome-text">
            <h2>Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
            <p>{{ Auth::user()->jabatan }} — {{ Auth::user()->departemen }} | NIP: {{ Auth::user()->nip }}</p>
        </div>
        <div class="welcome-date">
            <div class="date-big" id="dashDate"></div>
            <div class="date-sub" id="dashMonth"></div>
        </div>
    </div>
</div>

<!-- Today Attendance Status -->
<div class="absen-status-card animate-in {{ $todayAttendance ? ($todayAttendance->check_out ? 'checked-out' : '') : 'not-checked' }}">
    <div class="status-row">
        <div>
            <div style="font-size:12px;color:var(--gray);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Status Kehadiran Hari Ini</div>
            @if($todayAttendance)
                @if($todayAttendance->status === 'terlambat')
                    <span class="badge badge-terlambat"><i class="fas fa-clock"></i> Terlambat</span>
                @else
                    <span class="badge badge-hadir"><i class="fas fa-check-circle"></i> Hadir</span>
                @endif
            @else
                <span class="badge" style="background:#f3f4f6;color:#6b7280;"><i class="fas fa-minus-circle"></i> Belum Absen</span>
            @endif
        </div>
        <div class="status-times">
            <div class="time-block">
                <label>Masuk</label>
                <div class="time-val green">{{ $todayAttendance?->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '--:--' }}</div>
            </div>
            <div class="time-block">
                <label>Keluar</label>
                <div class="time-val blue">{{ $todayAttendance?->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') : '--:--' }}</div>
            </div>
            @if($todayAttendance && $todayAttendance->check_out)
            <div class="time-block">
                <label>Durasi</label>
                <div class="time-val" style="color:var(--dark);">{{ $todayAttendance->work_hours }}</div>
            </div>
            @endif
        </div>
        @if(!$todayAttendance)
        <a href="{{ route('attendance.index') }}" class="btn btn-primary">
            <i class="fas fa-fingerprint"></i> Absen Sekarang
        </a>
        @elseif(!$todayAttendance->check_out)
        <a href="{{ route('attendance.index') }}" class="btn btn-outline">
            <i class="fas fa-sign-out-alt"></i> Absen Keluar
        </a>
        @endif
    </div>
</div>

<!-- Stats -->
@if(Auth::user()->isAdmin())
<div class="stats-grid animate-in">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $adminData['total_pegawai'] }}</div>
            <div class="stat-label">Total Pegawai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-user-check"></i></div>
        <div>
            <div class="stat-value">{{ $adminData['hadir_hari_ini'] }}</div>
            <div class="stat-label">Hadir Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-user-clock"></i></div>
        <div>
            <div class="stat-value">{{ $adminData['terlambat_hari_ini'] }}</div>
            <div class="stat-label">Terlambat</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-user-times"></i></div>
        <div>
            <div class="stat-value">{{ $adminData['belum_absen'] }}</div>
            <div class="stat-label">Belum Absen</div>
        </div>
    </div>
</div>
@else
<div class="stats-grid animate-in">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $monthlyStats['hadir'] }}</div>
            <div class="stat-label">Hadir Bulan Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-clock"></i></div>
        <div>
            <div class="stat-value">{{ $monthlyStats['terlambat'] }}</div>
            <div class="stat-label">Terlambat</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="stat-value">{{ $monthlyStats['izin'] }}</div>
            <div class="stat-label">Izin / Sakit</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-star"></i></div>
        <div>
            <div class="stat-value">{{ $monthlyStats['hadir'] > 0 ? round(($monthlyStats['hadir'] / max(date('j'), 1)) * 100) : 0 }}%</div>
            <div class="stat-label">Tingkat Kehadiran</div>
        </div>
    </div>
</div>
@endif

<!-- Grid Content -->
<div class="grid-2">
    <!-- Recent Attendance -->
    <div class="card animate-in">
        <div class="card-header">
            <div class="card-title">Riwayat Absensi Terbaru</div>
            <a href="{{ route('attendance.history') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding-top:16px;">
            @forelse($recentAttendance as $att)
            <div class="activity-item">
                <div class="activity-dot {{ $att->status === 'hadir' ? 'green' : ($att->status === 'terlambat' ? 'gold' : 'red') }}">
                    <i class="fas {{ $att->status === 'hadir' ? 'fa-check' : ($att->status === 'terlambat' ? 'fa-clock' : 'fa-times') }}"></i>
                </div>
                <div class="activity-info">
                    <div class="activity-name">{{ $att->date->translatedFormat('l, d M Y') }}</div>
                    <div class="activity-sub">
                        Masuk: {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-' }}
                        @if($att->check_out) — Keluar: {{ \Carbon\Carbon::parse($att->check_out)->format('H:i') }} @endif
                    </div>
                </div>
                <div class="activity-time">
                    @if($att->status === 'hadir') <span class="badge badge-hadir">Hadir</span>
                    @elseif($att->status === 'terlambat') <span class="badge badge-terlambat">Terlambat</span>
                    @else <span class="badge badge-absent">{{ ucfirst($att->status) }}</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:32px 0;">
                <i class="fas fa-calendar-times"></i>
                <p>Belum ada riwayat absensi</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Admin: Today's Attendance / User: Quick Actions -->
    @if(Auth::user()->isAdmin())
    <div class="card animate-in">
        <div class="card-header">
            <div class="card-title">Kehadiran Hari Ini</div>
            <a href="{{ route('attendance.recap') }}" class="btn btn-secondary btn-sm">Rekap Lengkap</a>
        </div>
        <div class="card-body" style="padding-top:16px;">
            @forelse($adminData['recent_all'] as $att)
            <div class="activity-item">
                <div class="activity-dot {{ $att->status === 'hadir' ? 'green' : ($att->status === 'terlambat' ? 'gold' : 'blue') }}">
                    {{ strtoupper(substr($att->user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="activity-info">
                    <div class="activity-name">{{ $att->user->name ?? 'Unknown' }}</div>
                    <div class="activity-sub">{{ $att->user->jabatan ?? '-' }}</div>
                </div>
                <div class="activity-time">
                    <div style="font-size:13px;font-weight:600;color:var(--green-main);">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-' }}</div>
                    @if($att->status === 'terlambat') <span class="badge badge-terlambat" style="margin-top:2px;">Terlambat</span> @endif
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:32px 0;">
                <i class="fas fa-users"></i>
                <p>Belum ada absensi hari ini</p>
            </div>
            @endforelse
        </div>
    </div>
    @else
    <div class="card animate-in">
        <div class="card-header">
            <div class="card-title">Aksi Cepat</div>
        </div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px;margin-top:4px;">
                <a href="{{ route('attendance.index') }}" style="display:flex;align-items:center;gap:16px;padding:16px;background:var(--green-pale);border-radius:12px;text-decoration:none;transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <div style="width:44px;height:44px;background:var(--green-main);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;color:var(--green-deep);font-size:14px;">Absensi</div>
                        <div style="font-size:12px;color:var(--gray);">Check in & check out kehadiran</div>
                    </div>
                    <i class="fas fa-chevron-right" style="margin-left:auto;color:var(--green-main);"></i>
                </a>
                <a href="{{ route('attendance.history') }}" style="display:flex;align-items:center;gap:16px;padding:16px;background:#f0f9ff;border-radius:12px;text-decoration:none;transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <div style="width:44px;height:44px;background:#3b82f6;border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#1e3a5f;font-size:14px;">Riwayat Absensi</div>
                        <div style="font-size:12px;color:var(--gray);">Lihat histori kehadiran bulanan</div>
                    </div>
                    <i class="fas fa-chevron-right" style="margin-left:auto;color:#3b82f6;"></i>
                </a>
                <a href="{{ route('profile.index') }}" style="display:flex;align-items:center;gap:16px;padding:16px;background:#fefce8;border-radius:12px;text-decoration:none;transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                    <div style="width:44px;height:44px;background:var(--gold);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#713f12;font-size:14px;">Profil Saya</div>
                        <div style="font-size:12px;color:var(--gray);">Edit informasi & ubah password</div>
                    </div>
                    <i class="fas fa-chevron-right" style="margin-left:auto;color:var(--gold);"></i>
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const now = new Date();
    document.getElementById('dashDate').textContent = now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    document.getElementById('dashMonth').textContent = days[now.getDay()];
</script>
@endpush

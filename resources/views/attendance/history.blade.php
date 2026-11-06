@extends('layouts.app')
@section('page-title', 'Riwayat Absensi')
@section('title', 'Riwayat Absensi')

@section('content')
<div class="page-header">
    <div>
        <h1>Riwayat Absensi</h1>
        <p>Rekap kehadiran Anda per bulan</p>
    </div>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 24px;">
        <form method="GET" action="{{ route('attendance.history') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;">
                <label style="font-size:13px;font-weight:600;color:var(--gray);">Bulan:</label>
                <select name="month" class="form-control" style="width:auto;">
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <label style="font-size:13px;font-weight:600;color:var(--gray);">Tahun:</label>
                <select name="year" class="form-control" style="width:auto;">
                    @foreach(range(date('Y')-1, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div><div class="stat-value">{{ $stats['hadir'] }}</div><div class="stat-label">Hadir</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-clock"></i></div>
        <div><div class="stat-value">{{ $stats['terlambat'] }}</div><div class="stat-label">Terlambat</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-file-medical"></i></div>
        <div><div class="stat-value">{{ $stats['izin'] }}</div><div class="stat-label">Izin/Sakit</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-calendar-alt"></i></div>
        <div><div class="stat-value">{{ $stats['hadir'] + $stats['terlambat'] + $stats['izin'] }}</div><div class="stat-label">Total Tercatat</div></div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Detail Absensi — {{ \Carbon\Carbon::create(null, $month)->translatedFormat('F') }} {{ $year }}</div>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Durasi</th>
                        <th>Lokasi Masuk</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $att->date->translatedFormat('d M Y') }}</div>
                            <div style="font-size:12px;color:var(--gray);">{{ $att->date->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            <span style="font-family:'Sora',sans-serif;font-weight:600;color:var(--green-main);">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '—' }}
                            </span>
                        </td>
                        <td>
                            <span style="font-family:'Sora',sans-serif;font-weight:600;color:#3b82f6;">
                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '—' }}
                            </span>
                        </td>
                        <td style="font-size:13px;">{{ $att->work_hours ?? '—' }}</td>
                        <td style="max-width:180px;">
                            @if($att->check_in_address)
                            <div style="font-size:12px;color:var(--gray);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;" title="{{ $att->check_in_address }}">
                                <i class="fas fa-map-pin" style="color:#ef4444;"></i> {{ Str::limit($att->check_in_address, 40) }}
                            </div>
                            @else — @endif
                        </td>
                        <td>
                            @if($att->status === 'hadir') <span class="badge badge-hadir"><i class="fas fa-check"></i> Hadir</span>
                            @elseif($att->status === 'terlambat') <span class="badge badge-terlambat"><i class="fas fa-clock"></i> Terlambat</span>
                            @elseif($att->status === 'izin') <span class="badge badge-izin">Izin</span>
                            @elseif($att->status === 'sakit') <span class="badge badge-sakit">Sakit</span>
                            @else <span class="badge badge-absent">Alpha</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>Tidak ada data absensi untuk periode ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $attendances->withQueryString()->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection

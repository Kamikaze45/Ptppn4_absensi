@extends('layouts.app')
@section('page-title', 'Rekap Absensi')
@section('title', 'Rekap Absensi')

@section('content')
<div class="page-header">
    <div>
        <h1>Rekap Absensi Pegawai</h1>
        <p>Rekap kehadiran seluruh pegawai per bulan</p>
    </div>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 24px;">
        <form method="GET" action="{{ route('attendance.recap') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <select name="month" class="form-control" style="width:auto;">
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                </option>
                @endforeach
            </select>
            <select name="year" class="form-control" style="width:auto;">
                @foreach(range(date('Y')-1, date('Y')+1) as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            Rekap {{ \Carbon\Carbon::create(null, $month)->translatedFormat('F') }} {{ $year }}
        </div>
        <div style="font-size:13px;color:var(--gray);">Total {{ $employees->count() }} pegawai</div>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pegawai</th>
                        <th>Jabatan</th>
                        <th style="text-align:center;">Hadir</th>
                        <th style="text-align:center;">Terlambat</th>
                        <th style="text-align:center;">Izin/Sakit</th>
                        <th style="text-align:center;">Alpha</th>
                        <th style="text-align:center;">Total</th>
                        <th style="text-align:center;">Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                    @php
                        $hadir = $emp->attendances->where('status', 'hadir')->count();
                        $terlambat = $emp->attendances->where('status', 'terlambat')->count();
                        $izin = $emp->attendances->whereIn('status', ['izin', 'sakit'])->count();
                        $alpha = $emp->attendances->where('status', 'alpha')->count();
                        $total = $emp->attendances->count();
                        $workDays = \Carbon\Carbon::create($year, $month)->daysInMonth;
                        $pct = $workDays > 0 ? round((($hadir + $terlambat) / $workDays) * 100) : 0;
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--green-pale);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--green-deep);font-size:13px;flex-shrink:0;overflow:hidden;border:2px solid var(--green-pale2);">
                                    @if($emp->photo)<img src="{{ Storage::url($emp->photo) }}" style="width:100%;height:100%;object-fit:cover;">@else{{ strtoupper(substr($emp->name,0,1)) }}@endif
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">{{ $emp->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $emp->nip }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;">{{ $emp->jabatan ?? '-' }}</td>
                        <td style="text-align:center;"><span class="badge badge-hadir">{{ $hadir }}</span></td>
                        <td style="text-align:center;"><span class="badge badge-terlambat">{{ $terlambat }}</span></td>
                        <td style="text-align:center;"><span class="badge badge-izin">{{ $izin }}</span></td>
                        <td style="text-align:center;"><span class="badge badge-absent">{{ $alpha }}</span></td>
                        <td style="text-align:center;font-weight:700;">{{ $total }}</td>
                        <td style="text-align:center;">
                            <div style="display:flex;align-items:center;gap:8px;justify-content:center;">
                                <div style="flex:1;max-width:80px;height:6px;background:#e5e7eb;border-radius:3px;overflow:hidden;">
                                    <div style="height:100%; background: {{ $pct >= 80 ? 'var(--green-main)' : ($pct >= 60 ? '#f59e0b' : '#ef4444') }}; width: {{ $pct }}%;border-radius:3px; transition:width 1s;"></div>
                                </div>
                                <span style="font-size:12px; font-weight:600; color: {{ $pct >= 80 ? 'var(--green-main)' : ($pct >= 60 ? '#d97706' : '#ef4444') }};">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

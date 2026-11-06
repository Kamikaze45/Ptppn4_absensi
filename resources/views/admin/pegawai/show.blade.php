@extends('layouts.app')
@section('title', 'Detail Pegawai')
@section('page-title', 'Detail Pegawai')

@section('content')
<style>
    .dp-wrap{
        display:flex;
        gap:20px;
        align-items:flex-start;
        flex-wrap:wrap;
    }

    /* ===== Profile Card ===== */
    .dp-profile-card{
        width:280px;
        flex-shrink:0;
        overflow:hidden;
    }
    .dp-cover{
        background:linear-gradient(135deg,#004D2C,#006B3F);
        height:90px;
        border-radius:14px 14px 0 0;
        position:relative;
    }
    .dp-avatar{
        position:absolute;
        bottom:-40px;
        left:50%;
        transform:translateX(-50%);
        width:80px;
        height:80px;
        border-radius:50%;
        border:4px solid #fff;
        background:var(--primary);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:28px;
        color:#fff;
        overflow:hidden;
        box-shadow:0 4px 12px rgba(0,0,0,0.15);
    }
    .dp-avatar img{ width:100%; height:100%; object-fit:cover; }

    .dp-identity{ padding:52px 20px 20px; text-align:center; }
    .dp-name{ font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .dp-nip{ font-size:12px; color:var(--text-muted); margin-bottom:8px; }
    .dp-jabatan-badge{
        display:inline-block;
        font-size:11px;
        background:rgba(0,107,63,0.08);
        color:var(--primary);
        padding:4px 12px;
        border-radius:20px;
        font-weight:600;
    }

    .dp-info-list{ border-top:1px solid var(--border); padding:16px; }
    .dp-info-row{
        display:flex;
        gap:10px;
        margin-bottom:12px;
        padding:6px 8px;
        border-radius:8px;
        transition:background .15s ease;
    }
    .dp-info-row:hover{ background:rgba(0,107,63,0.05); }
    .dp-info-row i{ color:var(--primary); font-size:12px; width:16px; margin-top:2px; flex-shrink:0; }
    .dp-info-label{ font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.3px; }
    .dp-info-value{ font-size:12.5px; color:var(--text-primary); word-break:break-word; }

    .dp-status-row{ margin-top:4px; }

    .dp-actions{ padding:0 16px 16px; display:flex; gap:8px; }
    .dp-actions .btn{ flex:1; justify-content:center; }

    /* ===== Right column ===== */
    .dp-right{ flex:1; min-width:300px; }

    .dp-stats-grid{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:14px;
        margin-bottom:20px;
    }
    .stat-card{ transition:transform .15s ease, box-shadow .15s ease; }
    .stat-card:hover{ transform:translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,0.08); }

    /* Compact stat card (mobile) */
    .dp-stat-compact{
        display:flex !important;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        text-align:center;
        gap:4px;
        padding:12px 6px !important;
    }
    .dp-stat-compact .stat-icon{
        width:30px;
        height:30px;
        min-width:30px;
        font-size:13px;
        margin:0 0 2px;
    }
    .dp-stat-compact .stat-info h3{ font-size:17px; margin:0; line-height:1.1; }
    .dp-stat-compact .stat-info p{ font-size:9.5px; margin:0; white-space:nowrap; }

    .dp-table-wrap{ overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .dp-table-wrap table{ min-width:520px; width:100%; }
    .dp-tanggal{ font-weight:600; font-size:13px; }
    .dp-jam-in{ font-weight:600; color:var(--success); font-size:13px; }
    .dp-jam-out{ font-weight:600; color:var(--info); font-size:13px; }
    .dp-durasi{ font-size:12px; color:var(--text-secondary); }
    .dp-empty{ text-align:center; padding:24px; color:var(--text-muted); }

    /* ===== Responsive breakpoints ===== */
    @media (max-width: 768px){
        .dp-wrap{ gap:16px; }
        .dp-profile-card{ width:100%; }
        .dp-stats-grid{ grid-template-columns:repeat(3,1fr); gap:10px; }
        .stat-card{ padding:12px !important; }
    }

    @media (max-width: 520px){
        .dp-cover{ height:70px; }
        .dp-avatar{ width:64px; height:64px; bottom:-32px; font-size:22px; }
        .dp-identity{ padding:42px 16px 16px; }
        .dp-name{ font-size:15px; }
        .dp-jabatan-badge{ font-size:10px; padding:3px 10px; }
        .dp-info-list{ padding:12px; }
        .dp-info-row{ margin-bottom:8px; padding:5px 6px; }
        .dp-info-value{ font-size:12px; }
        .dp-actions{ flex-direction:column; }

        /* Stat card jadi kompak & tetap 3 kolom sejajar, bukan ditumpuk */
        .dp-stats-grid{ grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:16px; }
        .stat-card{
            display:flex !important;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            text-align:center;
            gap:2px;
            padding:10px 4px !important;
        }
        .stat-card .stat-icon{
            width:28px;
            height:28px;
            min-width:28px;
            font-size:12px;
            margin:0 0 2px;
        }
        .stat-card .stat-info h3{ font-size:16px; margin:0; line-height:1.1; }
        .stat-card .stat-info p{ font-size:9px; margin:0; line-height:1.2; }

        /* Card riwayat absensi lebih ringkas */
        .card-title{ font-size:13px; }
        .dp-table-wrap table{ min-width:460px; }
        .dp-table-wrap th, .dp-table-wrap td{ padding:8px 6px !important; font-size:12px; }
    }
</style>

<div class="dp-wrap">
    {{-- Profile Card --}}
    <div class="card dp-profile-card">
        <div class="dp-cover">
            <div class="dp-avatar">
                @if($pegawai->photo)
                    <img src="{{ Storage::url($pegawai->photo) }}" alt="{{ $pegawai->name }}">
                @else
                    {{ strtoupper(substr($pegawai->name,0,1)) }}
                @endif
            </div>
        </div>

        <div class="dp-identity">
            <h2 class="dp-name">{{ $pegawai->name }}</h2>
            <p class="dp-nip">NIP: {{ $pegawai->nip ?? '-' }}</p>
            <span class="dp-jabatan-badge">{{ $pegawai->jabatan }}</span>
        </div>

        <div class="dp-info-list">
            @foreach([
                ['fas fa-envelope', 'Email', $pegawai->email],
                ['fas fa-phone', 'Telepon', $pegawai->phone ?? '-'],
                ['fas fa-building', 'Divisi', $pegawai->divisi ?? '-'],
                ['fas fa-map-marker-alt', 'Alamat', $pegawai->alamat ?? '-'],
            ] as [$icon, $label, $value])
            <div class="dp-info-row">
                <i class="{{ $icon }}"></i>
                <div>
                    <p class="dp-info-label">{{ $label }}</p>
                    <p class="dp-info-value">{{ $value }}</p>
                </div>
            </div>
            @endforeach

            <div class="dp-status-row">
                <span class="badge {{ $pegawai->is_active ? 'badge-success' : 'badge-danger' }}">
                    <i class="fas fa-circle" style="font-size:8px;margin-right:4px;"></i>
                    {{ $pegawai->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div class="dp-actions">
            <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Stats & Riwayat --}}
    <div class="dp-right">
        <div class="dp-stats-grid">
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-check"></i></div>
                <div class="stat-info"><h3>{{ $totalHadir }}</h3><p>Hadir Bulan Ini</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-times"></i></div>
                <div class="stat-info"><h3>{{ $totalAlpha }}</h3><p>Alpha</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-file-medical"></i></div>
                <div class="stat-info"><h3>{{ $totalIzin }}</h3><p>Izin/Sakit</p></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-history" style="color:var(--primary);"></i> Riwayat Absensi Terbaru</span>
            </div>
            <div class="dp-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $item)
                        <tr>
                            <td><span class="dp-tanggal">{{ $item->tanggal->locale('id')->isoFormat('D MMM Y') }}</span></td>
                            <td>
                                @if($item->check_in)
                                    <span class="dp-jam-in">{{ $item->check_in }}</span>
                                @else
                                    <span style="color:var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->check_out)
                                    <span class="dp-jam-out">{{ $item->check_out }}</span>
                                @else
                                    <span style="color:var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td><span class="dp-durasi">{{ $item->durasi_kerja }}</span></td>
                            <td><span class="badge badge-{{ $item->status_badge }}">{{ ucfirst($item->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="dp-empty">Belum ada data absensi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')
@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')

@section('content')
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('absensi.riwayat') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0;flex:1;min-width:140px;">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-control">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;flex:1;min-width:120px;">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-control">
                    @foreach(range(date('Y'), date('Y')-3) as $y)
                        <option value="{{ $y }}" {{ request('tahun',$y) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height:40px;">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('absensi.riwayat') }}" class="btn btn-outline" style="height:40px;">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list-alt" style="color:var(--primary);"></i>Data Absensi</span>
        <span style="font-size:12px;color:var(--text-muted);">Total: {{ $absensi->total() }} data</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    @if(Auth::user()->isAdmin())
                    <th>Nama</th>
                    @endif
                    <th>Check-In</th>
                    <th>Lokasi Masuk</th>
                    <th>Check-Out</th>
                    <th>Lokasi Keluar</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $i => $item)
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;">{{ $absensi->firstItem() + $i }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $item->tanggal->locale('id')->isoFormat('D MMM Y') }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $item->tanggal->locale('id')->isoFormat('dddd') }}</div>
                    </td>
                    @if(Auth::user()->isAdmin())
                    <td>{{ $item->user->name }}</td>
                    @endif

                    <td>
                        @if($item->check_in)
                            <span style="font-weight:600;color:var(--success);">{{ $item->check_in }}</span>
                        @else <span style="color:var(--text-muted);">-</span> @endif
                    </td>
                    <td>
                        @if($item->check_in_lat)
                            <a href="https://maps.google.com/?q={{ $item->check_in_lat }},{{ $item->check_in_lng }}" target="_blank"
                               style="font-size:12px;color:var(--primary);text-decoration:none;" title="{{ $item->check_in_alamat }}">
                                <i class="fas fa-map-marker-alt"></i> Lihat Maps
                            </a>
                        @else <span style="color:var(--text-muted);font-size:12px;">-</span> @endif
                    </td>
                    <td>
                        @if($item->check_out)
                            <span style="font-weight:600;color:var(--info);">{{ $item->check_out }}</span>
                        @else <span style="color:var(--text-muted);">-</span> @endif
                    </td>
                    <td>
                        @if($item->check_out_lat)
                            <a href="https://maps.google.com/?q={{ $item->check_out_lat }},{{ $item->check_out_lng }}" target="_blank"
                               style="font-size:12px;color:var(--primary);text-decoration:none;" title="{{ $item->check_out_alamat }}">
                                <i class="fas fa-map-marker-alt"></i> Lihat Maps
                            </a>
                        @else <span style="color:var(--text-muted);font-size:12px;">-</span> @endif
                    </td>
                    <td style="font-size:12px;color:var(--text-secondary);">{{ $item->durasi_kerja }}</td>
                    <td><span class="badge badge-{{ $item->status_badge }}">{{ ucfirst($item->status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <i class="fas fa-calendar-times" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                        Tidak ada data absensi untuk filter ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px;">{{ $absensi->withQueryString()->links() }}</div>
    </div>
</div>
@endsection

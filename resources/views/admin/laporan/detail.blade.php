@extends('layouts.app')

@section('title', 'Detail Laporan Surat')

@push('styles')
<style>
.detail-hero {
    border-radius: 20px; padding: 2rem 2.5rem;
    margin-bottom: 1.5rem; position: relative; overflow: hidden; color: #fff;
}
.detail-hero.cuti  { background: linear-gradient(135deg,#0f5132,#157347,#20c997); }
.detail-hero.izin  { background: linear-gradient(135deg,#1e3a5f,#2563eb,#3b82f6); }
.detail-hero.sakit { background: linear-gradient(135deg,#7f1d1d,#dc2626,#f87171); }
.detail-hero::before { content:'';position:absolute;top:-40%;right:-5%;width:280px;height:280px;background:rgba(255,255,255,.06);border-radius:50%; }
.detail-hero .hero-chip {
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(8px); border-radius: 20px;
    padding: .3rem .85rem; font-size: .78rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: .4rem; margin-bottom: .75rem;
}
.detail-hero h3  { font-size: 1.5rem; font-weight: 800; margin-bottom: .3rem; }
.detail-hero .ds { color: rgba(255,255,255,.75); font-size: .875rem; }

.info-card {
    background: #fff; border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border: 1px solid #f0f0f0; overflow: hidden; margin-bottom: 1.25rem;
}
.info-card .ic-head {
    padding: 1rem 1.5rem; border-bottom: 1px solid #f0f0f0;
    font-size: .73rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .8px; color: #6b7280;
    display: flex; align-items: center; gap: .5rem;
}
.info-card .ic-body { padding: 1.5rem; }

.info-row { display:flex; gap:1rem; padding:.65rem 0; border-bottom:1px solid #f8f9fa; align-items:flex-start; }
.info-row:last-child { border-bottom:none; padding-bottom:0; }
.info-row .ir-l { flex:0 0 140px; font-size:.78rem; color:#9ca3af; font-weight:600; padding-top:.1rem; }
.info-row .ir-v { flex:1; font-size:.875rem; color:#1a1a2e; font-weight:500; }

.status-pill { display:inline-flex; align-items:center; gap:.4rem; padding:.42rem 1rem; border-radius:20px; font-size:.82rem; font-weight:700; }
.sp-menunggu  { background:#fff7e6; color:#d97706; border:1.5px solid #fcd34d; }
.sp-disetujui { background:#f0fdf4; color:#15803d; border:1.5px solid #86efac; }
.sp-ditolak   { background:#fff5f5; color:#dc2626; border:1.5px solid #fca5a5; }

.tipe-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.28rem .75rem; border-radius:8px; font-size:.78rem; font-weight:800; text-transform:uppercase; }
.tp-cuti  { background:#e0f2fe; color:#0369a1; }
.tp-izin  { background:#ede9fe; color:#7c3aed; }
.tp-sakit { background:#fee2e2; color:#dc2626; }

.btn-kembali {
    display:inline-flex; align-items:center; gap:.4rem;
    color:#6b7280; font-size:.875rem; text-decoration:none; font-weight:600; transition:color .15s;
}
.btn-kembali:hover { color:#1a1a2e; }

.processed-box { border-radius:12px; padding:1.25rem; display:flex; align-items:center; gap:1rem; }
.pb-menunggu  { background:#fff7e6; border:1.5px solid #fcd34d; }
.pb-disetujui { background:#f0fdf4; border:1.5px solid #86efac; }
.pb-ditolak   { background:#fff5f5; border:1.5px solid #fca5a5; }
.pb-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; flex-shrink:0; }

.lampiran-box { border:1.5px solid #e4e7ec; border-radius:12px; overflow:hidden; }
.lampiran-box img { width:100%; max-height:300px; object-fit:contain; background:#f8f9fa; }
.lampiran-footer { padding:.75rem 1rem; background:#f8f9fa; display:flex; align-items:center; justify-content:space-between; border-top:1px solid #e4e7ec; }

@media(max-width:768px){
    .info-row { flex-direction:column; gap:.2rem; }
    .info-row .ir-l { flex:none; }
    .detail-hero { padding:1.5rem; }
}
</style>
@endpush

@section('content')

{{-- Kembali --}}
<a href="{{ route('admin.laporan.index') }}" class="btn-kembali mb-3 d-inline-flex">
    <i class="fas fa-arrow-left"></i> Kembali ke Laporan
</a>

{{-- Hero --}}
<div class="detail-hero {{ $laporan->tipe }}">
    <div class="hero-chip">
        @if($laporan->tipe=='cuti') 🏖️ Surat Cuti
        @elseif($laporan->tipe=='izin') 📋 Surat Izin
        @else 🏥 Surat Sakit
        @endif
    </div>
    <h3>Detail Laporan #{{ $laporan->id }}</h3>
    <div class="ds">
        Diajukan oleh <strong>{{ $laporan->user->name ?? '-' }}</strong>
        &bull; {{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y, H:i') }}
    </div>
</div>

@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-3 border-0" style="background:#f0fdf4;color:#15803d;">
    <i class="fas fa-check-circle fs-5"></i><div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    {{-- Kiri --}}
    <div class="col-lg-7">

        {{-- Info Pegawai --}}
        <div class="info-card">
            <div class="ic-head"><i class="fas fa-user text-success"></i> Informasi Pegawai</div>
            <div class="ic-body">
                <div class="info-row">
                    <div class="ir-l">Nama</div>
                    <div class="ir-v fw-bold">{{ $laporan->user->name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Jabatan</div>
                    <div class="ir-v">{{ $laporan->user->jabatan ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Departemen</div>
                    <div class="ir-v">{{ $laporan->user->departemen ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Email</div>
                    <div class="ir-v">{{ $laporan->user->email ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Detail Surat --}}
        <div class="info-card">
            <div class="ic-head"><i class="fas fa-file-alt text-success"></i> Detail Surat</div>
            <div class="ic-body">
                <div class="info-row">
                    <div class="ir-l">Jenis Surat</div>
                    <div class="ir-v">
                        <span class="tipe-pill tp-{{ $laporan->tipe }}">{{ ucfirst($laporan->tipe) }}</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Status</div>
                    <div class="ir-v">
                        <span class="status-pill sp-{{ $laporan->status }}">
                            @if($laporan->status=='menunggu') <i class="fas fa-clock"></i> Menunggu
                            @elseif($laporan->status=='disetujui') <i class="fas fa-check"></i> Disetujui
                            @else <i class="fas fa-times"></i> Ditolak
                            @endif
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Tanggal Mulai</div>
                    <div class="ir-v">{{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Tanggal Selesai</div>
                    <div class="ir-v">{{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Durasi</div>
                    <div class="ir-v">
                        <span class="fw-bold">
                            {{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($laporan->tanggal_selesai)) + 1 }} hari
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Keterangan</div>
                    <div class="ir-v" style="white-space:pre-wrap;">{{ $laporan->alasan ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-l">Tanggal Ajuan</div>
                    <div class="ir-v text-muted">{{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- ✅ LAMPIRAN — DIPERBAIKI                                 --}}
        {{-- Sekarang pakai accessor $laporan->lampiran_url (bukan   --}}
        {{-- asset('storage/...') langsung), supaya:                --}}
        {{-- 1. Mendukung file yang disimpan di disk baru            --}}
        {{--    'public_uploads' (public/uploads) MAUPUN disk lama   --}}
        {{--    'public' (storage/app/public)                        --}}
        {{-- 2. File diserve lewat route PHP (izin.lampiran.show),   --}}
        {{--    jadi tidak butuh symlink storage:link dan tidak      --}}
        {{--    kena 403 di php artisan serve                        --}}
        {{-- 3. Kalau file memang sudah hilang dari server, tampil   --}}
        {{--    pesan "File tidak ditemukan" yang jelas, bukan 403   --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if($laporan->file_lampiran)
        <div class="info-card">
            <div class="ic-head"><i class="fas fa-paperclip text-success"></i> Lampiran</div>
            <div class="ic-body">
                @if($laporan->lampiran_url)
                    @php $ext = pathinfo($laporan->file_lampiran, PATHINFO_EXTENSION); @endphp
                    @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                        <div class="lampiran-box">
                            <img src="{{ $laporan->lampiran_url }}" alt="Lampiran">
                            <div class="lampiran-footer">
                                <span class="text-muted small">{{ basename($laporan->file_lampiran) }}</span>
                                <a href="{{ $laporan->lampiran_url }}" target="_blank"
                                   style="background:#eff6ff;color:#2563eb;border:1px solid #93c5fd;border-radius:8px;padding:.3rem .7rem;font-size:.78rem;font-weight:600;text-decoration:none;">
                                    <i class="fas fa-external-link-alt me-1"></i>Buka
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-3 p-3" style="background:#f8f9fa;border-radius:12px;border:1.5px solid #e4e7ec;">
                            <div style="width:44px;height:44px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:1.3rem;">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size:.875rem;">{{ basename($laporan->file_lampiran) }}</div>
                                <div class="text-muted small">Dokumen PDF</div>
                            </div>
                            <a href="{{ $laporan->lampiran_url }}" target="_blank"
                               style="background:#eff6ff;color:#2563eb;border:1px solid #93c5fd;border-radius:8px;padding:.35rem .85rem;font-size:.78rem;font-weight:700;text-decoration:none;white-space:nowrap;">
                                <i class="fas fa-eye me-1"></i>Lihat PDF
                            </a>
                        </div>
                    @endif
                @else
                    <div class="d-flex align-items-center gap-3 p-3" style="background:#fff5f5;border-radius:12px;border:1.5px solid #fca5a5;">
                        <div style="width:44px;height:44px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:1.3rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:.875rem;color:#dc2626;">File tidak ditemukan di server</div>
                            <div class="text-muted small">{{ basename($laporan->file_lampiran) }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Kanan --}}
    <div class="col-lg-5">

        {{-- Status saja, tanpa tombol Setujui & Tolak --}}
        <div class="info-card">
            <div class="ic-head"><i class="fas fa-info-circle text-success"></i> Status Pengajuan</div>
            <div class="ic-body">
                @if($laporan->status == 'menunggu')
                <div class="processed-box pb-menunggu">
                    <div class="pb-icon" style="background:#fff7e6;color:#d97706;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="color:#d97706;">Menunggu Keputusan</div>
                        <div class="text-muted" style="font-size:.82rem;">Pengajuan ini sedang menunggu keputusan Manajer.</div>
                    </div>
                </div>
                @elseif($laporan->status == 'disetujui')
                <div class="processed-box pb-disetujui">
                    <div class="pb-icon" style="background:#dcfce7;color:#15803d;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="color:#15803d;">Sudah Disetujui</div>
                        <div class="text-muted" style="font-size:.82rem;">Pengajuan ini telah disetujui oleh admin.</div>
                    </div>
                </div>
                @else
                <div class="processed-box pb-ditolak">
                    <div class="pb-icon" style="background:#fee2e2;color:#dc2626;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="color:#dc2626;">Sudah Ditolak</div>
                        <div class="text-muted" style="font-size:.82rem;">Pengajuan ini telah ditolak oleh admin.</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Ringkasan Periode --}}
        <div class="info-card">
            <div class="ic-head"><i class="fas fa-calendar-check text-success"></i> Ringkasan Periode</div>
            <div class="ic-body">
                <div class="text-center py-2">
                    <div style="font-size:2.75rem;font-weight:800;color:#157347;line-height:1;">
                        {{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($laporan->tanggal_selesai)) + 1 }}
                    </div>
                    <div class="text-muted mt-1" style="font-size:.82rem;">Hari {{ ucfirst($laporan->tipe) }}</div>
                </div>
                <hr style="margin:.75rem 0;">
                <div class="d-flex justify-content-between align-items-center" style="font-size:.82rem;">
                    <div>
                        <div class="text-muted" style="font-size:.73rem;">MULAI</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->format('d M Y') }}</div>
                    </div>
                    <i class="fas fa-arrow-right text-muted"></i>
                    <div class="text-end">
                        <div class="text-muted" style="font-size:.73rem;">SELESAI</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
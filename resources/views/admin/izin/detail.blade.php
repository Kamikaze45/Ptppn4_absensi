@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@push('styles')
<style>
.detail-hero { border-radius:20px;padding:2rem;margin-bottom:1.5rem;position:relative;overflow:hidden;color:#fff; }
.detail-hero.cuti  { background:linear-gradient(135deg,#0f5132,#157347,#20c997); }
.detail-hero.izin  { background:linear-gradient(135deg,#1e3a5f,#2563eb,#3b82f6); }
.detail-hero.sakit { background:linear-gradient(135deg,#7f1d1d,#dc2626,#f87171); }
.detail-hero::before { content:'';position:absolute;top:-40%;right:-5%;width:250px;height:250px;background:rgba(255,255,255,.06);border-radius:50%; }
.detail-hero .hero-badge { display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);padding:.3rem .8rem;border-radius:20px;font-size:.78rem;font-weight:600;margin-bottom:.75rem;backdrop-filter:blur(8px); }
.detail-hero h3  { font-weight:800;margin-bottom:.25rem; }
.detail-hero .sub { color:rgba(255,255,255,.75);font-size:.875rem; }
.info-card { background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #f0f0f0;overflow:hidden;margin-bottom:1.25rem; }
.info-card .card-head { padding:1rem 1.5rem;border-bottom:1px solid #f0f0f0;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;display:flex;align-items:center;gap:.5rem; }
.info-card .card-body-inner { padding:1.5rem; }
.info-row { display:flex;gap:1rem;padding:.65rem 0;border-bottom:1px solid #f8f9fa;align-items:flex-start; }
.info-row:last-child { border-bottom:none;padding-bottom:0; }
.info-row .ir-label { flex:0 0 140px;font-size:.8rem;color:#9ca3af;font-weight:600;padding-top:.1rem; }
.info-row .ir-value { flex:1;font-size:.875rem;color:#1a1a2e;font-weight:500; }
.status-pill { display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:20px;font-size:.82rem;font-weight:700; }
.status-pill.menunggu  { background:#fff7e6;color:#d97706;border:1.5px solid #fcd34d; }
.status-pill.disetujui { background:#f0fdf4;color:#15803d;border:1.5px solid #86efac; }
.status-pill.ditolak   { background:#fff5f5;color:#dc2626;border:1.5px solid #fca5a5; }
.tipe-pill { display:inline-flex;align-items:center;gap:.4rem;padding:.3rem .8rem;border-radius:8px;font-size:.78rem;font-weight:700;text-transform:uppercase; }
.tipe-pill.cuti  { background:#e0f2fe;color:#0369a1; }
.tipe-pill.izin  { background:#ede9fe;color:#7c3aed; }
.tipe-pill.sakit { background:#fee2e2;color:#dc2626; }
.action-card { background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #f0f0f0;padding:1.5rem;margin-bottom:1.25rem; }
.btn-back-link { display:inline-flex;align-items:center;gap:.4rem;color:#6b7280;font-size:.875rem;text-decoration:none;font-weight:600;transition:color .15s; }
.btn-back-link:hover { color:#1a1a2e; }
.lampiran-box { border:1.5px solid #e4e7ec;border-radius:12px;overflow:hidden; }
.lampiran-box img { width:100%;max-height:300px;object-fit:contain;background:#f8f9fa; }
.lampiran-box .lampiran-footer { padding:.75rem 1rem;background:#f8f9fa;display:flex;align-items:center;justify-content:space-between;border-top:1px solid #e4e7ec; }
.processed-notice { border-radius:12px;padding:1.25rem;display:flex;align-items:center;gap:1rem; }
.processed-notice.approved  { background:#f0fdf4;border:1.5px solid #86efac; }
.processed-notice.rejected  { background:#fff5f5;border:1.5px solid #fca5a5; }
.processed-notice.waiting   { background:#fff7e6;border:1.5px solid #fcd34d; }

/* ── Form aksi manajer ── */
.manajer-action-box { border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #f0f0f0;overflow:hidden;margin-bottom:1.25rem; }
.manajer-action-box .mab-head { padding:1rem 1.5rem;border-bottom:1px solid #f0f0f0;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#fff;display:flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#0f5132,#157347); }
.manajer-action-box .mab-body { padding:1.5rem;background:#fff; }
.btn-approve {
    width:100%;background:linear-gradient(135deg,#157347,#0f5132);color:#fff;border:none;border-radius:12px;
    padding:.75rem 1.25rem;font-weight:700;font-size:.9rem;
    display:inline-flex;align-items:center;justify-content:center;gap:.5rem;
    transition:all .2s;cursor:pointer;margin-bottom:.6rem;
}
.btn-approve:hover { opacity:.9;transform:translateY(-1px);box-shadow:0 6px 20px rgba(21,115,71,.35); }
.btn-reject {
    width:100%;background:#fff;color:#dc2626;border:2px solid #fca5a5;border-radius:12px;
    padding:.75rem 1.25rem;font-weight:700;font-size:.9rem;
    display:inline-flex;align-items:center;justify-content:center;gap:.5rem;
    transition:all .2s;cursor:pointer;
}
.btn-reject:hover { background:#fff5f5;border-color:#f87171;transform:translateY(-1px); }
.form-control-action {
    border:1.5px solid #e4e7ec;border-radius:10px;font-size:.875rem;padding:.65rem .9rem;
    width:100%;resize:vertical;font-family:inherit;
    transition:border-color .15s,box-shadow .15s;
}
.form-control-action:focus { border-color:#157347;box-shadow:0 0 0 3px rgba(21,115,71,.1);outline:none; }
.catatan-label { font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem;display:block; }
@media(max-width:768px){
    .info-row { flex-direction:column;gap:.2rem; }
    .info-row .ir-label { flex:none; }
}
</style>
@endpush

@section('content')

@php
    // ✅ Penentu apakah tombol Setuju/Tolak ditampilkan.
    // Kalau tombol tidak muncul, cek 2 hal ini:
    // 1. auth()->user()->role harus PERSIS string 'manajer' (huruf kecil semua)
    // 2. $pengajuan->status harus PERSIS string 'menunggu'
    $isManajer = auth()->user()->role === 'manajer';
@endphp

{{-- Tombol kembali (beda route untuk manajer vs admin) --}}
<a href="{{ $isManajer ? route('manajer.izin.index') : route('admin.izin.index') }}" class="btn-back-link mb-3 d-inline-flex">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
</a>

<div class="detail-hero {{ $pengajuan->tipe }}">
    <div class="hero-badge">
        @if($pengajuan->tipe=='cuti') 🏖️
        @elseif($pengajuan->tipe=='izin') 📋
        @else 🏥
        @endif
        Surat {{ ucfirst($pengajuan->tipe) }}
    </div>
    <h3>Detail Pengajuan #{{ $pengajuan->id }}</h3>
    <p class="sub">
        Diajukan oleh <strong>{{ $pengajuan->user->name ?? '-' }}</strong>
        &bull; {{ \Carbon\Carbon::parse($pengajuan->created_at)->format('d M Y, H:i') }}
    </p>
</div>

@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-3">
    <i class="fas fa-check-circle fs-5"></i><div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-3">
    <i class="fas fa-exclamation-circle fs-5"></i><div>{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- 🔍 Debug sementara — hapus setelah tombol terbukti muncul --}}
@if(config('app.debug'))
<div class="alert alert-info" style="font-size:.75rem;">
    DEBUG: role = "{{ auth()->user()->role }}" | isManajer = {{ $isManajer ? 'true' : 'false' }} | status pengajuan = "{{ $pengajuan->status }}"
</div>
@endif

<div class="row g-3">
    {{-- ═══════════════ KOLOM KIRI ═══════════════ --}}
    <div class="col-lg-7">

        <div class="info-card">
            <div class="card-head"><i class="fas fa-user text-success"></i> Informasi Pegawai</div>
            <div class="card-body-inner">
                <div class="info-row">
                    <div class="ir-label">Nama</div>
                    <div class="ir-value fw-bold">{{ $pengajuan->user->name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Jabatan</div>
                    <div class="ir-value">{{ $pengajuan->user->jabatan ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Departemen</div>
                    <div class="ir-value">{{ $pengajuan->user->departemen ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Email</div>
                    <div class="ir-value">{{ $pengajuan->user->email ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="card-head"><i class="fas fa-file-alt text-success"></i> Detail Pengajuan</div>
            <div class="card-body-inner">
                <div class="info-row">
                    <div class="ir-label">Tipe Surat</div>
                    <div class="ir-value"><span class="tipe-pill {{ $pengajuan->tipe }}">{{ ucfirst($pengajuan->tipe) }}</span></div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Status</div>
                    <div class="ir-value">
                        <span class="status-pill {{ $pengajuan->status }}">
                            @if($pengajuan->status=='menunggu') <i class="fas fa-clock"></i> Menunggu
                            @elseif($pengajuan->status=='disetujui') <i class="fas fa-check"></i> Disetujui
                            @else <i class="fas fa-times"></i> Ditolak
                            @endif
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Tanggal Mulai</div>
                    <div class="ir-value">{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Tanggal Selesai</div>
                    <div class="ir-value">{{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Durasi</div>
                    <div class="ir-value fw-bold">
                        {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($pengajuan->tanggal_selesai)) + 1 }} hari
                    </div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Keterangan</div>
                    <div class="ir-value" style="white-space:pre-wrap;">{{ $pengajuan->alasan ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="ir-label">Tanggal Ajuan</div>
                    <div class="ir-value text-muted">{{ \Carbon\Carbon::parse($pengajuan->created_at)->format('d M Y, H:i') }}</div>
                </div>
                @if($pengajuan->catatan_admin)
                <div class="info-row">
                    <div class="ir-label">Catatan Manajer</div>
                    <div class="ir-value">
                        <div class="p-2 rounded-3" style="background:#f8f9fa;font-size:.85rem;border-left:3px solid #157347;">
                            {{ $pengajuan->catatan_admin }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($pengajuan->file_lampiran)
        <div class="info-card">
            <div class="card-head"><i class="fas fa-paperclip text-success"></i> Lampiran / Surat Dokter</div>
            <div class="card-body-inner">
                @if($pengajuan->lampiran_url)
                    @php $ext = pathinfo($pengajuan->file_lampiran, PATHINFO_EXTENSION); @endphp
                    @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                        <div class="lampiran-box">
                            <img src="{{ $pengajuan->lampiran_url }}" alt="Lampiran">
                            <div class="lampiran-footer">
                                <span class="text-muted small">{{ basename($pengajuan->file_lampiran) }}</span>
                                <a href="{{ $pengajuan->lampiran_url }}" target="_blank"
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
                                <div class="fw-semibold" style="font-size:.875rem;">{{ basename($pengajuan->file_lampiran) }}</div>
                                <div class="text-muted small">Dokumen PDF</div>
                            </div>
                            <a href="{{ $pengajuan->lampiran_url }}" target="_blank"
                               style="background:#eff6ff;color:#2563eb;border:1px solid #93c5fd;border-radius:8px;padding:.35rem .85rem;font-size:.78rem;font-weight:600;white-space:nowrap;text-decoration:none;">
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
                            <div class="text-muted small">{{ basename($pengajuan->file_lampiran) }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ═══════════════ KOLOM KANAN ═══════════════ --}}
    <div class="col-lg-5">

        {{-- ╔══════════════════════════════════════════╗ --}}
        {{-- ║  AKSI MANAJER — hanya tampil jika role   ║ --}}
        {{-- ║  manajer & status masih menunggu          ║ --}}
        {{-- ╚══════════════════════════════════════════╝ --}}
        @if($isManajer && $pengajuan->status === 'menunggu')
        <div class="manajer-action-box">
            <div class="mab-head">
                <i class="fas fa-gavel"></i> Tindakan Manajer
            </div>
            <div class="mab-body">
                <p class="text-muted mb-3" style="font-size:.82rem;">
                    Tinjau pengajuan ini dan berikan keputusan. Catatan opsional untuk persetujuan, <strong>wajib</strong> untuk penolakan.
                </p>

                {{-- Form Setujui --}}
                <form action="{{ route('manajer.izin.setujui', $pengajuan->id) }}" method="POST" id="formSetujui">
                    @csrf
                    <label class="catatan-label">Catatan Persetujuan <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea name="catatan_admin" class="form-control-action mb-3" rows="2"
                        placeholder="Tambahkan catatan jika perlu..."
                        id="catatanSetujui"></textarea>
                    <button type="submit" class="btn-approve" onclick="return konfirmasiSetujui()">
                        <i class="fas fa-check-circle"></i> Setujui Pengajuan
                    </button>
                </form>

                <hr style="border-color:#f0f0f0;margin:1rem 0;">

                {{-- Form Tolak --}}
                <form action="{{ route('manajer.izin.tolak', $pengajuan->id) }}" method="POST" id="formTolak">
                    @csrf
                    <label class="catatan-label">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea name="catatan_admin" class="form-control-action mb-3" rows="2"
                        placeholder="Tuliskan alasan penolakan..."
                        required id="catatanTolak"></textarea>
                    <button type="submit" class="btn-reject" onclick="return konfirmasiTolak()">
                        <i class="fas fa-times-circle"></i> Tolak Pengajuan
                    </button>
                </form>
            </div>
        </div>
        @else
        {{-- Status Notice (read-only: untuk admin atau pengajuan yang sudah diproses) --}}
        <div class="action-card">
            @if($pengajuan->status == 'menunggu')
            <div class="processed-notice waiting">
                <div style="width:44px;height:44px;background:#fff7e6;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#d97706;font-size:1.3rem;flex-shrink:0;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="fw-bold" style="color:#d97706;">Menunggu Persetujuan</div>
                    <div class="text-muted small">Pengajuan ini sedang menunggu keputusan dari Manajer.</div>
                </div>
            </div>
            @elseif($pengajuan->status == 'disetujui')
            <div class="processed-notice approved">
                <div style="width:44px;height:44px;background:#dcfce7;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#15803d;font-size:1.3rem;flex-shrink:0;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="fw-bold" style="color:#15803d;">Pengajuan Disetujui</div>
                    <div class="text-muted small">Pengajuan ini telah disetujui oleh Manajer.</div>
                    @if($pengajuan->catatan_admin)
                    <div class="mt-1 p-2 rounded-2" style="background:#dcfce7;font-size:.8rem;color:#15803d;">
                        <i class="fas fa-quote-left me-1" style="font-size:.7rem;"></i>{{ $pengajuan->catatan_admin }}
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="processed-notice rejected">
                <div style="width:44px;height:44px;background:#fee2e2;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:1.3rem;flex-shrink:0;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="fw-bold" style="color:#dc2626;">Pengajuan Ditolak</div>
                    <div class="text-muted small">Pengajuan ini telah ditolak oleh Manajer.</div>
                    @if($pengajuan->catatan_admin)
                    <div class="mt-1 p-2 rounded-2" style="background:#fee2e2;font-size:.8rem;color:#dc2626;">
                        <i class="fas fa-quote-left me-1" style="font-size:.7rem;"></i>{{ $pengajuan->catatan_admin }}
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Ringkasan Periode --}}
        <div class="info-card">
            <div class="card-head"><i class="fas fa-calendar text-success"></i> Ringkasan Periode</div>
            <div class="card-body-inner">
                <div class="text-center py-2">
                    <div style="font-size:2.5rem;font-weight:800;color:#157347;line-height:1;">
                        {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($pengajuan->tanggal_selesai)) + 1 }}
                    </div>
                    <div class="text-muted" style="font-size:.8rem;">Hari {{ ucfirst($pengajuan->tipe) }}</div>
                </div>
                <hr style="margin:.75rem 0;">
                <div class="d-flex justify-content-between" style="font-size:.82rem;">
                    <div>
                        <div class="text-muted">Mulai</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }}</div>
                    </div>
                    <div class="text-center text-muted"><i class="fas fa-arrow-right"></i></div>
                    <div class="text-end">
                        <div class="text-muted">Selesai</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
@if($isManajer && $pengajuan->status === 'menunggu')
function konfirmasiSetujui() {
    return confirm('Yakin ingin menyetujui pengajuan ini?');
}
function konfirmasiTolak() {
    const alasan = document.getElementById('catatanTolak').value.trim();
    if (!alasan) {
        alert('Alasan penolakan wajib diisi!');
        document.getElementById('catatanTolak').focus();
        return false;
    }
    return confirm('Yakin ingin menolak pengajuan ini?');
}
@endif
</script>
@endpush
@endsection
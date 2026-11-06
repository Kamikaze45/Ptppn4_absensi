@extends('layouts.app')

@section('title', 'Pengajuan Surat Cuti')

@push('styles')
<style>
    .form-hero {
        background: linear-gradient(135deg, #0f5132 0%, #157347 50%, #20c997 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .form-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .form-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        right: 10%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .form-hero .hero-icon {
        width: 56px;
        height: 56px;
        background: rgba(255,255,255,0.15);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        backdrop-filter: blur(10px);
    }
    .form-hero h2 { color: #fff; font-weight: 700; margin-bottom: 0.25rem; }
    .form-hero p  { color: rgba(255,255,255,0.75); margin: 0; font-size: 0.9rem; }

    /* ✅ BARU — Kartu info sisa kuota cuti */
    .kuota-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: #fff;
        border: 1.5px solid #eef0f4;
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,.04);
    }
    .kuota-card.kuota-habis { border-color: #fca5a5; background: #fff5f5; }
    .kuota-icon {
        width: 48px; height: 48px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        background: #dcfce7; color: #15803d;
    }
    .kuota-card.kuota-habis .kuota-icon { background: #fee2e2; color: #dc2626; }
    .kuota-text { flex: 1; }
    .kuota-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: #111827;
        line-height: 1.1;
    }
    .kuota-card.kuota-habis .kuota-val { color: #dc2626; }
    .kuota-lbl {
        font-size: .78rem;
        color: #6b7280;
        font-weight: 500;
    }
    .kuota-bar-wrap {
        width: 100%;
        height: 6px;
        background: #f3f4f6;
        border-radius: 20px;
        margin-top: .4rem;
        overflow: hidden;
    }
    .kuota-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #157347, #20c997);
        border-radius: 20px;
        transition: width .3s;
    }
    .kuota-card.kuota-habis .kuota-bar-fill { background: linear-gradient(90deg, #dc2626, #f87171); }

    /* ── Download Template Button (tema hijau) ── */
    .download-template-wrap { width: 100%; margin-bottom: 1rem; }
    .btn-download-template {
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, #0f5132 0%, #157347 100%);
        border-radius: 16px;
        padding: 14px 20px;
        text-decoration: none;
        color: white;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(21,115,71,0.3);
        position: relative;
        overflow: hidden;
    }
    .btn-download-template::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20px;
        width: 140px;
        height: 140px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
        pointer-events: none;
    }
    .btn-download-template:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(21,115,71,0.45);
        color: white;
        text-decoration: none;
    }
    .btn-download-template:active { transform: translateY(0); }
    .btn-dl-icon {
        width: 46px;
        height: 46px;
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        backdrop-filter: blur(4px);
    }
    .btn-dl-text {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .btn-dl-title {
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .btn-dl-sub {
        font-size: 0.75rem;
        opacity: 0.8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .btn-dl-arrow {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        transition: background 0.2s;
    }
    .btn-download-template:hover .btn-dl-arrow {
        background: rgba(255,255,255,0.25);
    }

    .form-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        border: 1px solid rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .form-section {
        padding: 1.75rem;
        border-bottom: 1px solid #f0f0f0;
    }
    .form-section:last-child { border-bottom: none; }
    .section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c757d;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e9ecef;
    }
    .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #344054;
        margin-bottom: 0.5rem;
    }
    .form-control, .form-select {
        border: 1.5px solid #e4e7ec;
        border-radius: 10px;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: #fafafa;
    }
    .form-control:focus, .form-select:focus {
        border-color: #157347;
        box-shadow: 0 0 0 3px rgba(21,115,71,0.12);
        background: #fff;
    }
    textarea.form-control { resize: vertical; min-height: 110px; }

    .drop-zone {
        border: 2px dashed #d0d5dd;
        border-radius: 14px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        background: #fafafa;
        position: relative;
    }
    .drop-zone:hover, .drop-zone.drag-over {
        border-color: #157347;
        background: #f0fdf4;
        transform: translateY(-1px);
    }
    .drop-zone .dz-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.4rem;
        color: #157347;
    }
    .drop-zone .dz-title { font-weight: 600; color: #344054; margin-bottom: 0.25rem; font-size: 0.95rem; }
    .drop-zone .dz-sub   { font-size: 0.8rem; color: #98a2b3; }
    .drop-zone .dz-badge {
        display: inline-block;
        background: #fff3cd;
        color: #856404;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        margin-top: 0.5rem;
        border: 1px solid #ffc107;
    }

    .file-preview {
        display: none;
        align-items: center;
        gap: 1rem;
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        border-radius: 12px;
        padding: 1rem 1.25rem;
    }
    .file-preview .fp-icon {
        width: 44px;
        height: 44px;
        background: #dcfce7;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #15803d;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .file-preview .fp-info { flex: 1; min-width: 0; }
    .file-preview .fp-name { font-weight: 600; color: #15803d; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .file-preview .fp-size { font-size: 0.78rem; color: #6b7280; }

    .btn-submit {
        background: linear-gradient(135deg, #157347, #0f5132);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.25s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-submit:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(21,115,71,0.35);
        color: #fff;
    }
    .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }
    .btn-back {
        background: #f8f9fa;
        color: #495057;
        border: 1.5px solid #e4e7ec;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    .btn-back:hover { background: #e9ecef; color: #344054; border-color: #ced4da; }

    /* Responsif mobile */
    @media (max-width: 576px) {
        .form-hero { padding: 1.5rem; }
        .form-section { padding: 1.25rem; }
        .drop-zone { padding: 1.75rem 1rem; }
        .btn-submit, .btn-back { width: 100%; justify-content: center; }
        .action-row { flex-direction: column; }
        .btn-download-template { padding: 12px 14px; gap: 10px; border-radius: 12px; }
        .btn-dl-icon { width: 40px; height: 40px; font-size: 1.1rem; border-radius: 10px; }
        .btn-dl-title { font-size: 0.85rem; }
        .btn-dl-sub { display: none; }
        .btn-dl-arrow { width: 30px; height: 30px; border-radius: 8px; font-size: 0.8rem; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="form-hero">
    <div class="hero-icon">🏖️</div>
    <h2>Pengajuan Surat Cuti</h2>
    <p>Isi formulir di bawah untuk mengajukan permohonan cuti</p>
</div>

{{-- ✅ BARU — Info sisa kuota cuti tahun berjalan.
     $sisaKuotaCuti dan $sudahDipakai dikirim dari
     PengajuanIzinController::formCuti() --}}
@php
    $kuotaMaksimal = 12;
    $persenTerpakai = $kuotaMaksimal > 0 ? min(100, round(($sudahDipakai / $kuotaMaksimal) * 100)) : 0;
    $kuotaHabis = $sisaKuotaCuti <= 0;
@endphp
<div class="kuota-card {{ $kuotaHabis ? 'kuota-habis' : '' }}">
    <div class="kuota-icon">
        <i class="fas {{ $kuotaHabis ? 'fa-exclamation-triangle' : 'fa-umbrella-beach' }}"></i>
    </div>
    <div class="kuota-text">
        <div class="kuota-val">
            {{ $sisaKuotaCuti }} hari
            <span style="font-size:.75rem;font-weight:600;color:#9ca3af;">tersisa dari {{ $kuotaMaksimal }} hari/tahun</span>
        </div>
        <div class="kuota-lbl">
            @if($kuotaHabis)
                Kuota cuti kamu tahun {{ now()->year }} sudah habis terpakai.
            @else
                Sudah terpakai {{ $sudahDipakai }} hari (termasuk yang masih menunggu persetujuan).
            @endif
        </div>
        <div class="kuota-bar-wrap">
            <div class="kuota-bar-fill" style="width: {{ $persenTerpakai }}%;"></div>
        </div>
    </div>
</div>

{{-- ✅ TOMBOL DOWNLOAD TEMPLATE SURAT CUTI --}}
<div class="download-template-wrap">
    <a href="{{ asset('templates/Cuti.docx') }}" download="Template_Surat_Cuti.docx"
       class="btn-download-template">
        <div class="btn-dl-icon">
            <i class="fas fa-file-word"></i>
        </div>
        <div class="btn-dl-text">
            <span class="btn-dl-title">Download Template Surat Cuti</span>
            <span class="btn-dl-sub">Format .docx · Isi & upload sebagai lampiran</span>
        </div>
        <div class="btn-dl-arrow">
            <i class="fas fa-download"></i>
        </div>
    </a>
</div>

{{-- Alert Sukses --}}
@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-3" role="alert">
    <i class="fas fa-check-circle fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Alert Error --}}
@if($errors->any())
<div class="alert alert-danger d-flex align-items-start gap-2 rounded-3 mb-3" role="alert">
    <i class="fas fa-exclamation-circle fs-5 mt-1"></i>
    <div>
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Form Card --}}
<div class="form-card">
    <form action="{{ route('izin.submit') }}" method="POST" enctype="multipart/form-data" id="formCuti">
        @csrf
        <input type="hidden" name="tipe" value="cuti">

        {{-- Tanggal --}}
        <div class="form-section">
            <div class="section-title"><i class="fas fa-calendar-alt text-success"></i> Periode Cuti</div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai"
                        class="form-control @error('tanggal_mulai') is-invalid @enderror"
                        value="{{ old('tanggal_mulai') }}" required>
                    @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_selesai"
                        class="form-control @error('tanggal_selesai') is-invalid @enderror"
                        value="{{ old('tanggal_selesai') }}" required>
                    @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Keterangan --}}
        <div class="form-section">
            <div class="section-title"><i class="fas fa-align-left text-success"></i> Keterangan</div>
            <label class="form-label">Alasan Pengajuan Cuti <span class="text-danger">*</span></label>
            <textarea name="alasan" rows="4"
                class="form-control @error('alasan') is-invalid @enderror"
                placeholder="Tuliskan alasan cuti secara jelas..." required>{{ old('alasan') }}</textarea>
            @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Lampiran --}}
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-paperclip text-success"></i> Lampiran
                <span class="badge bg-secondary ms-1" style="font-size:0.65rem;border-radius:6px;">Opsional</span>
            </div>

            <div id="dropZone" class="drop-zone @error('file_lampiran') border-danger @enderror"
                onclick="document.getElementById('lampiranInput').click()"
                ondragover="handleDragOver(event)"
                ondragleave="handleDragLeave(event)"
                ondrop="handleDrop(event)">
                <div id="dropPlaceholder">
                    <div class="dz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <p class="dz-title">Klik atau seret file ke sini</p>
                    <p class="dz-sub">PDF, JPG, JPEG, PNG</p>
                    <span class="dz-badge">Maks. 2MB</span>
                </div>
                <div id="filePreview" class="file-preview">
                    <div class="fp-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="fp-info">
                        <div class="fp-name" id="fileName">—</div>
                        <div class="fp-size" id="fileSize">—</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="removeFile(event)">
                        <i class="fas fa-times me-1"></i>Hapus
                    </button>
                </div>
            </div>

            <input type="file" id="lampiranInput" name="file_lampiran" class="d-none"
                accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this)">

            @error('file_lampiran')
                <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="form-section">
            <div class="d-flex flex-wrap gap-2 action-row">
                <button type="submit" id="btnSubmit" class="btn-submit" {{ $kuotaHabis ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                </button>
                <a href="{{ route('izin.riwayat') }}" class="btn-back">
                    <i class="fas fa-list-ul"></i> Riwayat
                </a>
            </div>
            @if($kuotaHabis)
            <p class="text-danger small mt-2 mb-0">
                <i class="fas fa-info-circle me-1"></i>Kamu tidak bisa mengajukan cuti baru karena kuota tahun ini sudah habis.
            </p>
            @endif
        </div>
    </form>
</div>

@push('scripts')
<script>
const dropZone      = document.getElementById('dropZone');
const lampiranInput = document.getElementById('lampiranInput');

function handleFileSelect(input) {
    if (input.files && input.files[0]) showFile(input.files[0]);
}
function handleDragOver(e) {
    e.preventDefault();
    dropZone.classList.add('drag-over');
}
function handleDragLeave(e) {
    dropZone.classList.remove('drag-over');
}
function handleDrop(e) {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        lampiranInput.files = dt.files;
        showFile(file);
    }
}
function showFile(file) {
    if (!file.type.match(/pdf|jpg|jpeg|png/i)) {
        alert('Format tidak didukung. Gunakan PDF, JPG, atau PNG.');
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 2MB.');
        return;
    }
    document.getElementById('dropPlaceholder').style.display = 'none';
    const fp = document.getElementById('filePreview');
    fp.style.display = 'flex';
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
    dropZone.style.border = '2px solid #86efac';
}
function removeFile(e) {
    e.stopPropagation();
    lampiranInput.value = '';
    document.getElementById('dropPlaceholder').style.display = 'block';
    document.getElementById('filePreview').style.display = 'none';
    dropZone.style.border = '2px dashed #d0d5dd';
}
</script>
@endpush
@endsection
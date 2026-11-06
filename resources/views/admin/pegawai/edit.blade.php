@extends('layouts.app')
@section('title', 'Edit Pegawai')
@section('page-title', 'Edit Data Pegawai')

@push('styles')
<style>
/* ════════════════════════════════
   PASSWORD FIELD DENGAN TOGGLE MATA
════════════════════════════════ */
.pw-field {
    position: relative;
    width: 100%;
}
.pw-field .form-control {
    padding-right: 44px; /* ruang untuk ikon mata */
}
.pw-toggle {
    position: absolute;
    top: 50%;
    right: 4px;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    border: none;
    background: transparent;
    color: var(--text-muted, #9ca3af);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: color .15s, background .15s;
}
.pw-toggle:hover {
    color: var(--primary, #157347);
    background: rgba(21,115,71,.08);
}
.pw-toggle:focus-visible {
    outline: 2px solid var(--primary, #157347);
    outline-offset: 1px;
}
.pw-toggle i { font-size: 14px; pointer-events: none; }

/* ════════════════════════════════
   SEDIKIT POLESAN VISUAL UNTUK
   SECTION PASSWORD
════════════════════════════════ */
.pw-section-title {
    grid-column: 1/-1;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 8px 0 2px;
    padding-top: 12px;
    border-top: 1px dashed var(--border, #e4e7ec);
}
.pw-section-title i { color: var(--primary, #157347); font-size: 13px; }
.pw-section-title span {
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--text-muted, #9ca3af);
}
.pw-hint {
    grid-column: 1/-1;
    font-size: 12px;
    color: var(--text-muted, #9ca3af);
    margin-top: -6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>
@endpush

@section('content')
<div style="max-width:720px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-user-edit" style="color:var(--primary);"></i>Edit: {{ $pegawai->name }}</span>
            <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pegawai.update', $pegawai) }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div style="display:flex;gap:16px;align-items:center;margin-bottom:24px;padding:16px;background:var(--bg-body);border-radius:12px;">
                    <div id="photoPreview" style="width:80px;height:80px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-size:28px;color:white;overflow:hidden;flex-shrink:0;border:3px solid var(--border);">
                        @if($pegawai->photo)
                            <img src="{{ Storage::url($pegawai->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            {{ strtoupper(substr($pegawai->name,0,1)) }}
                        @endif
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;margin-bottom:10px;">Ganti Foto</p>
                        <label style="cursor:pointer;" class="btn btn-outline btn-sm">
                            <i class="fas fa-camera"></i> Pilih Foto
                            <input type="file" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                        </label>
                    </div>
                </div>

                <div class="grid grid-2" style="gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $pegawai->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SAP *</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $pegawai->nip) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $pegawai->email) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $pegawai->phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="jabatan" class="form-control" required>
                            @foreach($jabatanList as $j)
                                <option value="{{ $j }}" {{ old('jabatan', $pegawai->jabatan) == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="divisi" class="form-control" value="{{ old('divisi', $pegawai->divisi) }}">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $pegawai->alamat) }}</textarea>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;display:flex;align-items:center;gap:10px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;color:var(--text-secondary);">
                            <input type="checkbox" name="is_active" value="1" {{ $pegawai->is_active ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary);">
                            Akun Aktif
                        </label>
                    </div>

                    {{-- ══ Section Password ══════════════════════════ --}}
                    <div class="pw-section-title">
                        <i class="fas fa-lock"></i>
                        <span>Ubah Password</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password Baru <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                        <div class="pw-field">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
                            <button type="button" class="pw-toggle" data-target="password" aria-label="Tampilkan password" title="Tampilkan/sembunyikan password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <div class="pw-field">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                            <button type="button" class="pw-toggle" data-target="password_confirmation" aria-label="Tampilkan password" title="Tampilkan/sembunyikan password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <p class="pw-hint"><i class="fas fa-info-circle"></i> Kosongkan kedua kolom jika tidak ingin mengubah password pegawai ini.</p>
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline btn-lg">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreview').innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Toggle show/hide password ──────────────────────────
document.querySelectorAll('.pw-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const targetId = btn.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            btn.setAttribute('aria-label', 'Sembunyikan password');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            btn.setAttribute('aria-label', 'Tampilkan password');
        }
    });
});
</script>
@endpush
@extends('layouts.app')
@section('title', 'Tambah Pegawai')
@section('page-title', 'Tambah Pegawai Baru')

@section('content')
<div style="max-width:720px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-user-plus" style="color:var(--primary);"></i>Form Registrasi Pegawai</span>
            <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pegawai.store') }}" enctype="multipart/form-data">
                @csrf

                <div style="display:flex;gap:16px;align-items:center;margin-bottom:24px;padding:16px;background:var(--bg-body);border-radius:12px;">
                    <div id="photoPreview" style="width:80px;height:80px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-size:28px;color:white;overflow:hidden;flex-shrink:0;border:3px solid var(--border);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:4px;">Foto Profil</p>
                        <p style="font-size:12px;color:var(--text-muted);margin-bottom:10px;">JPG/PNG, maks 2MB</p>
                        <label style="cursor:pointer;" class="btn btn-outline btn-sm">
                            <i class="fas fa-camera"></i> Pilih Foto
                            <input type="file" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                        </label>
                    </div>
                </div>

                <div class="grid grid-2" style="gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span style="color:var(--danger);">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@ptpn4.co.id" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xx-xxxx-xxxx">
                    </div>

                    {{-- Jabatan: text bebas + saran datalist --}}
                    <div class="form-group">
                        <label class="form-label">Jabatan <span style="color:var(--danger);">*</span></label>
                        <input type="text"
                               name="jabatan"
                               class="form-control"
                               value="{{ old('jabatan') }}"
                               placeholder="Ketik atau pilih jabatan..."
                               list="jabatan-suggestions"
                               autocomplete="off"
                               required>
                        <datalist id="jabatan-suggestions">
                            @foreach($jabatanList as $j)
                                <option value="{{ $j }}">
                            @endforeach
                        </datalist>
                        <small style="color:var(--text-muted);font-size:11px;margin-top:4px;display:block;">
                            <i class="fas fa-info-circle"></i> Bisa ketik bebas atau pilih dari saran
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Divisi / Unit Kerja</label>
                        <input type="text" name="divisi" class="form-control" value="{{ old('divisi') }}" placeholder="Contoh: Divisi Keuangan">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span style="color:var(--danger);">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="pw1" class="form-control" placeholder="Min. 8 karakter" required>
                            <button type="button" onclick="toggleField('pw1')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password <span style="color:var(--danger);">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="password_confirmation" id="pw2" class="form-control" placeholder="Ulangi password" required>
                            <button type="button" onclick="toggleField('pw2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Simpan Pegawai
                    </button>
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
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function toggleField(id) {
    const f = document.getElementById(id);
    f.type = f.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
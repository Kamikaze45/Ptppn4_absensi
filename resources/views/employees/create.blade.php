@extends('layouts.app')
@section('page-title', 'Tambah Pegawai')
@section('title', 'Tambah Pegawai')

@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Pegawai Baru</h1>
        <p>Isi data lengkap untuk mendaftarkan pegawai</p>
    </div>
    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="form-grid">
        <!-- Left -->
        <div>
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><div class="card-title"><i class="fas fa-user" style="color:var(--green-main);"></i> Data Pribadi</div></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="nip" class="form-control {{ $errors->has('nip') ? 'is-invalid' : '' }}" value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai" required>
                        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" placeholder="nama@ptpn4.co.id" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Bergabung</label>
                        <input type="date" name="join_date" class="form-control" value="{{ old('join_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right -->
        <div>
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><div class="card-title"><i class="fas fa-briefcase" style="color:var(--gold);"></i> Data Pekerjaan</div></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Jabatan <span style="color:#ef4444;">*</span></label>
                        <select name="jabatan" class="form-control {{ $errors->has('jabatan') ? 'is-invalid' : '' }}" required>
                            <option value="">— Pilih Jabatan —</option>
                            @foreach(\App\Models\User::jabatanList() as $key => $val)
                            <option value="{{ $key }}" {{ old('jabatan') === $key ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                        @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Departemen <span style="color:#ef4444;">*</span></label>
                        <select name="departemen" class="form-control {{ $errors->has('departemen') ? 'is-invalid' : '' }}" required>
                            <option value="">— Pilih Departemen —</option>
                            @foreach(\App\Models\User::departemenList() as $key => $val)
                            <option value="{{ $key }}" {{ old('departemen') === $key ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><div class="card-title"><i class="fas fa-lock" style="color:#ef4444;"></i> Akun Login</div></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Password <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Minimal 8 karakter" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Konfirmasi Password <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title"><i class="fas fa-camera" style="color:#7c3aed;"></i> Foto Pegawai</div></div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="file" name="photo" accept="image/*" class="form-control" onchange="previewPhoto(this)">
                        <div id="photoPreview" style="display:none;margin-top:12px;">
                            <img id="previewImg" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--green-pale2);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pegawai</button>
    </div>
</form>

@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('photoPreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<style>
@media(max-width:768px) { .form-grid { grid-template-columns: 1fr !important; } }
</style>
@endpush

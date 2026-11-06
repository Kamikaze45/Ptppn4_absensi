@extends('layouts.app')
@section('page-title', 'Edit Pegawai')
@section('title', 'Edit Pegawai')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Data Pegawai</h1>
        <p>Perbarui informasi pegawai {{ $employee->name }}</p>
    </div>
    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="form-grid">
        <div>
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><div class="card-title"><i class="fas fa-user" style="color:var(--green-main);"></i> Data Pribadi</div></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $employee->nip) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Bergabung</label>
                        <input type="date" name="join_date" class="form-control" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $employee->address) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><div class="card-title"><i class="fas fa-briefcase" style="color:var(--gold);"></i> Data Pekerjaan</div></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan" class="form-control">
                            @foreach(\App\Models\User::jabatanList() as $k => $v)
                            <option value="{{ $k }}" {{ old('jabatan', $employee->jabatan) === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Departemen</label>
                        <select name="departemen" class="form-control">
                            @foreach(\App\Models\User::departemenList() as $k => $v)
                            <option value="{{ $k }}" {{ old('departemen', $employee->departemen) === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif" {{ $employee->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $employee->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><div class="card-title"><i class="fas fa-lock" style="color:#ef4444;"></i> Ubah Password (opsional)</div></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><div class="card-title"><i class="fas fa-camera" style="color:#7c3aed;"></i> Foto</div></div>
                <div class="card-body">
                    @if($employee->photo)
                    <div style="margin-bottom:12px;display:flex;align-items:center;gap:12px;">
                        <img src="{{ Storage::url($employee->photo) }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid var(--green-pale2);">
                        <span style="font-size:12px;color:var(--gray);">Foto saat ini</span>
                    </div>
                    @endif
                    <input type="file" name="photo" accept="image/*" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
    </div>
</form>
@endsection
<style>
@media(max-width:768px) { .form-grid { grid-template-columns: 1fr !important; } }
</style>

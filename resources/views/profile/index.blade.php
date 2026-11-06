@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
.profile-page-wrap { width: 100%; max-width: 100%; overflow-x: hidden; box-sizing: border-box; }
.profile-grid { display: grid; grid-template-columns: 300px 1fr; gap: 20px; align-items: start; width: 100%; }
.profile-card { background: var(--bg-card); border-radius: 20px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; position: sticky; top: calc(var(--topbar-height) + 20px); width: 100%; }
.profile-banner { background: linear-gradient(135deg, #004D2C 0%, #006B3F 55%, #00A060 100%); height: 100px; position: relative; overflow: hidden; }
.profile-banner::before { content: ''; position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,215,0,0.12); border-radius: 50%; }
.profile-banner::after  { content: ''; position: absolute; bottom: -35px; left: -15px; width: 90px; height: 90px; background: rgba(255,255,255,0.06); border-radius: 50%; }
.profile-avatar-wrap { display: flex; justify-content: center; margin-top: -48px; position: relative; z-index: 10; }
#avatarPreview { width: 92px; height: 92px; border-radius: 50%; border: 4px solid white; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 30px; color: white; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.15); flex-shrink: 0; }
#avatarPreview img { width: 100%; height: 100%; object-fit: cover; display: block; }
.profile-info { padding: 12px 16px 18px; text-align: center; }
.profile-info h2 { font-size: 15px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary); word-break: break-word; }
.profile-info .p-email { font-size: 11.5px; color: var(--text-muted); margin: 0 0 10px; word-break: break-all; }
.profile-jabatan { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; background: rgba(0,107,63,0.08); color: var(--primary); padding: 4px 12px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(0,107,63,0.12); }
.profile-divisi { font-size: 11.5px; color: var(--text-muted); margin: 6px 0 8px; }
.profile-meta { border-top: 1px solid var(--border); padding: 14px 16px; }
.profile-meta-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
.profile-meta-item:last-child { margin-bottom: 0; }
.meta-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(0,107,63,0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--primary); font-size: 12px; }
.meta-content label { display: block; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); margin-bottom: 1px; }
.meta-content span  { font-size: 12.5px; font-weight: 500; color: var(--text-primary); word-break: break-word; }
.profile-forms { display: flex; flex-direction: column; gap: 20px; width: 100%; min-width: 0; }
.form-card { background: var(--bg-card); border-radius: 18px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; width: 100%; }
.form-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.form-card-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
.form-card-icon.green  { background: rgba(0,107,63,0.1);   color: var(--primary); }
.form-card-icon.yellow { background: rgba(245,158,11,0.12); color: var(--warning); }
.form-card-header-text h3 { font-size: 14px; font-weight: 700; margin: 0; color: var(--text-primary); }
.form-card-header-text p  { font-size: 11px; color: var(--text-muted); margin: 2px 0 0; }
.form-card-body { padding: 20px; }
.photo-upload-area { display: flex; align-items: center; gap: 14px; padding: 14px; background: var(--bg-body); border-radius: 12px; border: 1.5px dashed var(--border); margin-bottom: 20px; }
#photoThumb { width: 58px; height: 58px; border-radius: 50%; background: var(--primary); overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 20px; color: white; border: 2px solid var(--border); }
#photoThumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.photo-upload-info h4 { font-size: 13px; font-weight: 700; margin: 0 0 2px; }
.photo-upload-info p  { font-size: 11px; color: var(--text-muted); margin: 0 0 8px; }
.btn-hapus-foto { background: rgba(239,68,68,0.08); color: #dc2626; border: 1.5px solid rgba(239,68,68,0.2); font-size: 12px; padding: 5px 11px; border-radius: 7px; cursor: pointer; font-family: inherit; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; }
.btn-hapus-foto:hover { background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.4); }
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-row-full { grid-column: 1 / -1; }
.input-icon-wrap { position: relative; }
.input-icon-wrap .fi { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 12px; pointer-events: none; }
.input-icon-wrap .form-control { padding-left: 36px; }
.pw-wrap { position: relative; }
.pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; font-size: 13px; transition: color 0.2s; }
.pw-toggle:hover { color: var(--primary); }
.pw-strength { height: 3px; border-radius: 3px; margin-top: 6px; background: var(--border); overflow: hidden; display: none; }
.pw-strength-bar { height: 100%; border-radius: 3px; width: 0%; transition: width 0.4s, background 0.4s; }
.form-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 6px; }

/* Upload progress */
.upload-progress { display:none; margin-top:10px; }
.upload-progress .up-bar-wrap { background:#f3f4f6; border-radius:99px; height:6px; overflow:hidden; margin-top:6px; }
.upload-progress .up-bar { height:100%; border-radius:99px; background:linear-gradient(90deg,#00A060,#20c997); width:0%; transition:width .3s; }

/* MODAL CROP */
#photoModal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 9999; align-items: center; justify-content: center; padding: 16px; backdrop-filter: blur(4px); box-sizing: border-box; }
.modal-box { background: white; border-radius: 20px; padding: 24px; width: 100%; max-width: 440px; box-shadow: 0 24px 80px rgba(0,0,0,0.4); animation: scaleIn 0.3s ease both; box-sizing: border-box; }
@keyframes scaleIn { from { transform: scale(0.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.modal-head { display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
.modal-head-icon { width: 38px; height: 38px; border-radius: 50%; background: rgba(0,160,96,0.1); display: flex; align-items: center; justify-content: center; color: #00A060; font-size: 15px; flex-shrink: 0; }
.modal-head h3 { font-size: 15px; font-weight: 700; margin: 0; }
.modal-sub { font-size: 11.5px; color: #999; margin: 0 0 16px; }
.crop-area { width: 100%; height: 260px; overflow: hidden; border-radius: 12px; border: 2px solid #e5e7eb; background: #f3f4f6; margin-bottom: 16px; }
.crop-area img { display: block; max-width: 100%; }
.crop-prev-wrap { background: #f8fafc; border-radius: 12px; padding: 12px; margin-bottom: 18px; text-align: center; }
.crop-prev-label { font-size: 9.5px; color: #aaa; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; margin: 0 0 10px; }
.crop-prev-row { display: flex; justify-content: center; align-items: flex-end; gap: 20px; }
.crop-prev-item { text-align: center; }
.crop-preview-box { border-radius: 50%; overflow: hidden; border: 3px solid #00A060; margin: 0 auto; box-shadow: 0 3px 10px rgba(0,160,96,0.2); }
.crop-prev-item span { display: block; font-size: 9.5px; color: #888; margin-top: 4px; font-weight: 600; }
.modal-btns { display: flex; gap: 10px; }
.modal-btn-cancel  { flex: 1; padding: 11px; border: 1.5px solid #e0e0e0; border-radius: 10px; background: white; cursor: pointer; font-size: 13px; font-weight: 600; color: #666; transition: background 0.2s; font-family: inherit; }
.modal-btn-cancel:hover { background: #f5f5f5; }
.modal-btn-confirm { flex: 1; padding: 11px; border: none; border-radius: 10px; background: linear-gradient(135deg, #00A060, #007a48); color: white; cursor: pointer; font-size: 13px; font-weight: 700; box-shadow: 0 4px 14px rgba(0,160,96,0.35); transition: transform 0.2s, box-shadow 0.2s; font-family: inherit; }
.modal-btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,160,96,0.45); }
.modal-btn-confirm:disabled { opacity:.6; cursor:not-allowed; transform:none; }

#metaMobile { display: none !important; }

@media (max-width: 900px) {
    .profile-grid { grid-template-columns: 1fr; gap: 16px; }
    .profile-card { position: static; }
    .profile-card-inner { display: flex; align-items: stretch; }
    .profile-left-col  { width: 200px; flex-shrink: 0; border-right: 1px solid var(--border); }
    .profile-right-col { flex: 1; min-width: 0; padding: 16px; }
    .profile-right-col .profile-meta { border-top: none; padding: 0; }
}
@media (max-width: 640px) {
    .profile-grid { gap: 14px; }
    .profile-card-inner { display: block !important; }
    .profile-left-col  { width: 100% !important; border-right: none !important; }
    .profile-right-col { display: none !important; }
    #metaMobile { display: block !important; }
    .profile-banner { height: 78px; }
    #avatarPreview { width: 76px; height: 76px; font-size: 24px; }
    .profile-avatar-wrap { margin-top: -40px; }
    .profile-info { padding: 10px 14px 14px; }
    .profile-info h2 { font-size: 14px; }
    .profile-info .p-email { font-size: 11px; }
    #metaMobile { padding: 12px 14px !important; }
    #metaMobile .profile-meta-item { margin-bottom: 10px; }
    .form-row-2 { grid-template-columns: 1fr; gap: 12px; }
    .form-card-body { padding: 14px; }
    .form-card-header { padding: 13px 14px; gap: 10px; }
    .form-card-header-text h3 { font-size: 13px; }
    .form-card-header-text p  { display: none; }
    .photo-upload-area { padding: 12px; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
    #photoThumb { width: 50px; height: 50px; font-size: 17px; }
    .photo-upload-info h4 { font-size: 12px; }
    .photo-upload-info p  { font-size: 10.5px; margin-bottom: 6px; }
    .foto-action-btns { flex-wrap: wrap; }
    .form-actions { flex-direction: column; }
    .form-actions .btn { width: 100%; justify-content: center; }
    .modal-box { padding: 16px; }
    .crop-area { height: 200px; }
}
</style>
@endpush

@section('content')

{{--
    CATATAN DEVELOPER:
    $jabatanTampil → jabatan yang ditampilkan di UI.
    Untuk role 'admin', jabatan selalu ditampilkan sebagai 'Staff SDM'
    tanpa mengubah data di database.

    CATATAN PERBAIKAN FOTO PROFIL:
    Semua URL foto profil di halaman ini sekarang memakai route('storage.local', $path)
    — sama seperti yang dipakai di layouts/app.blade.php (sidebar & topbar) yang
    sudah terbukti berhasil menampilkan gambar. Sebelumnya halaman ini memakai
    asset($user->photo) dan Storage::url($user->photo) yang menghasilkan URL salah
    sehingga gambar tidak muncul (hanya tampil teks alt "foto").
--}}
@php
    $jabatanTampil = $user->isAdmin() ? 'Staff SDM' : $user->jabatan;
@endphp

{{-- ══ MODAL CROP ══ --}}
<div id="photoModal">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-head-icon"><i class="fas fa-crop-alt"></i></div>
            <h3>Sesuaikan Foto Profil</h3>
        </div>
        <p class="modal-sub">Geser & zoom untuk mengatur posisi, lalu klik <strong>Gunakan Foto Ini</strong></p>
        <div class="crop-area"><img id="cropImage" src="" alt="crop"></div>
        <div class="crop-prev-wrap">
            <p class="crop-prev-label">Preview Hasil</p>
            <div class="crop-prev-row">
                <div class="crop-prev-item"><div class="crop-preview-box" style="width:72px;height:72px;"></div><span>Besar</span></div>
                <div class="crop-prev-item"><div class="crop-preview-box" style="width:48px;height:48px;border-width:2px;"></div><span>Sedang</span></div>
                <div class="crop-prev-item"><div class="crop-preview-box" style="width:34px;height:34px;border-width:2px;"></div><span>Kecil</span></div>
            </div>
        </div>
        <div class="modal-btns">
            <button class="modal-btn-cancel"  onclick="cancelPhoto()"><i class="fas fa-times"></i> Batal</button>
            <button class="modal-btn-confirm" id="btnConfirmPhoto" onclick="confirmPhoto()"><i class="fas fa-check"></i> Gunakan Foto Ini</button>
        </div>
    </div>
</div>

{{-- ══ LAYOUT UTAMA ══ --}}
<div class="profile-page-wrap">
<div class="profile-grid">

    {{-- ══ Kartu Profil Kiri ══ --}}
    <div class="profile-card">
        <div class="profile-card-inner">
            <div class="profile-left-col">
                <div class="profile-banner"></div>
                <div class="profile-avatar-wrap">
                    <div id="avatarPreview">
                        @if($user->photo)
                            <img id="avatarImg" src="{{ asset($user->photo) }}?v={{ time() }}" alt="foto">
                        @else
                            <span id="avatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
                <div class="profile-info">
                    <h2>{{ $user->name }}</h2>
                    <p class="p-email">{{ $user->email }}</p>

                    {{-- Jabatan: admin selalu tampil "Staff SDM" --}}
                    <div class="profile-jabatan">
                        <i class="fas fa-briefcase" style="font-size:9px;"></i>
                        {{ $jabatanTampil }}
                    </div>

                    @if($user->divisi && !$user->isAdmin())
                        <p class="profile-divisi">{{ $user->isManajer() ? 'Pimpinan' : $user->divisi }}</p>
                    @endif

                    <div style="margin-top:8px;">
                        <span class="badge {{ $user->isAdmin() ? 'badge-warning' : ($user->isManajer() ? 'badge-info' : 'badge-success') }}">
                            <i class="fas {{ $user->isAdmin() ? 'fa-shield-alt' : ($user->isManajer() ? 'fa-user-tie' : 'fa-user') }}" style="font-size:9px;margin-right:3px;"></i>
                            {{ $user->isAdmin() ? 'Administrator' : ($user->isManajer() ? 'Manajer' : 'Pegawai') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="profile-right-col">
                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <div class="meta-icon"><i class="fas fa-id-card"></i></div>
                        <div class="meta-content"><label>SAP</label><span>{{ $user->nip ?? 'Belum diisi' }}</span></div>
                    </div>
                    <div class="profile-meta-item">
                        <div class="meta-icon"><i class="fas fa-briefcase"></i></div>
                        {{-- Jabatan di meta kanan juga pakai $jabatanTampil --}}
                        <div class="meta-content"><label>Jabatan</label><span>{{ $jabatanTampil }}</span></div>
                    </div>
                    <div class="profile-meta-item">
                        <div class="meta-icon"><i class="fas fa-phone"></i></div>
                        <div class="meta-content"><label>No. Telepon</label><span>{{ $user->phone ?? 'Belum diisi' }}</span></div>
                    </div>
                    <div class="profile-meta-item">
                        <div class="meta-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="meta-content"><label>Alamat</label><span>{{ $user->alamat ?? 'Belum diisi' }}</span></div>
                    </div>
                    <div class="profile-meta-item">
                        <div class="meta-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="meta-content"><label>Bergabung</label><span>{{ $user->created_at->locale('id')->isoFormat('D MMMM Y') }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Meta versi mobile --}}
        <div id="metaMobile" class="profile-meta">
            <div class="profile-meta-item">
                <div class="meta-icon"><i class="fas fa-id-card"></i></div>
                <div class="meta-content"><label>SAP</label><span>{{ $user->nip ?? 'Belum diisi' }}</span></div>
            </div>
            <div class="profile-meta-item">
                <div class="meta-icon"><i class="fas fa-briefcase"></i></div>
                {{-- Jabatan di mobile meta juga pakai $jabatanTampil --}}
                <div class="meta-content"><label>Jabatan</label><span>{{ $jabatanTampil }}</span></div>
            </div>
            <div class="profile-meta-item">
                <div class="meta-icon"><i class="fas fa-phone"></i></div>
                <div class="meta-content"><label>No. Telepon</label><span>{{ $user->phone ?? 'Belum diisi' }}</span></div>
            </div>
            <div class="profile-meta-item">
                <div class="meta-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="meta-content"><label>Alamat</label><span>{{ $user->alamat ?? 'Belum diisi' }}</span></div>
            </div>
            <div class="profile-meta-item" style="margin-bottom:0;">
                <div class="meta-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="meta-content"><label>Bergabung</label><span>{{ $user->created_at->locale('id')->isoFormat('D MMMM Y') }}</span></div>
            </div>
        </div>
    </div>

    {{-- ══ Form Kanan ══ --}}
    <div class="profile-forms">

        {{-- Form Edit Profil --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon green"><i class="fas fa-user-edit"></i></div>
                <div class="form-card-header-text">
                    <h3>Edit Informasi Profil</h3>
                    <p>Perbarui data pribadi dan foto profil</p>
                </div>
            </div>
            <div class="form-card-body">

                {{-- Alert AJAX foto --}}
                <div id="photoAlert" style="display:none;border-radius:12px;padding:10px 14px;margin-bottom:14px;font-size:13px;font-weight:600;"></div>

                {{-- Error validasi form profil --}}
                @if($errors->hasAny(['name','phone','alamat']))
                    <div class="alert alert-danger" style="border-radius:12px;margin-bottom:16px;">
                        <i class="fas fa-exclamation-circle"></i>
                        @error('name')<div>{{ $message }}</div>@enderror
                        @error('phone')<div>{{ $message }}</div>@enderror
                        @error('alamat')<div>{{ $message }}</div>@enderror
                    </div>
                @endif

                <form id="profileForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- Area Foto --}}
                    <div class="photo-upload-area">
                        <div id="photoThumb">
                            @if($user->photo)
                                <img src="{{ asset($user->photo) }}?v={{ time() }}" alt="foto">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="photo-upload-info">
                            <h4>Foto Profil</h4>
                            <p>JPG, PNG. Maks. 3MB</p>
                            <div class="foto-action-btns" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <label class="btn btn-outline btn-sm" style="cursor:pointer;margin:0;">
                                    <i class="fas fa-camera"></i> Ganti Foto
                                    <input id="photoInput" type="file" accept="image/jpeg,image/png,image/jpg" style="display:none;" onchange="openCropper(this)">
                                </label>
                                <span id="hapusFotoWrap">
                                @if($user->photo)
                                    <button type="button" class="btn-hapus-foto" onclick="konfirmasiHapusFoto()">
                                        <i class="fas fa-trash-alt"></i> Hapus Foto
                                    </button>
                                @endif
                                </span>
                            </div>
                            <div class="upload-progress" id="uploadProgress">
                                <span style="font-size:11px;color:#6b7280;">Mengupload foto...</span>
                                <div class="up-bar-wrap"><div class="up-bar" id="uploadBar"></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap *</label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-user fi"></i>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. Telepon</label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-phone fi"></i>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $user->phone) }}" placeholder="08xx-xxxx-xxxx">
                            </div>
                        </div>
                        <div class="form-group form-row-full">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"
                                placeholder="Alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                        <button type="reset"  class="btn btn-outline btn-sm" style="height:38px;"><i class="fas fa-undo"></i> Reset</button>
                    </div>
                </form>

                {{-- Form hapus foto (terpisah) --}}
                <form id="formHapusFoto" method="POST" action="{{ route('profile.photo.delete') }}" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>

        {{-- Form Ganti Password --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon yellow"><i class="fas fa-lock"></i></div>
                <div class="form-card-header-text">
                    <h3>Ganti Password</h3>
                    <p>Gunakan password yang kuat dan unik</p>
                </div>
            </div>
            <div class="form-card-body">

                @if($errors->hasAny(['current_password', 'password']))
                    <div class="alert alert-danger" style="border-radius:12px;margin-bottom:16px;">
                        <i class="fas fa-exclamation-circle"></i>
                        @error('current_password')<div>{{ $message }}</div>@enderror
                        @error('password')<div>{{ $message }}</div>@enderror
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-row-2">
                        <div class="form-group form-row-full">
                            <label class="form-label">Password Saat Ini *</label>
                            <div class="pw-wrap">
                                <input type="password" name="current_password" id="curPw"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="Password lama" required style="padding-right:42px;">
                                <button type="button" class="pw-toggle" onclick="togglePw('curPw','eyeCur')">
                                    <i class="fas fa-eye" id="eyeCur"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password Baru *</label>
                            <div class="pw-wrap">
                                <input type="password" name="password" id="newPw"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Min. 8 karakter" required style="padding-right:42px;"
                                    oninput="checkStrength(this.value)">
                                <button type="button" class="pw-toggle" onclick="togglePw('newPw','eyeNew')">
                                    <i class="fas fa-eye" id="eyeNew"></i>
                                </button>
                            </div>
                            <div class="pw-strength" id="pwStrength"><div class="pw-strength-bar" id="pwStrengthBar"></div></div>
                            <p id="pwStrengthText" style="font-size:11px;margin:3px 0 0;display:none;"></p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password *</label>
                            <div class="pw-wrap">
                                <input type="password" name="password_confirmation" id="confPw"
                                    class="form-control"
                                    placeholder="Ulangi password baru" required style="padding-right:42px;">
                                <button type="button" class="pw-toggle" onclick="togglePw('confPw','eyeConf')">
                                    <i class="fas fa-eye" id="eyeConf"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-key"></i> Ubah Password</button>
                    </div>
                </form>

            </div>
        </div>

    </div>{{-- /profile-forms --}}
</div>{{-- /profile-grid --}}
</div>{{-- /profile-page-wrap --}}

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
let cropper     = null;
let croppedBlob = null;

function showPhotoAlert(msg, type) {
    const el = document.getElementById('photoAlert');
    el.style.display    = 'block';
    el.style.background = type === 'success' ? '#f0fdf4' : '#fff5f5';
    el.style.color      = type === 'success' ? '#15803d' : '#dc2626';
    el.style.border     = type === 'success' ? '1.5px solid #86efac' : '1.5px solid #fca5a5';
    el.innerHTML        = (type === 'success' ? '✅ ' : '❌ ') + msg;
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function uploadFotoAjax(blob) {
    const formData = new FormData();
    formData.append('photo', blob, 'foto_profil.jpg');
    formData.append('_token', '{{ csrf_token() }}');

    const progress = document.getElementById('uploadProgress');
    const bar      = document.getElementById('uploadBar');
    progress.style.display = 'block';

    let pct = 0;
    const timer = setInterval(() => { pct = Math.min(pct + 15, 85); bar.style.width = pct + '%'; }, 100);

    fetch('{{ route("profile.photo.upload") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        clearInterval(timer);
        bar.style.width = '100%';
        setTimeout(() => { progress.style.display = 'none'; bar.style.width = '0%'; }, 600);

        if (data.success) {
            showPhotoAlert('Foto profil berhasil disimpan!', 'success');
            // PENTING: pakai URL dari server (route storage.local) yang dikembalikan backend,
            // JANGAN pakai data.url mentah kalau backend mengirim path storage biasa.
            // Pastikan controller mengirim data.url = route('storage.local', $path)
            const newUrl = data.url + (data.url.includes('?') ? '&' : '?') + 'v=' + Date.now();
            function buatImg(src) {
                const img = document.createElement('img');
                img.src = src;
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
                return img;
            }
            document.getElementById('avatarPreview').innerHTML = '';
            document.getElementById('avatarPreview').appendChild(buatImg(newUrl));
            document.getElementById('photoThumb').innerHTML = '';
            document.getElementById('photoThumb').appendChild(buatImg(newUrl));

            const hapusWrap = document.getElementById('hapusFotoWrap');
            if (!hapusWrap.querySelector('.btn-hapus-foto')) {
                hapusWrap.innerHTML = '<button type="button" class="btn-hapus-foto" onclick="konfirmasiHapusFoto()"><i class="fas fa-trash-alt"></i> Hapus Foto</button>';
            }
        } else {
            showPhotoAlert(data.message || 'Gagal menyimpan foto.', 'error');
        }
    })
    .catch(() => {
        clearInterval(timer);
        progress.style.display = 'none';
        showPhotoAlert('Terjadi kesalahan saat upload foto.', 'error');
    });
}

function openCropper(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (!['image/jpeg','image/png','image/jpg'].includes(file.type)) {
        alert('Format tidak didukung! Gunakan JPG atau PNG.'); input.value = ''; return;
    }
    if (file.size > 3 * 1024 * 1024) {
        alert('Ukuran file terlalu besar! Maksimal 3MB.'); input.value = ''; return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
        const modal   = document.getElementById('photoModal');
        const cropImg = document.getElementById('cropImage');
        if (cropper) { cropper.destroy(); cropper = null; }
        cropImg.onload = null; cropImg.src = '';
        modal.style.display = 'flex';
        cropImg.onload = function() {
            cropper = new Cropper(cropImg, {
                aspectRatio: 1, viewMode: 1, dragMode: 'move',
                autoCropArea: 0.85, restore: false, guides: true,
                center: true, highlight: false,
                cropBoxMovable: false, cropBoxResizable: false,
                toggleDragModeOnDblclick: false,
                preview: document.querySelectorAll('.crop-preview-box'),
            });
        };
        setTimeout(() => { cropImg.src = e.target.result; }, 50);
    };
    reader.readAsDataURL(file);
}

function confirmPhoto() {
    if (!cropper) { alert('Cropper belum siap.'); return; }
    const btn = document.getElementById('btnConfirmPhoto');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

    const canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
    if (!canvas) { alert('Gagal memproses gambar.'); btn.disabled = false; return; }

    canvas.toBlob(function(blob) {
        if (!blob) { alert('Gagal membuat file.'); btn.disabled = false; return; }
        croppedBlob = blob;

        const dataUrl = canvas.toDataURL('image/jpeg', 0.82);
        function buatImg(src) {
            const img = document.createElement('img');
            img.src = src; img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
            return img;
        }
        document.getElementById('avatarPreview').innerHTML = '';
        document.getElementById('avatarPreview').appendChild(buatImg(dataUrl));
        document.getElementById('photoThumb').innerHTML = '';
        document.getElementById('photoThumb').appendChild(buatImg(dataUrl));

        closeModal();
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Gunakan Foto Ini';
        uploadFotoAjax(croppedBlob);
    }, 'image/jpeg', 0.82);
}

function cancelPhoto() {
    document.getElementById('photoInput').value = '';
    closeModal();
}

function closeModal() {
    const modal   = document.getElementById('photoModal');
    const cropImg = document.getElementById('cropImage');
    modal.style.display = 'none';
    cropImg.onload = null; cropImg.src = '';
    if (cropper) { cropper.destroy(); cropper = null; }
}

document.getElementById('photoModal').addEventListener('click', function(e) {
    if (e.target === this) cancelPhoto();
});

function konfirmasiHapusFoto() {
    if (confirm('Yakin ingin menghapus foto profil?\nFoto akan diganti dengan inisial nama Anda.')) {
        document.getElementById('formHapusFoto').submit();
    }
}

function togglePw(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    inp.type       = inp.type === 'password' ? 'text' : 'password';
    icon.className = inp.type === 'text' ? 'fas fa-eye-slash' : 'fas fa-eye';
}

function checkStrength(val) {
    const bar  = document.getElementById('pwStrengthBar');
    const wrap = document.getElementById('pwStrength');
    const text = document.getElementById('pwStrengthText');
    if (!val) { wrap.style.display = 'none'; text.style.display = 'none'; return; }
    wrap.style.display = 'block'; text.style.display = 'block';
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const lv = [
        { w:'25%',  c:'#ef4444', t:'⚠️ Lemah'       },
        { w:'50%',  c:'#f59e0b', t:'🟡 Cukup'        },
        { w:'75%',  c:'#3b82f6', t:'🔵 Kuat'         },
        { w:'100%', c:'#22c55e', t:'✅ Sangat Kuat'   },
    ][score - 1] || { w:'25%', c:'#ef4444', t:'⚠️ Lemah' };
    bar.style.width      = lv.w;
    bar.style.background = lv.c;
    text.textContent     = lv.t;
    text.style.color     = lv.c;
}

{{-- Auto-scroll ke card password jika ada error --}}
@if($errors->hasAny(['current_password', 'password']))
document.addEventListener('DOMContentLoaded', function() {
    const pwCard = document.querySelector('.form-card:last-child');
    if (pwCard) setTimeout(() => pwCard.scrollIntoView({ behavior: 'smooth', block: 'start' }), 300);
});
@endif
</script>
@endpush
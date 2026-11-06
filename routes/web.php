<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PengajuanIzinController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LaporanController;

// ═══════════════════════════════════════════════════════════════
// AUTH
// ═══════════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ═══════════════════════════════════════════════════════════════
// SEMUA ROLE (harus login)
// ═══════════════════════════════════════════════════════════════
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Profil ──────────────────────────────────────────────────
    Route::get('/profile',               [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile',               [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',      [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/photo',      [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    Route::post('/profile/photo/upload', [ProfileController::class, 'uploadPhoto'])->name('profile.photo.upload');

    // ── Absensi ─────────────────────────────────────────────────
    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/',           [AbsensiController::class, 'index'])->name('index');
        Route::post('/check-in',  [AbsensiController::class, 'checkIn'])->name('checkin');
        Route::post('/check-out', [AbsensiController::class, 'checkOut'])->name('checkout');
        Route::get('/riwayat',    [AbsensiController::class, 'riwayat'])->name('riwayat');
    });

    // ── Pengajuan Izin ──────────────────────────────────────────
    Route::prefix('izin')->name('izin.')->group(function () {
        Route::get('/cuti',    [PengajuanIzinController::class, 'formCuti'])->name('cuti');
        Route::get('/izin',    [PengajuanIzinController::class, 'formIzin'])->name('izin');
        Route::get('/sakit',   [PengajuanIzinController::class, 'formSakit'])->name('sakit');
        Route::post('/submit', [PengajuanIzinController::class, 'submit'])->name('submit');
        Route::get('/riwayat', [PengajuanIzinController::class, 'riwayat'])->name('riwayat');

        // Serve file lampiran (dipanggil oleh accessor lampiran_url di
        // Model PengajuanIzin)
        Route::get('/lampiran/{filename}', [PengajuanIzinController::class, 'lampiran'])->name('lampiran.show');

        // Hapus massal riwayat milik pegawai sendiri yang sudah diverifikasi
        // Ditaruh SEBELUM Route::delete('/{id}', ...) di bawah, supaya
        // tidak tertangkap duluan oleh wildcard {id}.
        Route::delete('/hapus-terverifikasi', [PengajuanIzinController::class, 'hapusTerverifikasi'])->name('hapusTerverifikasi');

        Route::get('/{id}',    [PengajuanIzinController::class, 'detail'])->name('detail');
        Route::delete('/{id}', [PengajuanIzinController::class, 'delete'])->name('delete');
    });
});

// ═══════════════════════════════════════════════════════════════
// MANAJER
// ═══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:manajer'])->prefix('manajer')->name('manajer.')->group(function () {
    Route::get('/izin',               [PengajuanIzinController::class, 'manajerIndex'])->name('izin.index');
    Route::get('/izin/{id}',          [PengajuanIzinController::class, 'manajerDetail'])->name('izin.detail');
    Route::post('/izin/{id}/setujui', [PengajuanIzinController::class, 'setujui'])->name('izin.setujui');
    Route::post('/izin/{id}/tolak',   [PengajuanIzinController::class, 'tolak'])->name('izin.tolak');
});

// ═══════════════════════════════════════════════════════════════
// ADMIN
// ═══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── Pegawai ─────────────────────────────────────────────────
    Route::prefix('pegawai')->name('pegawai.')->group(function () {
        Route::get('/',                  [PegawaiController::class, 'index'])->name('index');
        Route::get('/create',            [PegawaiController::class, 'create'])->name('create');
        Route::post('/',                 [PegawaiController::class, 'store'])->name('store');
        Route::get('/export/excel',      [PegawaiController::class, 'export'])->name('export');
        Route::get('/template/download', [PegawaiController::class, 'downloadTemplate'])->name('template');
        Route::post('/import',           [PegawaiController::class, 'import'])->name('import');
        Route::delete('/hapus-semua',    [PegawaiController::class, 'hapusSemua'])->name('hapusSemua');
        Route::get('/{pegawai}',         [PegawaiController::class, 'show'])->name('show');
        Route::get('/{pegawai}/edit',    [PegawaiController::class, 'edit'])->name('edit');
        Route::put('/{pegawai}',         [PegawaiController::class, 'update'])->name('update');
        Route::delete('/{pegawai}',      [PegawaiController::class, 'destroy'])->name('destroy');
    });

    // ── Laporan ─────────────────────────────────────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',              [LaporanController::class, 'index'])->name('index');
        Route::get('/realtime',      [LaporanController::class, 'realtime'])->name('realtime');
        Route::get('/detail/{id}',   [LaporanController::class, 'detail'])->name('detail');

        // Laporan Absensi rekap harian/mingguan/bulanan
        Route::get('/absensi',        [LaporanController::class, 'absensi'])->name('absensi');

        // Ekspor Excel Laporan Absensi Harian
        // Nama akhir otomatis menjadi: admin.laporan.absensi.export
        // URL akhir: /admin/laporan/absensi/export
        Route::get('/absensi/export', [LaporanController::class, 'exportAbsensi'])->name('absensi.export');
    });

    // ── Izin (admin: lihat + hapus data yang sudah tuntas) ──────
    Route::prefix('izin')->name('izin.')->group(function () {
        Route::get('/',     [PengajuanIzinController::class, 'adminIndex'])->name('index');
        Route::get('/{id}', [PengajuanIzinController::class, 'adminDetail'])->name('detail');

        // ✅ ROUTE BARU — Hapus massal pengajuan yang sudah tuntas
        // (status: disetujui / ditolak). Dipanggil dari checkbox +
        // tombol "Hapus Terpilih" di halaman admin.izin.index.
        Route::delete('/hapus-terverifikasi', [PengajuanIzinController::class, 'adminHapusTerverifikasi'])
            ->name('hapusTerverifikasi');
    });

    // ── Export ──────────────────────────────────────────────────
    Route::get('/export/absensi', [ExportController::class, 'absensi'])->name('export.absensi');
});
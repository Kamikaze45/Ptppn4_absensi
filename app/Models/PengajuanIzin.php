<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'user_id',
        'tipe',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'file_lampiran',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ alias pegawai → user
    // supaya LaporanController tidak perlu diubah banyak
    public function pegawai()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =========================================================================
    // ✅ Accessor URL Lampiran (anti gagal, mendukung file lama & baru)
    // =========================================================================
    //
    // Lampiran diserve lewat route khusus (PengajuanIzinController@lampiran)
    // yang membaca file langsung via PHP dari disk manapun (baru/lama),
    // bukan lewat static file serving. Ini menghindari masalah PHP built-in
    // server yang menolak melayani file lewat symlink (public/storage),
    // sehingga lampiran tetap bisa dibuka baik di admin maupun manajer,
    // baik untuk file baru maupun file lama.
    //
    // Cara pakai di blade:  {{ $pengajuan->lampiran_url }}
    public function getLampiranUrlAttribute()
    {
        if (!$this->file_lampiran) {
            return null;
        }

        $filename = basename($this->file_lampiran);

        // Cek file benar-benar ada di salah satu disk sebelum bikin URL
        $adaDiBaru = \Illuminate\Support\Facades\Storage::disk('public_uploads')
            ->exists('lampiran-izin/'.$filename);
        $adaDiLama = \Illuminate\Support\Facades\Storage::disk('public')
            ->exists('lampiran-izin/'.$filename);

        if (!$adaDiBaru && !$adaDiLama) {
            return null;
        }

        return route('izin.lampiran.show', $filename);
    }
}
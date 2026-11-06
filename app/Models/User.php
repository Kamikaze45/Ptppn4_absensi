<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'nip', 'email', 'password', 'role', 'jabatan',
        'divisi', 'phone', 'photo', 'alamat', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ── Relasi ──────────────────────────────────────────────────────

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function absensiHariIni()
    {
        return $this->hasOne(Absensi::class)->whereDate('tanggal', today());
    }

    // ── Role helpers ────────────────────────────────────────────────  

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManajer(): bool
    {
        return $this->role === 'manajer';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
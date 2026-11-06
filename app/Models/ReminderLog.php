<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'phone',
        'status',
        'message_id',
        'error',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scope Helper ─────────────────────────────────────────────────────────

    /**
     * Ambil log reminder hari ini.
     * Contoh: ReminderLog::hariIni()->get()
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('sent_at', today());
    }

    /**
     * Filter hanya yang berhasil.
     */
    public function scopeBerhasil($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Filter hanya yang gagal.
     */
    public function scopeGagal($query)
    {
        return $query->where('status', 'failed');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'user_id',
        'tanggal',
        'tipe_absensi',
        'check_in',
        'check_in_lat',
        'check_in_lng',
        'check_in_alamat',
        'check_out',
        'check_out_lat',
        'check_out_lng',
        'check_out_alamat',
        'status',
        'status_masuk',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'check_in_lat'  => 'float',
        'check_in_lng'  => 'float',
        'check_out_lat' => 'float',
        'check_out_lng' => 'float',
    ];

    // ── Relasi ───────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Alias pegawai → user (untuk kompatibilitas kode lama)
    public function pegawai()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Accessors ────────────────────────────────────────────────────

    /**
     * Label tipe absensi dengan emoji
     */
    public function getTipeAbsensiLabelAttribute(): string
    {
        return match($this->tipe_absensi) {
            'WFO'        => '🏢 WFO',
            'WFH'        => '🏠 WFH',
            'Dinas Luar' => '🚗 Dinas Luar',
            default      => $this->tipe_absensi ?? '-',
        };
    }

    /**
     * Jam check-in format H:i
     */
    public function getJamMasukAttribute(): ?string
    {
        return $this->check_in
            ? Carbon::parse($this->check_in)->format('H:i')
            : null;
    }

    /**
     * Jam check-out format H:i
     */
    public function getJamKeluarAttribute(): ?string
    {
        return $this->check_out
            ? Carbon::parse($this->check_out)->format('H:i')
            : null;
    }

    /**
     * Durasi kerja (selisih check_in - check_out), format "Xj Ym"
     * Usage: $absensi->durasi_kerja
     *
     * - Kalau check_in atau check_out belum ada → tampilkan "-"
     * - Kalau check_out lebih kecil dari check_in (shift lewat tengah
     *   malam) → tambah 1 hari supaya durasi tidak jadi minus.
     */
    public function getDurasiKerjaAttribute(): string
    {
        if (!$this->check_in || !$this->check_out) {
            return '-';
        }

        $masuk  = Carbon::parse($this->check_in);
        $keluar = Carbon::parse($this->check_out);

        if ($keluar->lt($masuk)) {
            $keluar->addDay();
        }

        $menitTotal = $masuk->diffInMinutes($keluar);
        $jam        = intdiv($menitTotal, 60);
        $menit      = $menitTotal % 60;

        return "{$jam}j {$menit}m";
    }

    /**
     * Badge warna status masuk (untuk Blade)
     * Usage: $absensi->status_masuk_badge
     */
    public function getStatusMasukBadgeAttribute(): string
    {
        return match($this->status_masuk) {
            'Hadir'     => 'bg-green-100 text-green-700',
            'Terlambat' => 'bg-red-100 text-red-600',
            default     => 'bg-gray-100 text-gray-500',
        };
    }

    /**
     * Badge warna tipe absensi (untuk Blade)
     * Usage: $absensi->tipe_badge
     */
    public function getTipeBadgeAttribute(): string
    {
        return match($this->tipe_absensi) {
            'WFO'        => 'bg-blue-100 text-blue-700',
            'WFH'        => 'bg-purple-100 text-purple-700',
            'Dinas Luar' => 'bg-orange-100 text-orange-700',
            default      => 'bg-gray-100 text-gray-500',
        };
    }

    /**
     * Badge warna status kehadiran
     * Usage: $absensi->status_badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'Hadir'  => 'bg-green-100 text-green-700',
            'Izin'   => 'bg-yellow-100 text-yellow-700',
            'Sakit'  => 'bg-blue-100 text-blue-700',
            'Cuti'   => 'bg-purple-100 text-purple-700',
            'Alpha'  => 'bg-red-100 text-red-700',
            default  => 'bg-gray-100 text-gray-500',
        };
    }

    // ── Scopes ───────────────────────────────────────────────────────

    /**
     * Filter berdasarkan bulan & tahun
     * Usage: Absensi::bulanIni()->get()
     *        Absensi::bulan(3, 2025)->get()
     */
    public function scopeBulan($query, int $bulan = null, int $tahun = null)
    {
        $bulan = $bulan ?? now()->month;
        $tahun = $tahun ?? now()->year;
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                     ->whereYear('tanggal', now()->year);
    }

    /**
     * Filter hanya hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    /**
     * Filter berdasarkan user
     */
    public function scopeUntukUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
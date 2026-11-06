<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model {
    protected $fillable = ['key', 'value', 'description'];

    // Helper: ambil value berdasarkan key
    // Contoh pakai: AttendanceSetting::get('check_in_time')
    public static function get(string $key, mixed $default = null): mixed {
        return static::where('key', $key)->value('value') ?? $default;
    }
}
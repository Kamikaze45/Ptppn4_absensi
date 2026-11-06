<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('attendance_settings')->insert([

            // ===== JAM MASUK PER HARI =====
            ['key' => 'check_in_monday',     'value' => '07:00', 'description' => 'Jam masuk Senin',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_in_tuesday',    'value' => '08:00', 'description' => 'Jam masuk Selasa',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_in_wednesday',  'value' => '08:00', 'description' => 'Jam masuk Rabu',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_in_thursday',   'value' => '08:00', 'description' => 'Jam masuk Kamis',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_in_friday',     'value' => '07:30', 'description' => 'Jam masuk Jumat',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_in_saturday',   'value' => '07:30', 'description' => 'Jam masuk Sabtu',    'created_at' => now(), 'updated_at' => now()],

            // ===== JAM PULANG PER HARI =====
            ['key' => 'check_out_monday',    'value' => '16:00', 'description' => 'Jam pulang Senin',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_out_tuesday',   'value' => '16:00', 'description' => 'Jam pulang Selasa',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_out_wednesday', 'value' => '16:00', 'description' => 'Jam pulang Rabu',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_out_thursday',  'value' => '16:00', 'description' => 'Jam pulang Kamis',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_out_friday',    'value' => '16:00', 'description' => 'Jam pulang Jumat',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'check_out_saturday',  'value' => '13:00', 'description' => 'Jam pulang Sabtu',   'created_at' => now(), 'updated_at' => now()],

            // ===== TOLERANSI & NOTIFIKASI =====
            ['key' => 'late_tolerance_minutes',  'value' => '60', 'description' => 'Batas absen setelah jam masuk (menit), lewat = terlambat', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'reminder_before_minutes', 'value' => '60', 'description' => 'Notif WA dikirim X menit sebelum jam masuk',               'created_at' => now(), 'updated_at' => now()],

            // ===== LOKASI KANTOR =====
            ['key' => 'office_lat',          'value' => '-5.073259',  'description' => 'Latitude kantor',                 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'office_lng',          'value' => '105.135010', 'description' => 'Longitude kantor',                'created_at' => now(), 'updated_at' => now()],
            ['key' => 'office_radius_meter', 'value' => '100',        'description' => 'Radius maksimal absensi (meter)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};
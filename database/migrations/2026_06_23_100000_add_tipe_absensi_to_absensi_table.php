<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            if (!Schema::hasColumn('absensi', 'tipe_absensi')) {
                $table->enum('tipe_absensi', ['WFO', 'WFH', 'Dinas Luar'])
                      ->nullable()
                      ->after('status');
            }
            if (!Schema::hasColumn('absensi', 'status_masuk')) {
                $table->string('status_masuk')->nullable()->after('tipe_absensi'); // Tepat Waktu / Terlambat
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['tipe_absensi', 'status_masuk']);
        });
    }
};
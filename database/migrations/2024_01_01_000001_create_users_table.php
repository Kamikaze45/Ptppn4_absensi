<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nip')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->enum('jabatan', [
                'Manajer',
                'Asisten Manajer',
                'Kepala Bagian',
                'Staff Administrasi',
                'Staff Keuangan',
                'Staff IT',
                'Staff Operasional',
                'Supervisor',
                'Mandor',
                'Karyawan Lapangan',
                'Security',
                'Operator'
            ])->default('Staff Administrasi');
            $table->string('divisi')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah nilai 'manajer' ke ENUM role di tabel users
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manajer', 'user') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        // Kembalikan ke 2 role semula
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel log pengiriman reminder WA.
 *
 * Fungsi:
 * - Mencegah double kirim di hari yang sama
 * - Monitoring status pengiriman (sent/failed)
 * - Menyimpan message_id dari FlowKirim untuk tracking
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // 'masuk' atau 'pulang'
            $table->enum('type', ['masuk', 'pulang']);

            // Nomor WA yang dituju (disimpan untuk audit)
            $table->string('phone', 20);

            // Status pengiriman
            $table->enum('status', ['sent', 'failed'])->default('failed');

            // ID pesan dari FlowKirim (untuk tracking di dashboard FlowKirim)
            $table->string('message_id')->nullable();

            // Pesan error jika gagal
            $table->text('error')->nullable();

            // Waktu pengiriman (bisa berbeda dari created_at karena delay queue)
            $table->timestamp('sent_at');

            $table->timestamps();

            // Index untuk query cepat saat cek double-kirim
            $table->index(['user_id', 'type', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
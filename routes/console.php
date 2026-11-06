<?php

/**
 * routes/console.php
 *
 * Laravel 11 — Scheduler dikonfigurasi di sini (bukan di Kernel.php).
 * Jika kamu pakai Laravel 10 ke bawah, lihat bagian bawah file ini.
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ── Bawaan Laravel ───────────────────────────────────────────────────────────
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Reminder WA Absensi ──────────────────────────────────────────────────────
//
// Menjalankan command tiap 5 menit, hanya di hari kerja (Senin–Jumat).
// Command itu sendiri yang memutuskan apakah sekarang waktunya kirim atau tidak.
//
// withoutOverlapping()  → jika command sebelumnya belum selesai, skip
// runInBackground()     → tidak memblokir scheduler jika command lama
// appendOutputTo()      → simpan output ke log file untuk debugging

Schedule::command('absensi:send-reminder')
    ->everyFiveMinutes()        // cek tiap 5 menit
    ->weekdays()                // Senin–Jumat saja
    ->withoutOverlapping(5)     // lock selama 5 menit agar tidak tumpang tindih
    ->runInBackground()         // jalan di background, tidak block scheduler
    ->appendOutputTo(storage_path('logs/reminder-scheduler.log'));

// ──────────────────────────────────────────────────────────────────────────────
// CATATAN UNTUK LARAVEL 10 DAN KE BAWAH:
// ──────────────────────────────────────────────────────────────────────────────
// Jika proyekmu pakai Laravel 10, tambahkan ke app/Console/Kernel.php:
//
//   protected function schedule(Schedule $schedule): void
//   {
//       $schedule->command('absensi:send-reminder')
//           ->everyFiveMinutes()
//           ->weekdays()
//           ->withoutOverlapping(5)
//           ->runInBackground()
//           ->appendOutputTo(storage_path('logs/reminder-scheduler.log'));
//   }
//
// ──────────────────────────────────────────────────────────────────────────────
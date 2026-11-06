<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Daftarkan alias middleware custom Anda
        $middleware->alias([
            'admin'   => \App\Http\Middleware\AdminMiddleware::class,
            'manajer' => \App\Http\Middleware\ManajerMiddleware::class,
            'role'    => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // 2. Tambahkan baris ini untuk mengecualikan rute pengujian Postman dari proteksi CSRF
        $middleware->validateCsrfTokens(except: [
            'login',
            'absensi/checkin',
            'api/login',          // Ditambahkan juga untuk berjaga-jaga jika rute API aktif
            'api/absensi/checkin'  
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── MANAJER ───────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['nip' => '001'],
            [
                'name'      => 'Manajer',
                'email'     => 'manajer@ptpn4.com',
                'password'  => Hash::make('manajer123'),
                'role'      => 'manajer',
                'jabatan'   => 'Manajer',
                'divisi'    => 'Operasional',
                'is_active' => true,
            ]
        );

        // ── ADMIN ─────────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['nip' => '002'],
            [
                'name'      => 'Administrator',
                'email'     => 'admin@ptpn4.com',
                'password'  => Hash::make('admin123'),
                'role'      => 'admin',
                'jabatan'   => 'Staff IT',
                'divisi'    => 'IT',
                'is_active' => true,
            ]
        );

        // ── USER / PEGAWAI ────────────────────────────────────────────────────
        User::updateOrCreate(
            ['nip' => '7000123'],
            [
                'name'      => 'Pegawai Contoh',
                'email'     => 'pegawai@ptpn4.com',
                'password'  => Hash::make('11111111'),
                'role'      => 'user',
                'jabatan'   => 'Staff Administrasi',
                'divisi'    => 'Administrasi',
                'is_active' => true,
            ]
        );
    }
}
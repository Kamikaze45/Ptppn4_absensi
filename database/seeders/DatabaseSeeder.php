<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin akun
        User::create([
            'name'      => 'Administrator PTPN4',
            'nip'       => 'ADM001',
            'email'     => 'admin@ptpn4.co.id',
            'password'  => Hash::make('admin123'),
            'role'      => 'admin',
            'jabatan'   => 'Manajer',
            'divisi'    => 'Administrasi Regional 7',
            'phone'     => '0811-0000-001',
            'is_active' => true,
        ]);

        // Contoh pegawai
        $pegawai = [
            ['Budi Santoso',    'PTN001', 'budi@ptpn4.co.id',    'Kepala Bagian',     'Divisi Produksi'],
            ['Siti Rahayu',     'PTN002', 'siti@ptpn4.co.id',    'Staff Administrasi','Divisi Administrasi'],
            ['Ahmad Fauzi',     'PTN003', 'ahmad@ptpn4.co.id',   'Staff Keuangan',    'Divisi Keuangan'],
            ['Dewi Lestari',    'PTN004', 'dewi@ptpn4.co.id',    'Supervisor',        'Divisi Operasional'],
            ['Hendra Wijaya',   'PTN005', 'hendra@ptpn4.co.id',  'Mandor',            'Unit Kebun'],
            ['Rina Sari',       'PTN006', 'rina@ptpn4.co.id',    'Staff IT',          'Divisi IT'],
            ['Joko Prabowo',    'PTN007', 'joko@ptpn4.co.id',    'Security',          'Keamanan'],
            ['Nurul Hidayah',   'PTN008', 'nurul@ptpn4.co.id',   'Karyawan Lapangan', 'Unit Kebun'],
        ];

        foreach ($pegawai as [$name, $nip, $email, $jabatan, $divisi]) {
            User::create([
                'name'      => $name,
                'nip'       => $nip,
                'email'     => $email,
                'password'  => Hash::make('password123'),
                'role'      => 'user',
                'jabatan'   => $jabatan,
                'divisi'    => $divisi,
                'phone'     => '0812-' . rand(1000,9999) . '-' . rand(1000,9999),
                'is_active' => true,
            ]);
        }
    }
}

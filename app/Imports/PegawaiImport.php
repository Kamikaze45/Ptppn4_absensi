<?php

namespace App\Imports;

use App\Models\User;
use App\Services\SimpleXlsxReader;
use Illuminate\Support\Facades\Hash;

class PegawaiImport
{
    private int   $imported = 0;
    private int   $skipped  = 0;
    private array $errors   = [];

    public function import(string $filePath): void
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                $rows = SimpleXlsxReader::toArray($filePath);
            } else {
                $rows = $this->readCsv($filePath);
            }
        } catch (\Throwable $e) {
            $this->errors[] = 'Gagal membuka file: ' . $e->getMessage();
            return;
        }

        if (empty($rows)) {
            $this->errors[] = 'File kosong atau tidak ada data yang bisa dibaca.';
            return;
        }

        // Cari baris header yang mengandung 'nama_lengkap'
        $headerRowIndex = null;
        foreach ($rows as $index => $row) {
            $rowLower = array_map(fn($v) => strtolower(trim((string) $v)), $row);
            if (in_array('nama_lengkap', $rowLower)) {
                $headerRowIndex = $index;
                break;
            }
        }

        if ($headerRowIndex === null) {
            $this->errors[] = 'Header tidak ditemukan. Pastikan ada kolom nama_lengkap.';
            return;
        }

        $headers = array_map(
            fn($h) => strtolower(trim((string) $h)),
            $rows[$headerRowIndex]
        );

        // Proses baris data (setelah header)
        for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
            $rawRow = $rows[$i];

            // Lengkapi kolom yang kurang
            while (count($rawRow) < count($headers)) {
                $rawRow[] = '';
            }

            $row  = array_combine($headers, array_slice($rawRow, 0, count($headers)));
            $nama = trim((string) ($row['nama_lengkap'] ?? ''));
            $nip  = trim((string) ($row['nip'] ?? ''));

            // Skip baris kosong
            if (empty($nama) || empty($nip)) {
                $this->skipped++;
                continue;
            }

            // Skip baris contoh/instruksi
            if (str_contains($nama, '↑') || str_contains(strtoupper($nama), 'CONTOH')) {
                continue;
            }

            $email   = trim((string) ($row['email']   ?? ''));
            $jabatan = trim((string) ($row['jabatan'] ?? ''));

            if (empty($email) || empty($jabatan)) {
                $this->errors[] = "NIP {$nip}: email atau jabatan kosong.";
                $this->skipped++;
                continue;
            }

            // Skip jika NIP atau email sudah ada
            if (User::where('nip', $nip)->orWhere('email', $email)->exists()) {
                $this->skipped++;
                continue;
            }

            try {
                User::create([
                    'name'      => $nama,
                    'nip'       => $nip,
                    'email'     => $email,
                    'password'  => Hash::make(!empty($row['password']) ? $row['password'] : 'password123'),
                    'jabatan'   => $jabatan,
                    'divisi'    => $row['divisi']     ?? null,
                    'phone'     => $row['no_telepon'] ?? null,
                    'alamat'    => $row['alamat']     ?? null,
                    'role'      => 'user',
                    'is_active' => true,
                ]);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = "NIP {$nip}: " . $e->getMessage();
                $this->skipped++;
            }
        }
    }

    /**
     * Baca file CSV native PHP (tidak butuh extension zip).
     */
    private function readCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Gagal membuka file CSV.');
        }

        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count((string) $firstLine, ';') > substr_count((string) $firstLine, ',')) ? ';' : ',';

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        if (!empty($rows) && isset($rows[0][0])) {
            $rows[0][0] = preg_replace('/^\x{FEFF}/u', '', $rows[0][0]);
        }

        return $rows;
    }

    public function errors(): array         { return $this->errors; }
    public function getImportedCount(): int { return $this->imported; }
    public function getSkippedCount(): int  { return $this->skipped; }
}
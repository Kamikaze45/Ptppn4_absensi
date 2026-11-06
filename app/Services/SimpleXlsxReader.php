<?php

namespace App\Services;

/**
 * Pembaca file .xlsx tanpa bergantung pada ekstensi PHP "zip" (ZipArchive).
 *
 * File .xlsx pada dasarnya adalah arsip ZIP berisi beberapa file XML.
 * Class ini mem-parsing struktur ZIP secara manual lalu men-decompress
 * isinya menggunakan gzinflate() — bagian dari ekstensi zlib yang hampir
 * selalu aktif secara default di PHP (berbeda dengan ekstensi "zip").
 *
 * Catatan keterbatasan:
 * - Hanya membaca sheet pertama (xl/worksheets/sheet1.xml).
 * - Tidak mendukung entry ZIP yang memakai "data descriptor" tanpa ukuran
 *   pasti di local header (kasus ini jarang terjadi pada file yang dibuat
 *   Excel/LibreOffice/openpyxl untuk kebutuhan import data sederhana).
 */
class SimpleXlsxReader
{
    /**
     * Mengembalikan seluruh baris pada sheet pertama sebagai array 2 dimensi.
     */
    public static function toArray(string $filePath): array
    {
        $entries = self::readZipEntries($filePath);

        if (!isset($entries['xl/worksheets/sheet1.xml'])) {
            throw new \RuntimeException(
                'File xlsx tidak valid atau struktur internalnya tidak dikenali.'
            );
        }

        $sharedStrings = [];
        if (isset($entries['xl/sharedStrings.xml'])) {
            $sharedStrings = self::parseSharedStrings($entries['xl/sharedStrings.xml']);
        }

        return self::parseSheet($entries['xl/worksheets/sheet1.xml'], $sharedStrings);
    }

    /**
     * Mem-parsing struktur ZIP secara manual (local file header per entry)
     * dan mengembalikan isi setiap entry yang sudah di-decompress.
     *
     * @return array<string, string> nama_file => isi_file
     */
    private static function readZipEntries(string $filePath): array
    {
        $data = file_get_contents($filePath);
        if ($data === false) {
            throw new \RuntimeException('Gagal membaca file dari disk.');
        }

        $entries = [];
        $offset  = 0;
        $length  = strlen($data);

        while ($offset + 4 <= $length) {
            $signature = substr($data, $offset, 4);

            // Local File Header
            if ($signature === "PK\x03\x04") {
                if ($offset + 30 > $length) {
                    break;
                }

                $unpacked = unpack(
                    'vversion/vflags/vmethod/vmtime/vmdate/Vcrc/Vcsize/Vusize/vnamelen/vextralen',
                    substr($data, $offset + 4, 26)
                );

                $nameStart  = $offset + 30;
                $name       = substr($data, $nameStart, $unpacked['namelen']);
                $extraStart = $nameStart + $unpacked['namelen'];
                $dataStart  = $extraStart + $unpacked['extralen'];
                $csize      = $unpacked['csize'];

                // Flag bit 3 = ukuran ditulis setelah data (data descriptor).
                // Fallback: cari signature data descriptor PK\x07\x08 berikutnya.
                if (($unpacked['flags'] & 0x0008) && $csize === 0) {
                    $descPos = strpos($data, "PK\x07\x08", $dataStart);
                    if ($descPos === false) {
                        break;
                    }
                    $csize = $descPos - $dataStart;
                }

                $rawContent = substr($data, $dataStart, $csize);

                if ($unpacked['method'] === 0) {
                    // Stored (tidak dikompresi)
                    $content = $rawContent;
                } elseif ($unpacked['method'] === 8) {
                    // Deflate
                    $content = @gzinflate($rawContent);
                    $content = ($content === false) ? '' : $content;
                } else {
                    // Metode kompresi lain tidak didukung, lewati
                    $content = '';
                }

                $entries[$name] = $content;
                $offset = $dataStart + $csize;
                continue;
            }

            // Sudah masuk ke Central Directory / End of Central Directory -> selesai
            if ($signature === "PK\x01\x02" || $signature === "PK\x05\x06") {
                break;
            }

            // Signature tidak dikenali, hentikan agar tidak infinite loop
            break;
        }

        return $entries;
    }

    /**
     * Parsing xl/sharedStrings.xml menjadi array string terurut sesuai index.
     */
    private static function parseSharedStrings(string $xml): array
    {
        $strings = [];

        $doc = @simplexml_load_string($xml);
        if ($doc === false) {
            return $strings;
        }

        foreach ($doc->si as $si) {
            if (isset($si->t)) {
                $strings[] = (string) $si->t;
            } else {
                $text = '';
                foreach ($si->r as $r) {
                    $text .= (string) $r->t;
                }
                $strings[] = $text;
            }
        }

        return $strings;
    }

    /**
     * Parsing xl/worksheets/sheet1.xml menjadi array baris (0-indexed),
     * tiap baris berupa array kolom (0-indexed, A=0, B=1, dst).
     */
    private static function parseSheet(string $xml, array $sharedStrings): array
    {
        $doc = @simplexml_load_string($xml);
        if ($doc === false) {
            throw new \RuntimeException('Gagal mem-parsing isi sheet excel (XML tidak valid).');
        }

        $rows = [];

        foreach ($doc->sheetData->row as $row) {
            $rowIndex = ((int) $row['r']) - 1;
            if ($rowIndex < 0) {
                continue;
            }

            $rowData = [];

            foreach ($row->c as $cell) {
                $ref    = (string) $cell['r'];
                $letters = preg_replace('/[0-9]/', '', $ref);
                $colIdx = self::columnLetterToIndex($letters);
                $type   = (string) $cell['t'];

                if ($type === 's') {
                    $idx   = isset($cell->v) ? (int) $cell->v : -1;
                    $value = $sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = isset($cell->is->t) ? (string) $cell->is->t : '';
                } else {
                    $value = isset($cell->v) ? (string) $cell->v : '';
                }

                $rowData[$colIdx] = $value;
            }

            if (!empty($rowData)) {
                $maxCol     = max(array_keys($rowData));
                $orderedRow = [];
                for ($c = 0; $c <= $maxCol; $c++) {
                    $orderedRow[] = $rowData[$c] ?? '';
                }
                $rows[$rowIndex] = $orderedRow;
            }
        }

        ksort($rows);
        return array_values($rows);
    }

    /**
     * Konversi huruf kolom Excel (A, B, ..., Z, AA, AB, ...) menjadi index 0-based.
     */
    private static function columnLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $index   = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return $index - 1;
    }
}
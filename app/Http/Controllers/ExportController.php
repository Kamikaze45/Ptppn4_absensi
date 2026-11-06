<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportController extends Controller
{
    // ══════════════════════════════════════════════════════════
    // EXPORT ABSENSI
    // ══════════════════════════════════════════════════════════
    public function absensi(Request $request)
    {
        $request->validate([
            'periode' => 'required|in:harian,mingguan,bulanan',
            'tanggal' => 'nullable',
        ]);

        $periode = $request->periode;
        $raw     = $request->tanggal ?? Carbon::today()->toDateString();

        if ($periode === 'bulanan' && strlen($raw) === 7) {
            $raw = $raw . '-01';
        }

        $ref   = Carbon::parse($raw);
        $query = Absensi::with('user');

        switch ($periode) {
            case 'harian':
                $query->whereDate('tanggal', $ref->toDateString());
                break;
            case 'mingguan':
                $query->whereBetween('tanggal', [
                    $ref->copy()->startOfWeek(Carbon::MONDAY)->toDateString(),
                    $ref->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
                ]);
                break;
            default:
                $query->whereYear('tanggal', $ref->year)
                      ->whereMonth('tanggal', $ref->month);
                break;
        }

        $data = $query->orderBy('tanggal')->orderBy('user_id')->get();

        $headers = [
            'No', 'Tanggal', 'Hari', 'Nama Pegawai', 'NIP',
            'Jabatan', 'Jam Masuk', 'Jam Keluar', 'Durasi Kerja', 'Status', 'Keterangan'
        ];

        $rows = [];
        foreach ($data as $i => $a) {
            $durasi = '-';
            if ($a->check_in && $a->check_out) {
                $menit  = Carbon::parse($a->check_in)->diffInMinutes(Carbon::parse($a->check_out));
                $durasi = intdiv($menit, 60) . 'j ' . ($menit % 60) . 'm';
            }

            $statusLabel = match($a->status) {
                'hadir'     => 'Hadir',
                'terlambat' => 'Terlambat',
                'alpha'     => 'Alpha',
                'izin'      => 'Izin',
                'sakit'     => 'Sakit',
                default     => ucfirst($a->status),
            };

            $rows[] = [
                $i + 1,
                Carbon::parse($a->tanggal)->format('d/m/Y'),
                Carbon::parse($a->tanggal)->locale('id')->isoFormat('dddd'),
                $a->user->name    ?? '-',
                $a->user->nip     ?? '-',
                $a->user->jabatan ?? '-',
                $a->check_in      ?? '-',
                $a->check_out     ?? '-',
                $durasi,
                $statusLabel,
                $a->keterangan    ?? '-',
            ];
        }

        $headerColor = match($periode) {
            'harian'   => '0369A1',
            'mingguan' => '7C3AED',
            default    => '157347',
        };

        $judulSheet = match($periode) {
            'harian'   => 'Absensi ' . $ref->format('d-m-Y'),
            'mingguan' => 'Absensi Minggu ' . $ref->weekOfYear . '-' . $ref->year,
            default    => 'Absensi ' . $ref->locale('id')->isoFormat('MMMM YYYY'),
        };

        $filename = match($periode) {
            'harian'   => 'absensi-harian-'  . $ref->format('d-m-Y')               . '.xlsx',
            'mingguan' => 'absensi-minggu-'  . $ref->weekOfYear . '-' . $ref->year  . '.xlsx',
            default    => 'absensi-'         . $ref->format('m-Y')                  . '.xlsx',
        };

        return $this->streamXlsx($filename, $judulSheet, $headers, $rows, $headerColor);
    }

    // ══════════════════════════════════════════════════════════
    // HELPER: Generate XLSX pakai PhpSpreadsheet
    // ══════════════════════════════════════════════════════════
    private function streamXlsx(
        string $filename,
        string $sheetTitle,
        array  $headers,
        array  $rows,
        string $headerBgColor = '157347'
    ) {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($sheetTitle, 0, 31));

        // ── Tulis header ──
        $sheet->fromArray($headers, null, 'A1');

        // ── Style header ──
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $headerBgColor],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ── Tulis data ──
        foreach ($rows as $ri => $row) {
            $excelRow = $ri + 2;
            $sheet->fromArray(array_values($row), null, "A{$excelRow}");

            // Warna baris berdasarkan status (kolom ke-10, index 9)
            $statusVal = $row[9] ?? '';
            [$bgColor, $fontColor] = match($statusVal) {
                'Terlambat' => ['FEF9C3', 'A16207'],
                'Alpha'     => ['FEE2E2', 'B91C1C'],
                'Izin'      => ['EFF6FF', '1D4ED8'],
                'Sakit'     => ['FDF4FF', '7E22CE'],
                'Hadir'     => ['F0FDF4', '15803D'],
                default     => ($ri % 2 === 0 ? ['F9FAFB', '111827'] : ['FFFFFF', '111827']),
            };

            $sheet->getStyle("A{$excelRow}:{$lastCol}{$excelRow}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'font'      => ['color' => ['rgb' => $fontColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E5E7EB'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($excelRow)->setRowHeight(18);
        }

        // ── Auto width kolom ──
        foreach (range(1, count($headers)) as $colIndex) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Stream response ──
        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
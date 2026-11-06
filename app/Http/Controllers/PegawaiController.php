<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;
use App\Models\Absensi;
use App\Imports\PegawaiImport;

class PegawaiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(\App\Http\Middleware\AdminMiddleware::class),
        ];
    }

    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%')
                  ->orWhere('jabatan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->jabatan) {
            $query->where('jabatan', $request->jabatan);
        }

        $pegawai = $query->orderBy('name')->paginate(12);

        $jabatanList = [
            'Manajer', 'Asisten Manajer', 'Kepala Bagian',
            'Staff Administrasi', 'Staff Keuangan', 'Staff IT',
            'Staff Operasional', 'Supervisor', 'Mandor',
            'Karyawan Lapangan', 'Security', 'Operator'
        ];

        return view('admin.pegawai.index', compact('pegawai', 'jabatanList'));
    }

    public function create()
    {
        $jabatanList = [
            'Manajer', 'Asisten Manajer', 'Kepala Bagian',
            'Staff Administrasi', 'Staff Keuangan', 'Staff IT',
            'Staff Operasional', 'Supervisor', 'Mandor',
            'Karyawan Lapangan', 'Security', 'Operator'
        ];
        return view('admin.pegawai.create', compact('jabatanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nip'      => 'required|string|unique:users,nip',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'jabatan'  => 'required|string',
            'divisi'   => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        User::create([
            'name'      => $request->name,
            'nip'       => $request->nip,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'jabatan'   => $request->jabatan,
            'divisi'    => $request->divisi,
            'phone'     => $request->phone,
            'alamat'    => $request->alamat,
            'photo'     => $photoPath,
            'role'      => 'user',
            'is_active' => true,
        ]);

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil ditambahkan!');
    }

    public function show(User $pegawai)
    {
        $absensiStats = Absensi::where('user_id', $pegawai->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->get();

        $totalHadir = $absensiStats->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalAlpha = $absensiStats->where('status', 'alpha')->count();
        $totalIzin  = $absensiStats->whereIn('status', ['izin', 'sakit'])->count();

        $riwayat = Absensi::where('user_id', $pegawai->id)
            ->orderBy('tanggal', 'desc')->take(10)->get();

        return view('admin.pegawai.show', compact('pegawai', 'totalHadir', 'totalAlpha', 'totalIzin', 'riwayat'));
    }

    public function edit(User $pegawai)
    {
        $jabatanList = [
            'Manajer', 'Asisten Manajer', 'Kepala Bagian',
            'Staff Administrasi', 'Staff Keuangan', 'Staff IT',
            'Staff Operasional', 'Supervisor', 'Mandor',
            'Karyawan Lapangan', 'Security', 'Operator'
        ];
        return view('admin.pegawai.edit', compact('pegawai', 'jabatanList'));
    }

    public function update(Request $request, User $pegawai)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nip'      => 'required|string|unique:users,nip,' . $pegawai->id,
            'email'    => 'required|email|unique:users,email,' . $pegawai->id,
            'jabatan'  => 'required|string',
            'divisi'   => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'nip', 'email', 'jabatan', 'divisi', 'phone', 'alamat']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            if ($pegawai->photo) {
                Storage::disk('public')->delete($pegawai->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pegawai->update($data);

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy(User $pegawai)
    {
        if ($pegawai->photo) {
            Storage::disk('public')->delete($pegawai->photo);
        }
        $pegawai->delete();
        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }

    // ──────────────────────────────────────────
    // IMPORT dari Excel
    // ──────────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $path     = $request->file('file')->store('imports', 'local');
        $fullPath = Storage::disk('local')->path($path);
        
        $import = new PegawaiImport();
        $import->import($fullPath);

        Storage::disk('local')->delete($path);

        $imported = $import->getImportedCount();
        $skipped  = $import->getSkippedCount();
        $errors   = $import->errors();

        $message = "Berhasil mengimpor {$imported} pegawai. {$skipped} dilewati.";

        // Tampilkan detail error untuk debug
        if (count($errors) > 0) {
            $message .= ' Error: ' . implode(' | ', $errors);
        }

        return redirect()->route('admin.pegawai.index')->with('success', $message);
    }

    // ──────────────────────────────────────────
    // EXPORT ke Excel
    // ──────────────────────────────────────────
    public function export()
    {
        $filename = 'data-pegawai-' . now()->format('Y-m-d') . '.xlsx';

        $users = User::where('role', 'user')
            ->orderBy('name')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pegawai');

        $sheet->fromArray(['No', 'NIP', 'Nama Lengkap', 'Email', 'Jabatan', 'Divisi', 'No. Telepon', 'Alamat', 'Status', 'Tgl Bergabung'], null, 'A1');

        foreach ($users as $i => $u) {
            $sheet->fromArray([
                $i + 1, $u->nip, $u->name, $u->email, $u->jabatan,
                $u->divisi ?? '-', $u->phone ?? '-', $u->alamat ?? '-',
                $u->is_active ? 'Aktif' : 'Nonaktif',
                $u->created_at->format('d/m/Y'),
            ], null, 'A' . ($i + 2));
        }

        $lastRow = $users->count() + 1;

        foreach (['A'=>5,'B'=>18,'C'=>28,'D'=>30,'E'=>22,'F'=>20,'G'=>18,'H'=>35,'I'=>12,'J'=>15] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getStyle("A{$i}:J{$i}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => ($i % 2 === 0) ? 'EEF2FF' : 'FFFFFF']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($i)->setRowHeight(18);
        }

        $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ──────────────────────────────────────────
    // Download Template Import
    // ──────────────────────────────────────────
    public function downloadTemplate()
    {
        $path = public_path('template-import-pegawai.xlsx');

        if (!file_exists($path)) {
            return back()->with('error', 'File template tidak ditemukan.');
        }

        return response()->download(
            $path,
            'template-import-pegawai.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
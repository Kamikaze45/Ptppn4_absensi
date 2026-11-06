<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanIzin;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LaporanController extends Controller
{
    // ═══════════════════════════════════════════════════════
    // LAPORAN SURAT (Cuti / Izin / Sakit) — sudah ada
    // ═══════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = PengajuanIzin::with('user')->latest();

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('cari')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->cari . '%');
            });
        }

        $laporan = $query->get();

        return view('admin.laporan.index', compact('laporan'));
    }

    public function detail($id)
    {
        $laporan = PengajuanIzin::with('user')->findOrFail($id);
        return view('admin.laporan.detail', compact('laporan'));
    }

    // ═══════════════════════════════════════════════════════
    // LAPORAN ABSENSI — REKAP HARIAN / MINGGUAN / BULANAN
    // ═══════════════════════════════════════════════════════

    public function absensi(Request $request)
    {
        $mode  = $request->input('mode', 'harian');   // harian | mingguan | bulanan
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);

        $totalPegawai = User::where('role', 'user')
                            ->where('is_active', true)
                            ->count();

        // ── HARIAN ────────────────────────────────────────
        if ($mode === 'harian') {
            $tanggal = $request->input('tanggal', today()->toDateString());

            $hasil        = $this->buildDataHarian($tanggal);
            $dataPegawai  = $hasil['dataPegawai'];
            $ringkasan    = $hasil['ringkasan'];
            $tglCarbon    = $hasil['tglCarbon'];

            return view('admin.laporan.absensi', compact(
                'mode', 'tahun', 'bulan', 'tanggal', 'tglCarbon',
                'dataPegawai', 'ringkasan', 'totalPegawai'
            ));
        }

        // ── MINGGUAN ──────────────────────────────────────
        if ($mode === 'mingguan') {
            $minggu = (int) $request->input('minggu', 1);
            $hasil  = $this->buildDataMingguan($tahun, $bulan, $minggu);

            return view('admin.laporan.absensi', array_merge(
                compact('mode', 'tahun', 'bulan', 'totalPegawai'),
                $hasil
            ));
        }

        // ── BULANAN ───────────────────────────────────────
        $hasil = $this->buildDataBulanan($tahun, $bulan);

        return view('admin.laporan.absensi', array_merge(
            compact('mode', 'tahun', 'bulan', 'totalPegawai'),
            $hasil
        ));
    }

    // ══════════════════════════════════════════════════════════════
    // EKSPOR EXCEL — HARIAN / MINGGUAN / BULANAN
    // Rute: GET /admin/laporan/absensi/export
    // Query: ?mode=harian&tanggal=YYYY-MM-DD
    //        ?mode=mingguan&tahun=YYYY&bulan=MM&minggu=N
    //        ?mode=bulanan&tahun=YYYY&bulan=MM
    //
    // Catatan: export dibuat via HTML table dikirim dengan header
    // Excel (.xls). Ini menghindari kebutuhan ekstensi PHP tambahan
    // (gd/zip) dan dependency composer (maatwebsite/excel) yang
    // bentrok versi dengan phpspreadsheet yang sudah terpasang.
    // ══════════════════════════════════════════════════════════════
    public function exportAbsensi(Request $request)
    {
        $mode = $request->input('mode', 'harian');

        if ($mode === 'mingguan') {
            return $this->exportMingguan($request);
        }

        if ($mode === 'bulanan') {
            return $this->exportBulanan($request);
        }

        return $this->exportHarian($request);
    }

    private function exportHarian(Request $request)
    {
        $tanggal = $request->input('tanggal', today()->toDateString());

        $hasil       = $this->buildDataHarian($tanggal);
        $dataPegawai = $hasil['dataPegawai'];
        $tglCarbon   = $hasil['tglCarbon'];

        $namaSheet = 'Absensi ' . $tglCarbon->format('d-m-Y');
        $namaFile  = 'Laporan-Absensi-Harian-' . $tglCarbon->format('d-m-Y') . '.xls';

        $html = view('admin.laporan.export.harian', compact('dataPegawai', 'tglCarbon', 'namaSheet'))->render();

        return $this->downloadExcel($html, $namaFile);
    }

    private function exportMingguan(Request $request)
    {
        $tahun  = (int) $request->input('tahun', now()->year);
        $bulan  = (int) $request->input('bulan', now()->month);
        $minggu = (int) $request->input('minggu', 1);

        $hasil = $this->buildDataMingguan($tahun, $bulan, $minggu);

        $namaBulanTahun = Carbon::create($tahun, $bulan, 1)->isoFormat('MMMM Y');
        $namaSheet = 'Minggu ' . $minggu . ' - ' . $namaBulanTahun;
        $namaFile  = 'Laporan-Absensi-Mingguan-' . $minggu . '-' .
                     Carbon::create($tahun, $bulan, 1)->format('m-Y') . '.xls';

        $html = view('admin.laporan.export.mingguan', array_merge($hasil, compact('namaSheet')))->render();

        return $this->downloadExcel($html, $namaFile);
    }

    private function exportBulanan(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);

        $hasil = $this->buildDataBulanan($tahun, $bulan);

        $namaBulan = Carbon::create($tahun, $bulan, 1)->isoFormat('MMMM Y');
        $namaSheet = 'Bulanan ' . $namaBulan;
        $namaFile  = 'Laporan-Absensi-Bulanan-' . Carbon::create($tahun, $bulan, 1)->format('m-Y') . '.xls';

        $html = view('admin.laporan.export.bulanan', array_merge($hasil, compact('namaSheet', 'namaBulan')))->render();

        return $this->downloadExcel($html, $namaFile);
    }

    // ── Helper: kirim response file .xls (HTML table) ─────────────
    private function downloadExcel(string $html, string $namaFile)
    {
        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ── Helper: bangun data pegawai untuk mode harian ─────────────
    // Dipakai bersama oleh method absensi() (tampilan) dan
    // exportHarian() (ekspor Excel) supaya logikanya tidak dobel.
    private function buildDataHarian(string $tanggal): array
    {
        $tglCarbon = Carbon::parse($tanggal);

        $totalPegawai = User::where('role', 'user')
                            ->where('is_active', true)
                            ->count();

        $semuaPegawai = User::where('role', 'user')
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->get();

        $absensiHari = Absensi::with('user')
                              ->whereDate('tanggal', $tanggal)
                              ->get()
                              ->keyBy('user_id');

        $izinAktif = PengajuanIzin::with('user')
                        ->where('status', 'disetujui')
                        ->where('tanggal_mulai', '<=', $tanggal)
                        ->where('tanggal_selesai', '>=', $tanggal)
                        ->get()
                        ->keyBy('user_id');

        $dataPegawai = $semuaPegawai->map(function ($user) use ($absensiHari, $izinAktif) {
            $abs  = $absensiHari->get($user->id);
            $izin = $izinAktif->get($user->id);

            if ($abs) {
                $statusTampil  = $abs->status;
                $statusMasuk   = $abs->status_masuk ?? '-';
                $checkIn       = $abs->jam_masuk ?? $abs->check_in ?? '-';
                $checkOut      = $abs->jam_keluar ?? $abs->check_out ?? '-';
                $tipe          = $abs->tipe_absensi ?? '-';
                $keterangan    = $abs->keterangan ?? '';

                $tipeKerja       = $abs->tipe_absensi ?? '-';
                $checkInLat      = $abs->check_in_lat ?? null;
                $checkInLng      = $abs->check_in_lng ?? null;
                $checkInAlamat   = $abs->check_in_alamat ?? null;
                $checkOutLat     = $abs->check_out_lat ?? null;
                $checkOutLng     = $abs->check_out_lng ?? null;
                $checkOutAlamat  = $abs->check_out_alamat ?? null;
            } elseif ($izin) {
                $statusTampil  = ucfirst($izin->tipe);
                $statusMasuk   = '-';
                $checkIn       = '-';
                $checkOut      = '-';
                $tipe          = ucfirst($izin->tipe);
                $keterangan    = $izin->alasan ?? '';

                $tipeKerja       = '-';
                $checkInLat      = null;
                $checkInLng      = null;
                $checkInAlamat   = null;
                $checkOutLat     = null;
                $checkOutLng     = null;
                $checkOutAlamat  = null;
            } else {
                $statusTampil  = 'Alpha';
                $statusMasuk   = '-';
                $checkIn       = '-';
                $checkOut      = '-';
                $tipe          = '-';
                $keterangan    = '';

                $tipeKerja       = '-';
                $checkInLat      = null;
                $checkInLng      = null;
                $checkInAlamat   = null;
                $checkOutLat     = null;
                $checkOutLng     = null;
                $checkOutAlamat  = null;
            }

            return [
                'user'              => $user,
                'status'            => $statusTampil,
                'status_masuk'      => $statusMasuk,
                'check_in'          => $checkIn,
                'check_out'         => $checkOut,
                'tipe'              => $tipe,
                'keterangan'        => $keterangan,

                'tipe_kerja'        => $tipeKerja,
                'check_in_lat'      => $checkInLat,
                'check_in_lng'      => $checkInLng,
                'check_in_alamat'   => $checkInAlamat,
                'check_out_lat'     => $checkOutLat,
                'check_out_lng'     => $checkOutLng,
                'check_out_alamat'  => $checkOutAlamat,
            ];
        });

        $ringkasan = [
            'hadir'    => $absensiHari->where('status', 'Hadir')->count(),
            'terlambat'=> $absensiHari->filter(fn($a) => $a->status_masuk === 'Terlambat')->count(),
            'alpha'    => $totalPegawai - $absensiHari->count() - $izinAktif->count(),
            'izin'     => $izinAktif->where('tipe', 'izin')->count(),
            'cuti'     => $izinAktif->where('tipe', 'cuti')->count(),
            'sakit'    => $izinAktif->where('tipe', 'sakit')->count(),
        ];
        $ringkasan['alpha'] = max(0, $ringkasan['alpha']);

        return [
            'dataPegawai'  => $dataPegawai,
            'ringkasan'    => $ringkasan,
            'totalPegawai' => $totalPegawai,
            'tglCarbon'    => $tglCarbon,
        ];
    }

    // ── Helper: bangun data rekap mingguan ────────────────────────
    // Dipakai bersama oleh method absensi() (tampilan) dan
    // exportMingguan() (ekspor Excel).
    private function buildDataMingguan(int $tahun, int $bulan, int $minggu): array
    {
        $tglPertama  = Carbon::create($tahun, $bulan, 1);
        $awalMinggu  = $tglPertama->copy()->startOfWeek(Carbon::MONDAY)
                                  ->addWeeks($minggu - 1);

        $awalBulan   = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $akhirBulan  = Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();

        if ($awalMinggu->lt($awalBulan)) $awalMinggu = $awalBulan->copy();
        $akhirMinggu = $awalMinggu->copy()->endOfWeek(Carbon::SUNDAY);
        if ($akhirMinggu->gt($akhirBulan)) $akhirMinggu = $akhirBulan->copy();

        $hariKerja = [];
        foreach (CarbonPeriod::create($awalMinggu, $akhirMinggu) as $hari) {
            if ($hari->isWeekday()) $hariKerja[] = $hari->copy();
        }

        $absensiMinggu = Absensi::with('user')
            ->whereBetween('tanggal', [
                $awalMinggu->toDateString(),
                $akhirMinggu->toDateString(),
            ])
            ->get();

        $izinMinggu = PengajuanIzin::with('user')
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $akhirMinggu->toDateString())
            ->where('tanggal_selesai', '>=', $awalMinggu->toDateString())
            ->get();

        $semuaPegawai = User::where('role', 'user')
                            ->where('is_active', true)
                            ->orderBy('name')->get();

        $totalPegawai = $semuaPegawai->count();

        $rekapMinggu = $semuaPegawai->map(function ($user) use ($hariKerja, $absensiMinggu, $izinMinggu) {
            $hadirCount    = 0;
            $terlambatCount= 0;
            $alphaCount    = 0;
            $izinCount     = 0;
            $cutiCount     = 0;
            $sakitCount    = 0;
            $detail        = [];

            foreach ($hariKerja as $hari) {
                $tgl = $hari->toDateString();

                // ── FIX: bandingkan tanggal dgn Carbon->toDateString() ──
                // Kolom 'tanggal' di model Absensi di-cast sebagai 'date',
                // begitu juga 'tanggal_mulai' / 'tanggal_selesai' di
                // PengajuanIzin. Artinya nilainya adalah objek Carbon,
                // bukan string. Membandingkan objek Carbon langsung dengan
                // string ('==' bawaan Collection::where()) sering gagal
                // match karena representasi string-nya beda format
                // ("2024-01-15 00:00:00" vs "2024-01-15"). Makanya di
                // mode mingguan/bulanan data selalu kebaca "Alpha".
                $abs = $absensiMinggu->first(function ($a) use ($user, $tgl) {
                    return (int) $a->user_id === (int) $user->id
                        && $a->tanggal?->toDateString() === $tgl;
                });

                $izin = $izinMinggu->first(function ($i) use ($user, $tgl) {
                    return (int) $i->user_id === (int) $user->id
                        && $i->tanggal_mulai?->toDateString() <= $tgl
                        && $i->tanggal_selesai?->toDateString() >= $tgl;
                });

                if ($abs) {
                    $statusHari = $abs->status_masuk === 'Terlambat' ? 'Terlambat' : 'Hadir';
                    $hadirCount++;
                    if ($abs->status_masuk === 'Terlambat') $terlambatCount++;
                } elseif ($izin) {
                    $statusHari = ucfirst($izin->tipe);
                    if ($izin->tipe === 'izin')  $izinCount++;
                    if ($izin->tipe === 'cuti')  $cutiCount++;
                    if ($izin->tipe === 'sakit') $sakitCount++;
                } else {
                    $statusHari = 'Alpha';
                    $alphaCount++;
                }

                $detail[$tgl] = [
                    'status'            => $statusHari,
                    'tipe_kerja'        => $abs->tipe_absensi ?? null,
                    'check_in_lat'      => $abs->check_in_lat ?? null,
                    'check_in_lng'      => $abs->check_in_lng ?? null,
                    'check_in_alamat'   => $abs->check_in_alamat ?? null,
                    'check_out_lat'     => $abs->check_out_lat ?? null,
                    'check_out_lng'     => $abs->check_out_lng ?? null,
                    'check_out_alamat'  => $abs->check_out_alamat ?? null,
                ];
            }

            return [
                'user'      => $user,
                'hadir'     => $hadirCount,
                'terlambat' => $terlambatCount,
                'alpha'     => $alphaCount,
                'izin'      => $izinCount,
                'cuti'      => $cutiCount,
                'sakit'     => $sakitCount,
                'detail'    => $detail,
            ];
        });

        $daftarMinggu = $this->daftarMingguDalamBulan($tahun, $bulan);

        return compact(
            'minggu', 'hariKerja', 'awalMinggu', 'akhirMinggu',
            'rekapMinggu', 'totalPegawai', 'daftarMinggu'
        );
    }

    // ── Helper: bangun data rekap bulanan ─────────────────────────
    // Dipakai bersama oleh method absensi() (tampilan) dan
    // exportBulanan() (ekspor Excel).
    private function buildDataBulanan(int $tahun, int $bulan): array
    {
        $awalBulan  = Carbon::create($tahun, $bulan, 1);
        $akhirBulan = $awalBulan->copy()->endOfMonth();

        $hariKerjaBulan = [];
        foreach (CarbonPeriod::create($awalBulan, $akhirBulan) as $hari) {
            if ($hari->isWeekday()) $hariKerjaBulan[] = $hari->copy();
        }
        $totalHariKerja = count($hariKerjaBulan);

        $absensibulan = Absensi::with('user')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $izinBulan = PengajuanIzin::with('user')
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $akhirBulan->toDateString())
            ->where('tanggal_selesai', '>=', $awalBulan->toDateString())
            ->get();

        $semuaPegawai = User::where('role', 'user')
                            ->where('is_active', true)
                            ->orderBy('name')->get();

        $totalPegawai = $semuaPegawai->count();

        $rekapBulanan = $semuaPegawai->map(function ($user) use (
            $hariKerjaBulan, $absensibulan, $izinBulan, $totalHariKerja
        ) {
            $hadirCount    = 0;
            $terlambatCount= 0;
            $alphaCount    = 0;
            $izinCount     = 0;
            $cutiCount     = 0;
            $sakitCount    = 0;

            $riwayatLokasi = [];

            foreach ($hariKerjaBulan as $hari) {
                $tgl = $hari->toDateString();

                // ── FIX: sama seperti di buildDataMingguan() ──
                // 'tanggal', 'tanggal_mulai', 'tanggal_selesai' adalah
                // objek Carbon (hasil $casts di model), jadi harus
                // dibandingkan lewat ->toDateString(), bukan langsung
                // dengan operator relasional terhadap string $tgl.
                $abs = $absensibulan->first(function ($a) use ($user, $tgl) {
                    return (int) $a->user_id === (int) $user->id
                        && $a->tanggal?->toDateString() === $tgl;
                });

                $izin = $izinBulan->first(function ($i) use ($user, $tgl) {
                    return (int) $i->user_id === (int) $user->id
                        && $i->tanggal_mulai?->toDateString() <= $tgl
                        && $i->tanggal_selesai?->toDateString() >= $tgl;
                });

                if ($abs) {
                    $hadirCount++;
                    if ($abs->status_masuk === 'Terlambat') $terlambatCount++;

                    $riwayatLokasi[] = [
                        'tanggal'           => $tgl,
                        'tipe_kerja'        => $abs->tipe_absensi ?? '-',
                        'check_in'          => $abs->check_in,
                        'check_in_lat'      => $abs->check_in_lat,
                        'check_in_lng'      => $abs->check_in_lng,
                        'check_in_alamat'   => $abs->check_in_alamat,
                        'check_out'         => $abs->check_out,
                        'check_out_lat'     => $abs->check_out_lat,
                        'check_out_lng'     => $abs->check_out_lng,
                        'check_out_alamat'  => $abs->check_out_alamat,
                    ];
                } elseif ($izin) {
                    if ($izin->tipe === 'izin')  $izinCount++;
                    if ($izin->tipe === 'cuti')  $cutiCount++;
                    if ($izin->tipe === 'sakit') $sakitCount++;
                } else {
                    $alphaCount++;
                }
            }

            $persenKehadiran = $totalHariKerja > 0
                ? round(($hadirCount / $totalHariKerja) * 100, 1)
                : 0;

            return [
                'user'            => $user,
                'hadir'           => $hadirCount,
                'terlambat'       => $terlambatCount,
                'alpha'           => $alphaCount,
                'izin'            => $izinCount,
                'cuti'            => $cutiCount,
                'sakit'           => $sakitCount,
                'persen_kehadiran'=> $persenKehadiran,
                'riwayat_lokasi'  => $riwayatLokasi,
            ];
        });

        $ringkasanBulan = [
            'total_hadir'     => $absensibulan->where('status', 'Hadir')->count(),
            'total_terlambat' => $absensibulan->filter(fn($a) => $a->status_masuk === 'Terlambat')->count(),
            'rata_kehadiran'  => $rekapBulanan->avg('persen_kehadiran'),
        ];

        return compact(
            'awalBulan', 'akhirBulan', 'totalHariKerja',
            'rekapBulanan', 'ringkasanBulan', 'totalPegawai'
        );
    }

    // ══════════════════════════════════════════════════════════════
    // DETAIL RIWAYAT ABSEN PER PEGAWAI — termasuk lokasi
    // Rute: GET /admin/laporan/absensi/riwayat/{user}
    // ══════════════════════════════════════════════════════════════
    public function riwayatPegawai(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $riwayat = Absensi::where('user_id', $userId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderByDesc('tanggal')
            ->get([
                'id', 'tanggal', 'tipe_absensi', 'status', 'status_masuk',
                'check_in', 'check_in_lat', 'check_in_lng', 'check_in_alamat',
                'check_out', 'check_out_lat', 'check_out_lng', 'check_out_alamat',
                'keterangan',
            ]);

        return view('admin.laporan.riwayat-pegawai', compact('user', 'riwayat', 'bulan', 'tahun'));
    }

    // ── Helper: daftar minggu dalam bulan ────────────────
    private function daftarMingguDalamBulan(int $tahun, int $bulan): array
    {
        $awal   = Carbon::create($tahun, $bulan, 1);
        $akhir  = $awal->copy()->endOfMonth();
        $result = [];
        $minggu = 1;
        $current = $awal->copy()->startOfWeek(Carbon::MONDAY);

        while ($current->lte($akhir)) {
            $endWeek = $current->copy()->endOfWeek(Carbon::SUNDAY);
            if ($endWeek->gt($akhir)) $endWeek = $akhir->copy();
            $startDisplay = $current->lt($awal) ? $awal->copy() : $current->copy();

            if ($startDisplay->lte($akhir)) {
                $result[] = [
                    'ke'    => $minggu,
                    'label' => 'Minggu ke-' . $minggu .
                               ' (' . $startDisplay->format('d') .
                               '–' . $endWeek->format('d M Y') . ')',
                ];
                $minggu++;
            }
            $current->addWeek();
        }

        return $result;
    }
}
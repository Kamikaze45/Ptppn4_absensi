<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Absensi;
use App\Models\PengajuanIzin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Arahkan ke method sesuai role
        return match ($user->role) {
            'admin'   => $this->forAdmin($user),
            'manajer' => $this->forManajer($user),
            default   => $this->forUser($user),   // role 'user' (pegawai)
        };
    }

    // =========================================================================
    // Dashboard ADMIN
    // Tampilan: statistik seluruh pegawai + chart absensi
    // View: resources/views/dashboard.blade.php (tidak berubah)
    // =========================================================================
    private function forAdmin(User $user)
    {
        $today        = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        // Statistik milik admin sendiri (tetap dikirim agar view tidak error)
        $absensiHariIni = Absensi::where('user_id', $user->id)->whereDate('tanggal', $today)->first();
        $totalHadir     = 0;
        $totalTerlambat = 0;
        $totalAlpha     = 0;
        $riwayatAbsensi = collect();

        // Statistik seluruh pegawai
        $totalPegawai = User::where('role', 'user')->where('is_active', true)->count();
        $hadirHariIni = Absensi::whereDate('tanggal', $today)->count();
        $belumAbsen   = max(0, $totalPegawai - $hadirHariIni);

        // Chart per-hari bulan ini
        $chartData = Absensi::whereYear('tanggal', $currentYear)
            ->whereMonth('tanggal', $currentMonth)
            ->select(
                DB::raw('DATE(tanggal) as tgl'),
                DB::raw("SUM(status IN ('hadir','terlambat')) as hadir"),
                DB::raw("SUM(status = 'terlambat') as terlambat"),
                DB::raw("SUM(status = 'alpha') as alpha"),
                DB::raw("SUM(status = 'izin') as izin"),
                DB::raw("SUM(status = 'sakit') as sakit")
            )
            ->groupBy('tgl')->orderBy('tgl')->get();

        $labels = $dHadir = $dTerlambat = $dAlpha = $dIzin = $dSakit = [];
        foreach ($chartData as $row) {
            $labels[]     = Carbon::parse($row->tgl)->format('d/m');
            $dHadir[]     = (int) $row->hadir;
            $dTerlambat[] = (int) $row->terlambat;
            $dAlpha[]     = (int) $row->alpha;
            $dIzin[]      = (int) $row->izin;
            $dSakit[]     = (int) $row->sakit;
        }

        $chartLabels    = json_encode($labels);
        $chartHadir     = json_encode($dHadir);
        $chartTerlambat = json_encode($dTerlambat);
        $chartAlpha     = json_encode($dAlpha);
        $chartIzin      = json_encode($dIzin);
        $chartSakit     = json_encode($dSakit);

        $rekap = [
            'hadir'     => Absensi::whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->whereIn('status', ['hadir','terlambat'])->count(),
            'terlambat' => Absensi::whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'terlambat')->count(),
            'alpha'     => Absensi::whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'alpha')->count(),
            'izin'      => Absensi::whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'izin')->count(),
            'sakit'     => Absensi::whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'sakit')->count(),
        ];

        return view('dashboard', compact(
            'user', 'absensiHariIni',
            'totalHadir', 'totalTerlambat', 'totalAlpha', 'riwayatAbsensi',
            'totalPegawai', 'hadirHariIni', 'belumAbsen',
            'chartLabels', 'chartHadir', 'chartTerlambat',
            'chartAlpha', 'chartIzin', 'chartSakit', 'rekap'
        ));
    }

    // =========================================================================
    // Dashboard MANAJER
    // Tampilan: pemantauan real-time absensi pegawai + pengajuan izin
    // View: resources/views/dashboard.blade.php (tetap sama)
    // =========================================================================
    private function forManajer(User $user)
    {
        $today        = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        // Data absensi manajer sendiri
        $absensiHariIni = Absensi::where('user_id', $user->id)->whereDate('tanggal', $today)->first();
        $totalHadir     = Absensi::where('user_id', $user->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->whereIn('status', ['hadir','terlambat'])->count();
        $totalTerlambat = Absensi::where('user_id', $user->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'terlambat')->count();
        $totalAlpha     = Absensi::where('user_id', $user->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'alpha')->count();
        $riwayatAbsensi = Absensi::where('user_id', $user->id)->orderBy('tanggal', 'desc')->take(7)->get();

        // Data khusus manajer: pengajuan izin menunggu
        $izinMenunggu      = PengajuanIzin::where('status', 'menunggu')->count();
        $izinDisetujuiBulanIni = PengajuanIzin::where('status', 'disetujui')
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->count();

        // ─────────────────────────────────────────────────
        // Pemantauan real-time seluruh pegawai hari ini
        // (nama, NIK SAP, jabatan, jam masuk/keluar, lokasi, status)
        // ─────────────────────────────────────────────────
        $totalPegawai = User::where('role', 'user')->where('is_active', true)->count();

        $semuaPegawai = User::where('role', 'user')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'nip', 'jabatan', 'divisi', 'photo']);

        $absensiHariIniSemua = Absensi::whereDate('tanggal', $today)->get()->keyBy('user_id');

        $pemantauanPegawai = $semuaPegawai->map(function ($pegawai) use ($absensiHariIniSemua) {
            $abs = $absensiHariIniSemua->get($pegawai->id);
            return (object) [
                'user'             => $pegawai,
                'check_in'         => $abs->check_in ?? null,
                'check_out'        => $abs->check_out ?? null,
                'check_in_lat'     => $abs->check_in_lat ?? null,
                'check_in_lng'     => $abs->check_in_lng ?? null,
                'check_in_alamat'  => $abs->check_in_alamat ?? null,
                'check_out_lat'    => $abs->check_out_lat ?? null,
                'check_out_lng'    => $abs->check_out_lng ?? null,
                'check_out_alamat' => $abs->check_out_alamat ?? null,
                'tipe_absensi'     => $abs->tipe_absensi ?? null,
                'status'           => $abs->status ?? 'alpha',
            ];
        });

        $hadirHariIni     = $absensiHariIniSemua->whereIn('status', ['hadir', 'terlambat'])->count();
        $terlambatHariIni = $absensiHariIniSemua->where('status', 'terlambat')->count();
        $belumAbsen       = max(0, $totalPegawai - $absensiHariIniSemua->count());

        // Nilai kosong agar bagian chart admin tidak error (tidak ditampilkan utk manajer)
        $chartLabels = $chartHadir = $chartTerlambat = $chartAlpha = $chartIzin = $chartSakit = '[]';
        $rekap = [];

        return view('dashboard', compact(
            'user', 'absensiHariIni',
            'totalHadir', 'totalTerlambat', 'totalAlpha', 'riwayatAbsensi',
            'totalPegawai', 'hadirHariIni', 'belumAbsen', 'terlambatHariIni',
            'chartLabels', 'chartHadir', 'chartTerlambat',
            'chartAlpha', 'chartIzin', 'chartSakit', 'rekap',
            // Tambahan khusus manajer
            'izinMenunggu', 'izinDisetujuiBulanIni', 'pemantauanPegawai'
        ));
    }

    // =========================================================================
    // Dashboard USER (Pegawai)
    // Tampilan: absensi hari ini + statistik pribadi
    // View: resources/views/dashboard.blade.php (tidak berubah)
    // =========================================================================
    private function forUser(User $user)
    {
        $today        = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        $absensiHariIni = Absensi::where('user_id', $user->id)->whereDate('tanggal', $today)->first();
        $totalHadir     = Absensi::where('user_id', $user->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->whereIn('status', ['hadir','terlambat'])->count();
        $totalTerlambat = Absensi::where('user_id', $user->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'terlambat')->count();
        $totalAlpha     = Absensi::where('user_id', $user->id)->whereMonth('tanggal', $currentMonth)->whereYear('tanggal', $currentYear)->where('status', 'alpha')->count();
        $riwayatAbsensi = Absensi::where('user_id', $user->id)->orderBy('tanggal', 'desc')->take(7)->get();

        // Nilai kosong agar view tidak error
        $totalPegawai = $hadirHariIni = $belumAbsen = 0;
        $chartLabels = $chartHadir = $chartTerlambat = $chartAlpha = $chartIzin = $chartSakit = '[]';
        $rekap = [];
        $izinMenunggu = $izinDisetujuiBulanIni = 0;

        return view('dashboard', compact(
            'user', 'absensiHariIni',
            'totalHadir', 'totalTerlambat', 'totalAlpha', 'riwayatAbsensi',
            'totalPegawai', 'hadirHariIni', 'belumAbsen',
            'chartLabels', 'chartHadir', 'chartTerlambat',
            'chartAlpha', 'chartIzin', 'chartSakit', 'rekap',
            'izinMenunggu', 'izinDisetujuiBulanIni'
        ));
    }
}
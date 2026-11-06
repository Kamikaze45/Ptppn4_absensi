<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // ── Koordinat Kantor PTPN4 Regional 7 Kebun Bekri ───────────────
    // Desa Sinar Banten, Kec. Bekri, Kab. Lampung Tengah, Lampung
    // Plus Code: W4GP+M2R
    const KANTOR_LAT   = -5.0562;
    const KANTOR_LNG   = 105.0978;
    const RADIUS_METER = 100;

    // ── Jam operasional check-in ─────────────────────────────────────
    const JAM_BUKA_CHECKIN    = '06:00:00'; // sebelum ini check-in ditolak
    const JAM_BATAS_TERLAMBAT = '08:30:00'; // setelah ini tetap bisa absen, tapi tercatat "Terlambat"

    // ── Index ────────────────────────────────────────────────────────

    public function index()
    {
        $user    = Auth::user();
        $absensi = Absensi::where('user_id', $user->id)
                        ->whereDate('tanggal', today())
                        ->first();

        // Riwayat 5 data terbaru untuk ditampilkan di halaman absensi
        $riwayat = Absensi::where('user_id', $user->id)
                        ->orderByDesc('tanggal')
                        ->limit(5)
                        ->get();

        return view('absensi.index', compact('absensi', 'riwayat'));
    }

    // ── Check-In ─────────────────────────────────────────────────────

    public function checkIn(Request $request)
    {
        $request->validate([
            'tipe_absensi' => 'required|in:WFO,WFH,Dinas Luar',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
        ]);

        $user = Auth::user();
        $now  = Carbon::now();

        // ✅ Jendela waktu check-in: dibuka mulai jam 06:00
        // Sebelum 06:00 → ditolak (belum dibuka)
        // Tidak ada batas tutup — check-in tetap bisa dilakukan kapan saja
        // setelah jam 06:00, tapi kalau lewat jam 08:30 akan tercatat
        // dengan status "Terlambat" (lihat logika status_masuk di bawah).
        $jamBuka = Carbon::today()->setTimeFromTimeString(self::JAM_BUKA_CHECKIN);

        if ($now->lt($jamBuka)) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in belum dibuka. Silakan check-in mulai pukul 06:00 WIB.',
            ], 422);
        }

        // Cek sudah check-in hari ini
        $existing = Absensi::where('user_id', $user->id)
                        ->whereDate('tanggal', today())
                        ->first();

        if ($existing && $existing->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in hari ini.',
            ], 422);
        }

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // Validasi radius khusus WFO
        if ($request->tipe_absensi === 'WFO') {
            $jarak = $this->hitungJarak($lat, $lng, self::KANTOR_LAT, self::KANTOR_LNG);
            if ($jarak > self::RADIUS_METER) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda berada {$jarak} meter dari kantor. WFO hanya bisa dalam radius " . self::RADIUS_METER . " meter.",
                    'jarak'   => $jarak,
                ], 422);
            }
        }

        // Tentukan status masuk:
        // 06:00 – 08:30 → "Hadir" (tepat waktu)
        // > 08:30       → "Terlambat", tapi tetap tercatat/absen sah
        $jamBatasTerlambat = Carbon::today()->setTimeFromTimeString(self::JAM_BATAS_TERLAMBAT);
        $statusMasuk       = $now->gt($jamBatasTerlambat) ? 'Terlambat' : 'Hadir';

        // Reverse geocode koordinat → alamat teks
        $alamat = $this->reverseGeocode($lat, $lng);

        // Simpan
        Absensi::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => today()],
            [
                'tipe_absensi'    => $request->tipe_absensi,
                'check_in'        => $now->format('H:i:s'),
                'check_in_lat'    => $lat,
                'check_in_lng'    => $lng,
                'check_in_alamat' => $alamat,
                'status'          => 'Hadir',
                'status_masuk'    => $statusMasuk,
            ]
        );

        return response()->json([
            'success'      => true,
            'message'      => "Check-in berhasil sebagai {$request->tipe_absensi}!",
            'jam_masuk'    => $now->format('H:i'),
            'tipe_absensi' => $request->tipe_absensi,
            'status_masuk' => $statusMasuk,
            'alamat'       => $alamat,
        ]);
    }

    // ── Check-Out ────────────────────────────────────────────────────

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user    = Auth::user();
        $now     = Carbon::now();
        $absensi = Absensi::where('user_id', $user->id)
                        ->whereDate('tanggal', today())
                        ->first();

        if (!$absensi || !$absensi->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in hari ini.',
            ], 422);
        }

        if ($absensi->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini.',
            ], 422);
        }

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // Reverse geocode koordinat → alamat teks
        $alamat = $this->reverseGeocode($lat, $lng);

        $absensi->update([
            'check_out'        => $now->format('H:i:s'),
            'check_out_lat'    => $lat,
            'check_out_lng'    => $lng,
            'check_out_alamat' => $alamat,
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Check-out berhasil!',
            'jam_keluar' => $now->format('H:i'),
            'alamat'     => $alamat,
        ]);
    }

    // ── Riwayat ──────────────────────────────────────────────────────

    public function riwayat(Request $request)
    {
        $user  = Auth::user();
        $query = Absensi::where('user_id', $user->id);

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter tahun (default tahun ini)
        $tahun = $request->input('tahun', date('Y'));
        $query->whereYear('tanggal', $tahun);

        $absensi = $query->orderByDesc('tanggal')->paginate(20);

        return view('absensi.riwayat', compact('absensi'));
    }

    // ── Helper Haversine ─────────────────────────────────────────────

    private function hitungJarak(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return (int) round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    // ── Helper Reverse Geocoding (Nominatim / OpenStreetMap) ──────────
    //
    // Mengubah koordinat (lat, lng) menjadi alamat teks yang bisa dibaca.
    // Nominatim gratis & tidak butuh API key, tapi ada batas pemakaian
    // (maks. ±1 request/detik). Untuk absensi (beberapa kali per hari
    // per pegawai) ini jauh di bawah batas tersebut.
    //
    // Kalau request gagal/timeout, kita tidak mau proses check-in/out
    // pegawai ikut gagal — jadi errornya ditangkap dan alamat di-set null.

    private function reverseGeocode(float $lat, float $lng): ?string
    {
        try {
            $response = Http::withHeaders([
                    // Nominatim mewajibkan User-Agent yang jelas mengidentifikasi aplikasi
                    'User-Agent' => 'PTPN4-Absensi-App/1.0 (admin@ptpn4regional7.local)',
                ])
                ->timeout(5)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat'            => $lat,
                    'lon'            => $lng,
                    'format'         => 'json',
                    'zoom'           => 18,
                    'addressdetails' => 1,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['display_name'] ?? null;
            }

            Log::warning('Reverse geocode gagal', [
                'lat' => $lat, 'lng' => $lng, 'status' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Reverse geocode error: ' . $e->getMessage(), [
                'lat' => $lat, 'lng' => $lng,
            ]);
        }

        return null;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengajuanIzinController extends Controller
{
    // =========================================================================
    // Kuota cuti tahunan (hari). Ubah di sini kalau perlu diganti nanti,
    // atau pindahkan ke config('cuti.kuota_tahunan') bila ingin lebih fleksibel.
    // =========================================================================
    private const KUOTA_CUTI_TAHUNAN = 12;

    // =========================================================================
    // BAGIAN USER (Pegawai)
    // =========================================================================

    public function formCuti()
    {
        $tahunIni = now()->year;

        // ✅ Hitung total hari cuti yang sudah "terpakai" tahun berjalan.
        // Dihitung dari status 'menunggu' (masih diproses) + 'disetujui',
        // supaya kuota ikut terpotong walau pengajuan belum diverifikasi
        // Manajer — mencegah user mengajukan dobel melebihi kuota sambil
        // menunggu persetujuan pengajuan sebelumnya.
        $sudahDipakai = $this->hitungHariCutiTerpakai(Auth::id(), $tahunIni);

        $sisaKuotaCuti = max(0, self::KUOTA_CUTI_TAHUNAN - $sudahDipakai);

        return view('izin.form.cuti', compact('sisaKuotaCuti', 'sudahDipakai'));
    }

    public function formIzin()
    {
        return view('izin.form.izin');
    }

    public function formSakit()
    {
        return view('izin.form.sakit');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'tipe'            => 'required|in:cuti,izin,sakit',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|max:500',
            'file_lampiran'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $jumlahHariDiajukan = Carbon::parse($request->tanggal_mulai)
            ->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;

        // ✅ Validasi kuota cuti tahunan (maks 12 hari/tahun).
        // PENTING: validasi ini WAJIB ada di server, bukan hanya di frontend
        // (tombol disabled bisa dilewati lewat request manual/DevTools).
        if ($request->tipe === 'cuti') {
            $tahunIni = now()->year;
            $sudahDipakai = $this->hitungHariCutiTerpakai(Auth::id(), $tahunIni);

            if ($sudahDipakai + $jumlahHariDiajukan > self::KUOTA_CUTI_TAHUNAN) {
                $sisa = max(0, self::KUOTA_CUTI_TAHUNAN - $sudahDipakai);

                return back()->withInput()->withErrors([
                    'tanggal_mulai' => "Kuota cuti tidak mencukupi. Sisa kuota kamu tahun ini hanya {$sisa} hari, "
                        ."sedangkan pengajuan ini membutuhkan {$jumlahHariDiajukan} hari.",
                ]);
            }
        }

        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            // pakai disk 'public_uploads' (simpan ke public/uploads)
            // supaya tidak butuh symlink storage:link — cocok untuk hosting
            // seperti InfinityFree yang tidak mengizinkan symlink
            $filePath = $request->file('file_lampiran')->store('lampiran-izin', 'public_uploads');
        }

        PengajuanIzin::create([
            'user_id'         => Auth::id(),
            'tipe'            => $request->tipe,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan'          => $request->alasan,
            'file_lampiran'   => $filePath,
            'status'          => 'menunggu',
        ]);

        return redirect()->route('izin.riwayat')
            ->with('success', 'Pengajuan berhasil dikirim, menunggu persetujuan Manajer.');
    }

    public function riwayat()
    {
        $pengajuan = PengajuanIzin::where('user_id', Auth::id())
            ->latest()->paginate(10);

        return view('izin.riwayat', compact('pengajuan'));
    }

    public function detail($id)
    {
        $pengajuan = PengajuanIzin::where('user_id', Auth::id())->findOrFail($id);

        return view('izin.detail', compact('pengajuan'));
    }

    public function delete($id)
    {
        $pengajuan = PengajuanIzin::where('user_id', Auth::id())
            ->where('status', 'menunggu')
            ->findOrFail($id);

        if ($pengajuan->file_lampiran) {
            // hapus dari disk 'public_uploads' juga
            Storage::disk('public_uploads')->delete($pengajuan->file_lampiran);
        }

        $pengajuan->delete();

        return redirect()->route('izin.riwayat')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    // =========================================================================
    // Hapus massal riwayat milik PEGAWAI sendiri yang SUDAH DIVERIFIKASI
    // (Disetujui/Ditolak). Dibatasi hanya milik user yang login.
    // =========================================================================
    public function hapusTerverifikasi(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $daftarPengajuan = PengajuanIzin::where('user_id', Auth::id())
            ->whereIn('id', $request->ids)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->get();

        $jumlah = 0;
        foreach ($daftarPengajuan as $item) {
            if ($item->file_lampiran) {
                Storage::disk('public_uploads')->delete($item->file_lampiran);
            }
            $item->delete();
            $jumlah++;
        }

        if ($jumlah === 0) {
            return redirect()->route('izin.riwayat')
                ->with('error', 'Tidak ada riwayat yang berhasil dihapus. Pastikan hanya memilih riwayat yang sudah diverifikasi.');
        }

        return redirect()->route('izin.riwayat')
            ->with('success', "{$jumlah} riwayat pengajuan terverifikasi berhasil dihapus.");
    }

    // =========================================================================
    // Serve lampiran lewat PHP (bukan static file)
    // =========================================================================
    // Dibuat karena PHP built-in server (php artisan serve) menolak melayani
    // file yang diakses lewat symlink (public/storage -> storage/app/public),
    // sehingga file lama yang tersimpan di disk 'public' selalu 403 Forbidden
    // walau filenya benar-benar ada. Dengan method ini, file dibaca langsung
    // dari disk manapun (baru atau lama) lewat PHP, jadi tidak bergantung pada
    // kemampuan web server melayani symlink sama sekali.
    public function lampiran($filename)
    {
        $relativePath = 'lampiran-izin/'.basename($filename);

        // 1) Cek lokasi BARU: disk 'public_uploads' (public/uploads/...)
        if (Storage::disk('public_uploads')->exists($relativePath)) {
            $fullPath = Storage::disk('public_uploads')->path($relativePath);
            return response()->file($fullPath);
        }

        // 2) Cek lokasi LAMA: disk 'public' (storage/app/public/...)
        if (Storage::disk('public')->exists($relativePath)) {
            $fullPath = Storage::disk('public')->path($relativePath);
            return response()->file($fullPath);
        }

        abort(404, 'File lampiran tidak ditemukan.');
    }

    // =========================================================================
    // BAGIAN MANAJER — method untuk setujui/tolak
    // =========================================================================

    /** Daftar semua pengajuan (untuk Manajer) */
    public function manajerIndex(Request $request)
    {
        $status  = $request->get('status', 'menunggu');
        $tipe    = $request->get('tipe');
        $cari    = $request->get('cari');

        $laporan = PengajuanIzin::with('user')
            ->when($status !== 'semua', fn($q) => $q->where('status', $status))
            ->when($tipe, fn($q) => $q->where('tipe', $tipe))
            ->when($cari, fn($q) => $q->whereHas('user', function ($q2) use ($cari) {
                $q2->where('name', 'like', '%'.$cari.'%');
            }))
            ->latest()->paginate(15);

        return view('admin.izin.daftar', compact('laporan', 'status'));
    }

    /** Detail pengajuan (untuk Manajer) */
    public function manajerDetail($id)
    {
        $pengajuan = PengajuanIzin::with('user')->findOrFail($id);

        return view('admin.izin.detail', compact('pengajuan'));
    }

    /** Setujui pengajuan (hanya Manajer) */
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pengajuan = PengajuanIzin::findOrFail($id);

        // ✅ Cek ulang kuota di sisi Manajer khusus untuk tipe 'cuti'.
        // Ini mencegah Manajer menyetujui pengajuan yang ternyata
        // membuat total cuti user tahun ini melebihi 12 hari — misalnya
        // karena user punya beberapa pengajuan 'menunggu' lain yang
        // kalau disetujui semua akan melebihi kuota.
        if ($pengajuan->tipe === 'cuti') {
            $tahunIni = Carbon::parse($pengajuan->tanggal_mulai)->year;

            $sudahDisetujuiLainnya = $this->hitungHariCutiTerpakai(
                $pengajuan->user_id,
                $tahunIni,
                ['disetujui'],
                $pengajuan->id
            );

            $hariPengajuanIni = Carbon::parse($pengajuan->tanggal_mulai)
                ->diffInDays(Carbon::parse($pengajuan->tanggal_selesai)) + 1;

            if ($sudahDisetujuiLainnya + $hariPengajuanIni > self::KUOTA_CUTI_TAHUNAN) {
                return back()->with('error',
                    'Tidak bisa menyetujui: total cuti user ini tahun '.$tahunIni.
                    ' akan melebihi kuota '.self::KUOTA_CUTI_TAHUNAN.' hari.');
            }
        }

        $pengajuan->update([
            'status'        => 'disetujui',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->route('manajer.izin.index')
            ->with('success', 'Pengajuan izin telah disetujui.');
    }

    /** Tolak pengajuan (hanya Manajer) */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ]);

        PengajuanIzin::findOrFail($id)->update([
            'status'        => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->route('manajer.izin.index')
            ->with('success', 'Pengajuan izin telah ditolak.');
    }

    // =========================================================================
    // BAGIAN ADMIN — lihat + hapus data yang sudah tuntas
    // =========================================================================

    /** Daftar semua pengajuan (untuk Admin) */
    public function adminIndex(Request $request)
    {
        $status  = $request->get('status', 'semua');
        $tipe    = $request->get('tipe');
        $cari    = $request->get('cari');

        $laporan = PengajuanIzin::with('user')
            ->when($status !== 'semua', fn($q) => $q->where('status', $status))
            ->when($tipe, fn($q) => $q->where('tipe', $tipe))
            ->when($cari, fn($q) => $q->whereHas('user', function ($q2) use ($cari) {
                $q2->where('name', 'like', '%'.$cari.'%');
            }))
            ->latest()->paginate(15);

        return view('admin.izin.daftar', compact('laporan', 'status'));
    }

    /** Detail pengajuan (untuk Admin) */
    public function adminDetail($id)
    {
        $pengajuan = PengajuanIzin::with('user')->findOrFail($id);

        return view('admin.izin.detail', compact('pengajuan'));
    }

    // =========================================================================
    // ✅ Hapus massal pengajuan yang SUDAH TUNTAS (untuk Admin)
    // =========================================================================
    // Beda dengan hapusTerverifikasi() milik pegawai (dibatasi
    // where('user_id', Auth::id())), method ini untuk ADMIN sehingga
    // boleh menghapus data siapa saja, TAPI tetap dibatasi hanya status
    // 'disetujui' atau 'ditolak' — pengajuan berstatus 'menunggu' tidak
    // bisa terhapus lewat sini.
    public function adminHapusTerverifikasi(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:pengajuan_izin,id',
        ]);

        $daftarPengajuan = PengajuanIzin::whereIn('id', $request->ids)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->get();

        $jumlah = 0;
        foreach ($daftarPengajuan as $item) {
            if ($item->file_lampiran) {
                // hapus file dari kedua kemungkinan disk (baru & lama)
                Storage::disk('public_uploads')->delete($item->file_lampiran);
                Storage::disk('public')->delete($item->file_lampiran);
            }
            $item->delete();
            $jumlah++;
        }

        if ($jumlah === 0) {
            return redirect()->route('admin.izin.index')
                ->with('error', 'Tidak ada data yang dihapus. Pastikan hanya memilih pengajuan berstatus Disetujui/Ditolak.');
        }

        return redirect()->route('admin.izin.index')
            ->with('success', "{$jumlah} pengajuan berhasil dihapus.");
    }

    // =========================================================================
    // Helper privat — hitung total hari cuti yang sudah "terpakai"
    // (dalam status yang dianggap memotong kuota) untuk user tertentu
    // pada tahun tertentu.
    // =========================================================================
    private function hitungHariCutiTerpakai(
        int $userId,
        int $tahun,
        array $statusDihitung = ['menunggu', 'disetujui'],
        ?int $kecualiId = null
    ): int {
        return PengajuanIzin::where('user_id', $userId)
            ->where('tipe', 'cuti')
            ->whereIn('status', $statusDihitung)
            ->whereYear('tanggal_mulai', $tahun)
            ->when($kecualiId, fn($q) => $q->where('id', '!=', $kecualiId))
            ->get()
            ->sum(function ($item) {
                return Carbon::parse($item->tanggal_mulai)
                    ->diffInDays(Carbon::parse($item->tanggal_selesai)) + 1;
            });
    }
}
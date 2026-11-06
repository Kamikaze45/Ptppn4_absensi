<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Exception;

class ProfileController extends Controller
{
    /**
     * Folder tujuan upload foto, relatif terhadap public_path().
     *
     * Foto disimpan LANGSUNG di dalam folder public/ (bukan lewat
     * disk 'public' + storage:link) karena:
     * 1. Banyak shared hosting gratis (InfinityFree/rf.gd, dsb)
     *    tidak mendukung symbolic link di server produksi.
     * 2. Pendekatan ini bekerja sama baiknya untuk upload dari
     *    localhost, jaringan lokal, maupun dari internet publik —
     *    tidak ada perbedaan penanganan berdasarkan asal request.
     */
    private const PHOTO_DIR = 'uploads/photos';

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update data profil (nama, phone, alamat) — TANPA foto
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user->update($request->only(['name', 'phone', 'alamat']));

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Upload foto profil via AJAX — endpoint terpisah
     * Route: POST /profile/photo/upload
     *
     * Menerima file dari sumber manapun: galeri, kamera HP,
     * drag-drop, hasil crop kanvas (Cropper.js), dll — selama
     * dikirim sebagai multipart/form-data dengan field 'photo'.
     */
    public function uploadPhoto(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:3072',
        ]);

        try {
            $targetDir = public_path(self::PHOTO_DIR);

            // Buat folder tujuan jika belum ada
            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                    throw new Exception('Gagal membuat folder tujuan upload.');
                }
            }

            // Pastikan folder benar-benar bisa ditulis
            if (!is_writable($targetDir)) {
                Log::error('Folder upload foto tidak bisa ditulis: ' . $targetDir);
                return response()->json([
                    'success' => false,
                    'message' => 'Server tidak memiliki izin menulis ke folder upload. Hubungi administrator.',
                ], 500);
            }

            $file = $request->file('photo');

            if (!$file->isValid()) {
                throw new Exception('File yang diupload tidak valid atau rusak.');
            }

            // Nama file unik agar tidak bentrok antar pengguna/waktu
            $filename = uniqid('foto_', true) . '.' . strtolower($file->getClientOriginalExtension());

            // Simpan foto lama untuk dihapus SETELAH file baru berhasil disimpan
            // (biar kalau upload baru gagal, foto lama tidak ikut hilang)
            $oldPhoto = $user->photo;

            $file->move($targetDir, $filename);

            $path = self::PHOTO_DIR . '/' . $filename;

            // Verifikasi file benar-benar ada setelah dipindah
            if (!is_file(public_path($path))) {
                throw new Exception('File gagal tersimpan ke server.');
            }

            $user->update(['photo' => $path]);

            // Baru hapus foto lama setelah semua proses di atas sukses
            $this->hapusFileFotoLama($oldPhoto);

            return response()->json([
                'success' => true,
                'url'     => asset($path),
                'message' => 'Foto berhasil disimpan.',
            ]);

        } catch (Exception $e) {
            Log::error('Upload foto profil gagal: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan foto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus foto profil
     * Route: DELETE /profile/photo
     */
    public function deletePhoto(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->photo) {
            return back()->with('error', 'Tidak ada foto profil untuk dihapus.');
        }

        $this->hapusFileFotoLama($user->photo);

        $user->update(['photo' => null]);

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    /**
     * Hapus file fisik foto lama dari public/uploads/photos,
     * jika file tersebut memang ada di sana.
     */
    private function hapusFileFotoLama(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = public_path($path);

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Ganti password — logout otomatis setelah berhasil
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Logout otomatis setelah ganti password
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }
}
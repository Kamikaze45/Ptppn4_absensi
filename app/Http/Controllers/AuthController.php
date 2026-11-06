<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip'      => 'required|string',
            'password' => 'required|min:6',
        ], [
            'nip.required'      => 'NIK SAP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        // Cari user berdasarkan NIK SAP
        $user = User::where('nip', $request->nip)->first();

        // Cek user ada & password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['nip' => 'NIK SAP atau password salah.'])
                ->withInput($request->only('nip'));
        }

        // Cek akun aktif
        if (!$user->is_active) {
            return back()
                ->withErrors(['nip' => 'Akun Anda telah dinonaktifkan. Hubungi admin.'])
                ->withInput($request->only('nip'));
        }

        // Login manual
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
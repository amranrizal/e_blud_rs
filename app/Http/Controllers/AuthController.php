<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. TAMPILKAN FORM LOGIN
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. PROSES LOGIN (VALIDASI)
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba Login (Laravel otomatis cek hash password)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek Role untuk Redirect (Opsional)
            // if (Auth::user()->role == 'admin') { return redirect()->route('admin.dashboard'); }
            
            // Default Redirect ke Dashboard
            return redirect()->intended('/dashboard')->with('success', 'Selamat Datang, '.Auth::user()->name.'!');
        }

        // Jika Gagal
        return back()->withErrors([
            'email' => 'Email atau Password salah.',
        ])->onlyInput('email');
    }

    // 3. PROSES LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('info', 'Anda berhasil logout.');
    }
}
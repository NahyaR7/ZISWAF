<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ==========================================
    // 1. LOGIN MANUAL (EMAIL & PASSWORD)
    // ==========================================
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin' ? redirect()->route('dashboard') : redirect()->route('dashboard-user');
        }
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return Auth::user()->role === 'admin' ? redirect()->route('dashboard') : redirect()->route('dashboard-user');
        }

        return back()->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])->onlyInput('email');
    }

    // ==========================================
    // 2. REGISTRASI MANUAL
    // ==========================================
    public function showRegister()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin' ? redirect()->route('dashboard') : redirect()->route('dashboard-user');
        }
        return view('pages.register');
    }

    public function register(Request $request)
    {
        // Validasi input pendaftaran
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Simpan akun nasabah baru ke database
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'nasabah' // Default role
        ]);

        // Langsung login otomatis setelah daftar
        Auth::login($user);

        return redirect()->route('dashboard-user')->with('success', 'Selamat datang! Akun Anda berhasil dibuat.');
    }

    // ==========================================
    // 3. AUTENTIKASI VIA GOOGLE (SSO)
    // ==========================================
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah email sudah terdaftar di database kita
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Jika belum ada, buat akun baru secara otomatis!
                // Kita beri password acak karena SQLite butuh password, dan username diambil dari email
                $username = explode('@', $googleUser->getEmail())[0] . rand(100, 999);
                
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'username' => $username,
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'nasabah'
                ]);
            } else {
                // Jika email sudah ada tapi belum di-link dengan google_id, kita update
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user);
            return Auth::user()->role === 'admin' ? redirect()->route('dashboard') : redirect()->route('dashboard-user');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan Google. Pastikan API tersambung dengan benar.']);
        }
    }

    // ==========================================
    // 4. LOGOUT
    // ==========================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
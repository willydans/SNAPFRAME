<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan Form Login
    public function showLogin() {
        // Pastikan file view kamu ada di folder resources/views/auth/login.blade.php
        return view('auth.login');
    }

    // Proses Login
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // PERUBAHAN DI SINI:
            // Sekarang langsung diarahkan ke route 'dashboard'
            return redirect()->route('dashboard'); 
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Tampilkan Form Register
    public function showRegister() {
        // Pastikan file view kamu ada di folder resources/views/auth/register.blade.php
        return view('auth.register');
    }

    // Proses Register
    public function register(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Pastikan di form ada input name="password_confirmation"
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        // PERUBAHAN DI SINI:
        // Setelah daftar langsung masuk Dashboard
        return redirect()->route('dashboard');
    }

    // Proses Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Setelah logout balik ke halaman login
        return redirect()->route('login');
    }
}
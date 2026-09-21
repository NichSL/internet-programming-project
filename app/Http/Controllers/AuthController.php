<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. PROSES REGISTRASI USER BIASA
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^((?!admin).)*$/i'], 
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'username.regex' => 'username anda'
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }

    // 2. PROSES LOGIN USER BIASA & SELLER
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Jika Admin nyasar login di sini, tendang ke login admin
            if (Auth::user()->role === 'admin') {
                Auth::logout();
                return redirect('/admin/login')->withErrors(['username' => 'Admin harus login melalui portal khusus.']);
            }
            
            return redirect('/dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
    }

    // 3. MENAMPILKAN FORM LOGIN KHUSUS ADMIN
    public function showAdminLogin()
    {
        return view('auth.admin_login');
    }

    // 4. PROSES LOGIN KHUSUS ADMIN
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'regex:/admin/i'],
            'password' => 'required|string',
        ], [
            'username.regex' => 'Format username admin tidak valid.'
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['username' => 'Akun ini tidak memiliki hak akses Admin.']);
            }
            
            $request->session()->regenerate();
            return redirect('/admin/dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password admin salah.'])->onlyInput('username');
    }

    // 5. PROSES LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
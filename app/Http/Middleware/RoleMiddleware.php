<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // Tambahkan titik tiga (...) sebelum $roles agar bisa menerima banyak role sekaligus
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah role user saat ini ada di dalam daftar role yang diizinkan
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses Ditolak. Halaman ini bukan untuk role Anda.');
        }

        return $next($request);
    }
}
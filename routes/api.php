<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import Controller API kita
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\MarketApiController;

/*
|--------------------------------------------------------------------------
| RUTE API TERBUKA (PUBLIC)
|--------------------------------------------------------------------------
*/

// 1. RUTE LOGIN API (Untuk mendapatkan Token di Postman)
Route::post('/login', function (Request $request) {
    // Validasi inputan dari Postman
    $credentials = $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    // Cek apakah username dan password cocok di database
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        
        // Menciptakan token rahasia menggunakan Sanctum
        $token = $user->createToken('API Token')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'role'    => $user->role // Menampilkan role agar Anda tahu ini login sebagai siapa
        ]);
    }

    // Jika salah password / username
    return response()->json([
        'success' => false,
        'message' => 'Username atau Password salah'
    ], 401);
});


/*
|--------------------------------------------------------------------------
| RUTE API TERTUTUP (Membutuhkan Token)
|--------------------------------------------------------------------------
*/

// Middleware 'auth:sanctum' memastikan rute di bawah ini wajib pakai Token
Route::middleware('auth:sanctum')->group(function () {
    
    // ----------------------------------------------------
    // API UNTUK SELLER (Kelola Produk Sendiri)
    // ----------------------------------------------------
    // Rute ini otomatis membuat endpoint GET, POST, PUT, DELETE untuk '/products'
    Route::apiResource('products', ProductApiController::class);


    // ----------------------------------------------------
    // API UNTUK USER / PEMBELI (Jelajah MarketHub)
    // ----------------------------------------------------
    // Mengambil semua katalog produk
    Route::get('/market', [MarketApiController::class, 'index']);
    
    // Mengambil detail 1 produk
    Route::get('/market/{id}', [MarketApiController::class, 'show']);



    // ----------------------------------------------------
    // API UNTUK MENDAPATKAN DATA PROFIL USER SAAT INI
    // ----------------------------------------------------
    Route::get('/user', function (Request $request) {
        $user = $request->user(); // Mengambil data user berdasarkan Token yang dikirim
        
        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil',
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => $user->role // Saya tambahkan role agar lebih lengkap
            ]
        ], 200);
    });

});

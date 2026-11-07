<?php

use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\MarketApiController;
// Import Controller API
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
        'password' => 'required',
    ]);

    // Cek apakah username dan password cocok di database
    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Menciptakan token rahasia menggunakan Sanctum
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'role' => $user->role, // Menampilkan role agar Anda tahu ini login sebagai siapa
        ]);
    }

    // Jika salah password / username
    return response()->json([
        'success' => false,
        'message' => 'Username atau Password salah',
    ], 401);
});

// 2. RUTE LOGIN KHUSUS ADMIN (Menolak role selain admin)
Route::post('/admin/login', function (Request $request) {
    $credentials = $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    // Cek kecocokan password
    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // CEK ROLE: Jika dia bukan admin, batalkan login dan hapus sesinya
        if (strtolower($user->role) !== 'admin') {
            // Logout paksa karena dia bukan admin
            Auth::logout();

            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak! Anda bukan Admin.',
            ], 403);
        }

        // Jika dia benar-benar admin, buatkan Token
        $token = $user->createToken('Admin API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Admin berhasil',
            'token' => $token,
            'role' => $user->role,
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Username atau Password salah',
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
    Route::apiResource('products', ProductApiController::class)->names('api.products');

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
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
    });

    // ----------------------------------------------------
    // API KHUSUS ADMIN (Melihat User & Toko)
    // ----------------------------------------------------
    // List semua user
    Route::get('/admin/users', [AdminApiController::class, 'indexUsers']);

    // History belanja user biasa
    Route::get('/admin/user/{id}/history', [AdminApiController::class, 'showUserHistory']);

    // History produk dan penjualan toko (seller)
    Route::get('/admin/store/{id}/history', [AdminApiController::class, 'showStoreHistory']);

    // ----------------------------------------------------
    // API KERANJANG BELANJA (Scoped to authenticated user)
    // ----------------------------------------------------
    Route::get('/cart', [CartApiController::class, 'index']);                // GET  /api/cart
    Route::post('/cart', [CartApiController::class, 'store']);               // POST /api/cart
    Route::delete('/cart', [CartApiController::class, 'clear']);             // DELETE /api/cart (clear all)
    Route::patch('/cart/{itemId}', [CartApiController::class, 'update']);    // PATCH /api/cart/{id}
    Route::delete('/cart/{itemId}', [CartApiController::class, 'destroy']);  // DELETE /api/cart/{id}

    // ----------------------------------------------------
    // API RIWAYAT PESANAN (Scoped to authenticated user)
    // ----------------------------------------------------
    // IMPORTANT: 'my-orders' must come before '{id}' to avoid routing conflict
    Route::get('/orders/my-orders', [OrderApiController::class, 'myOrders']); // GET /api/orders/my-orders
    Route::get('/orders/{id}', [OrderApiController::class, 'show']);           // GET /api/orders/{id}

});

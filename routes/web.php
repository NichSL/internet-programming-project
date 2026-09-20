<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    // Portal User Biasa
    Route::view('/login', 'auth.login')->name('login'); 
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Portal Khusus Admin
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 1. Role: USER BIASA & SELLER (Keduanya bisa mengakses halaman ini)
    Route::middleware('role:user,seller')->group(function () {
        Route::view('/dashboard', 'dashboards.user')->name('user.dashboard');
        Route::view('/become-seller', 'dashboards.become_seller')->name('become.seller');
        Route::post('/become-seller', [DashboardController::class, 'upgradeToSeller']);
    });

    // 2. Role: SELLER (Hanya Seller yang bisa masuk sini)
    Route::middleware('role:seller')->group(function () {
        Route::view('/seller/dashboard', 'dashboards.seller')->name('seller.dashboard');
    });

    // 3. Role: ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::view('/admin/dashboard', 'dashboards.admin')->name('admin.dashboard');
    });

    // RUTE PENGATURAN (Bisa diakses User & Seller)
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/store', [\App\Http\Controllers\SettingsController::class, 'updateStore'])->name('settings.store');
});
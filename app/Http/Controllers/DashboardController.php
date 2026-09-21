<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Memproses upgrade dari User ke Seller
    public function upgradeToSeller(Request $request)
    {
        $request->validate([
            'store_name'    => 'required|string|max:255',
            'store_address' => 'required|string',
        ]);

        $user = Auth::user();
        $user->role = 'seller';
        $user->store_name = $request->store_name;
        $user->store_address = $request->store_address;
        $user->save();

        return redirect('/seller/dashboard')->with('success', 'Selamat! Toko Anda berhasil dibuat.');
    }
}
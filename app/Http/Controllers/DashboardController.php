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

    public function sellerDashboard()
    {
        $products = Auth::user()->products()->latest()->get();
        return view('dashboards.seller', compact('products'));
    }

    
    public function adminDashboard()
    {
        $totalPengguna = \App\Models\User::count();
        $totalToko = \App\Models\User::where('role', 'seller')->count();
        
        // Ambil semua daftar pengguna
        $users = \App\Models\User::latest()->get();

        return view('dashboards.admin', compact('totalPengguna', 'totalToko', 'users'));

    }

    public function viewUser($id)
    {
        // CEK ROLE: Jika bukan admin, tampilkan halaman Error 403
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Halaman ini khusus untuk Admin.');
        }

        $user = \App\Models\User::with('purchases.product')->findOrFail($id);
        return view('dashboards.admin.user_detail', compact('user'));
    }

    public function viewStore($id)
    {
        // CEK ROLE: Jika bukan admin, tampilkan halaman Error 403
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Halaman ini khusus untuk Admin.');
        }

        $seller = \App\Models\User::where('role', 'seller')->with(['products', 'sales.product', 'sales.buyer'])->findOrFail($id);
        return view('dashboards.admin.store_detail', compact('seller'));
    }


    // 1. FUNGSI UNTUK MENAMPILKAN DASHBOARD USER (Katalog & Pencarian)
    public function userDashboard(\Illuminate\Http\Request $request)
    {
        // Ambil kata kunci pencarian jika ada
        $search = $request->input('search');

        // Ambil semua produk dari database, filter jika ada pencarian
        $products = \App\Models\Product::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get();

        // Kirim variabel $products dan $search ke view user.blade.php
        return view('dashboards.user', compact('products', 'search'));
    }

    // 2. FUNGSI UNTUK MEMPROSES PESANAN (Beli Barang)
    public function buyProduct(Request $request, $id)
    {
        // Validasi input jumlah beli
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($id);

        // Cek apakah stok mencukupi
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Pesanan gagal: Stok barang tidak mencukupi!');
        }

        // Kurangi stok produk di database
        $product->decrement('stock', $request->quantity);

        // Catat ke tabel orders (History)
        Order::create([
            'buyer_id'    => Auth::id(),
            'seller_id'   => $product->user_id,
            'product_id'  => $product->id,
            'quantity'    => $request->quantity,
            'total_price' => $product->price * $request->quantity,
            'status'      => 'selesai'
        ]);

        return back()->with('success', 'Berhasil! Anda telah membeli ' . $request->quantity . 'x ' . $product->name);
    }
}


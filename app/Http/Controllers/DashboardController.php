<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Memproses upgrade dari User ke Seller
    public function upgradeToSeller(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
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
        $totalPengguna = User::count();
        $totalToko = User::where('role', 'seller')->count();

        // Ambil semua daftar pengguna
        $users = User::latest()->get();

        return view('dashboards.admin', compact('totalPengguna', 'totalToko', 'users'));

    }

    public function viewUser($id)
    {
        // CEK ROLE: Jika bukan admin, tampilkan halaman Error 403
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Halaman ini khusus untuk Admin.');
        }

        $user = User::with('purchases.product')->findOrFail($id);

        return view('dashboards.admin.user_detail', compact('user'));
    }

    public function viewStore($id)
    {
        // CEK ROLE: Jika bukan admin, tampilkan halaman Error 403
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Halaman ini khusus untuk Admin.');
        }

        $seller = User::where('role', 'seller')->with(['products', 'sales.product', 'sales.buyer'])->findOrFail($id);

        return view('dashboards.admin.store_detail', compact('seller'));
    }

    // 1. FUNGSI UNTUK MENAMPILKAN DASHBOARD USER (Katalog & Pencarian)
    public function userDashboard(Request $request)
    {
        // Ambil kata kunci pencarian jika ada
        $search = $request->input('search');

        // Ambil semua produk dari database, filter jika ada pencarian
        $products = Product::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
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
            'quantity' => 'required|integer|min:1',
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
            'buyer_id' => Auth::id(),
            'seller_id' => $product->user_id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'total_price' => $product->price * $request->quantity,
            'status' => 'selesai',
        ]);

        return back()->with('success', 'Berhasil! Anda telah membeli '.$request->quantity.'x '.$product->name);
    }

    // 3. FUNGSI UNTUK MENAMPILKAN RIWAYAT PESANAN
    public function orderHistory()
    {
        $orders = Order::where('buyer_id', Auth::id())
            ->with(['product.user'])
            ->latest()
            ->get();

        return view('dashboards.order_history', compact('orders'));
    }

    // 4. FUNGSI UNTUK MENAMPILKAN HALAMAN CHECKOUT
    public function checkout()
    {
        return view('dashboards.checkout');
    }

    // 5. FUNGSI UNTUK MEMPROSES CHECKOUT DARI KERANJANG
    public function processCheckout(Request $request)
    {
        $items = json_decode($request->input('items', '[]'), true);

        if (! is_array($items) || empty($items)) {
            return redirect()->route('user.checkout')->with('error', 'Keranjang belanja kosong.');
        }

        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            $qty = (int) ($item['quantity'] ?? 0);

            if (! $product || $qty <= 0) {
                continue;
            }

            if ($product->stock < $qty) {
                return redirect()->route('user.checkout')->with('error', "Stok untuk produk {$product->name} tidak mencukupi.");
            }

            $product->decrement('stock', $qty);

            Order::create([
                'buyer_id' => Auth::id(),
                'seller_id' => $product->user_id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'total_price' => $product->price * $qty,
                'status' => 'selesai',
            ]);
        }

        Auth::user()->cartItems()->delete();

        return redirect()->route('user.orders')->with('success', 'Checkout berhasil! Pesanan Anda telah dicatat.');
    }
}

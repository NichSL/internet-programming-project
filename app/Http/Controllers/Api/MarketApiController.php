<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class MarketApiController extends Controller
{
    // MENGAMBIL SEMUA PRODUK (Untuk Halaman Utama MarketHub)
    public function index()
    {
        // Mengambil semua produk beserta data nama dan nama toko penjualnya
        $products = Product::with('user:id,name,store_name')->latest()->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar semua produk di MarketHub berhasil diambil',
            'data'    => $products
        ], 200);
    }

    // MENGAMBIL DETAIL SATU PRODUK (Saat User klik produk tertentu)
    public function show($id)
    {
        // Mengambil detail produk beserta alamat tokonya
        $product = Product::with('user:id,name,store_name,store_address')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil',
            'data'    => $product
        ], 200);
    }
}
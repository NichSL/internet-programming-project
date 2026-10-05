<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    // 1. READ: Mengambil semua data produk milik seller
    public function index()
    {
        $products = Auth::user()->products()->latest()->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data'    => $products
        ], 200);
    }

    // 2. CREATE: Menyimpan data produk baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
        ]);

        $product = Auth::user()->products()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $product
        ], 201);
    }

    // 3. READ: Mengambil detail satu produk (berdasarkan ID)
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product || $product->user_id !== Auth::id()) {
            return response()->json([
                'success' => false, 
                'message' => 'Produk tidak ditemukan atau akses ditolak'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil',
            'data'    => $product
        ], 200);
    }

    // 4. UPDATE: Memperbarui data produk
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product || $product->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data'    => $product
        ], 200);
    }

    // 5. DELETE: Menghapus produk (Soft Delete akan otomatis bekerja)
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product || $product->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 404);
        }

        $product->delete(); // Karena model Product pakai SoftDeletes, ini otomatis jadi soft delete

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ], 200);
    }
}
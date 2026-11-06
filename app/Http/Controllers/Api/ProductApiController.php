<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    // 1. READ ALL: Melihat semua produk milik seller yang sedang login
    public function index()
    {
        $products = Auth::user()->products()->latest()->get();
        return response()->json(['success' => true, 'data' => $products]);
    }

    // 2. CREATE (TAMBAH): Menambah produk baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
        ]);

        // Otomatis memasukkan user_id milik seller yang sedang login
        $product = Auth::user()->products()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan!',
            'data'    => $product
        ], 201);
    }

    // 3. READ DETAIL: Melihat 1 produk
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product || $product->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan/Akses ditolak'], 404);
        }

        return response()->json(['success' => true, 'data' => $product]);
    }

    // 4. UPDATE (EDIT): Mengedit produk yang sudah ada
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        // Cek apakah produk ada & apakah milik seller ini
        if (!$product || $product->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak atau produk tidak ada'], 403);
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
            'message' => 'Produk berhasil diperbarui!',
            'data'    => $product
        ]);
    }

    // 5. DELETE (HAPUS): Menghapus produk
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product || $product->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak atau produk tidak ada'], 403);
        }

        $product->delete(); // Karena pakai trait SoftDeletes, ini jadi soft delete

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus!'
        ]);
    }
}
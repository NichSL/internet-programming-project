<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartApiController extends Controller
{
    /**
     * Format a collection of cart items into the standard response structure.
     *
     * @param  Collection  $items
     * @return array{items: array, totalItems: int, totalPrice: int}
     */
    private function buildCartResponse($items): array
    {
        $validItems = $items->filter(fn ($item) => $item->product !== null);

        $formatted = $validItems->map(fn ($item) => [
            'id' => $item->id,
            'itemId' => $item->id,
            'productId' => $item->product_id,
            'product_id' => $item->product_id,
            'productName' => $item->product->name,
            'name' => $item->product->name,
            'image' => $item->product->image ?? null,
            'price' => (int) $item->product->price,
            'stock' => (int) $item->product->stock,
            'quantity' => (int) $item->quantity,
            'subtotal' => (int) ($item->product->price * $item->quantity),
        ]);

        return [
            'items' => $formatted->values()->all(),
            'totalItems' => (int) $formatted->sum('quantity'),
            'totalPrice' => (int) $formatted->sum('subtotal'),
        ];
    }

    /**
     * GET /api/cart
     * Mengambil seluruh item keranjang milik user yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = CartItem::where('user_id', $user->id)
            ->with('product')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data keranjang berhasil diambil.',
            'data' => $this->buildCartResponse($items),
        ], 200);
    }

    /**
     * POST /api/cart
     * Menambahkan produk ke keranjang belanja milik user yang sedang login.
     */
    public function store(Request $request): JsonResponse
    {
        $productId = $request->input('productId') ?? $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        if (! $productId || ! is_numeric($productId) || $quantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Product ID dan kuantitas valid (minimal 1) wajib diisi.',
            ], 422);
        }

        $product = Product::find($productId);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan atau sudah tidak tersedia.',
            ], 404);
        }

        if ($product->stock < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk sudah habis.',
            ], 400);
        }

        $user = $request->user();

        return DB::transaction(function () use ($user, $product, $quantity) {
            $cartItem = CartItem::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $currentQty = $cartItem ? $cartItem->quantity : 0;
            $newQty = $currentQty + $quantity;

            if ($newQty > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Jumlah yang diminta ({$newQty}) melebihi stok yang tersedia ({$product->stock}).",
                ], 400);
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQty]);
            } else {
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }

            $items = CartItem::where('user_id', $user->id)
                ->with('product')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'data' => $this->buildCartResponse($items),
            ], 200);
        });
    }

    /**
     * PATCH /api/cart/{itemId}
     * Mengubah jumlah produk dalam keranjang berdasarkan item ID milik user yang login.
     */
    public function update(Request $request, int $itemId): JsonResponse
    {
        $quantity = (int) $request->input('quantity', 0);

        if ($quantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Kuantitas minimal adalah 1.',
            ], 422);
        }

        $user = $request->user();

        $cartItem = CartItem::where('id', $itemId)
            ->where('user_id', $user->id)
            ->with('product')
            ->first();

        if (! $cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan atau bukan milik Anda.',
            ], 404);
        }

        if (! $cartItem->product) {
            $cartItem->delete();

            return response()->json([
                'success' => false,
                'message' => 'Produk ini sudah tidak tersedia dan telah dihapus dari keranjang.',
            ], 404);
        }

        if ($quantity > $cartItem->product->stock) {
            return response()->json([
                'success' => false,
                'message' => "Kuantitas melebihi stok yang tersedia (stok: {$cartItem->product->stock}).",
            ], 400);
        }

        $cartItem->update(['quantity' => $quantity]);

        $items = CartItem::where('user_id', $user->id)
            ->with('product')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Kuantitas keranjang berhasil diperbarui.',
            'data' => $this->buildCartResponse($items),
        ], 200);
    }

    /**
     * DELETE /api/cart/{itemId}
     * Menghapus satu item dari keranjang milik user yang sedang login.
     */
    public function destroy(Request $request, int $itemId): JsonResponse
    {
        $user = $request->user();

        $cartItem = CartItem::where('id', $itemId)
            ->where('user_id', $user->id)
            ->first();

        if (! $cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan atau bukan milik Anda.',
            ], 404);
        }

        $cartItem->delete();

        $items = CartItem::where('user_id', $user->id)
            ->with('product')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang.',
            'data' => $this->buildCartResponse($items),
        ], 200);
    }

    /**
     * DELETE /api/cart
     * Mengosongkan seluruh keranjang milik user yang sedang login.
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();

        CartItem::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan.',
            'data' => [
                'items' => [],
                'totalItems' => 0,
                'totalPrice' => 0,
            ],
        ], 200);
    }
}

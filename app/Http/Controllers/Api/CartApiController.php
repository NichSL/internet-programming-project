<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartApiController extends Controller
{
    /**
     * Format a collection of cart items into the standard response structure.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $items
     * @return array{items: array, totalItems: int, totalPrice: int}
     */
    private function buildCartResponse($items): array
    {
        $formatted = $items->map(fn ($item) => [
            'id'          => $item->id,
            'productId'   => $item->product_id,
            'productName' => $item->product->name,
            'price'       => $item->product->price,
            'stock'       => $item->product->stock,
            'quantity'    => $item->quantity,
            'subtotal'    => $item->product->price * $item->quantity,
        ]);

        return [
            'items'      => $formatted->values(),
            'totalItems' => $formatted->sum('quantity'),
            'totalPrice' => $formatted->sum('subtotal'),
        ];
    }

    /**
     * GET /api/cart
     * Return the authenticated user's cart.
     */
    public function index(Request $request)
    {
        $items = Auth::user()->cartItems()->with('product')->get();

        return response()->json([
            'success' => true,
            'data'    => $this->buildCartResponse($items),
        ]);
    }

    /**
     * POST /api/cart
     * Add a product to the cart (or increase quantity if already present).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'productId' => 'required|integer',
            'quantity'  => 'required|integer|min:1',
        ]);

        $product = Product::find($validated['productId']);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        if ($product->stock < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah habis.',
            ], 400);
        }

        // Upsert: if item exists, increment; otherwise create
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $validated['quantity'];
            // Cap at available stock
            $newQty = min($newQty, $product->stock);
            $cartItem->update(['quantity' => $newQty]);
        } else {
            $qty = min($validated['quantity'], $product->stock);
            $cartItem = CartItem::create([
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
                'quantity'   => $qty,
            ]);
        }

        $items = Auth::user()->cartItems()->with('product')->get();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'data'    => $this->buildCartResponse($items),
        ], 201);
    }

    /**
     * PATCH /api/cart/{itemId}
     * Update quantity of a specific cart item (must belong to authenticated user).
     */
    public function update(Request $request, int $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('id', $itemId)
            ->where('user_id', Auth::id())
            ->with('product')
            ->first();

        if (! $cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan.',
            ], 404);
        }

        // Cap at available stock
        $newQty = min($validated['quantity'], $cartItem->product->stock);
        $cartItem->update(['quantity' => $newQty]);

        $items = Auth::user()->cartItems()->with('product')->get();

        return response()->json([
            'success' => true,
            'message' => 'Kuantitas berhasil diperbarui.',
            'data'    => $this->buildCartResponse($items),
        ]);
    }

    /**
     * DELETE /api/cart/{itemId}
     * Remove a specific item from the cart (must belong to authenticated user).
     */
    public function destroy(int $itemId)
    {
        $cartItem = CartItem::where('id', $itemId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan.',
            ], 404);
        }

        $cartItem->delete();

        $items = Auth::user()->cartItems()->with('product')->get();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang.',
            'data'    => $this->buildCartResponse($items),
        ]);
    }

    /**
     * DELETE /api/cart
     * Clear the authenticated user's entire cart.
     */
    public function clear()
    {
        Auth::user()->cartItems()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan.',
            'data'    => ['items' => [], 'totalItems' => 0, 'totalPrice' => 0],
        ]);
    }
}

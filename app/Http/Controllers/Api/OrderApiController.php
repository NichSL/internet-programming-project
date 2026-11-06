<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    /**
     * GET /api/orders/my-orders
     * Return ALL orders belonging to the authenticated user only.
     * User identity is derived from the authenticated session/token — never from request params.
     */
    public function myOrders()
    {
        $orders = Order::where('buyer_id', Auth::id())
            ->with('product.user')   // product + seller store info
            ->latest()
            ->get()
            ->map(fn ($order) => [
                'id'           => $order->id,
                'orderNumber'  => 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'date'         => $order->created_at->translatedFormat('d F Y'),
                'dateTime'     => $order->created_at->toIso8601String(),
                'status'       => $order->status,
                'productId'    => $order->product_id,
                'productName'  => $order->product->name ?? '(Produk dihapus)',
                'storeName'    => $order->product->user->store_name ?? 'Toko',
                'price'        => $order->total_price / max($order->quantity, 1), // per-unit snapshot
                'quantity'     => $order->quantity,
                'totalPrice'   => $order->total_price,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pesanan berhasil diambil.',
            'data'    => $orders,
        ]);
    }

    /**
     * GET /api/orders/{id}
     * Return a specific order — but ONLY if it belongs to the authenticated user.
     * Returns 404 (not 403) to avoid leaking whether an order exists for another user.
     */
    public function show(int $id)
    {
        $order = Order::where('id', $id)
            ->where('buyer_id', Auth::id())   // ← ownership enforced on the backend
            ->with('product.user')
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $data = [
            'id'          => $order->id,
            'orderNumber' => 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
            'date'        => $order->created_at->translatedFormat('d F Y, H:i'),
            'dateTime'    => $order->created_at->toIso8601String(),
            'status'      => $order->status,
            'productId'   => $order->product_id,
            'productName' => $order->product->name ?? '(Produk dihapus)',
            'storeName'   => $order->product->user->store_name ?? 'Toko',
            'price'       => $order->total_price / max($order->quantity, 1),
            'quantity'    => $order->quantity,
            'totalPrice'  => $order->total_price,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail pesanan berhasil diambil.',
            'data'    => $data,
        ]);
    }
}

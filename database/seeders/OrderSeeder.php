<?php

namespace Database\Seeders;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buyerNiko = User::where('username', 'Akuganteng123')->first();
        $buyerQwe = User::where('username', 'qwe')->first();

        if (! $buyerNiko && ! $buyerQwe) {
            return;
        }

        // Ambil beberapa produk untuk contoh transaksi
        $mouse = Product::where('name', 'like', '%Mouse%')->first();
        $keyboard = Product::where('name', 'like', '%Keyboard%')->first();
        $kaos = Product::where('name', 'like', '%Kaos%')->first();
        $hoodie = Product::where('name', 'like', '%Hoodie%')->first();

        // 1. Contoh Pesanan untuk Akuganteng123
        if ($buyerNiko && $mouse) {
            $qty = 1;
            Order::firstOrCreate(
                [
                    'buyer_id' => $buyerNiko->id,
                    'seller_id' => $mouse->user_id,
                    'product_id' => $mouse->id,
                ],
                [
                    'quantity' => $qty,
                    'total_price' => $mouse->price * $qty,
                    'status' => 'selesai',
                    'created_at' => now()->subDays(3),
                    'updated_at' => now()->subDays(3),
                ]
            );
        }

        if ($buyerNiko && $kaos) {
            $qty = 2;
            Order::firstOrCreate(
                [
                    'buyer_id' => $buyerNiko->id,
                    'seller_id' => $kaos->user_id,
                    'product_id' => $kaos->id,
                ],
                [
                    'quantity' => $qty,
                    'total_price' => $kaos->price * $qty,
                    'status' => 'selesai',
                    'created_at' => now()->subDays(1),
                    'updated_at' => now()->subDays(1),
                ]
            );
        }

        // 2. Contoh Pesanan untuk tester qwe
        if ($buyerQwe && $keyboard) {
            $qty = 1;
            Order::firstOrCreate(
                [
                    'buyer_id' => $buyerQwe->id,
                    'seller_id' => $keyboard->user_id,
                    'product_id' => $keyboard->id,
                ],
                [
                    'quantity' => $qty,
                    'total_price' => $keyboard->price * $qty,
                    'status' => 'selesai',
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ]
            );
        }

        // 3. Contoh Item Keranjang Belanja Aktif (untuk mendemokan Cart di UI)
        if ($buyerNiko && $hoodie) {
            CartItem::firstOrCreate(
                [
                    'user_id' => $buyerNiko->id,
                    'product_id' => $hoodie->id,
                ],
                [
                    'quantity' => 1,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellerBudi = User::where('username', 'seller_budi')->first()
            ?? User::where('role', 'seller')->first();

        $sellerSiti = User::where('username', 'seller_siti')->first()
            ?? User::where('role', 'seller')->latest()->first();

        if (! $sellerBudi) {
            return;
        }

        $products = [
            // Produk Toko Elektronik Jaya (Budi)
            [
                'user_id' => $sellerBudi->id,
                'name' => 'Laptop ASUS VivoBook 14',
                'description' => 'Laptop ringkas dan andal dengan Intel Core i5, RAM 8GB, SSD 512GB NVMe, dan layar 14 inci FHD anti-glare. Cocok untuk produktivitas kerja, coding, dan kuliah.',
                'price' => 8200000,
                'stock' => 15,
            ],
            [
                'user_id' => $sellerBudi->id,
                'name' => 'Keyboard Mechanical RGB 87 Keys',
                'description' => 'Keyboard mekanikal tenkeyless (TKL) dengan switch blue clicky responsif, pencahayaan RGB 18 mode, dan keycaps double-shot PBT tahan aus.',
                'price' => 450000,
                'stock' => 35,
            ],
            [
                'user_id' => $sellerBudi->id,
                'name' => 'Wireless Gaming Mouse 16000 DPI',
                'description' => 'Mouse gaming nirkabel ultra-ringan dengan sensor optik presisi tinggi hingga 16.000 DPI, konektivitas 2.4GHz tanpa jeda, dan baterai isi ulang.',
                'price' => 320000,
                'stock' => 50,
            ],
            [
                'user_id' => $sellerBudi->id,
                'name' => 'Monitor IPS 24 Inch 144Hz',
                'description' => 'Monitor gaming bezel-less 24 inci panel IPS Full HD 1080p dengan refresh rate 144Hz, response time 1ms MPRT, dan dukungan AMD FreeSync.',
                'price' => 1950000,
                'stock' => 20,
            ],
            [
                'user_id' => $sellerBudi->id,
                'name' => 'Headset Gaming 7.1 Surround Sound',
                'description' => 'Headset gaming dengan driver audio 50mm, virtual 7.1 surround sound, mikrofon omnidirectional noise-cancelling, dan busa memori nyaman.',
                'price' => 385000,
                'stock' => 40,
            ],
            [
                'user_id' => $sellerBudi->id,
                'name' => 'Webcam Full HD 1080p with Mic',
                'description' => 'Webcam USB plug and play resolusi 1080p @30fps dengan mikrofon ganda stereo dan fitur auto light correction untuk meeting dan streaming.',
                'price' => 275000,
                'stock' => 25,
            ],

            // Produk Siti Fashion Corner (Siti)
            [
                'user_id' => ($sellerSiti ?? $sellerBudi)->id,
                'name' => 'Kaos Polos Cotton Combed 30s Hitam',
                'description' => 'Kaos polos kasual berbahan 100% cotton combed 30s reaktif super lembut, menyerap keringat dengan baik, dan jahitan rantai standar distro.',
                'price' => 65000,
                'stock' => 100,
            ],
            [
                'user_id' => ($sellerSiti ?? $sellerBudi)->id,
                'name' => 'Jaket Hoodie Zipper Fleece Tebal',
                'description' => 'Hoodie zipper unisex bahan cotton fleece tebal 280 gsm hangat dan lembut, saku kanguru ganda, serta tali serut tudung berkualitas.',
                'price' => 175000,
                'stock' => 45,
            ],
            [
                'user_id' => ($sellerSiti ?? $sellerBudi)->id,
                'name' => 'Celana Chino Slim Fit Pria',
                'description' => 'Celana panjang chino model slim fit bahan katun twill stretch premium lentur, cocok untuk gaya formal kantor maupun hangout kasual santai.',
                'price' => 140000,
                'stock' => 60,
            ],
            [
                'user_id' => ($sellerSiti ?? $sellerBudi)->id,
                'name' => 'Tas Ransel Laptop Waterproof 20L',
                'description' => 'Tas ransel harian dengan kompartemen laptop hingga 15.6 inci, bahan oxford tahan air, kantong multifungsi tersembunyi, dan port USB charger.',
                'price' => 210000,
                'stock' => 30,
            ],
            [
                'user_id' => ($sellerSiti ?? $sellerBudi)->id,
                'name' => 'Sepatu Sneakers Kasual Putih',
                'description' => 'Sneakers kanvas sol karet vulkanisir anti-slip, insole empuk nyaman untuk pemakaian sehari-hari dengan desain clean minimalis.',
                'price' => 260000,
                'stock' => 20,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                [
                    'user_id' => $productData['user_id'],
                    'name' => $productData['name'],
                ],
                $productData
            );
        }
    }
}

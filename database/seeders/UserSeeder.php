<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator Niko',
                'username' => 'admin_niko',
                'email' => 'admin@markethub.com',
                'password' => Hash::make('adminniko123'),
                'role' => 'admin',
                'store_name' => null,
                'store_address' => null,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'seller_budi',
                'email' => 'seller_budi@markethub.com',
                'password' => Hash::make('12345678'),
                'role' => 'seller',
                'store_name' => 'Toko Elektronik Jaya',
                'store_address' => 'Jl. Sudirman No. 12, Jakarta Pusat',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Siti Rahma',
                'username' => 'seller_siti',
                'email' => 'seller_siti@markethub.com',
                'password' => Hash::make('12345678'),
                'role' => 'seller',
                'store_name' => 'Siti Fashion Corner',
                'store_address' => 'Jl. Malioboro No. 88, Yogyakarta',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Niko Pratama',
                'username' => 'Akuganteng123',
                'email' => 'akuganteng@example.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'store_name' => null,
                'store_address' => null,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Tester Qwe',
                'username' => 'qwe',
                'email' => 'qwe@example.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'store_name' => null,
                'store_address' => null,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }
    }
}

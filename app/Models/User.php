<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 1. Tambahkan pemanggilan ini

class User extends Authenticatable
{
    // 2. Sisipkan HasApiTokens di sini
    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'store_name',
        'store_address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    
    // History belanja (sebagai pembeli)
    public function purchases() {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // History penjualan (sebagai penjual)
    public function sales()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    // Keranjang belanja
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
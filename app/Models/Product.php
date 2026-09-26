<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Panggil class SoftDeletes

class Product extends Model
{
    use HasFactory, SoftDeletes; // 2. Tambahkan di dalam class

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'stock',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
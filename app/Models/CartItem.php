<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity'];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Subtotal calculated from the live product price (never trusted from frontend).
     */
    public function getSubtotalAttribute(): int
    {
        return $this->product ? $this->product->price * $this->quantity : 0;
    }
}

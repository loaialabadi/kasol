<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'cart_id',
        'add_id',
        'quantity',
        'price',
        'total_price',
        'size_id',
        'discount_price'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->with('sizes','service');
    }

    public function add()
    {
        return $this->belongsTo(Add::class);
    }
}

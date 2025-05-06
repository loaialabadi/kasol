<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\Add;
use App\Models\Size;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'add_id',
        'quantity',
        'price',
        'discount_price',
        'total_price',
        'type',
        'product_id',
        'service_id', 
        'user_lat',
        'user_long',
        'size_id'
    ];
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    public function products()
    {
        return $this->belongsTo(Product::class)->with('sizes', 'service');
    }
    public function adds()
    {
        return $this->hasMany(Add::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->hasOne(Order::class);
    }
    public function getTotalPriceAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function add(){
        return $this->belongsTo(Add::class);
    }
    public function size(){
        return $this->belongsTo(Size::class);
    }
}

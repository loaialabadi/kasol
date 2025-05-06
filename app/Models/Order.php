<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderItems;
use App\Models\Branch;
use App\Models\Delivery;
use App\Models\DeliveryOrders;
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_price',
        'payment_method',
        'shipping_address',
        'notes',
        'receiving_method',
        'service_id',
        'online_method',
        'cart_id',
        'branch_id',
        'delivery_cost',
        'status',
        'pay_order_id',
        'payment_status',
        'delivery_id',
        'discount_price',
        'money',
        'user_long',
        'order_notes',
        'user_lat',
        'fcm_token',
        'size_id',
        'show_ord'
    ];
    public $casts=[
            'notes'=>'string',
            'money'=>'float',
            'created_at'=>'datetime'
        ];
    /**
     * Relationship to the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function cartItems()
    // {
    //     return $this->hasMany(CartItem::class);
    // }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function order_items(){
        return $this->hasMany(OrderItems::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class)->select('id','service_id','name','image','address','phone','lat','long');
    }
    
        public function delivery(){
        return $this->belongsTo(Delivery::class);
    }

    public function deliveryorder(){
        return $this->belongsTo(DeliveryOrders::class,'order_id');
    }
  
}
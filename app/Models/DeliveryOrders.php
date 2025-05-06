<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrders extends Model
{
    public $fillable=['delivery_id','order_id','assigned_date'];
    public function delivery(){
        return $this->belongsTo(Delivery::class,'delivery_id');
    }
    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
}
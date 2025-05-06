<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderItems;
use App\Models\Branch;
use App\Models\Delivery;
use App\Models\DeliveryOrders;
class ServiceOrder extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
       'service_id',
       'order_id',
       'order_value',
       'pay_method',
       'discount_price',
       'company_ratio',
       'date',
    ];
    public $casts=[
            'order_value'=>'double',
            'company_ratio'=>'double',
        ];
    

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
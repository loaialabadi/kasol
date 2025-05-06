<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Service;
class DeliveryWallets extends Model
{
    use HasFactory;
    public $table='delivery_wallets';
    protected $fillable = [
        'delivery_id',
        'order_id',
        'delivery_cost',
        'pay_method',
        'service_id',
        'branch_id',
        'user_long',
        'user_lat',
        'date',
        'total',
    ];
    public function branch(){
        return $this->belongsTo(Branch::class);
    }
    public function service(){
        return $this->belongsTo(Service::class);
    }
    
    public function order(){
        return $this->belongsTo(Order::class);
    }
    
}

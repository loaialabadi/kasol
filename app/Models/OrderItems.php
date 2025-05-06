<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\Add;
use App\Models\Size;
class OrderItems extends Model
{
    use HasFactory;
    
    public $timestamps=false;
    public $table='order_items';

    protected $fillable = [
        'order_id', 
        'product_id',
        'add_id',
        'quantity',
        'price',
        'size_id',
        'discount_price',
        'total_price',
        'type',
    ];  
    public function product(){
        return $this->belongsTo(Product::class)->select('id','name','slug','description','image','price','status');
    }  
    public function add(){
        return $this->belongsTo(Add::class)->select('id','service_id','name','image','wight','price');
    }
    public function size(){
        return $this->belongsTo(Size::class);
    }
    /**
     * Relationship to the User model.
     */
  
}
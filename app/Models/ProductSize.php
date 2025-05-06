<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Size;
class ProductSize extends Model
{
    use HasFactory;
    public $table='product_size';
    protected $fillable = ['product_id', 'size_id','price'];
    public $casts=[
            'price'=>'double'
        ];
    public function size(){
        return $this->belongsTo(Size::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}

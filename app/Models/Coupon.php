<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'much_used',
        'status',
        'limit_use',
    ];
    public $casts=[
            'value'=>'integer',
            'limit_use'=>'integer',
            'much_used'=>'integer',
        ];
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}

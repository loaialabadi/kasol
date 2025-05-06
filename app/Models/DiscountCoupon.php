<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCoupon extends Model
{
    protected $table = "discount_coupons";
    protected $fillable = [
        'code',
        'discount',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'baner',
        'logo',
        'menu',
        'rating',
        'status',
        'description',
        'start_work_date',
        'end_work_date',
        'price_range',
        'image_id',
    ];

    public function images()
    {
        return $this->hasMany(Images::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->whereHas('products', function ($productQuery) use ($filters) {
                if (isset($filters['min_price'])) {
                    $productQuery->where('price', '>=', $filters['min_price']);
                }
                if (isset($filters['max_price'])) {
                    $productQuery->where('price', '<=', $filters['max_price']);
                }
            });
        }

        if (isset($filters['subCategory'])) {
            $query->whereHas('products', function ($productQuery) use ($filters) {
                $productQuery->where('sub_category_id', $filters['subCategory']);
            });
        }

        if (isset($filters['address'])) {
            $query->where('address', 'LIKE', '%' . $filters['address'] . '%');
        }

        return $query;
    }
}

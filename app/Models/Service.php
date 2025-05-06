<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
class Service extends Model
{
    use HasFactory,HasApiTokens;
    
    
        protected $appends = ['baner_url', 'logo_url', 'menu_url'];
        
        
         public function getBanerUrlAttribute()
    {
        return $this->baner ? asset(Storage::url($this->baner)) : null;
    }

    // Accessor for Logo
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset(Storage::url($this->logo)) : null;
    }

    // Accessor for Menu
    public function getMenuUrlAttribute()
    {
        return $this->menu ? asset(Storage::url($this->menu)) : null;
    }


    protected $fillable = [
        'name',
        'address',
        'phone',
        'money',
        'email',
        'baner',
        'service_ratio',
        'fcm_token',
        'open_status',
        'logo',
        'has_delivery',
        'menu',
        'rating',
        'status',
        'user_id',
        'description',
        'start_work_date',
        'end_work_date',
        'price_range',
        'password',
        'image_id',
        'category_id'
    ];

    public function images()
    {
        return $this->hasMany(Images::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function adds()
    {
        return $this->hasMany(Add::class);
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
        return $this->belongsTo(User::class)->with('stories');
    }
    public function stories()
    {
        return $this->hasMany(Story::class);
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

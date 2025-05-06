<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'discount_rate',
        'start_date',
        'end_date',
        'price',
        'descounted_price',
        'stars_rate',
        'created_by',
        'status',
        'service_id',
        'product_id',
        'user_id',
        'image_id',
    ];
    
    
          protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Check if the image is stored in the public disk
            return asset('storage/' . $this->image);
        }
        return null;
    }



    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function image()
    {
        return $this->belongsTo(Images::class);
    }
      public function images(){
        return $this->hasMany(Images::class);
    }
}

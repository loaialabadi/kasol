<?php

namespace App\Models;
use App\Models\ProductSize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'price',
        'status',
        'image_id',
        'sub_category_id',
        'service_id',
        'user_id',
        'add_id',
    ];
public $casts=[
        'id'=>'integer',
        'service_id'=>'integer',
        'add_id'=>'integer',
        'sub_category_id'=>'integer',
        'price'=>'float',
        'image_id'=>'integer',
        'user_id'=>'integer',
    ];
    public function image()
    {
        return $this->belongsTo(Images::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->with('branches');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adds()
    {
        return $this->hasMany(Add::class);
    }
    public function productsize(){
        return $this->hasMany(ProductSize::class);
    }
    public function sizes()
    {
        return $this->belongsToMany(Size::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}

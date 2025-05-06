<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Branch extends Authenticatable 
{
    use HasFactory, SoftDeletes,HasApiTokens;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'image',
        'start_work_date',
        'fcm_token',
        'end_work_date',
        'lat',
        'long',
        'password',
        'id',
        'service_id',
        'user_name'
        
    ];
        // protected $appends = ['image'];

    //   public function image(): Attribute
    // {
    //     return new Attribute(function () {
    //         return $this->image;
    //         // return  asset();
    //     });
    // }


    /**
     * Relationship to the Image model.
     */
    public function services()
    {
        return $this->belongsTo(Service::class);
    }
}
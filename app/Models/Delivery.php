<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Delivery extends Authenticatable
{
    use HasApiTokens;
    public $fillable=['name','lat','long','phone','email','password','ban','fcm_token','money'];
    protected $hidden = [
        'password',
    ];
}

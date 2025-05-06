<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    public $timestamps=false;
    public $fillable=['terms_and_conds','email','phone','about_app','delivery_price','start_order_price'];
}
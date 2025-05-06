<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftsUser extends Model
{
    //
    public $table='gift_users';
    public $fillable=['user_id','gift_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function gift(){
        return $this->belongsTo(Gift::class);
    }
}
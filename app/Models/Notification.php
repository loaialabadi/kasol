<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    public $fillable = ['title','content','type','user_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }

}
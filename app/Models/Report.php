<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
        public $fillable=['status','user_id','text','service_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function service(){
        return $this->belongsTo(Service::class);
    }
}
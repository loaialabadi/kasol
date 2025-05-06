<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSeen extends Model
{
    //
    public $fillable = ['user_id','notification_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function notification(){
        return $this->belongsTo(Notification::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function Service(){
        return $this->belongsTo(Service::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Images extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['image', 'service_id','offer_id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

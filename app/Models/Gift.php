<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gift extends Model
{
    //
    use SoftDeletes;
    public $fillable=['status','type','discount'];
    public $casts=[
        'discount'=>'float'
    ];
}
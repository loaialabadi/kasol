<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissions extends Model
{
    use HasFactory;
public $timestamps=false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $table='role_has_permissions';
    protected $fillable = [
        'permission_id',
        'role_id',
    ];
    public function role(){
        return $this->belongsTo(Role::class);
    }
    public function permission(){
        return $this->belongsTo(Permission::class);
    }
}
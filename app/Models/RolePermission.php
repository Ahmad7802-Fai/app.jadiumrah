<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'role_id',
        'perm_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Relasi ke roles
    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }

    // Relasi ke permissions
    public function permission()
    {
        return $this->belongsTo(\App\Models\Permission::class, 'perm_id');
    }
}

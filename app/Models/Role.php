<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['role_name','description'];

    public function permissions()
    {
        // pivot table role_permissions (role_id, perm_id)
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'perm_id');
    }
}

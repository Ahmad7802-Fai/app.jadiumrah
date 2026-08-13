<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false; // jika tabel permission tidak pakai timestamps
    protected $fillable = ['perm_key','perm_name'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'perm_id', 'role_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';

    // Tabel ini tidak punya primary key auto increment
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Relasi ke role
    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }
}

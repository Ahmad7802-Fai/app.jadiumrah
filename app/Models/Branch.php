<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'alamat',
        'kota',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function agents()
    {
        return $this->hasManyThrough(
            Agent::class,
            User::class,
            'branch_id',   // FK di users
            'user_id',     // FK di agents
            'id',
            'id'
        );
    }

    public function jamaah()
    {
        return $this->hasMany(Jamaah::class, 'branch_id');
    }

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return "{$this->kode_cabang} - {$this->nama_cabang}";
    }
}

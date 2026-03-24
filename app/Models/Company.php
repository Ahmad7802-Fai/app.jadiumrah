<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'company_menu')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
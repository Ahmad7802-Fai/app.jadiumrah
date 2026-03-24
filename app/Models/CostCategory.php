<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CostCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',       // operational / marketing / departure / etc
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function costs()
    {
        return $this->hasMany(Cost::class);
    }
}
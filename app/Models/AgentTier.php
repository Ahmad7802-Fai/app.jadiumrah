<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentTier extends Model
{
    protected $fillable = [
        'name',
        'min_closing',
        'max_closing',
        'bonus_percentage',
    ];

    protected $casts = [
        'bonus_percentage' => 'decimal:2',
    ];
}
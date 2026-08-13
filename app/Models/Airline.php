<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'code',
        'name',
        'country',
        'is_active',
    ];
}

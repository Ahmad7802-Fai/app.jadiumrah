<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = [
        'country',
        'city',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function paketDestinations()
    {
        return $this->hasMany(PaketDestination::class);
    }

    public function pakets()
    {
        return $this->belongsToMany(
            Paket::class,
            'paket_destinations'
        )
        ->withPivot('day_order', 'note');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingLock extends Model
{
    protected $fillable = [
        'paket_departure_id',
        'user_id',
        'qty',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('expired_at', '>', now());
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */
    public function departure()
    {
        return $this->belongsTo(PaketDeparture::class, 'paket_departure_id');
    }
}
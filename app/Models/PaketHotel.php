<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketHotel extends Model
{
    protected $fillable = [
        'paket_id',
        'city',
        'hotel_name',
        'rating',
        'distance_to_haram',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }
}
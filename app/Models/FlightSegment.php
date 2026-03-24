<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSegment extends Model
{
    protected $fillable = [
        'flight_id',
        'segment_order',
        'origin',
        'destination',
        'departure_time',
        'arrival_time',
        'terminal',
        'gate',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time'   => 'datetime',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
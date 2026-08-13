<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $fillable = [
        'airline',
        'flight_number',
        'aircraft_type',
        'aircraft_capacity',
        'is_charter',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'is_charter' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function segments()
    {
        return $this->hasMany(FlightSegment::class)
                    ->orderBy('segment_order');
    }

    public function departures()
    {
        return $this->belongsToMany(
            \App\Models\PaketDeparture::class,
            'departure_flight',
            'flight_id',      // foreign key di pivot untuk model ini
            'departure_id'    // foreign key di pivot untuk model departure
        )->withPivot('type')
        ->withTimestamps();
    }

    public function seatAllocations()
    {
        return $this->hasMany(SeatAllocation::class);
    }
}
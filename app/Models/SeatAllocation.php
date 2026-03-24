<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatAllocation extends Model
{
    protected $fillable = [
        'flight_id',
        'departure_id',
        'total_seat',
        'blocked_seat',
        'used_seat',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function departure()
    {
        return $this->belongsTo(PaketDeparture::class, 'departure_id');
    }

    public function getAvailableSeatAttribute()
    {
        return $this->total_seat
             - $this->blocked_seat
             - $this->used_seat;
    }
}

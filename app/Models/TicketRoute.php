<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketRoute extends Model
{
    protected $table = 'ticket_routes';

    protected $fillable = [
        'pnr_id',
        'flight_number',
        'sector',
        'origin',
        'destination',
        'departure_date',
        'departure_time',
        'arrival_time',
        'arrival_day_offset',
    ];

    /* ======================================================
     | CASTING (INI YANG HILANG)
     ====================================================== */
    protected $casts = [
        'departure_date' => 'date',

    ];

    public function pnr()
    {
        return $this->belongsTo(TicketPnr::class, 'pnr_id');
    }
}



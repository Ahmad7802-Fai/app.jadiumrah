<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPnrRoute extends Model
{
    protected $table = 'ticket_routes';

    protected $fillable = [
        'pnr_id',
        'sector',
        'flight_number',
        'origin',
        'destination',
        'departure_date',
    ];

    protected $casts = [
        'departure_date' => 'date',
    ];

    public function pnr()
    {
        return $this->belongsTo(TicketPnr::class, 'pnr_id');
    }
}

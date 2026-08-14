<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAllocation extends Model
{
    protected $table = 'ticket_allocations';

    protected $fillable = [
        'pnr_id',
        'allocated_amount',
        'allocation_date',
        'status',
    ];

    public function pnr()
    {
        return $this->belongsTo(TicketPnr::class, 'pnr_id');
    }

}

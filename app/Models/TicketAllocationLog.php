<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAllocationLog extends Model
{
    protected $table = 'ticket_allocation_logs';

    protected $fillable = [
        'allocation_id',
        'received_amount',
        'received_date',
    ];

    public function allocation()
    {
        return $this->belongsTo(TicketAllocation::class, 'allocation_id');
    }
}

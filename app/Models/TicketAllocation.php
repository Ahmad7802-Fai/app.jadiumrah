<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAllocation extends Model
{
    protected $table = 'ticket_allocations';

    protected $fillable = [
        'pnr_id',
        'allocation_code',
        'allocated_amount',
        'allocation_date',
        'status',
    ];

    public function pnr()
    {
        return $this->belongsTo(TicketPnr::class, 'pnr_id');
    }

    public function logs()
    {
        return $this->hasMany(TicketAllocationLog::class, 'allocation_id');
    }

    public function invoice()
    {
        return $this->belongsTo(
            TicketInvoice::class,
            'ticket_invoice_id'
        );
    }

}

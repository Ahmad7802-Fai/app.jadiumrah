<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketInvoiceLog extends Model
{
    protected $table = 'ticket_invoice_logs';

    protected $fillable = [
        'ticket_invoice_id',
        'action',
        'description',
        'created_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(TicketInvoice::class);
    }
}

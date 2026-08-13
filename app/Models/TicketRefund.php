<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketRefund extends Model
{
    protected $table = 'ticket_refunds';

    public $timestamps = false; // ⛔ MATIKAN DEFAULT created_at

    protected $fillable = [
        'ticket_invoice_id',
        'amount',
        'reason',
        'status',
        'approval_status',
        'refunded_at',
        'refunded_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'refunded_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(TicketInvoice::class, 'ticket_invoice_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

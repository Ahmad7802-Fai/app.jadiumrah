<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPayment extends Model
{
    protected $table = 'ticket_payments';

    protected $fillable = [
        'ticket_invoice_id',
        'payment_date',
        'amount',
        'method',
        'bank',
        'reference',
        'receipt_file',
        'status',
        'created_by',
    ];
    protected $casts = [
        'payment_date' => 'date',
    ];

    public $timestamps = false; // 🔥 WAJIB
    public function invoice()
    {
        return $this->belongsTo(
            \App\Models\TicketInvoice::class,
            'ticket_invoice_id'
        );
    }

}

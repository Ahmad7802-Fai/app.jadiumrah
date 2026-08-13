<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketInvoiceItem extends Model
{
    protected $table = 'ticket_invoice_items';

    protected $fillable = [
        'ticket_invoice_id',
        'description',
        'qty',
        'unit_price',
        'subtotal',
    ];

    public $timestamps = false; // 🔥 INI PENTING

public function invoice()
{
    return $this->belongsTo(
        TicketInvoice::class,
        'ticket_invoice_id'
    );
}

}

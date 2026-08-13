<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    protected $table = 'vendor_payments';

    protected $fillable = [
        'layanan_item_id',
        'vendor_name',
        'invoice_number',
        'amount',
        'currency',
        'payment_method',
        'bank',
        'reference_no',
        'payment_date',
        'proof_file',
        'notes',
    ];

    public function layananItem()
    {
        return $this->belongsTo(LayananItem::class, 'layanan_item_id');
    }
    public function layanan_item()
    {
        return $this->belongsTo(\App\Models\LayananItem::class, 'layanan_item_id');
    }

}

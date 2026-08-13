<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'service_invoice_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_invoice',
        'id_layanan_transaksi',
        'qty',
        'harga',
        'subtotal',
    ];

    protected $casts = [
        'qty'     => 'integer',
        'harga'   => 'decimal:2',
        'subtotal'=> 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Item milik 1 service invoice
    public function invoice()
    {
        return $this->belongsTo(\App\Models\ServiceInvoice::class, 'id_invoice');
    }

    // Item terkait layanan transaksi tertentu
    public function layananTransaksi()
    {
        return $this->belongsTo(\App\Models\LayananTransaksi::class, 'id_layanan_transaksi');
    }
}

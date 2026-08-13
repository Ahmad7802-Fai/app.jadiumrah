<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceInvoice extends Model
{
    use HasFactory;

    protected $table = 'service_invoices';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_client',
        'nomor_invoice',
        'tanggal',
        'jatuh_tempo',
        'total',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'jatuh_tempo'  => 'date',
        'total'        => 'decimal:2',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Invoice milik 1 client
    public function client()
    {
        return $this->belongsTo(\App\Models\Clients::class, 'id_client');
    }

    // Invoice punya banyak item
    public function items()
    {
        return $this->hasMany(\App\Models\ServiceInvoiceItem::class, 'id_invoice');
    }

    // Invoice → banyak layanan transaksi via items
    public function layananTransaksi()
    {
        return $this->hasManyThrough(
            \App\Models\LayananTransaksi::class,
            \App\Models\ServiceInvoiceItem::class,
            'id_invoice',            // FK di service_invoice_items
            'id',                    // PK layanan_transaksi
            'id',                    // PK service_invoices
            'id_layanan_transaksi'   // FK ke layanan_transaksi
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS / HELPERS
    |--------------------------------------------------------------------------
    */

    // Hitung total otomatis (jika tidak mau pakai query manual)
    public function hitungTotal()
    {
        return $this->items()->sum('subtotal');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceInstallments extends Model
{
    use HasFactory;

    protected $table = 'invoice_installments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_id',
        'tanggal',
        'jumlah',
        'metode',
        'bukti_transfer',
        'status',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'jumlah'     => 'integer',
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Cicilan milik 1 invoice
    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoices::class, 'invoice_id');
    }
}

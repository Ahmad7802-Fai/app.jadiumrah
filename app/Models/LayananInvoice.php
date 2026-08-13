<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LayananInvoice extends Model
{
    use HasFactory;

    protected $table = 'layanan_invoices';

    protected $fillable = [
        'no_invoice',
        'id_transaksi',   // FIX – foreign key yang benar
        'amount',
        'currency',
        'paid_amount',
        'status',
        'due_date'
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date'    => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];


    /* ============================================================
       RELATIONSHIPS F7 FINAL
    ============================================================ */

    /** Transaksi layanan terkait */
    public function transaksi()
    {
        return $this->belongsTo(\App\Models\LayananTransaksi::class, 'id_transaksi');
    }


    /** Client → lewat transaksi (lebih aman daripada hasOneThrough) */
    public function client()
    {
        return $this->transaksi ? $this->transaksi->client : null;
    }

    /** Pembayaran invoice */
    public function payments()
    {
        return $this->hasMany(LayananPayment::class, 'layanan_invoice_id');
    }


    /* ============================================================
       ACCESSORS F7
    ============================================================ */

    public function getSisaAttribute(): float
{
    return max(
        (float) $this->amount - (float) $this->paid_amount,
        0
    );
}

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'paid'      => 'Lunas',
            'partial'   => 'Sebagian',
            'cancelled' => 'Dibatalkan',
            default     => 'Belum Dibayar',
        };
    }

    public function getAmountFormattedAttribute()
    {
        return "Rp " . number_format($this->amount, 0, ',', '.');
    }

    public function getPaidFormattedAttribute()
    {
        return "Rp " . number_format($this->paid_amount, 0, ',', '.');
    }

    public function getSisaFormattedAttribute()
    {
        return "Rp " . number_format($this->sisa, 0, ',', '.');
    }


    /* ============================================================
        SCOPES
    ============================================================ */

    public function scopeStatus($q, $status)
    {
        if ($status) {
            $q->where('status', $status);
        }
    }

    public function scopeClientName($q, $name)
    {
        if ($name) {
            $q->whereHas('transaksi.client', function ($qc) use ($name) {
                $qc->where('nama', 'LIKE', "%$name%");
            });
        }
    }

    public function scopePeriode($q, $from, $to)
    {
        if ($from && $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }
    }

}

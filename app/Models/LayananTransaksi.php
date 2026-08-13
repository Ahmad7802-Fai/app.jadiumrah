<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LayananTransaksi extends Model
{
    use HasFactory;

    protected $table = 'layanan_transaksi';

    protected $fillable = [
        'id_client',
        'currency',
        'subtotal',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'subtotal'   => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /* ============================================================
       RELATIONSHIPS
    ============================================================ */

    /** Client pembeli layanan */
    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    /** Item layanan (hotel / tiket / lainnya) */
    public function items()
    {
        return $this->hasMany(LayananTransaksiItem::class, 'id_transaksi');
    }

    /** Relasi invoice layanan */
    public function invoice()
    {
        return $this->hasOne(\App\Models\LayananInvoice::class, 'id_transaksi');
    }


    /* ============================================================
       ACCESSORS
    ============================================================ */

    public function getSubtotalFormattedAttribute()
    {
        return "Rp " . number_format($this->subtotal, 0, ',', '.');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'   => 'Menunggu',
            'invoiced'  => 'Sudah Diinvoice',
            'paid'      => 'Lunas',
            'canceled'  => 'Dibatalkan',
            default     => 'Tidak diketahui',
        };
    }


    /* ============================================================
       SCOPES
    ============================================================ */

    public function scopeClientName($q, $name)
    {
        if ($name) {
            $q->whereHas('client', fn($c) => 
                $c->where('nama', 'LIKE', "%$name%")
            );
        }
    }

    public function scopeStatus($q, $status)
    {
        if ($status) {
            $q->where('status', $status);
        }
    }

    public function scopePeriode($q, $from, $to)
    {
        if ($from && $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }
    }


    /* ============================================================
       UTILITIES
    ============================================================ */

    /** Hitung ulang total transaksi berdasarkan items */
    public function recalcTotal()
    {
        return $this->items->sum(fn($i) => $i->qty * $i->days * $i->harga);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LayananPayment extends Model
{
    use HasFactory;

    protected $table = 'layanan_payments';

    protected $fillable = [
        'layanan_invoice_id',
        'amount',
        'currency',
        'bank',
        'reference_no',
        'payer_name',
        'proof_filename',
        'status',
        'validated_by',
        'validated_at',
        'validation_note',
        'payment_method',
        'catatan'
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'validated_at'   => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];


    /* ============================================================
       RELATIONSHIP PREMIUM
    ============================================================ */

    /**
     * Relasi ke invoice layanan
     */
    public function invoice()
    {
        return $this->belongsTo(LayananInvoice::class, 'layanan_invoice_id');
    }

    /**
     * Validator (user yang approve/reject)
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }


    /* ============================================================
       ACCESSORS F4 PREMIUM
    ============================================================ */

    public function getAmountFormattedAttribute()
    {
        return "Rp " . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'approved' => 'Approved',
            'pending'  => 'Pending',
            'rejected' => 'Rejected',
            default    => 'Unknown'
        };
    }

    public function getProofUrlAttribute()
    {
        return $this->proof_filename
            ? asset('storage/' . $this->proof_filename)
            : null;
    }


    /* ============================================================
       SCOPES PREMIUM
    ============================================================ */

    public function scopeStatus($q, $status)
    {
        if ($status) {
            $q->where('status', $status);
        }
    }

    public function scopeDate($q, $date)
    {
        if ($date) {
            $q->whereDate('created_at', $date);
        }
    }

    public function scopeInvoice($q, $keyword)
    {
        if ($keyword) {
            $q->whereHas('invoice', fn($i) =>
                $i->where('no_invoice', 'LIKE', "%$keyword%")
            );
        }
    }
}

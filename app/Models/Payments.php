<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jamaah_id',
        'invoice_id',
        'metode',
        'tanggal_bayar',
        'jumlah',
        'keterangan',
        'bukti_transfer',
        'status',
        'validated_by',
        'validated_at',
        'created_by',
        'edited_by',
        'edited_at',
        'is_deleted',
        'is_correction',
        'corrected_from'
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah'        => 'integer',
        'is_deleted'    => 'boolean',
        'is_correction' => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'validated_at'  => 'datetime',
        'edited_at'     => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Payment → Jamaah
    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    // Payment → Invoice
    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoices::class, 'invoice_id');
    }

    // Payment → PaymentLogs
    public function logs()
    {
        return $this->hasMany(\App\Models\PaymentLogs::class, 'payment_id')
                    ->orderBy('id', 'desc');
    }

    // Payment → User (creator)
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Payment → User (validator)
    public function validator()
    {
        return $this->belongsTo(\App\Models\User::class, 'validated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (Getters)
    |--------------------------------------------------------------------------
    */

    // Format jumlah pembayaran
    public function getJumlahFormatAttribute()
    {
        return "Rp " . number_format($this->jumlah, 0, ',', '.');
    }

    // Status label yang rapi
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'valid'   => 'Valid',
            'ditolak' => 'Ditolak',
            default   => '-'
        };
    }
    // =====================
    // CONSTANTS
    // =====================
    // =====================
    // STATUS CONSTANTS
    // =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_VALID   = 'valid';
    public const STATUS_DITOLAK = 'ditolak';

    // =====================
    // METODE CONSTANTS
    // =====================
    public const METODE_TRANSFER = 'transfer';
    public const METODE_CASH     = 'cash';
    public const METODE_KANTOR   = 'kantor';
    public const METODE_GATEWAY  = 'gateway';

    // ...lanjutkan isi model


    public const ACTION_INPUT_AGENT = 'INPUT_AGENT';
    public const ACTION_APPROVE     = 'APPROVE';
    public const ACTION_REJECT      = 'REJECTED';
    public const ACTION_CORRECT     = 'CORRECT';

    public const CONTEXT_AGENT      = 'AGENT';
    public const CONTEXT_KEUANGAN   = 'KEUANGAN';
    public const CONTEXT_SYSTEM    = 'SYSTEM';
    // Cek apakah sudah divalidasi

    public function getIsValidatedAttribute()
    {
        return $this->status === 'valid';
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeValid($q)
    {
        return $q->where('status', 'valid');
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeDitolak($q)
    {
        return $q->where('status', 'ditolak');
    }

    public function scopeByJamaah($q, $jamaahId)
    {
        return $q->where('jamaah_id', $jamaahId);
    }

    public function scopeByInvoice($q, $invoiceId)
    {
        return $q->where('invoice_id', $invoiceId);
    }
}

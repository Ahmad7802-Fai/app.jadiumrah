<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomisiLogs extends Model
{
    protected $table = 'komisi_logs';

    protected $fillable = [
        'jamaah_id',
        'agent_id',
        'branch_id',

        'mode',
        'komisi_persen',
        'komisi_nominal',

        'status',

        // payout & finance
        'payout_request_id',

        // approval
        'approved_at',
        'approved_by',

        // reject
        'rejected_at',
        'rejected_by',
        'reject_reason',

        // optional relasi pembayaran
        'payment_id',
        'invoice_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'requested_at'=> 'datetime',
        'rejected_at' => 'datetime',
    ];

    public $timestamps = true;

    /* ============================================================
     | STATUS CONSTANTS (SINGLE SOURCE OF TRUTH)
     ============================================================ */
    public const STATUS_PENDING   = 'pending';     // menunggu keuangan
    public const STATUS_AVAILABLE = 'available';   // siap diajukan payout
    public const STATUS_REQUESTED = 'requested';   // masuk payout
    public const STATUS_PAID      = 'paid';        // sudah dibayar
    public const STATUS_REJECTED  = 'rejected';    // ditolak keuangan

    /* ============================================================
     | RELATIONS
     ============================================================ */

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function payout()
    {
        return $this->belongsTo(
            AgentPayoutRequest::class,
            'payout_request_id'
        );
    }

    public function payment()
    {
        return $this->belongsTo(
            Payments::class,
            'payment_id'
        );
    }

    public function invoice()
    {
        return $this->belongsTo(
            Invoices::class,
            'invoice_id'
        );
    }

    /* ============================================================
     | ACCESSORS
     ============================================================ */

    public function getInvoiceNumberAttribute(): ?string
    {
        return $this->payment?->invoice?->nomor_invoice
            ?? $this->invoice?->nomor_invoice;
    }
}

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use App\Models\Payments;
// use App\Models\Invoices;
// class KomisiLogs extends Model
// {
//     protected $table = 'komisi_logs';

//     protected $fillable = [
//         'jamaah_id',
//         'agent_id',
//         'branch_id',
//         'mode',
//         'komisi_persen',
//         'komisi_nominal',
//         'status',
//     ];
//     protected $casts = [
//         'approved_at' => 'datetime',
//         'requested_at'=> 'datetime',
//     ];

//     public $timestamps = true;
//     public const STATUS_PENDING   = 'pending';
//     public const STATUS_AVAILABLE = 'available';
//     public const STATUS_REQUESTED = 'requested';
//     public const STATUS_PAID      = 'paid';
//     /*
//     |--------------------------------------------------------------------------
//     | RELATIONS (OPTIONAL, TAPI BERGUNA)
//     |--------------------------------------------------------------------------
//     */

//     /* =========================================
//      | RELATIONS
//      ========================================= */

//     public function jamaah()
//     {
//         return $this->belongsTo(Jamaah::class);
//     }

//     public function agent()
//     {
//         return $this->belongsTo(Agent::class);
//     }

//     public function payout()
//     {
//         return $this->belongsTo(
//             AgentPayoutRequest::class,
//             'payout_request_id'
//         );
//     }

//     // ✅ INI YANG HILANG
//     public function payment()
//     {
//         return $this->belongsTo(
//             Payments::class,
//             'payment_id'
//         );
//     }

//     // ✅ OPSIONAL (kalau ada invoice_id)
//     public function invoice()
//     {
//         return $this->belongsTo(
//             Invoices::class,
//             'invoice_id'
//         );
//     }
//     public function getInvoiceNumberAttribute()
//     {
//         return $this->payment?->invoice?->invoice_number;
//     }

// }

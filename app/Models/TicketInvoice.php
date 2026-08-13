<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketInvoice extends Model
{
    protected $table = 'ticket_invoices';

    protected $fillable = [
        'invoice_number',
        'pnr_id',
        'total_amount',
        'paid_amount',
        'refunded_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'total_amount'    => 'integer',
        'paid_amount'     => 'integer',
        'refunded_amount' => 'integer',
    ];

    /* ======================================================
     | RELATIONS
     ====================================================== */

    public function pnr()
    {
        return $this->belongsTo(TicketPnr::class, 'pnr_id');
    }

    public function items()
    {
        return $this->hasMany(
            TicketInvoiceItem::class,
            'ticket_invoice_id'
        );
    }

    public function payments()
    {
        return $this->hasMany(
            TicketPayment::class,
            'ticket_invoice_id'
        );
    }

    public function refunds()
    {
        return $this->hasMany(
            TicketRefund::class,
            'ticket_invoice_id'
        );
    }

    /* ======================================================
     | DERIVED (READ ONLY – FINAL SOURCE OF TRUTH)
     ====================================================== */

    /**
     * Total refund approved
     */
    public function getApprovedRefundAmountAttribute(): int
    {
        return $this->refunds()
            ->where('approval_status', 'APPROVED')
            ->sum('amount');
    }

    /**
     * Net paid (uang benar-benar diterima)
     */
    public function getNetPaidAttribute(): int
    {
        return max(
            0,
            $this->paid_amount - $this->approved_refund_amount
        );
    }

    /**
     * Outstanding (sisa tagihan)
     */
    public function getOutstandingAmountAttribute(): int
    {
        return max(
            0,
            $this->total_amount - $this->net_paid
        );
    }

    /**
     * Sisa maksimal yang masih bisa direfund
     */
    public function getRefundableAmountAttribute(): int
    {
        return max(
            0,
            $this->net_paid
        );
    }

    /* ======================================================
     | STATUS HELPERS (UI SAFE)
     ====================================================== */

    public function isPaid(): bool
    {
        return $this->status === 'PAID';
    }

    public function isPartial(): bool
    {
        return $this->status === 'PARTIAL';
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'UNPAID';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'REFUNDED';
    }

    public function allocations()
    {
        return $this->hasMany(
            TicketAllocation::class,
            'ticket_invoice_id'
        );
    }

}


// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class TicketInvoice extends Model
// {
//     protected $table = 'ticket_invoices';

//     protected $fillable = [
//         'invoice_number',
//         'pnr_id',
//         'total_amount',
//         'paid_amount',
//         'refunded_amount',
//         'status',
//         'created_by',
//     ];

//     protected $casts = [
//         'total_amount'    => 'integer',
//         'paid_amount'     => 'integer',
//         'refunded_amount' => 'integer',
//     ];

//     /* ======================================================
//      | RELATIONS
//      ====================================================== */

//     public function pnr()
//     {
//         return $this->belongsTo(TicketPnr::class, 'pnr_id');
//     }

//     public function items()
//     {
//         return $this->hasMany(
//             TicketInvoiceItem::class,
//             'ticket_invoice_id'
//         );
//     }

//     public function payments()
//     {
//         return $this->hasMany(
//             TicketPayment::class,
//             'ticket_invoice_id'
//         );
//     }

//     public function refunds()
//     {
//         return $this->hasMany(
//             TicketRefund::class,
//             'ticket_invoice_id'
//         );
//     }

//     /* ======================================================
//      | DERIVED / READ ONLY
//      ====================================================== */

//     /**
//      * Total yang masih bisa direfund
//      */
//     public function getRefundableAmountAttribute(): int
//     {
//         $approvedRefund = $this->refunds()
//             ->where('approval_status', 'APPROVED')
//             ->sum('amount');

//         return max(
//             0,
//             $this->paid_amount - $approvedRefund
//         );
//     }

//     /**
//      * Outstanding (read-only)
//      */
//     public function getOutstandingAmountAttribute(): int
//     {
//         return max(
//             0,
//             $this->total_amount - $this->paid_amount
//         );
//     }

//     /* ======================================================
//      | STATUS HELPERS (UI SAFE)
//      ====================================================== */

//     public function isPaid(): bool
//     {
//         return $this->status === 'PAID';
//     }

//     public function isPartial(): bool
//     {
//         return $this->status === 'PARTIAL';
//     }

//     public function isUnpaid(): bool
//     {
//         return $this->status === 'UNPAID';
//     }

//     public function isRefunded(): bool
//     {
//         return $this->status === 'REFUNDED';
//     }
// }


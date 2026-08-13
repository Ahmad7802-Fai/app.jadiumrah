<?php

namespace App\Observers;

use App\Models\TicketAllocation;
use App\Services\Ticketing\TicketAuditService;
use Illuminate\Support\Facades\DB;

class TicketAllocationObserver
{
    /**
     * Trigger saat allocation BARU dibuat
     */
    public function created(TicketAllocation $allocation): void
    {
        DB::transaction(function () use ($allocation) {

            /* ==================================================
             | GUARD: HARUS ADA INVOICE
             ================================================== */
            if (!$allocation->ticket_invoice_id) {
                return;
            }

            /* ==================================================
             | LOCK INVOICE (ANTI RACE)
             ================================================== */
            $invoice = $allocation->invoice()
                ->lockForUpdate()
                ->first();

            if (!$invoice) {
                return;
            }

            /* ==================================================
             | TOTAL ALLOCATION (ACTIVE ONLY)
             ================================================== */
            $totalAllocated = $invoice->allocations()
                ->where('status', 'ALLOCATED')
                ->sum('allocated_amount');

            /* ==================================================
             | DETERMINE STATUS
             ================================================== */
            $before = $invoice->getOriginal();

            if ($totalAllocated <= 0) {
                $status = 'UNPAID';
            }
            elseif ($totalAllocated < $invoice->total_amount) {
                $status = 'PARTIAL';
            }
            else {
                $status = 'PAID';
            }

            /* ==================================================
             | UPDATE INVOICE (SINGLE SOURCE)
             ================================================== */
            $invoice->update([
                'paid_amount' => min(
                    $totalAllocated,
                    $invoice->total_amount
                ),
                'status' => $status,
            ]);

            /* ==================================================
             | AUDIT
             ================================================== */
            TicketAuditService::log(
                'INVOICE',
                $invoice->id,
                'INVOICE_UPDATED_BY_ALLOCATION',
                $before,
                $invoice->fresh()->toArray()
            );
        });
    }
}

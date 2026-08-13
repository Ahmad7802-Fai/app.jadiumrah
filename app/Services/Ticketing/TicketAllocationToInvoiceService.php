<?php

namespace App\Services\Ticketing;

use App\Models\{
    TicketAllocation,
    TicketInvoice
};
use Illuminate\Support\Facades\DB;
use Exception;

class TicketAllocationToInvoiceService
{
    /* ======================================================
     | ATTACH ALLOCATION TO INVOICE
     | - Append only
     | - Tidak ubah invoice langsung
     ====================================================== */
    public function attach(
        TicketAllocation $allocation,
        TicketInvoice $invoice
    ): void {
        DB::transaction(function () use ($allocation, $invoice) {

            // 🔐 LOCK
            $allocation = TicketAllocation::lockForUpdate()
                ->findOrFail($allocation->id);

            $invoice = TicketInvoice::lockForUpdate()
                ->findOrFail($invoice->id);

            /* ==================================================
             | VALIDATION
             ================================================== */
            if ($allocation->ticket_invoice_id) {
                throw new Exception('Allocation sudah terikat ke invoice.');
            }

            if ($invoice->pnr_id !== $allocation->pnr_id) {
                throw new Exception('Invoice dan allocation beda PNR.');
            }

            // total allocation existing ke invoice
            $allocatedToInvoice = $invoice->allocations()
                ->sum('allocated_amount');

            if (
                $allocatedToInvoice + $allocation->allocated_amount
                > $invoice->total_amount
            ) {
                throw new Exception(
                    'Allocation melebihi total invoice.'
                );
            }

            /* ==================================================
             | ATTACH
             ================================================== */
            $allocation->update([
                'ticket_invoice_id' => $invoice->id,
            ]);

            TicketAuditService::log(
                'ALLOCATION',
                $allocation->id,
                'ALLOCATION_ATTACHED_TO_INVOICE',
                null,
                [
                    'invoice_id' => $invoice->id,
                    'amount'     => $allocation->allocated_amount,
                ]
            );
        });
    }
}

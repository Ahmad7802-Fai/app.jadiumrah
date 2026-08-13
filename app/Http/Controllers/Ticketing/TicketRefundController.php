<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketInvoice;
use App\Models\TicketRefund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketRefundController extends Controller
{
    /**
     * MAKER – AJUKAN REFUND
     */
    public function store(Request $request, TicketInvoice $invoice)
    {
        $this->authorize('refund', $invoice);

        // 🔒 hanya invoice PAID / PARTIAL
        abort_if(
            !in_array($invoice->status, ['PAID', 'PARTIAL']),
            403,
            'Invoice tidak bisa direfund.'
        );

        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($invoice, $data) {

            // 🔐 LOCK INVOICE (WAJIB QUERY-BASED)
            $invoice = TicketInvoice::where('id', $invoice->id)
                ->lockForUpdate()
                ->firstOrFail();

            // 💰 total refund approved sebelumnya
            $approvedRefund = $invoice->refunds()
                ->where('approval_status', 'APPROVED')
                ->sum('amount');

            // 💵 NET PAID YANG MASIH BISA DIREFOUND
            $maxRefund = max(
                0,
                $invoice->paid_amount - $approvedRefund
            );

            abort_if(
                $data['amount'] > $maxRefund,
                422,
                'Jumlah refund melebihi saldo yang tersedia.'
            );

            TicketRefund::create([
                'ticket_invoice_id' => $invoice->id,
                'amount'            => $data['amount'],
                'reason'            => $data['reason'],
                'status'            => 'REQUESTED',
                'approval_status'   => 'PENDING',
                'refunded_by'       => auth()->id(),
            ]);
        });

        return back()->with(
            'success',
            'Refund berhasil diajukan dan menunggu approval.'
        );
    }
}

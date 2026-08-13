<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * ======================================================
     * PRINT INVOICE (READ ONLY – ALL ROLE)
     * ======================================================
     */
    public function print(Invoices $invoice)
    {
        $this->authorize('print', $invoice);

        // 🔒 SAFETY GUARD (DOUBLE LOCK)
        abort_if(
            empty($invoice->nomor_invoice)
            || !in_array($invoice->status, ['CICILAN', 'LUNAS']),
            403
        );

        // LOAD RELATION
        $invoice->load([
            'jamaah.branch',
            'jamaah.agent.user',
            'payments' => function ($q) {
                $q->where('status', 'valid')
                  ->where('is_deleted', 0)
                  ->orderBy('tanggal_bayar');
            },
        ]);

        return Pdf::loadView(
            'invoices.print',
            compact('invoice')
        )
        ->setPaper('a4')
        ->stream("Invoice-{$invoice->nomor_invoice}.pdf");
    }
}

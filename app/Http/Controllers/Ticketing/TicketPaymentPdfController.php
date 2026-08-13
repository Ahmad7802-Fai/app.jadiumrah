<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketPayment;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketPaymentPdfController extends Controller
{
   public function receipt(TicketPayment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load([
            'invoice',
            'invoice.pnr',
        ]);

        return Pdf::loadView(
            'pdf.payment_receipt',
            compact('payment')
        )->download('Bukti-Pembayaran-'.$payment->id.'.pdf');
    }

}

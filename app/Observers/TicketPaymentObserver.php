<?php

namespace App\Observers;

use App\Models\TicketPayment;
use App\Services\Ticketing\TicketInvoiceCalculator;

class TicketPaymentObserver
{
    public function created(TicketPayment $payment): void
    {
        // 🔁 SINGLE SOURCE OF TRUTH
        app(TicketInvoiceCalculator::class)
            ->recalculate($payment->invoice);
    }
}

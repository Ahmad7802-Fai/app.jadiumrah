<?php

namespace App\Observers;

use App\Models\TicketPayment;
use App\Services\Ticketing\TicketInvoiceCalculator;

class TicketPaymentObserver
{
    public function created(TicketPayment $payment): void
    {
        app(TicketInvoiceCalculator::class)
            ->recalculate($payment->invoice);
    }
}

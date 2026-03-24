<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Services\Finance\InvoiceService;

class GenerateInvoiceListener
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function handle(BookingConfirmed $event): void
    {
        $this->invoiceService->generateNumber($event->booking);
    }
}
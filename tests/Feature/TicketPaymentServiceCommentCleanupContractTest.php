<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPaymentServiceCommentCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Services/Ticketing/TicketPaymentService.php')
        );
    }

    public function test_service_has_no_invoice_observer_wording(): void
    {
        $this->assertStringNotContainsString(
            'InvoiceObserver = single source of truth',
            $this->source()
        );
    }

    public function test_service_has_no_automatic_observer_wording(): void
    {
        $this->assertStringNotContainsString(
            'Observer akan otomatis jalan',
            $this->source()
        );
    }

    public function test_service_keeps_single_pay_method(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'public function pay('
            )
        );
    }
}

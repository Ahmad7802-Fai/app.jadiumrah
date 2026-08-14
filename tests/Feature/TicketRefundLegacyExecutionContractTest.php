<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketRefundLegacyExecutionContractTest extends TestCase
{
    public function test_legacy_refund_observer_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path('Observers/TicketRefundObserver.php')
        );
    }

    public function test_legacy_refund_observer_is_not_registered(): void
    {
        $source = file_get_contents(
            app_path('Providers/AppServiceProvider.php')
        );

        $this->assertStringNotContainsString(
            'TicketRefundObserver',
            $source
        );

        $this->assertStringNotContainsString(
            'TicketRefund::observe',
            $source
        );
    }

    public function test_orphan_allocation_to_invoice_service_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path(
                'Services/Ticketing/TicketAllocationToInvoiceService.php'
            )
        );
    }
}

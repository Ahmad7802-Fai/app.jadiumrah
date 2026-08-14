<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPaymentServiceHistoricalCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Services/Ticketing/TicketPaymentService.php')
        );
    }

    public function test_service_has_single_namespace_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'namespace App\Services\Ticketing;'
            )
        );
    }

    public function test_service_has_single_class_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'class TicketPaymentService'
            )
        );
    }

    public function test_service_has_single_pay_method(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'public function pay('
            )
        );
    }

    public function test_service_has_no_legacy_invoice_service_recalculation(): void
    {
        $this->assertStringNotContainsString(
            'app(TicketInvoiceService::class)',
            $this->source()
        );
    }
}

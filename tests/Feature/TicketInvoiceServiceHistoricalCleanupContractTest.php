<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketInvoiceServiceHistoricalCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Services/Ticketing/TicketInvoiceService.php')
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
                'class TicketInvoiceService'
            )
        );
    }

    public function test_service_has_no_legacy_recalculate_method(): void
    {
        $this->assertStringNotContainsString(
            'public function recalculate(',
            $this->source()
        );
    }

    public function test_service_has_single_invoice_number_generator(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'protected function generateInvoiceNumber()'
            )
        );
    }
}

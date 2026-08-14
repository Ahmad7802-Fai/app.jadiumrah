<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketInvoiceObserverHistoricalCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Observers/TicketInvoiceObserver.php')
        );
    }

    public function test_observer_has_single_namespace_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'namespace App\Observers;'
            )
        );
    }

    public function test_observer_has_single_class_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'class TicketInvoiceObserver'
            )
        );
    }

    public function test_observer_has_single_updated_method(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'public function updated('
            )
        );
    }

    public function test_observer_has_no_legacy_paid_amount_refund_condition(): void
    {
        $this->assertStringNotContainsString(
            '$invoice->paid_amount === 0',
            $this->source()
        );
    }
}

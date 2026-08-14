<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPaymentObserverCommentCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Observers/TicketPaymentObserver.php')
        );
    }

    public function test_observer_has_no_stale_single_source_wording(): void
    {
        $this->assertStringNotContainsString(
            'SINGLE SOURCE OF TRUTH',
            $this->source()
        );
    }

    public function test_observer_keeps_single_created_method(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'public function created('
            )
        );
    }

    public function test_observer_keeps_invoice_calculator_recalculation(): void
    {
        $source = $this->source();

        $this->assertStringContainsString(
            'TicketInvoiceCalculator::class',
            $source
        );

        $this->assertStringContainsString(
            '->recalculate($payment->invoice);',
            $source
        );
    }
}

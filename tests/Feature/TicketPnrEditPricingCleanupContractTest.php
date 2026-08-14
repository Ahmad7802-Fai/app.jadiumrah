<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPnrEditPricingCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            resource_path('views/ticketing/pnr/edit.blade.php')
        );
    }

    public function test_edit_view_has_no_legacy_deposit_pricing_reference(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString(
            'deposit_per_pax',
            $source
        );

        $this->assertStringNotContainsString(
            'id="deposit"',
            $source
        );

        $this->assertStringNotContainsString(
            'totalDeposit',
            $source
        );

        $this->assertStringNotContainsString(
            "getElementById('deposit')",
            $source
        );
    }

    public function test_edit_pricing_preview_uses_pax_times_fare(): void
    {
        $source = $this->source();

        $this->assertStringContainsString(
            "balance.value = (pax * fare).toLocaleString('id-ID');",
            $source
        );

        $this->assertStringContainsString(
            "['pax', 'fare'].forEach",
            $source
        );
    }
}

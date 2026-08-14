<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketInvoiceServiceCommentCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Services/Ticketing/TicketInvoiceService.php')
        );
    }

    public function test_service_has_no_stale_observer_wording(): void
    {
        $this->assertStringNotContainsString(
            'Observer yang urus',
            $this->source()
        );
    }

    public function test_service_has_no_commented_route_text_builder(): void
    {
        $this->assertStringNotContainsString(
            '$routeText = $pnr->routes',
            $this->source()
        );
    }

    public function test_service_has_no_legacy_rich_description_block(): void
    {
        $this->assertStringNotContainsString(
            'INVOICE ITEM (SINGLE LINE, RICH DESCRIPTION)',
            $this->source()
        );
    }

    public function test_service_has_single_invoice_item_creation(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'TicketInvoiceItem::create(['
            )
        );
    }
}

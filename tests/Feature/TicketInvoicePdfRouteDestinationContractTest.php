<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketInvoicePdfRouteDestinationContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Http/Controllers/Ticketing/TicketInvoicePdfController.php')
        );
    }

    public function test_pdf_route_builder_reads_destination(): void
    {
        $this->assertStringContainsString(
            '$destination = strtoupper(trim((string) $r->destination));',
            $this->source()
        );
    }

    public function test_pdf_route_builder_formats_origin_to_destination(): void
    {
        $this->assertStringContainsString(
            '"{$origin} → {$destination}"',
            $this->source()
        );
    }

    public function test_pdf_route_builder_keeps_legacy_origin_fallback(): void
    {
        $this->assertStringContainsString(
            "str_replace('-', '→', \$origin)",
            $this->source()
        );
    }
}

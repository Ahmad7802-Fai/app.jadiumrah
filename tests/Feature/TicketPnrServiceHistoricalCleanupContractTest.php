<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPnrServiceHistoricalCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Services/Ticketing/TicketPnrService.php')
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
                'class TicketPnrService'
            )
        );
    }

    public function test_service_has_no_legacy_deposit_per_pax_reference(): void
    {
        $this->assertStringNotContainsString(
            'deposit_per_pax',
            $this->source()
        );
    }

    public function test_service_has_no_stale_issue_observer_comment(): void
    {
        $this->assertStringNotContainsString(
            'Biasanya dipanggil via Observer',
            $this->source()
        );
    }
}

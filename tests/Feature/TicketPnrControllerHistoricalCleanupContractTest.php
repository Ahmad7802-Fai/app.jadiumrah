<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPnrControllerHistoricalCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Http/Controllers/Ticketing/TicketPnrController.php')
        );
    }

    public function test_controller_has_single_namespace_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'namespace App\Http\Controllers\Ticketing;'
            )
        );
    }

    public function test_controller_has_single_class_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'class TicketPnrController extends Controller'
            )
        );
    }

    public function test_controller_has_no_legacy_ticket_pnr_route_reference(): void
    {
        $this->assertStringNotContainsString(
            'TicketPnrRoute',
            $this->source()
        );
    }

    public function test_controller_has_no_legacy_routes_edit_view_reference(): void
    {
        $this->assertStringNotContainsString(
            'ticketing.pnr.routes_edit',
            $this->source()
        );
    }
}

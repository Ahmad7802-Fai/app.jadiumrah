<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPnrLegacyRouteArtifactsContractTest extends TestCase
{
    public function test_legacy_ticket_pnr_route_model_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path('Models/TicketPnrRoute.php')
        );
    }

    public function test_legacy_routes_edit_view_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            resource_path('views/ticketing/pnr/routes_edit.blade.php')
        );
    }

    public function test_pnr_relation_uses_canonical_ticket_route_model(): void
    {
        $source = file_get_contents(
            app_path('Models/TicketPnr.php')
        );

        $this->assertStringContainsString(
            "hasMany(TicketRoute::class, 'pnr_id')",
            $source
        );
    }

    public function test_controller_uses_canonical_nested_route_editor_view(): void
    {
        $source = file_get_contents(
            app_path('Http/Controllers/Ticketing/TicketPnrController.php')
        );

        $this->assertMatchesRegularExpression(
            "/^[ \t]*return view\\('ticketing\\.pnr\\.routes\\.edit', \\[$/m",
            $source
        );
    }
}

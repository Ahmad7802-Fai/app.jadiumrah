<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPnrRoutePersistenceContractTest extends TestCase
{
    public function test_create_form_submits_origin_and_destination_for_initial_sector(): void
    {
        $source = file_get_contents(
            resource_path('views/ticketing/pnr/create.blade.php')
        );

        $this->assertStringContainsString(
            'name="routes[0][origin]"',
            $source
        );

        $this->assertStringContainsString(
            'name="routes[0][destination]"',
            $source
        );
    }

    public function test_create_form_submits_destination_for_dynamic_sectors(): void
    {
        $source = file_get_contents(
            resource_path('views/ticketing/pnr/create.blade.php')
        );

        $this->assertStringContainsString(
            'name="routes[${index}][destination]"',
            $source
        );
    }

    public function test_pnr_service_persists_route_destination(): void
    {
        $source = file_get_contents(
            app_path('Services/Ticketing/TicketPnrService.php')
        );

        $this->assertMatchesRegularExpression(
            "/^[ \t]*'destination'[ \t]*=>[ \t]*\\\$route\\['destination'\\],[ \t]*$/m",
            $source
        );
    }

    public function test_active_route_editor_submits_existing_destination(): void
    {
        $source = file_get_contents(
            resource_path('views/ticketing/pnr/routes/edit.blade.php')
        );

        $this->assertStringContainsString(
            'name="routes[{{ $i }}][destination]"',
            $source
        );
    }

    public function test_active_route_editor_submits_destination_for_new_sector(): void
    {
        $source = file_get_contents(
            resource_path('views/ticketing/pnr/routes/edit.blade.php')
        );

        $this->assertStringContainsString(
            'name="routes[${routeIndex}][destination]"',
            $source
        );
    }

    public function test_route_update_requires_origin_and_destination(): void
    {
        $source = file_get_contents(
            app_path('Http/Controllers/Ticketing/TicketPnrController.php')
        );

        $this->assertStringContainsString(
            "'routes.*.origin' => 'required|string|max:10'",
            $source
        );

        $this->assertStringContainsString(
            "'routes.*.destination' => 'required|string|max:10'",
            $source
        );
    }
}

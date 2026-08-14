<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPnrRouteEditorScriptContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            resource_path('views/ticketing/pnr/routes/edit.blade.php')
        );
    }

    public function test_route_editor_has_single_script_push(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                "@push('scripts')"
            )
        );
    }

    public function test_route_editor_has_single_route_index_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'let routeIndex = {{ $pnr->routes->count() }};'
            )
        );
    }

    public function test_route_editor_has_single_add_route_function(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'function addRoute()'
            )
        );
    }

    public function test_route_editor_has_single_remove_route_function(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'function removeRoute(btn)'
            )
        );
    }

    public function test_route_editor_keeps_destination_for_dynamic_sector(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'name="routes[${routeIndex}][destination]"'
            )
        );
    }
}

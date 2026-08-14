<?php

namespace Tests\Feature;

use App\Models\TicketPnr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TicketAllocationLegacyRuntimeContractTest extends TestCase
{
    public function test_legacy_allocation_controller_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path(
                'Http/Controllers/Ticketing/TicketAllocationController.php'
            )
        );
    }

    public function test_legacy_allocation_service_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path(
                'Services/Ticketing/TicketAllocationService.php'
            )
        );
    }

    public function test_legacy_allocation_model_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path('Models/TicketAllocation.php')
        );
    }

    public function test_legacy_allocation_route_is_not_available(): void
    {
        $this->assertFalse(
            Route::has('ticketing.allocation.store')
        );
    }

    public function test_pnr_has_no_legacy_allocation_relation(): void
    {
        $this->assertFalse(
            method_exists(TicketPnr::class, 'allocations')
        );
    }

    public function test_pnr_controller_does_not_eager_load_allocations(): void
    {
        $source = file_get_contents(
            app_path(
                'Http/Controllers/Ticketing/TicketPnrController.php'
            )
        );

        $this->assertStringNotContainsString(
            "'allocations'",
            $source
        );
    }

    public function test_pnr_view_has_no_legacy_allocation_history(): void
    {
        $source = file_get_contents(
            resource_path(
                'views/ticketing/pnr/show.blade.php'
            )
        );

        $this->assertStringNotContainsString(
            'Allocation History',
            $source
        );
    }

    public function test_legacy_allocation_schema_is_not_present(): void
    {
        $this->assertFalse(
            Schema::hasTable('ticket_allocations')
        );
    }
}

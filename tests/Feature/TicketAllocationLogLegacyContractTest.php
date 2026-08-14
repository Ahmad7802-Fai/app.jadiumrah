<?php

namespace Tests\Feature;

use App\Models\TicketAllocation;
use App\Models\TicketPnr;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TicketAllocationLogLegacyContractTest extends TestCase
{
    public function test_legacy_allocation_log_model_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path('Models/TicketAllocationLog.php')
        );
    }

    public function test_allocation_has_no_legacy_logs_relation(): void
    {
        $this->assertFalse(
            method_exists(TicketAllocation::class, 'logs')
        );
    }

    public function test_active_pnr_allocation_relation_remains_available(): void
    {
        $this->assertTrue(
            method_exists(TicketPnr::class, 'allocations')
        );
    }

    public function test_active_allocation_route_remains_available(): void
    {
        $this->assertTrue(
            Route::has('ticketing.allocation.store')
        );
    }
}

<?php

namespace Tests\Feature;

use App\Models\TicketAllocation;
use App\Models\TicketInvoice;
use App\Models\TicketPnr;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TicketAllocationModelContractTest extends TestCase
{
    public function test_allocation_has_no_legacy_invoice_relation(): void
    {
        $this->assertFalse(
            method_exists(TicketAllocation::class, 'invoice')
        );
    }

    public function test_invoice_has_no_legacy_allocation_relation(): void
    {
        $this->assertFalse(
            method_exists(TicketInvoice::class, 'allocations')
        );
    }

    public function test_pnr_allocation_relation_remains_available(): void
    {
        $this->assertTrue(
            method_exists(TicketPnr::class, 'allocations')
        );
    }

    public function test_pnr_allocation_route_remains_available(): void
    {
        $this->assertTrue(
            Route::has('ticketing.allocation.store')
        );
    }
}

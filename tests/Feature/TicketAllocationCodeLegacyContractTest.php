<?php

namespace Tests\Feature;

use App\Models\TicketAllocation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TicketAllocationCodeLegacyContractTest extends TestCase
{
    public function test_allocation_model_has_no_legacy_allocation_code_fillable(): void
    {
        $this->assertNotContains(
            'allocation_code',
            (new TicketAllocation())->getFillable()
        );
    }

    public function test_active_allocation_fields_remain_fillable(): void
    {
        $fillable = (new TicketAllocation())->getFillable();

        $this->assertContains('pnr_id', $fillable);
        $this->assertContains('allocated_amount', $fillable);
        $this->assertContains('allocation_date', $fillable);
        $this->assertContains('status', $fillable);
    }

    public function test_allocation_schema_has_no_legacy_code_column(): void
    {
        $this->assertFalse(
            Schema::hasColumn(
                'ticket_allocations',
                'allocation_code'
            )
        );
    }

    public function test_active_allocation_route_remains_available(): void
    {
        $this->assertTrue(
            Route::has('ticketing.allocation.store')
        );
    }
}

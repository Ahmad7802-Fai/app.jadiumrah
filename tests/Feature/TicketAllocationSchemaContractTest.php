<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TicketAllocationSchemaContractTest extends TestCase
{
    public function test_allocation_schema_has_no_legacy_invoice_column(): void
    {
        $this->assertFalse(
            Schema::hasColumn(
                'ticket_allocations',
                'ticket_invoice_id'
            )
        );
    }

    public function test_active_allocation_schema_keeps_pnr_column(): void
    {
        $this->assertTrue(
            Schema::hasColumn(
                'ticket_allocations',
                'pnr_id'
            )
        );
    }

    public function test_active_allocation_schema_keeps_amount_column(): void
    {
        $this->assertTrue(
            Schema::hasColumn(
                'ticket_allocations',
                'allocated_amount'
            )
        );
    }
}

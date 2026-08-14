<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TicketAllocationCodeLegacyContractTest extends TestCase
{


    public function test_allocation_schema_has_no_legacy_code_column(): void
    {
        $this->assertFalse(
            Schema::hasColumn(
                'ticket_allocations',
                'allocation_code'
            )
        );
    }

}

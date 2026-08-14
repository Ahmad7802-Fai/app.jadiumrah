<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TicketDepositSchemaContractTest extends TestCase
{
    public function test_legacy_ticket_deposits_table_is_not_present(): void
    {
        $this->assertFalse(
            Schema::hasTable('ticket_deposits')
        );
    }

    public function test_active_ticket_allocations_table_remains_present(): void
    {
        $this->assertTrue(
            Schema::hasTable('ticket_allocations')
        );
    }

    public function test_active_ticket_pnrs_table_remains_present(): void
    {
        $this->assertTrue(
            Schema::hasTable('ticket_pnrs')
        );
    }
}

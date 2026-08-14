<?php

namespace Tests\Feature;

use App\Models\TicketInvoice;
use Tests\TestCase;

class TicketAllocationModelContractTest extends TestCase
{

    public function test_invoice_has_no_legacy_allocation_relation(): void
    {
        $this->assertFalse(
            method_exists(TicketInvoice::class, 'allocations')
        );
    }


}

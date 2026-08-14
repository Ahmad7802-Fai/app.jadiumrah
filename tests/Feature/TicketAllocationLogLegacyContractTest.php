<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketAllocationLogLegacyContractTest extends TestCase
{
    public function test_legacy_allocation_log_model_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path('Models/TicketAllocationLog.php')
        );
    }



}

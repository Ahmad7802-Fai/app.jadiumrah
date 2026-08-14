<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketAllocationLegacyObserverContractTest extends TestCase
{
    public function test_legacy_allocation_observer_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path('Observers/TicketAllocationObserver.php')
        );
    }

    public function test_legacy_allocation_observer_is_not_registered(): void
    {
        $source = file_get_contents(
            app_path('Providers/AppServiceProvider.php')
        );

        $this->assertStringNotContainsString(
            'TicketAllocationObserver',
            $source
        );

        $this->assertStringNotContainsString(
            'TicketAllocation::observe',
            $source
        );
    }

}

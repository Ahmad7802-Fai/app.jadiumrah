<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TicketDepositLegacyContractTest extends TestCase
{
    public function test_legacy_deposit_controller_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path(
                'Http/Controllers/Ticketing/TicketDepositController.php'
            )
        );
    }

    public function test_legacy_deposit_service_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path(
                'Services/Ticketing/TicketDepositService.php'
            )
        );
    }

    public function test_legacy_deposit_model_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path('Models/TicketDeposit.php')
        );
    }

    public function test_legacy_deposit_routes_are_not_available(): void
    {
        $this->assertFalse(
            Route::has('ticketing.deposit.store')
        );

        $this->assertFalse(
            Route::has('ticketing.deposit.approve')
        );
    }
}

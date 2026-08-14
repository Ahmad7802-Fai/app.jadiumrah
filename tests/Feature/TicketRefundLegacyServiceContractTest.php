<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TicketRefundLegacyServiceContractTest extends TestCase
{
    public function test_legacy_refund_service_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            app_path(
                'Services/Ticketing/TicketRefundService.php'
            ),
            'Legacy TicketRefundService tidak boleh kembali ke active codebase.'
        );
    }

    public function test_refund_has_no_execute_route(): void
    {
        $routes = collect(Route::getRoutes())
            ->map(fn ($route) => [
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ]);

        $this->assertFalse(
            $routes->contains(
                fn (array $route): bool => str_contains(
                    (string) $route['name'],
                    'refund.execute'
                )
                    || str_contains(
                        (string) $route['action'],
                        '@execute'
                    )
            )
        );
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Routing\Route;
use Tests\TestCase;

class TicketingModuleAuthorizationTest extends TestCase
{
    /**
     * @return array<int, Route>
     */
    private function ticketingRoutes(): array
    {
        return collect(app('router')->getRoutes())
            ->filter(
                fn (Route $route) =>
                    str_starts_with((string) $route->getName(), 'ticketing.')
            )
            ->values()
            ->all();
    }

    public function test_all_ticketing_routes_require_keuangan_role_boundary(): void
    {
        $routes = $this->ticketingRoutes();

        $this->assertNotEmpty(
            $routes,
            'Tidak ada named route ticketing.* yang ditemukan.'
        );

        foreach ($routes as $route) {
            $middleware = $route->gatherMiddleware();

            $hasKeuanganBoundary = collect($middleware)->contains(
                function (string $middleware): bool {
                    return $middleware === 'role:KEUANGAN'
                        || str_contains(
                            $middleware,
                            'CheckRole:KEUANGAN'
                        );
                }
            );

            $this->assertTrue(
                $hasKeuanganBoundary,
                sprintf(
                    'Route [%s] [%s] belum dilindungi role:KEUANGAN. Middleware: %s',
                    $route->getName(),
                    $route->uri(),
                    implode(', ', $middleware)
                )
            );
        }
    }
}

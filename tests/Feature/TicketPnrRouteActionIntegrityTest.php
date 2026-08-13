<?php

namespace Tests\Feature;

use Illuminate\Routing\Route;
use Tests\TestCase;

class TicketPnrRouteActionIntegrityTest extends TestCase
{
    /**
     * @return array<int, Route>
     */
    private function pnrRoutes(): array
    {
        return collect(app('router')->getRoutes())
            ->filter(
                fn (Route $route) =>
                    str_starts_with(
                        (string) $route->getName(),
                        'ticketing.pnr.'
                    )
            )
            ->values()
            ->all();
    }

    public function test_all_pnr_controller_actions_exist(): void
    {
        $routes = $this->pnrRoutes();

        $this->assertNotEmpty(
            $routes,
            'Tidak ada named route ticketing.pnr.* yang ditemukan.'
        );

        $missing = [];

        foreach ($routes as $route) {
            $action = $route->getActionName();

            if ($action === 'Closure' || !str_contains($action, '@')) {
                continue;
            }

            [$controller, $method] = explode('@', $action, 2);

            if (
                !class_exists($controller)
                || !method_exists($controller, $method)
            ) {
                $missing[] = sprintf(
                    '%s [%s] -> %s',
                    $route->getName(),
                    $route->uri(),
                    $action
                );
            }
        }

        $this->assertSame(
            [],
            $missing,
            "Route PNR menunjuk action yang tidak tersedia:\n"
                . implode("\n", $missing)
        );
    }
}

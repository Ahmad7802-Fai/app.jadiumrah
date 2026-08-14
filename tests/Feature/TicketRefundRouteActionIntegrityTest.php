<?php

namespace Tests\Feature;

use Illuminate\Routing\Route;
use Tests\TestCase;

class TicketRefundRouteActionIntegrityTest extends TestCase
{
    /**
     * @return array<int, Route>
     */
    private function refundRoutes(): array
    {
        return collect(app('router')->getRoutes())
            ->filter(
                fn (Route $route) =>
                    str_starts_with(
                        (string) $route->getName(),
                        'ticketing.refund.'
                    )
            )
            ->values()
            ->all();
    }

    public function test_refund_routes_only_reference_existing_actions(): void
    {
        $routes = $this->refundRoutes();

        $this->assertCount(
            4,
            $routes,
            'Named route ticketing.refund.* harus tepat 4.'
        );

        $missing = [];

        foreach ($routes as $route) {
            $action = $route->getActionName();

            if (
                $action === 'Closure'
                || !str_contains($action, '@')
            ) {
                continue;
            }

            [$controller, $method] = explode(
                '@',
                $action,
                2
            );

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
            "Route Refund menunjuk action yang tidak tersedia:\n"
                . implode("\n", $missing)
        );
    }

    public function test_refund_store_is_defined_once_in_route_source(): void
    {
        $source = file_get_contents(
            base_path('routes/modules/ticketing.php')
        );

        $this->assertSame(
            1,
            substr_count(
                $source,
                "->name('refund.store')"
            ),
            'refund.store harus didefinisikan tepat satu kali.'
        );
    }
}

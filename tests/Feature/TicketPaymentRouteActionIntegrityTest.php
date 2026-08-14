<?php

namespace Tests\Feature;

use Illuminate\Routing\Route;
use Tests\TestCase;

class TicketPaymentRouteActionIntegrityTest extends TestCase
{
    /**
     * @return array<int, Route>
     */
    private function paymentRoutes(): array
    {
        return collect(app('router')->getRoutes())
            ->filter(
                fn (Route $route) =>
                    str_starts_with(
                        (string) $route->getName(),
                        'ticketing.payment.'
                    )
            )
            ->values()
            ->all();
    }

    public function test_payment_routes_only_reference_existing_actions(): void
    {
        $routes = $this->paymentRoutes();

        $this->assertCount(
            2,
            $routes,
            'Named route ticketing.payment.* harus tepat 2.'
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
            "Route Payment menunjuk action yang tidak tersedia:\n"
                . implode("\n", $missing)
        );
    }
}

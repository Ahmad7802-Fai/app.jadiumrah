<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketPnrController;
use App\Models\TicketPnr;
use App\Models\User;
use App\Services\Ticketing\TicketPnrService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class TicketPnrControllerAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $user = new User();

        $user->forceFill([
            'role' => 'OPERATOR',
            'is_active' => true,
        ]);

        Auth::setUser($user);
    }

    protected function tearDown(): void
    {
        Auth::guard()->forgetUser();

        parent::tearDown();
    }

    private function pnr(): TicketPnr
    {
        $pnr = new TicketPnr();

        $pnr->forceFill([
            'status' => 'ON_FLOW',
        ]);

        return $pnr;
    }

    private function assertAuthorizationDenied(callable $callback): void
    {
        try {
            $callback();
        } catch (AuthorizationException) {
            $this->addToAssertionCount(1);

            return;
        }

        $this->fail(
            'Controller tidak menghentikan request dengan AuthorizationException.'
        );
    }

    public function test_create_requires_create_policy(): void
    {
        $controller = app(TicketPnrController::class);

        $this->assertAuthorizationDenied(
            fn () => $controller->create()
        );
    }

    public function test_store_requires_create_policy(): void
    {
        $controller = app(TicketPnrController::class);
        $service = Mockery::mock(TicketPnrService::class);

        $request = Request::create(
            '/ticketing/pnr',
            'POST',
            []
        );

        $this->assertAuthorizationDenied(
            fn () => $controller->store($request, $service)
        );
    }

    public function test_show_requires_view_policy(): void
    {
        $controller = app(TicketPnrController::class);

        $this->assertAuthorizationDenied(
            fn () => $controller->show($this->pnr())
        );
    }

    public function test_edit_requires_update_policy(): void
    {
        $controller = app(TicketPnrController::class);

        $this->assertAuthorizationDenied(
            fn () => $controller->edit($this->pnr())
        );
    }

    public function test_update_requires_update_policy(): void
    {
        $controller = app(TicketPnrController::class);

        $request = Request::create(
            '/ticketing/pnr/1',
            'PUT',
            [
                'pnr_code' => 'TEST-PNR',
                'pax' => 1,
                'fare_per_pax' => 1000,
            ]
        );

        $this->assertAuthorizationDenied(
            fn () => $controller->update(
                $request,
                $this->pnr()
            )
        );
    }

    public function test_edit_routes_requires_update_policy(): void
    {
        $controller = app(TicketPnrController::class);

        $this->assertAuthorizationDenied(
            fn () => $controller->editRoutes($this->pnr())
        );
    }

    public function test_update_routes_requires_update_policy(): void
    {
        $controller = app(TicketPnrController::class);

        $request = Request::create(
            '/ticketing/pnr/1/routes',
            'PUT',
            [
                'routes' => [
                    [
                        'origin' => 'CGK',
                        'destination' => 'JED',
                        'departure_date' => '2026-08-20',
                    ],
                ],
            ]
        );

        $this->assertAuthorizationDenied(
            fn () => $controller->updateRoutes(
                $request,
                $this->pnr()
            )
        );
    }

    public function test_confirm_requires_confirm_policy(): void
    {
        $controller = app(TicketPnrController::class);
        $service = Mockery::mock(TicketPnrService::class);

        $this->assertAuthorizationDenied(
            fn () => $controller->confirm(
                $this->pnr(),
                $service
            )
        );
    }
}

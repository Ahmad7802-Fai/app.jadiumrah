<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketPnrController;
use App\Models\TicketPnr;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TicketPnrRouteUpdateValidationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $user = new User();

        $user->forceFill([
            'id' => 1,
            'role' => 'KEUANGAN',
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
        return TicketPnr::create([
            'pnr_code' => 'RT' . strtoupper(bin2hex(random_bytes(4))),
            'pax' => 1,
            'fare_per_pax' => 1000000,
            'total_fare' => 1000000,
            'status' => 'ON_FLOW',
        ]);
    }

    private function validRoute(): array
    {
        return [
            'origin' => 'CGK',
            'destination' => 'JED',
            'departure_date' => '2026-08-20',
            'flight_number' => 'SV817',
            'departure_time' => '10:15',
            'arrival_time' => '17:00',
            'arrival_day_offset' => 0,
        ];
    }

    private function assertValidationFails(
        TicketPnr $pnr,
        array $payload,
        array $expectedErrors
    ): void {
        $request = Request::create(
            "/ticketing/pnr/{$pnr->id}/routes",
            'PUT',
            $payload
        );

        try {
            (new TicketPnrController())->updateRoutes(
                $request,
                $pnr
            );
        } catch (ValidationException $exception) {
            $this->assertSame(
                422,
                $exception->status
            );

            $actualErrors = array_keys(
                $exception->errors()
            );

            foreach ($expectedErrors as $key) {
                $this->assertContains(
                    $key,
                    $actualErrors
                );
            }

            return;
        }

        $this->fail(
            'Route update tidak menghentikan invalid request dengan validation error.'
        );
    }

    public function test_update_routes_rejects_invalid_time_and_day_offset(): void
    {
        $pnr = $this->pnr();

        $route = $this->validRoute();
        $route['departure_time'] = '25:99';
        $route['arrival_time'] = 'invalid';
        $route['arrival_day_offset'] = 2;

        $this->assertValidationFails(
            $pnr,
            [
                'routes' => [$route],
            ],
            [
                'routes.0.departure_time',
                'routes.0.arrival_time',
                'routes.0.arrival_day_offset',
            ]
        );
    }

    public function test_update_routes_rejects_unapproved_nested_keys(): void
    {
        $pnr = $this->pnr();

        $route = $this->validRoute();
        $route['sector'] = 99;
        $route['pnr_id'] = 999999999;

        $this->assertValidationFails(
            $pnr,
            [
                'routes' => [$route],
            ],
            [
                'routes.0',
            ]
        );
    }

    public function test_update_routes_persists_valid_route_contract(): void
    {
        $pnr = $this->pnr();

        $request = Request::create(
            "/ticketing/pnr/{$pnr->id}/routes",
            'PUT',
            [
                'routes' => [
                    $this->validRoute(),
                    [
                        'origin' => 'JED',
                        'destination' => 'MED',
                        'departure_date' => '2026-08-25',
                        'flight_number' => null,
                        'departure_time' => null,
                        'arrival_time' => null,
                        'arrival_day_offset' => 1,
                    ],
                ],
            ]
        );

        $response = (new TicketPnrController())->updateRoutes(
            $request,
            $pnr
        );

        $routes = $pnr->fresh()
            ->routes()
            ->orderBy('sector')
            ->get();

        $this->assertTrue(
            $response->isRedirect(
                route('ticketing.pnr.show', $pnr)
            )
        );

        $this->assertCount(2, $routes);

        $this->assertSame(1, $routes[0]->sector);
        $this->assertSame('CGK', $routes[0]->origin);
        $this->assertSame('JED', $routes[0]->destination);
        $this->assertSame('10:15:00', $routes[0]->departure_time);
        $this->assertSame('17:00:00', $routes[0]->arrival_time);
        $this->assertSame(0, $routes[0]->arrival_day_offset);

        $this->assertSame(2, $routes[1]->sector);
        $this->assertSame('JED', $routes[1]->origin);
        $this->assertSame('MED', $routes[1]->destination);
        $this->assertNull($routes[1]->flight_number);
        $this->assertNull($routes[1]->departure_time);
        $this->assertNull($routes[1]->arrival_time);
        $this->assertSame(1, $routes[1]->arrival_day_offset);
    }
}

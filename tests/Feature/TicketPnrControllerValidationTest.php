<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketPnrController;
use App\Models\Client;
use App\Models\TicketPnr;
use App\Models\User;
use App\Services\Ticketing\TicketPnrService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class TicketPnrControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    private Client $client;

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

        $this->client = Client::create([
            'tipe' => 'b2c',
            'nama' => 'PNR Validation Test Client',
        ]);
    }

    protected function tearDown(): void
    {
        Auth::guard()->forgetUser();

        parent::tearDown();
    }

    private function payload(): array
    {
        return [
            'pnr_code' => 'TV' . strtoupper(bin2hex(random_bytes(4))),
            'client_id' => $this->client->id,
            'airline_code' => 'SV',
            'airline_name' => 'Saudia',
            'airline_class' => 'ECONOMY',
            'category' => 'REGULAR',
            'pax' => 2,
            'fare_per_pax' => 1500000,
            'routes' => [
                [
                    'origin' => 'CGK',
                    'destination' => 'JED',
                    'departure_date' => '2026-08-20',
                    'flight_number' => 'SV817',
                    'departure_time' => '10:15',
                    'arrival_time' => '17:00',
                    'arrival_day_offset' => 0,
                ],
            ],
        ];
    }

    private function assertValidationFails(
        array $payload,
        array $expectedErrors
    ): void {
        $service = Mockery::mock(TicketPnrService::class);

        $service->shouldNotReceive('create');

        $request = Request::create(
            '/ticketing/pnr',
            'POST',
            $payload
        );

        try {
            (new TicketPnrController())->store(
                $request,
                $service
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
            'PNR store tidak menghentikan malformed request dengan validation error.'
        );
    }

    public function test_store_rejects_missing_required_fields_before_service(): void
    {
        $this->assertValidationFails(
            [],
            [
                'pnr_code',
                'client_id',
                'pax',
                'fare_per_pax',
                'routes',
            ]
        );
    }

    public function test_store_rejects_schema_boundary_violations(): void
    {
        $payload = $this->payload();

        $payload['pnr_code'] = str_repeat('P', 21);
        $payload['airline_code'] = str_repeat('A', 11);
        $payload['airline_name'] = str_repeat('A', 101);
        $payload['airline_class'] = str_repeat('C', 51);
        $payload['category'] = str_repeat('C', 51);
        $payload['pax'] = 0;
        $payload['fare_per_pax'] = -1;

        $this->assertValidationFails(
            $payload,
            [
                'pnr_code',
                'airline_code',
                'airline_name',
                'airline_class',
                'category',
                'pax',
                'fare_per_pax',
            ]
        );
    }

    public function test_store_rejects_invalid_route_contract(): void
    {
        $payload = $this->payload();

        $payload['routes'][0] = [
            'origin' => str_repeat('O', 11),
            'destination' => '',
            'departure_date' => 'not-a-date',
            'flight_number' => str_repeat('F', 21),
            'departure_time' => '25:99',
            'arrival_time' => 'invalid',
            'arrival_day_offset' => 2,
        ];

        $this->assertValidationFails(
            $payload,
            [
                'routes.0.origin',
                'routes.0.destination',
                'routes.0.departure_date',
                'routes.0.flight_number',
                'routes.0.departure_time',
                'routes.0.arrival_time',
                'routes.0.arrival_day_offset',
            ]
        );
    }

    public function test_store_rejects_unknown_client(): void
    {
        $payload = $this->payload();
        $payload['client_id'] = 999999999;

        $this->assertValidationFails(
            $payload,
            [
                'client_id',
            ]
        );
    }

    public function test_store_passes_only_validated_data_and_server_actor_to_service(): void
    {
        $payload = $this->payload();

        $payload['created_by'] = 999999999;
        $payload['status'] = 'ISSUED';
        $payload['unexpected'] = 'must-not-reach-service';

        $pnr = new TicketPnr();

        $pnr->forceFill([
            'id' => 123,
            'status' => 'ON_FLOW',
        ]);

        $service = Mockery::mock(TicketPnrService::class);

        $service
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) use ($payload): bool {
                return $data['pnr_code'] === $payload['pnr_code']
                    && $data['client_id'] === $this->client->id
                    && $data['created_by'] === 1
                    && $data['pax'] === 2
                    && $data['fare_per_pax'] === 1500000
                    && $data['routes'][0]['origin'] === 'CGK'
                    && $data['routes'][0]['destination'] === 'JED'
                    && ! array_key_exists('status', $data)
                    && ! array_key_exists('unexpected', $data);
            }))
            ->andReturn($pnr);

        $request = Request::create(
            '/ticketing/pnr',
            'POST',
            $payload
        );

        $response = (new TicketPnrController())->store(
            $request,
            $service
        );

        $this->assertTrue(
            $response->isRedirect(
                route('ticketing.pnr.show', $pnr)
            )
        );
    }
}

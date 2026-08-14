<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketPnrController;
use App\Models\Client;
use App\Models\TicketPnr;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TicketPnrControllerUpdateValidationTest extends TestCase
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

    private function client(string $name): Client
    {
        return Client::create([
            'tipe' => 'b2c',
            'nama' => $name,
        ]);
    }

    private function pnr(Client $client): TicketPnr
    {
        return TicketPnr::create([
            'pnr_code' => 'UP' . strtoupper(bin2hex(random_bytes(4))),
            'client_id' => $client->id,
            'airline_code' => 'SV',
            'airline_name' => 'Saudia',
            'airline_class' => 'ECONOMY',
            'category' => 'REGULAR',
            'pax' => 2,
            'fare_per_pax' => 1500000,
            'total_fare' => 3000000,
            'status' => 'ON_FLOW',
        ]);
    }

    private function validPayload(Client $client): array
    {
        return [
            'client_id' => $client->id,
            'airline_class' => 'BUSINESS',
            'pax' => 3,
            'fare_per_pax' => 2000000,
        ];
    }

    private function assertValidationFails(
        TicketPnr $pnr,
        array $payload,
        array $expectedErrors
    ): void {
        $request = Request::create(
            "/ticketing/pnr/{$pnr->id}",
            'PUT',
            $payload
        );

        try {
            (new TicketPnrController())->update(
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
            'PNR update tidak menghentikan invalid request dengan validation error.'
        );
    }

    public function test_update_rejects_missing_editable_fields(): void
    {
        $client = $this->client(
            'PNR Update Missing Test'
        );

        $pnr = $this->pnr($client);

        $this->assertValidationFails(
            $pnr,
            [],
            [
                'client_id',
                'airline_class',
                'pax',
                'fare_per_pax',
            ]
        );
    }

    public function test_update_rejects_invalid_editable_values(): void
    {
        $client = $this->client(
            'PNR Update Invalid Test'
        );

        $pnr = $this->pnr($client);

        $this->assertValidationFails(
            $pnr,
            [
                'client_id' => 999999999,
                'airline_class' => 'PREMIUM',
                'pax' => 0,
                'fare_per_pax' => -1,
            ],
            [
                'client_id',
                'airline_class',
                'pax',
                'fare_per_pax',
            ]
        );
    }

    public function test_update_whitelists_editable_fields_and_recalculates_total_fare(): void
    {
        $originalClient = $this->client(
            'PNR Original Client'
        );

        $newClient = $this->client(
            'PNR Updated Client'
        );

        $pnr = $this->pnr($originalClient);

        $originalCode = $pnr->pnr_code;

        $payload = $this->validPayload($newClient) + [
            'pnr_code' => 'HACKED-PNR',
            'airline_code' => 'XX',
            'airline_name' => 'Injected Airline',
            'category' => 'INJECTED',
            'total_fare' => 1,
            'status' => 'ISSUED',
            'created_by' => 999999999,
        ];

        $request = Request::create(
            "/ticketing/pnr/{$pnr->id}",
            'PUT',
            $payload
        );

        $response = (new TicketPnrController())->update(
            $request,
            $pnr
        );

        $pnr->refresh();

        $this->assertTrue(
            $response->isRedirect(
                route('ticketing.pnr.show', $pnr)
            )
        );

        $this->assertSame(
            $newClient->id,
            $pnr->client_id
        );

        $this->assertSame(
            'BUSINESS',
            $pnr->airline_class
        );

        $this->assertSame(3, $pnr->pax);
        $this->assertSame(2000000, $pnr->fare_per_pax);
        $this->assertSame(6000000, $pnr->total_fare);

        $this->assertSame(
            $originalCode,
            $pnr->pnr_code
        );

        $this->assertSame(
            'SV',
            $pnr->airline_code
        );

        $this->assertSame(
            'Saudia',
            $pnr->airline_name
        );

        $this->assertSame(
            'REGULAR',
            $pnr->category
        );

        $this->assertSame(
            'ON_FLOW',
            $pnr->status
        );
    }
}

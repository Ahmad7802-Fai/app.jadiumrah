<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Services\Ticketing\FlightService;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    protected FlightService $service;

    public function __construct(FlightService $service)
    {
        $this->service = $service;

        $this->authorizeResource(Flight::class, 'flight');
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $flights = Flight::with('segments')
            ->latest()
            ->paginate(15);

        return view('ticketing.flights.index', compact('flights'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('ticketing.flights.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $this->validateFlight($request);

        $this->service->create(
            $validated,
            $request->input('segments', [])
        );

        return redirect()
            ->route('ticketing.flights.index')
            ->with('success', 'Flight created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Flight $flight)
    {
        $flight->load('segments');

        return view('ticketing.flights.edit', compact('flight'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Flight $flight)
    {
        $validated = $this->validateFlight($request);

        $this->service->update(
            $flight,
            $validated,
            $request->input('segments', [])
        );

        return redirect()
            ->route('ticketing.flights.index')
            ->with('success', 'Flight updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(Flight $flight)
    {
        $this->service->delete($flight);

        return back()->with('success', 'Flight deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE VALIDATION
    |--------------------------------------------------------------------------
    */
    private function validateFlight(Request $request): array
    {
        return $request->validate([
            'airline'           => 'required|string|max:255',
            'flight_number'     => 'required|string|max:255',
            'aircraft_type'     => 'nullable|string|max:255',
            'aircraft_capacity' => 'nullable|integer|min:1',
            'is_charter'        => 'nullable|boolean',
            'is_active'         => 'nullable|boolean',
            'notes'             => 'nullable|string',

            // segments validation
            'segments'                     => 'nullable|array',
            'segments.*.origin'            => 'required_with:segments|string|max:255',
            'segments.*.destination'       => 'required_with:segments|string|max:255',
            'segments.*.departure_time'    => 'required_with:segments|date',
            'segments.*.arrival_time'      => 'required_with:segments|date|after:segments.*.departure_time',
            'segments.*.terminal'          => 'nullable|string|max:255',
            'segments.*.gate'              => 'nullable|string|max:255',
        ]);
    }
}
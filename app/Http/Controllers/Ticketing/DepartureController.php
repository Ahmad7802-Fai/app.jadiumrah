<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\PaketDeparture;
use App\Models\Flight;
use Illuminate\Http\Request;
use App\Services\Ticketing\DepartureFlightService;

class DepartureController extends Controller
{
    protected DepartureFlightService $flightService;

    public function __construct(DepartureFlightService $flightService)
    {
        $this->flightService = $flightService;

        $this->authorizeResource(PaketDeparture::class, 'departure');
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $departures = PaketDeparture::with(['paket', 'flights'])
            ->latest()
            ->paginate(15);

        return view('ticketing.departures.index', compact('departures'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $pakets = Paket::all();

        return view('ticketing.departures.create', compact('pakets'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'paket_id'        => 'required|exists:pakets,id',
            'departure_code'  => 'nullable|string|max:30',
            'departure_date'  => 'required|date',
            'return_date'     => 'nullable|date|after:departure_date',
            'quota'           => 'required|integer|min:1',
        ]);

        $validated['booked']    = 0;
        $validated['is_active'] = true;
        $validated['is_closed'] = false;

        PaketDeparture::create($validated);

        return redirect()
            ->route('ticketing.departures.index')
            ->with('success', 'Departure created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(PaketDeparture $departure)
    {
        $pakets  = Paket::all();
        $flights = Flight::where('is_active', 1)->get();

        return view('ticketing.departures.edit', compact(
            'departure',
            'pakets',
            'flights'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, PaketDeparture $departure)
    {
        $validated = $request->validate([
            'paket_id'        => 'required|exists:pakets,id',
            'departure_code'  => 'nullable|string|max:30',
            'departure_date'  => 'required|date',
            'return_date'     => 'nullable|date|after:departure_date',
            'quota'           => 'required|integer|min:1',
            'is_active'       => 'nullable|boolean',
        ]);

        $departure->update($validated);

        /*
        |--------------------------------------------------------------------------
        | 🔥 Re-sync seat allocation jika quota berubah
        |--------------------------------------------------------------------------
        */
        foreach ($departure->flights as $flight) {
            $this->flightService->assign(
                $departure,
                $flight,
                $flight->pivot->type
            );
        }

        return back()->with('success', 'Departure updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(PaketDeparture $departure)
    {
        $departure->delete();

        return back()->with('success', 'Departure deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN FORM
    |--------------------------------------------------------------------------
    */
    public function assignForm(PaketDeparture $departure)
    {
        $flights = Flight::where('is_active', 1)->get();

        return view('ticketing.departures.assign', compact(
            'departure',
            'flights'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN FLIGHT
    |--------------------------------------------------------------------------
    */
    public function assignFlight(Request $request, PaketDeparture $departure)
    {
        $validated = $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'type'      => 'required|in:departure,return',
        ]);

        $flight = Flight::findOrFail($validated['flight_id']);

        /*
        |--------------------------------------------------------------------------
        | 🚫 Prevent assign inactive flight
        |--------------------------------------------------------------------------
        */
        if (!$flight->is_active) {
            return back()->with('error', 'Flight is not active.');
        }

        /*
        |--------------------------------------------------------------------------
        | 🚫 Prevent duplicate assign (same type)
        |--------------------------------------------------------------------------
        */
        if ($departure->flights()
            ->where('flight_id', $flight->id)
            ->wherePivot('type', $validated['type'])
            ->exists()
        ) {
            return back()->with('error', 'Flight already assigned for this type.');
        }

        /*
        |--------------------------------------------------------------------------
        | 🚫 Prevent over capacity
        |--------------------------------------------------------------------------
        */
        if ($flight->aircraft_capacity &&
            $departure->quota > $flight->aircraft_capacity
        ) {
            return back()->with('error',
                'Quota exceeds aircraft capacity (' .
                $flight->aircraft_capacity . ' seats).'
            );
        }

        $this->flightService->assign(
            $departure,
            $flight,
            $validated['type']
        );

        return back()->with('success', 'Flight assigned successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE CLOSE
    |--------------------------------------------------------------------------
    */
    public function toggleClose(PaketDeparture $departure)
    {
        $departure->update([
            'is_closed' => !$departure->is_closed
        ]);

        return back()->with('success', 'Departure status updated.');
    }
}
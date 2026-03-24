<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Services\Ticketing\SeatAllocationService;
use Illuminate\Http\Request;

class SeatAllocationController extends Controller
{
    public function __construct(
        protected SeatAllocationService $service
    ) {}

    public function index()
    {
        $allocations = \App\Models\SeatAllocation::with([
            'flight',
            'departure'
        ])
        ->latest()
        ->paginate(15);

        return view('ticketing.seat_allocations.index', compact('allocations'));
    }

    public function allocate(Request $request)
    {
        $request->validate([
            'flight_id' => 'required',
            'departure_id' => 'required',
            'seat' => 'required|integer|min:1'
        ]);

        $this->service->allocate(
            $request->flight_id,
            $request->departure_id,
            $request->seat
        );

        return back()->with('success', 'Seat allocated.');
    }

    public function release(Request $request)
    {
        $this->service->release(
            $request->flight_id,
            $request->departure_id,
            $request->seat
        );

        return back()->with('success', 'Seat released.');
    }
}
<?php
namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Services\Ticketing\ManifestService;
use Illuminate\Http\Request;

class FlightManifestController extends Controller
{
    public function __construct(
        protected ManifestService $service
    ) {}

    public function index()
    {
        $flights = \App\Models\Flight::with('departures')
            ->latest()
            ->get();

        return view('ticketing.manifests.index', compact('flights'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'flight_id' => 'required',
            'departure_id' => 'required'
        ]);

        $this->service->generate(
            $request->flight_id,
            $request->departure_id
        );

        return back()->with('success', 'Manifest generated.');
    }
}
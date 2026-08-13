<?php 

namespace App\Services\Ticketing;

use App\Models\FlightManifest;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class ManifestService
{
    public function generate($flightId, $departureId)
    {
        $bookings = Booking::where('departure_id', $departureId)
            ->where('status', 'confirmed')
            ->with('jamaahs')
            ->get();

        // nanti bisa generate PDF

        return FlightManifest::create([
            'flight_id'    => $flightId,
            'departure_id' => $departureId,
            'generated_at' => now(),
            'generated_by' => Auth::id(),
            'file_path'    => null,
        ]);
    }
}
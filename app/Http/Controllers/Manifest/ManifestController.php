<?php

namespace App\Http\Controllers\Manifest;

use App\Http\Controllers\Controller;
use App\Models\PaketDeparture;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\Manifest\SeatGeneratorService;
class ManifestController extends Controller
{

public function __construct(
    protected SeatGeneratorService $seatService
) {}
    public function index()
    {
        $departures = PaketDeparture::with('paket')
            ->latest('departure_date')
            ->paginate(15);

        return view('manifests.index', compact('departures'));
    }

    public function show(PaketDeparture $departure)
    {
        $departure->load([
            'paket',
            'bookings.jamaahs.branch',
            'bookings.jamaahs.agent',
        ]);

        $jamaahs = $departure->bookings
            ->flatMap(fn ($booking) => $booking->jamaahs)
            ->unique('id')
            ->values();

        return view('manifests.show', compact('departure','jamaahs'));
    }

    public function exportPdf(PaketDeparture $departure)
    {
        $departure->load([
            'paket',
            'bookings.jamaahs.branch',
            'bookings.jamaahs.agent',
        ]);

        $jamaahs = $departure->bookings
            ->flatMap(fn ($booking) => $booking->jamaahs)
            ->unique('id')
            ->values();

        $pdf = Pdf::loadView('manifests.pdf', [
            'departure' => $departure,
            'jamaahs'   => $jamaahs,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream(
            'manifest-'.$departure->departure_date->format('Ymd').'.pdf'
        );
    }
    public function generateSeat(PaketDeparture $departure)
    {
        $this->seatService->generate($departure);

        return back()->with('success', 'Seat layout berhasil digenerate.');
    }

    public function exportNameTag(PaketDeparture $departure)
    {
        $departure->load([
            'paket',
            'bookings.jamaahs.branch'
        ]);

        $jamaahs = $departure->bookings
            ->flatMap(fn ($booking) => $booking->jamaahs)
            ->unique('id')
            ->values();

            $pdf = Pdf::loadView('manifests.nametag', [
                'departure' => $departure,
                'jamaahs'   => $jamaahs,
            ])->setPaper('a4', 'portrait');

        return $pdf->stream('nametag-'.$departure->departure_date->format('Ymd').'.pdf');
    }

}
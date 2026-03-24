<?php

namespace App\Http\Controllers\Rooming;

use App\Http\Controllers\Controller;
use App\Models\PaketDeparture;
use App\Models\Room;
use App\Services\Rooming\RoomingService;
use Illuminate\Http\Request;

class RoomingController extends Controller
{
    public function __construct(
        protected RoomingService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX (List Departure)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $departures = PaketDeparture::withCount('rooms')
            ->with('paket')
            ->latest('departure_date')
            ->paginate(15);

        return view('rooming.index', compact('departures'));
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW (Detail Rooming + Drag Drop)
    |--------------------------------------------------------------------------
    */
    public function show(PaketDeparture $departure)
    {
        $departure->load([
            'rooms.jamaahs',
            'bookings.jamaahs'
        ]);

        $assignedIds = $departure->rooms
            ->flatMap(fn ($room) => $room->jamaahs)
            ->pluck('id');

        $unassigned = $departure->bookings
            ->flatMap(fn ($booking) => $booking->jamaahs)
            ->where('approval_status', 'approved')
            ->whereNotIn('id', $assignedIds)
            ->unique('id')
            ->values();

        return view('rooming.show', [
            'departure' => $departure,
            'rooms' => $departure->rooms,
            'unassigned' => $unassigned
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE AUTO (SMART COUPLE + FAMILY)
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request, PaketDeparture $departure)
    {
        $request->validate([
            'city'  => 'required|in:makkah,madinah',
            'hotel' => 'nullable|string|max:255'
        ]);

        // Optional: clear dulu sebelum generate ulang
        $departure->rooms()->each(function ($room) {
            $room->jamaahs()->detach();
            $room->delete();
        });

        $this->service->generate(
            $departure,
            $request->city,
            $request->hotel
        );

        return back()->with('success','Rooming berhasil digenerate (Smart Mode).');
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN (Drag & Drop AJAX)
    |--------------------------------------------------------------------------
    */
    public function assign(Request $request)
    {
        $request->validate([
            'room_id'   => 'required|exists:rooms,id',
            'jamaah_id' => 'required|exists:jamaahs,id'
        ]);

        $room = Room::withCount('jamaahs')->findOrFail($request->room_id);

        if ($room->jamaahs_count >= $room->capacity) {
            return response()->json(['error' => 'Room sudah penuh'], 422);
        }

        if ($room->jamaahs()->where('jamaah_id',$request->jamaah_id)->exists()) {
            return response()->json(['error' => 'Jamaah sudah ada di room'], 422);
        }

        $room->jamaahs()->attach($request->jamaah_id);

        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE JAMAah FROM ROOM (Optional Advanced)
    |--------------------------------------------------------------------------
    */
    public function detach(Room $room, $jamaahId)
    {
        $room->jamaahs()->detach($jamaahId);

        return back()->with('success','Jamaah dipindahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE ROOM
    |--------------------------------------------------------------------------
    */
    public function destroy(Room $room)
    {
        $room->jamaahs()->detach();
        $room->delete();

        return back()->with('success','Room berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | CLEAR ALL ROOMING
    |--------------------------------------------------------------------------
    */
    public function clear(PaketDeparture $departure)
    {
        $departure->rooms()->each(function ($room) {
            $room->jamaahs()->detach();
            $room->delete();
        });

        return back()->with('success','Semua room berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */
    public function exportPdf(PaketDeparture $departure)
    {
        $departure->load('rooms.jamaahs');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'rooming.pdf',
            compact('departure')
        )->setPaper('a4','portrait');

        return $pdf->stream(
            'rooming-'.$departure->departure_date->format('Ymd').'.pdf'
        );
    }
}
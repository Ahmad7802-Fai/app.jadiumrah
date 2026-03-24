<?php

namespace App\Http\Controllers\Departure;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\PaketDeparture;
use Illuminate\Http\Request;

class DepartureController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PaketDeparture::class, 'departure');
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $this->authorize('viewAny', PaketDeparture::class);

        $query = PaketDeparture::with('paket')
            ->latest('departure_date');

        if ($request->search) {
            $query->whereHas('paket', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status === 'open') {
            $query->where('is_closed', false);
        }

        if ($request->status === 'closed') {
            $query->where('is_closed', true);
        }

        $departures = $query->paginate(15);

        return view('departures.index', compact('departures'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $this->authorize('create', PaketDeparture::class);

        $pakets = Paket::where('is_active', true)->get();

        return view('departures.create', compact('pakets'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $this->authorize('create', PaketDeparture::class);

        $validated = $request->validate([
            'paket_id'       => 'required|exists:pakets,id',
            'departure_code' => 'nullable|string|max:30',
            'flight_number'  => 'nullable|string|max:50',
            'meeting_point'  => 'nullable|string|max:255',
            'departure_date' => 'required|date',
            'return_date'    => 'nullable|date|after_or_equal:departure_date',
            'quota'          => 'required|integer|min:1',
            'is_active'      => 'boolean',
        ]);

        $validated['booked']    = 0;
        $validated['is_closed'] = false;

        PaketDeparture::create($validated);

        return redirect()
            ->route('departures.index')
            ->with('success', 'Departure berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(PaketDeparture $departure)
    {
        $this->authorize('update', $departure);

        $pakets = Paket::where('is_active', true)->get();

        return view('departures.edit', compact('departure', 'pakets'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, PaketDeparture $departure)
    {
        $this->authorize('update', $departure);

        $validated = $request->validate([
            'paket_id'       => 'required|exists:pakets,id',
            'departure_code' => 'nullable|string|max:30',
            'flight_number'  => 'nullable|string|max:50',
            'meeting_point'  => 'nullable|string|max:255',
            'departure_date' => 'required|date',
            'return_date'    => 'nullable|date|after_or_equal:departure_date',
            'quota'          => 'required|integer|min:1',
            'is_active'      => 'boolean',
        ]);

        $departure->update($validated);
        $departure->checkAndAutoClose();

        return redirect()
            ->route('departures.index')
            ->with('success', 'Departure berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(PaketDeparture $departure)
    {
        $this->authorize('delete', $departure);

        if ($departure->booked > 0) {
            return back()->with('error', 'Tidak bisa hapus departure yang sudah ada booking.');
        }

        $departure->delete();

        return redirect()
            ->route('departures.index')
            ->with('success', 'Departure berhasil dihapus.');
    }
}
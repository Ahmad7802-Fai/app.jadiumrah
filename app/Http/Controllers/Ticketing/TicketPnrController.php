<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\{
    TicketPnr,
    Client,
    Airline
};
use App\Services\Ticketing\TicketPnrService;
use Illuminate\Http\Request;

class TicketPnrController extends Controller
{
    /* =========================
     | INDEX
     ========================= */
    public function index()
    {
        $pnrs = TicketPnr::with('client')
            ->latest()
            ->paginate(20);

        return view('ticketing.pnr.index', compact('pnrs'));
    }

    /* =========================
     | CREATE
     ========================= */
    public function create()
    {
        $this->authorize('create', TicketPnr::class);

        $clients  = Client::orderBy('nama')->get();
        $airlines = Airline::orderBy('name')->get();

        return view('ticketing.pnr.create', compact(
            'clients',
            'airlines'
        ));
    }

    /* =========================
     | STORE
     ========================= */
    public function store(
        Request $request,
        TicketPnrService $service
    ) {
        $this->authorize('create', TicketPnr::class);

        $validated = $request->validate([
            'pnr_code' => 'required|string|max:20|unique:ticket_pnrs,pnr_code',
            'client_id' => 'required|integer|exists:clients,id',
            'airline_code' => 'nullable|string|max:10',
            'airline_name' => 'nullable|string|max:100',
            'airline_class' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
            'pax' => 'required|integer|min:1',
            'fare_per_pax' => 'required|integer|min:0',

            'routes' => 'required|array|min:1',
            'routes.*.origin' => 'required|string|max:10',
            'routes.*.destination' => 'required|string|max:10',
            'routes.*.departure_date' => 'required|date',
            'routes.*.flight_number' => 'nullable|string|max:20',
            'routes.*.departure_time' => 'nullable|date_format:H:i',
            'routes.*.arrival_time' => 'nullable|date_format:H:i',
            'routes.*.arrival_day_offset' => 'nullable|integer|in:0,1',
        ]);

        $pnr = $service->create(
            $validated + [
                'created_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('ticketing.pnr.show', $pnr)
            ->with('success', 'PNR berhasil dibuat');
    }

    /* =========================
     | SHOW
     ========================= */
    public function show(TicketPnr $pnr)
    {
        $this->authorize('view', $pnr);

        $pnr->load([
            'routes',
            'client',
            'agent',
            'invoices.payments',
            'invoices.refunds',
        ]);

        return view('ticketing.pnr.show', compact('pnr'));
    }
    /* =========================
     | EDIT
     ========================= */
    public function edit(TicketPnr $pnr)
    {
        $this->authorize('update', $pnr);

        $clients  = Client::orderBy('nama')->get();
        $airlines = Airline::orderBy('name')->get();

        return view('ticketing.pnr.edit', compact(
            'pnr',
            'clients',
            'airlines'
        ));
    }

    /* =========================
     | UPDATE
     ========================= */
    public function update(
        Request $request,
        TicketPnr $pnr
    ) {
        $this->authorize('update', $pnr);

        $pnr->update($request->only([
            'pnr_code',
            'client_id',
            'airline_code',
            'airline_name',
            'airline_class',
            'category',
            'pax',
            'fare_per_pax',
        ]));

        return redirect()
            ->route('ticketing.pnr.show', $pnr)
            ->with('success', 'PNR berhasil diperbarui');
    }

    /* =========================
     | CONFIRM
     ========================= */
    public function confirm(
        TicketPnr $pnr,
        TicketPnrService $service
    ) {
        $this->authorize('confirm', $pnr);

        $service->confirm($pnr);

        return back()->with('success', 'PNR dikonfirmasi');
    }

        /* ======================================================
    | EDIT ROUTES (FLIGHT SECTORS)
    ====================================================== */
    public function editRoutes(TicketPnr $pnr)
    {
        $this->authorize('update', $pnr);

        // safety: issued tidak boleh edit
        if ($pnr->status === 'ISSUED') {
            abort(403, 'PNR sudah ISSUED dan tidak bisa diubah.');
        }

        return view('ticketing.pnr.routes.edit', [
            'pnr' => $pnr->load('routes'),
        ]);
    }

    /* ======================================================
    | UPDATE ROUTES
    ====================================================== */
    public function updateRoutes(Request $request, TicketPnr $pnr)
    {
        $this->authorize('update', $pnr);

        if ($pnr->status === 'ISSUED') {
            abort(403);
        }

        $data = $request->validate([
            'routes' => 'required|array|min:1',
            'routes.*.origin' => 'required|string|max:10',
            'routes.*.destination' => 'required|string|max:10',
            'routes.*.departure_date' => 'required|date',
            'routes.*.flight_number' => 'nullable|string|max:20',
            'routes.*.departure_time' => 'nullable',
            'routes.*.arrival_time' => 'nullable',
            'routes.*.arrival_day_offset' => 'nullable|integer',
        ]);

        // reset routes
        $pnr->routes()->delete();

        foreach ($data['routes'] as $i => $route) {
            $pnr->routes()->create([
                'sector' => $i + 1,
                ...$route,
            ]);
        }

        return redirect()
            ->route('ticketing.pnr.show', $pnr)
            ->with('success', 'Flight routes berhasil diperbarui');
    }

}

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
        $pnr = $service->create(
            $request->all() + [
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
        $pnr->load([
            'routes',
            'client',
            'agent',
            'invoices.payments',
            'invoices.refunds',
            'allocations', // 🔥 INI YANG HILANG
        ]);

        return view('ticketing.pnr.show', compact('pnr'));
    }
    /* =========================
     | EDIT
     ========================= */
    public function edit(TicketPnr $pnr)
    {
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
        $service->confirm($pnr);

        return back()->with('success', 'PNR dikonfirmasi');
    }

        /* ======================================================
    | EDIT ROUTES (FLIGHT SECTORS)
    ====================================================== */
    public function editRoutes(TicketPnr $pnr)
    {
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


// namespace App\Http\Controllers\Ticketing;

// use App\Http\Controllers\Controller;
// use App\Models\{
//     TicketPnr,
//     Client,
//     Airline
// };
// use App\Services\Ticketing\TicketPnrService;
// use Illuminate\Http\Request;

// class TicketPnrController extends Controller
// {
//     /* ======================================================
//      | INDEX
//      ====================================================== */
//     public function index()
//     {
//         $pnrs = TicketPnr::latest()->paginate(20);

//         return view('ticketing.pnr.index', compact('pnrs'));
//     }

//     /* ======================================================
//      | CREATE
//      ====================================================== */
//     public function create()
//     {
//         return view('ticketing.pnr.create', [
//             'clients'  => Client::orderBy('nama')->get(),
//             'airlines' => Airline::orderBy('code')->get(),
//         ]);
//     }

//     /* ======================================================
//      | STORE
//      ====================================================== */
//     public function store(
//         Request $request,
//         TicketPnrService $service
//     ) {
//         $data = $request->validate([
//             'pnr_code'        => 'required|string|max:20|unique:ticket_pnrs,pnr_code',
//             'client_id'       => 'required|exists:clients,id',

//             'airline_code'    => 'required|string|max:10',
//             'airline_name'    => 'required|string|max:100',
//             'airline_class'   => 'nullable|string|max:50',

//             'category'        => 'nullable|string|max:50',

//             'pax'             => 'required|integer|min:1',
//             'fare_per_pax'    => 'required|integer|min:0',

//             'routes'                          => 'required|array|min:1',
//             'routes.*.origin'                 => 'required|string|max:10',
//             'routes.*.destination'            => 'required|string|max:10',
//             'routes.*.departure_date'         => 'required|date',
//             'routes.*.flight_number'          => 'nullable|string|max:20',
//             'routes.*.departure_time'         => 'nullable',
//             'routes.*.arrival_time'           => 'nullable',
//             'routes.*.arrival_day_offset'     => 'nullable|integer',
//         ]);

//         $data['created_by'] = auth()->id();

//         $pnr = $service->create($data);

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'PNR berhasil dibuat');
//     }

//     /* ======================================================
//      | SHOW
//      ====================================================== */
//     public function show(TicketPnr $pnr)
//     {
//         $pnr->load(['routes', 'client', 'agent', 'invoices']);

//         return view('ticketing.pnr.show', compact('pnr'));
//     }

//     /* ======================================================
//      | EDIT
//      ====================================================== */
//     public function edit(TicketPnr $pnr)
//     {
//         return view('ticketing.pnr.edit', [
//             'pnr'      => $pnr->load('routes'),
//             'clients'  => Client::orderBy('nama')->get(),
//             'airlines' => Airline::orderBy('code')->get(),
//         ]);
//     }

//     /* ======================================================
//      | UPDATE
//      ====================================================== */
//     public function update(
//         Request $request,
//         TicketPnr $pnr
//     ) {
//         $data = $request->validate([
//             'client_id'     => 'required|exists:clients,id',
//             'airline_code'  => 'required|string|max:10',
//             'airline_name'  => 'required|string|max:100',
//             'airline_class' => 'nullable|string|max:50',
//             'category'      => 'nullable|string|max:50',
//         ]);

//         $pnr->update($data);

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'PNR berhasil diperbarui');
//     }

//         public function confirm(
//         TicketPnr $pnr,
//         TicketPnrService $service
//     ) {
//         $service->confirm($pnr);

//         return back()->with('success', 'PNR dikonfirmasi');
//     }

//     public function cancel(
//         TicketPnr $pnr,
//         TicketPnrService $service
//     ) {
//         $service->cancel($pnr);

//         return back()->with('success', 'PNR dibatalkan');
//     }

//     /* ======================================================
//     | EDIT ROUTES (FLIGHT SECTORS)
//     ====================================================== */
//     public function editRoutes(TicketPnr $pnr)
//     {
//         // safety: issued tidak boleh edit
//         if ($pnr->status === 'ISSUED') {
//             abort(403, 'PNR sudah ISSUED dan tidak bisa diubah.');
//         }

//         return view('ticketing.pnr.routes.edit', [
//             'pnr' => $pnr->load('routes'),
//         ]);
//     }

//     /* ======================================================
//     | UPDATE ROUTES
//     ====================================================== */
//     public function updateRoutes(Request $request, TicketPnr $pnr)
//     {
//         if ($pnr->status === 'ISSUED') {
//             abort(403);
//         }

//         $data = $request->validate([
//             'routes' => 'required|array|min:1',
//             'routes.*.origin' => 'required|string|max:10',
//             'routes.*.destination' => 'required|string|max:10',
//             'routes.*.departure_date' => 'required|date',
//             'routes.*.flight_number' => 'nullable|string|max:20',
//             'routes.*.departure_time' => 'nullable',
//             'routes.*.arrival_time' => 'nullable',
//             'routes.*.arrival_day_offset' => 'nullable|integer',
//         ]);

//         // reset routes
//         $pnr->routes()->delete();

//         foreach ($data['routes'] as $i => $route) {
//             $pnr->routes()->create([
//                 'sector' => $i + 1,
//                 ...$route,
//             ]);
//         }

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'Flight routes berhasil diperbarui');
//     }


// }


// namespace App\Http\Controllers\Ticketing;

// use App\Http\Controllers\Controller;
// use App\Models\TicketPnr;
// use App\Services\Ticketing\TicketPnrService;
// use Illuminate\Http\Request;

// class TicketPnrController extends Controller
// {
//     public function index()
//     {
//         $pnrs = TicketPnr::with(['client'])
//             ->latest()
//             ->paginate(20);

//         return view('ticketing.pnr.index', compact('pnrs'));
//     }

//     public function show(TicketPnr $pnr)
//     {
//         $pnr->load([
//             'client',
//             'routes',
//             'invoices.payments',
//             'invoices.refunds',
//         ]);

//         return view('ticketing.pnr.show', compact('pnr'));
//     }


//     public function create()
//     {
//         return view('ticketing.pnr.create');
//     }

//     public function store(
//         Request $request,
//         TicketPnrService $service
//     ) {
//         $data = $request->validate([
//             'pnr_code'        => 'required|string|max:20|unique:ticket_pnrs,pnr_code',
//             'client_id'       => 'required|exists:clients,id',
//             'category'        => 'nullable|string',

//             'airline_code'    => 'nullable|string|max:10',
//             'airline_name'    => 'nullable|string|max:100',
//             'airline_class'   => 'nullable|string',

//             'pax'             => 'required|integer|min:1',
//             'fare_per_pax'    => 'required|integer|min:0',

//             'routes'          => 'required|array|min:1',
//             'routes.*.origin' => 'required|string|max:10',
//             'routes.*.destination' => 'required|string|max:10',
//             'routes.*.departure_date' => 'required|date',
//         ]);

//         $data['created_by'] = auth()->id();

//         $pnr = $service->create($data);

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'PNR berhasil dibuat');
//     }

//     public function edit(TicketPnr $pnr)
//     {
//         // ❌ Tidak boleh edit jika sudah ada invoice
//         if ($pnr->invoices()->exists()) {
//             abort(403, 'PNR sudah memiliki invoice');
//         }

//         // ❌ Tidak boleh edit jika bukan ON_FLOW
//         if ($pnr->status !== 'ON_FLOW') {
//             abort(403, 'PNR tidak bisa diedit');
//         }

//         $pnr->load('routes');

//         return view('ticketing.pnr.edit', compact('pnr'));
//     }

//     public function update(
//         Request $request,
//         TicketPnr $pnr,
//         TicketPnrService $service
//     ) {
//         $data = $request->validate([
//             'airline_code'  => 'nullable|string|max:10',
//             'airline_name'  => 'nullable|string|max:100',
//             'airline_class' => 'nullable|string',

//             'pax'           => 'required|integer|min:1',
//             'fare_per_pax'  => 'required|integer|min:0',

//             'routes'        => 'required|array|min:1',
//         ]);

//         $service->update($pnr, $data);

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'PNR berhasil diperbarui');
//     }


//     public function confirm(
//         TicketPnr $pnr,
//         TicketPnrService $service
//     ) {
//         $service->confirm($pnr);

//         return back()->with('success', 'PNR dikonfirmasi');
//     }

//     public function cancel(
//         TicketPnr $pnr,
//         TicketPnrService $service
//     ) {
//         $service->cancel($pnr);

//         return back()->with('success', 'PNR dibatalkan');
//     }
// }


// namespace App\Http\Controllers\Ticketing;

// use App\Http\Controllers\Controller;
// use App\Models\TicketPnr;
// use App\Models\Client;
// use App\Models\TicketPnrRoute;
// use App\Models\TicketRoute;
// use App\Models\Airline;
// use App\Services\Ticketing\TicketPnrService;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class TicketPnrController extends Controller
// {
//     public function __construct(
//         protected TicketPnrService $service
//     ) {}

//     /* ======================================================
//      | INDEX
//      ====================================================== */
//     public function index()
//     {
//         $pnrs = TicketPnr::with(['client', 'agent'])
//             ->latest()
//             ->paginate(20);

//         return view('ticketing.pnr.index', compact('pnrs'));
//     }

//     /* ======================================================
//      | SHOW
//      ====================================================== */
//     public function show(TicketPnr $pnr)
//     {
//         $this->authorize('view', $pnr);

//         $pnr->load(['client', 'agent', 'routes']);

//         return view('ticketing.pnr.show', compact('pnr'));
//     }

//     /* ======================================================
//      | CREATE
//      ====================================================== */
//     public function create()
//     {
//         $this->authorize('create', TicketPnr::class);

//         $clients = Client::orderBy('nama')->get();

//         $airlines = Airline::where('is_active', 1)
//             ->orderBy('name')
//             ->get();

//         return view('ticketing.pnr.create', compact(
//             'clients',
//             'airlines'
//         ));
//     }

//     /* ======================================================
//      | STORE
//      ====================================================== */

//     public function store(Request $request)
//     {
//         $this->authorize('create', TicketPnr::class);

//         $data = $request->validate([
//             // =====================
//             // CORE
//             // =====================
//             'pnr_code'   => 'required|string|max:20|unique:ticket_pnrs,pnr_code',
//             'client_id'  => 'required|exists:clients,id',
//             'category'   => 'nullable|string|max:50',

//             // =====================
//             // AIRLINE
//             // =====================
//             'airline_code'  => 'required|string|max:10',
//             'airline_name'  => 'required|string|max:255',
//             'airline_class' => 'nullable|string|max:50',

//             // =====================
//             // PRICING
//             // =====================
//             'pax'             => 'required|integer|min:1',
//             'fare_per_pax'    => 'required|numeric|min:0',
//             'deposit_per_pax' => 'required|numeric|min:0',
//             'seat'            => 'nullable|integer|min:0',

//             // =====================
//             // ROUTES
//             // =====================
//             'routes' => 'required|array|min:1',
//             'routes.*.origin'         => 'required|string|max:10',
//             'routes.*.destination'    => 'required|string|max:10',
//             'routes.*.departure_date' => 'required|date',

//             // ⏱️ JAM
//             'routes.*.departure_time'     => 'nullable|date_format:H:i',
//             'routes.*.arrival_time'       => 'nullable|date_format:H:i',
//             'routes.*.arrival_day_offset' => 'nullable|integer|min:0|max:1',

//             'routes.*.flight_number' => 'nullable|string|max:20',
//         ]);

//         DB::transaction(function () use (&$data, &$pnr) {

//             // ✈️ AUTO CREATE AIRLINE
//             Airline::firstOrCreate(
//                 ['code' => strtoupper($data['airline_code'])],
//                 ['name' => $data['airline_name']]
//             );

//             $data['created_by'] = auth()->id();

//             // 🔥 SEMUA LOGIC KE SERVICE
//             $pnr = $this->service->create($data);
//         });

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'PNR berhasil dibuat.');
//     }

//     /* ======================================================
//      | EDIT (LOCKED)
//      ====================================================== */
//     public function edit(TicketPnr $pnr)
//     {
//         $this->authorize('update', $pnr);

//         abort_if(
//             !$pnr->isOnFlow(),
//             403,
//             'PNR sudah dikonfirmasi / issued dan tidak bisa diedit.'
//         );

//         $pnr->load(['routes', 'client']);
//         $clients  = Client::orderBy('nama')->get();
//         $airlines = Airline::orderBy('name')->get();

//         return view('ticketing.pnr.edit', compact('pnr', 'clients', 'airlines'));
//     }


//     /* ======================================================
//      | UPDATE (AUTO RECALC – LOCKED)
//      ====================================================== */
//     public function update(Request $request, TicketPnr $pnr)
//     {
//         $this->authorize('update', $pnr);

//         abort_if(
//             !$pnr->isOnFlow(),
//             403,
//             'PNR sudah dikonfirmasi dan tidak bisa diubah.'
//         );

//         $data = $request->validate([
//             // =====================
//             // CORE (EDITABLE)
//             // =====================
//             'client_id' => 'required|exists:clients,id',
//             'agent_id'  => 'nullable|exists:agents,id',
//             'category'  => 'nullable|string|max:50',

//             // ❌ PNR CODE & AIRLINE SENGAJA TIDAK DIVALIDASI (LOCK)

//             'airline_class' => 'nullable|string|max:50',

//             // =====================
//             // PRICING
//             // =====================
//             'pax'             => 'required|integer|min:1',
//             'fare_per_pax'    => 'required|numeric|min:0',
//             'deposit_per_pax' => 'required|numeric|min:0',
//             'seat'            => 'nullable|integer|min:0',
//         ]);

//         /* 🔢 SERVER SIDE RECALC (WAJIB) */
//         $data['total_fare']    = $data['pax'] * $data['fare_per_pax'];
//         $data['total_deposit'] = $data['pax'] * $data['deposit_per_pax'];
//         $data['balance']       = max(0, $data['total_fare'] - $data['total_deposit']);

//         $pnr->update($data);

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'PNR berhasil diperbarui.');
//     }

//     /* ======================================================
//      | EDIT ROUTES (LOCKED)
//      ====================================================== */
//     public function editRoutes(TicketPnr $pnr)
//     {
//         $this->authorize('update', $pnr);

//         abort_if(
//             !$pnr->isOnFlow(),
//             403,
//             'Flight sector terkunci.'
//         );

//         $pnr->load('routes');

//         return view('ticketing.pnr.routes_edit', compact('pnr'));
//     }

//     /* ======================================================
//      | UPDATE ROUTES (FULL LOCK)
//      ====================================================== */
//     public function updateRoutes(Request $request, TicketPnr $pnr)
//     {
//         $this->authorize('update', $pnr);

//         abort_if(
//             !$pnr->isOnFlow(),
//             403,
//             'Flight sector tidak bisa diubah.'
//         );

//         /* ======================================================
//         | VALIDATION
//         ====================================================== */
//         $data = $request->validate([
//             'routes' => 'required|array|min:1',

//             'routes.*.id'                 => 'nullable|exists:ticket_routes,id',
//             'routes.*.origin'             => 'required|string|max:10',
//             'routes.*.destination'        => 'required|string|max:10',
//             'routes.*.departure_date'     => 'required|date',

//             'routes.*.departure_time'     => 'nullable|date_format:H:i',
//             'routes.*.arrival_time'       => 'nullable|date_format:H:i',
//             'routes.*.arrival_day_offset' => 'nullable|integer|min:0|max:1',

//             'routes.*.flight_number'      => 'nullable|string|max:20',
//         ]);

//         /* ======================================================
//         | TRANSACTION (FULL LOCK SAFE)
//         ====================================================== */
//         DB::transaction(function () use ($pnr, $data) {

//             /* ===============================
//             | EXISTING & INCOMING IDS
//             =============================== */
//             $existingIds = $pnr->routes()->pluck('id')->toArray();

//             $incomingIds = collect($data['routes'])
//                 ->pluck('id')
//                 ->filter()
//                 ->toArray();

//             /* ===============================
//             | DELETE REMOVED ROUTES
//             =============================== */
//             $deleteIds = array_diff($existingIds, $incomingIds);

//             if (!empty($deleteIds)) {
//                 TicketRoute::whereIn('id', $deleteIds)->delete();
//             }

//             /* ===============================
//             | UPSERT ROUTES
//             =============================== */
//             foreach ($data['routes'] as $index => $route) {

//                 $payload = [
//                     'sector'              => $index + 1,
//                     'origin'              => strtoupper($route['origin']),
//                     'destination'         => strtoupper($route['destination']),
//                     'departure_date'      => $route['departure_date'],

//                     'departure_time'      => $route['departure_time'] ?? null,
//                     'arrival_time'        => $route['arrival_time'] ?? null,
//                     'arrival_day_offset'  => $route['arrival_day_offset'] ?? 0,

//                     'flight_number'       => $route['flight_number'] ?? null,
//                 ];

//                 if (!empty($route['id'])) {
//                     TicketRoute::where('id', $route['id'])->update($payload);
//                 } else {
//                     $pnr->routes()->create($payload);
//                 }
//             }
//         });

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'Flight sector berhasil diperbarui.');
//     }

//     /* ======================================================
//      | CONFIRM PNR (ON_FLOW → CONFIRMED)
//      ====================================================== */
//     public function confirm(TicketPnr $pnr)
//     {
//         $this->authorize('update', $pnr);

//         DB::transaction(function () use ($pnr) {

//             $pnr->lockForUpdate();

//             abort_if(
//                 !$pnr->isOnFlow(),
//                 403,
//                 'PNR tidak bisa dikonfirmasi.'
//             );

//             abort_if(
//                 $pnr->routes()->count() === 0,
//                 422,
//                 'Flight sector belum diisi.'
//             );

//             $pnr->update([
//                 'status' => 'CONFIRMED',
//             ]);
//         });

//         return redirect()
//             ->route('ticketing.pnr.show', $pnr)
//             ->with('success', 'PNR berhasil dikonfirmasi.');
//     }


//     public function storeJson(Request $request)
//     {
//         $this->authorize('create', TicketPnr::class);

//         $data = $request->validate([
//             'pnr_code'        => 'required|string|max:20|unique:ticket_pnrs,pnr_code',
//             'client_id'       => 'required|exists:clients,id',
//             'agent_id'        => 'nullable|exists:agents,id',
//             'airline_class'   => 'nullable|string|max:50',

//             'pax'             => 'required|integer|min:1',
//             'fare_per_pax'    => 'required|numeric|min:0',
//             'deposit_per_pax' => 'required|numeric|min:0',
//             'seat'            => 'nullable|integer|min:0',

//             'routes' => 'required|array|min:1',
//             'routes.*.origin'        => 'required|string|max:10',
//             'routes.*.destination'   => 'required|string|max:10',
//             'routes.*.departure_date'=> 'required|date',
//             'routes.*.flight_number' => 'nullable|string|max:20',
//         ]);

//         $data['created_by'] = auth()->id();

//         $pnr = $this->service->create($data);

//         return response()->json([
//             'status'   => 'ok',
//             'redirect' => route('ticketing.pnr.show', $pnr),
//         ]);
//     }

// }

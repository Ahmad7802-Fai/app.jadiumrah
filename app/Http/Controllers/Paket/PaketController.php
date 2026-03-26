<?php

namespace App\Http\Controllers\Paket;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Destination;
use App\Services\Pakets\PaketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaketController extends Controller
{
    protected PaketService $service;

    public function __construct(PaketService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $this->authorize('viewAny', Paket::class);

        $pakets = Paket::query()
            ->withCount('departures')
            ->with(['departures' => fn($q) => $q->orderBy('departure_date')->limit(1)])

            ->leftJoin('paket_departures', 'pakets.id', '=', 'paket_departures.paket_id')
            ->leftJoin('paket_departure_prices', 'paket_departures.id', '=', 'paket_departure_prices.paket_departure_id')

            ->select(
                'pakets.*',

                DB::raw('
                    MIN(
                        CASE
                            WHEN paket_departure_prices.promo_type = "percent"
                                AND (
                                    paket_departure_prices.promo_expires_at IS NULL
                                    OR paket_departure_prices.promo_expires_at > NOW()
                                )
                            THEN paket_departure_prices.price
                                - (paket_departure_prices.price * paket_departure_prices.promo_value / 100)

                            WHEN paket_departure_prices.promo_type = "fixed"
                                AND (
                                    paket_departure_prices.promo_expires_at IS NULL
                                    OR paket_departure_prices.promo_expires_at > NOW()
                                )
                            THEN paket_departure_prices.price
                                - paket_departure_prices.promo_value

                            ELSE paket_departure_prices.price
                        END
                    ) as base_price
                '),

                DB::raw('MIN(paket_departure_prices.price) as original_price')
            )

            ->groupBy('pakets.id')
            ->latest('pakets.created_at')
            ->paginate(15);

        return view('pakets.index', compact('pakets'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $this->authorize('create', Paket::class);

        $paket = new Paket();
        $paket->setRelation('prices', collect());
        $paket->setRelation('hotels', collect());
        $paket->setRelation('itinerary', collect());
        $paket->setRelation('departures', collect());

        $destinations = Destination::where('is_active', true)->get();

        return view('pakets.create', compact('paket','destinations'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (🔥 FIX MULTI GALLERY)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $this->authorize('create', Paket::class);

        $validated = $this->validateData($request);

        // 🔥 WAJIB: ambil file manual
        $validated['thumbnail'] = $request->file('thumbnail');
        $validated['gallery']   = $request->file('gallery');

        try {
            $this->service->create($validated);

            return redirect()
                ->route('pakets.index')
                ->with('success','Paket berhasil dibuat.');

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(Paket $paket)
    {
        $this->authorize('view', $paket);

        $paket->load([
            'prices',
            'hotels',
            'itinerary.destination',
            'departures.prices'
        ]);

        return view('pakets.show', compact('paket'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Paket $paket)
    {
        $paket->load([
            'prices',
            'hotels',
            'itinerary.destination',
            'departures.prices',
        ]);

        $destinations = Destination::where('is_active', true)->get();

        return view('pakets.edit', compact('paket','destinations'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE (🔥 FIX MULTI GALLERY)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Paket $paket)
    {
        $this->authorize('update', $paket);

        $validated = $this->validateData($request);

        // 🔥 WAJIB: ambil file manual
        $validated['thumbnail'] = $request->file('thumbnail');

        // 🔥 JANGAN override kalau kosong
        if ($request->hasFile('gallery')) {
            $validated['gallery'] = $request->file('gallery');
        }

        try {
            $this->service->update($paket, $validated);

            return redirect()
                ->route('pakets.index')
                ->with('success','Paket berhasil diperbarui.');

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Paket $paket)
    {
        $this->authorize('delete', $paket);

        try {
            $this->service->delete($paket);

            return redirect()
                ->route('pakets.index')
                ->with('success','Paket berhasil dihapus.');

        } catch (\Throwable $e) {

            return back()
                ->with('error',$e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */
    private function validateData(Request $request): array
    {
        return $request->validate([

            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',

            'departure_city' => 'nullable|string|max:255',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'duration_days' => 'nullable|integer|min:1',
            'airline' => 'nullable|string|max:255',
            'quota' => 'nullable|integer|min:1',

            'short_description' => 'nullable|string',
            'description' => 'nullable|string',

            'is_active' => 'boolean',
            'is_published' => 'boolean',

            /*
            | MEDIA
            */
            'thumbnail' => 'nullable|image|max:2048',
            'gallery'   => 'nullable|array',
            'gallery.*' => 'image|max:4096',

            /*
            | RELATIONS (dipersingkat)
            */
            'hotels' => 'nullable|array',
            'itinerary' => 'nullable|array',
            'departures' => 'nullable|array',
        ]);
    }
}
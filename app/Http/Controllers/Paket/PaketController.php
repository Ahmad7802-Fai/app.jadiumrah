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

            ->with(['departures' => function ($q) {
                $q->orderBy('departure_date')->limit(1);
            }])

            ->leftJoin('paket_departures', 'pakets.id', '=', 'paket_departures.paket_id')

            ->leftJoin(
                'paket_departure_prices',
                'paket_departures.id',
                '=',
                'paket_departure_prices.paket_departure_id'
            )

            ->select(
                'pakets.*',

                // 🔥 FINAL PRICE (SUDAH INCLUDE PROMO)
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

                // 🔥 ORIGINAL PRICE (UNTUK STRIKE)
                DB::raw('
                    MIN(paket_departure_prices.price) as original_price
                ')
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
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $this->authorize('create', Paket::class);

        $validated = $this->validateData($request);

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
            'departures.prices', // 🔥 WAJIB INI
        ]);

        $destinations = Destination::where('is_active', true)->get();

        return view('pakets.edit', compact('paket','destinations'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Paket $paket)
    {
        $this->authorize('update', $paket);

        $validated = $this->validateData($request);

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
    | DESTROY
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
    | LOAD ACTIVE DEPARTURES (FOR BOOKING AJAX)
    |--------------------------------------------------------------------------
    */
    public function departures(Paket $paket)
    {
        $departures = $paket->departures()
            ->where('is_active', true)
            ->where('is_closed', false)
            ->whereColumn('booked', '<', 'quota')
            ->with('prices') // ambil dari paket_departure_prices
            ->orderBy('departure_date')
            ->get()
            ->map(function ($dep) {

                return [
                    'id'            => $dep->id,
                    'departure_date'=> $dep->departure_date,
                    'return_date'   => $dep->return_date,
                    'quota'         => $dep->quota,
                    'booked'        => $dep->booked,
                    'remaining'     => $dep->quota - $dep->booked,

                    // 🔥 ambil harga per room type
                    'prices' => $dep->prices->map(function ($price) {
                        return [
                            'room_type' => $price->room_type,
                            'price'     => $price->price,
                        ];
                    }),
                ];
            });

        return response()->json($departures);
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */
    private function validateData(Request $request): array
    {
        return $request->validate([

            /*
            |--------------------------------------------------------------------------
            | MAIN
            |--------------------------------------------------------------------------
            */
            'name'              => 'required|string|max:255',
            'code'              => 'required|string|max:255',
            // 'price'             => 'required|numeric|min:0',
            'departure_city'    => 'nullable|string|max:255',
            'departure_date'    => 'nullable|date',
            'return_date'       => 'nullable|date',
            'duration_days'     => 'nullable|integer|min:1',
            'airline'           => 'nullable|string|max:255',
            'quota'             => 'nullable|integer|min:1',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',
            'is_active'         => 'boolean',
            'is_published'      => 'boolean',

            /*
            |--------------------------------------------------------------------------
            | MEDIA
            |--------------------------------------------------------------------------
            */
            'thumbnail' => 'nullable|image|max:2048',
            'gallery'   => 'nullable|array',
            'gallery.*' => 'image|max:4096',

            /*
            |--------------------------------------------------------------------------
            | HOTELS
            |--------------------------------------------------------------------------
            */
            'hotels' => 'nullable|array',
            'hotels.*.city' => 'required_with:hotels|in:mekkah,madinah',
            'hotels.*.hotel_name' => 'required_with:hotels|string|max:255',
            'hotels.*.rating' => 'nullable|integer|min:1|max:5',
            'hotels.*.distance_to_haram' => 'nullable|string|max:255',

            /*
            |--------------------------------------------------------------------------
            | ITINERARY (FLEXIBLE DESTINATION)
            |--------------------------------------------------------------------------
            */
            'itinerary' => 'nullable|array',

            'itinerary.*.destination_id' =>
                'nullable|exists:destinations,id',

            'itinerary.*.destination_name' =>
                'nullable|string|max:255',

            'itinerary.*.note' =>
                'nullable|string',

            /*
            |--------------------------------------------------------------------------
            | DEPARTURES
            |--------------------------------------------------------------------------
            */
            'departures' => 'nullable|array',

            'departures.*.departure_date' =>
                'required_with:departures|date',

            'departures.*.return_date' =>
                'nullable|date|after_or_equal:departures.*.departure_date',

            'departures.*.quota' =>
                'required_with:departures|integer|min:1',

            /*
            |--------------------------------------------------------------------------
            | DEPARTURE ROOM PRICES
            |--------------------------------------------------------------------------
            */
            'departures.*.prices' => 'nullable|array',

            'departures.*.prices.*.room_type' =>
                'required_with:departures.*.prices|in:double,triple,quad',

            'departures.*.prices.*.price' =>
                'required_with:departures.*.prices|numeric|min:0',

            /*
            |--------------------------------------------------------------------------
            | PROMO PER ROOM
            |--------------------------------------------------------------------------
            */

            'departures.*.prices.*.promo_type' =>
                'nullable|in:percent,fixed',

            'departures.*.prices.*.promo_value' =>
                'nullable|numeric|min:0',

            'departures.*.prices.*.promo_label' =>
                'nullable|string|max:255',

            'departures.*.prices.*.promo_expires_at' =>
                'nullable|date',
                
        ]);
    }

}
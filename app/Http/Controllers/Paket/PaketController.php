<?php

namespace App\Http\Controllers\Paket;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Destination;
use App\Services\Pakets\PaketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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

        $page = request('page', 1);

        $pakets = Cache::remember("pakets.page.$page", 60, function () {

            $data = Paket::query()
                ->withCount('departures')
                ->with([
                    'departures' => function ($q) {
                        $q->orderBy('departure_date')
                        ->with('prices')
                        ->limit(1);
                    }
                ])
                ->latest('created_at')
                ->paginate(15);

            // 🔥 HITUNG HARGA DI PHP (BUKAN SQL)
            $data->getCollection()->transform(function ($paket) {

                $departure = $paket->departures->first();

                if (!$departure || $departure->prices->isEmpty()) {
                    $paket->base_price = null;
                    $paket->original_price = null;
                    return $paket;
                }

                $prices = $departure->prices;

                $original = $prices->min('price');

                $final = $prices->map(function ($p) {

                    if ($p->promo_type === 'percent') {
                        return $p->price - ($p->price * $p->promo_value / 100);
                    }

                    if ($p->promo_type === 'fixed') {
                        return $p->price - $p->promo_value;
                    }

                    return $p->price;

                })->min();

                $paket->original_price = $original;
                $paket->base_price = $final;

                return $paket;
            });

            return $data;
        });

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
        dd($request->file('gallery'));

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
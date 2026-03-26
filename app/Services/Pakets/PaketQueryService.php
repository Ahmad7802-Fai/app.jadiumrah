<?php

namespace App\Services\Pakets;

use App\Models\Paket;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaketQueryService
{
public function publicList(array $filters = []): LengthAwarePaginator
{
    $perPage = min((int) ($filters['per_page'] ?? 12), 100);

    $cacheKey = 'pakets:list:' . md5(json_encode($filters) . $perPage);

    return cache()->remember($cacheKey, 60, function () use ($filters, $perPage) {

        $today = now()->toDateString();

        return Paket::query()
            ->select([
                'id',
                'name',
                'slug',
                'departure_city',
                'duration_days',
                'airline',
                'short_description',
                'thumbnail',
                'created_at',
            ])

            ->where('is_active', true)
            ->where('is_published', true)

            ->addSelect([
                'base_price' => \DB::table('paket_departures as pd')
                    ->join('paket_departure_prices as pdp', 'pd.id', '=', 'pdp.paket_departure_id')
                    ->whereColumn('pd.paket_id', 'pakets.id')
                    ->where('pd.is_active', 1)
                    ->where('pd.is_closed', 0)
                    ->whereDate('pd.departure_date', '>=', $today)
                    ->whereRaw('COALESCE(pd.quota,0) > COALESCE(pd.booked,0)')
                    ->selectRaw('MIN(
                        CASE
                            WHEN pdp.promo_type = "percent"
                            THEN pdp.price - (pdp.price * pdp.promo_value / 100)
                            WHEN pdp.promo_type = "fixed"
                            THEN pdp.price - pdp.promo_value
                            ELSE pdp.price
                        END
                    )')
            ])

            ->addSelect([
                'original_price' => \DB::table('paket_departures as pd')
                    ->join('paket_departure_prices as pdp', 'pd.id', '=', 'pdp.paket_departure_id')
                    ->whereColumn('pd.paket_id', 'pakets.id')
                    ->where('pd.is_active', 1)
                    ->where('pd.is_closed', 0)
                    ->whereDate('pd.departure_date', '>=', $today)
                    ->whereRaw('COALESCE(pd.quota,0) > COALESCE(pd.booked,0)')
                    ->selectRaw('MIN(pdp.price)')
            ])

            ->withCount('bookings')
            ->latest()
            ->paginate($perPage);

    });
}
    // public function publicList(array $filters = []): LengthAwarePaginator
    // {
    //     $perPage = (int) ($filters['per_page'] ?? 12);
    //     $perPage = $perPage > 0 ? min($perPage, 100) : 12;

    //     $today = now()->startOfDay();

    //     $sort = $filters['sort'] ?? 'latest';

    //     $query = Paket::query()
    //         ->select([
    //             'pakets.id',
    //             'pakets.name',
    //             'pakets.code',
    //             'pakets.slug',
    //             'pakets.departure_city',
    //             'pakets.duration_days',
    //             'pakets.airline',
    //             'pakets.short_description',
    //             'pakets.thumbnail',
    //             'pakets.is_active',
    //             'pakets.is_published',
    //             'pakets.created_at',
    //         ])

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 🔥 JOIN PRICE ENGINE (FINAL PRICE)
    //         |--------------------------------------------------------------------------
    //         */
    //         ->leftJoin('paket_departures as pd', function ($join) use ($today) {
    //             $join->on('pakets.id', '=', 'pd.paket_id')
    //                 ->where('pd.is_active', true)
    //                 ->where('pd.is_closed', false)
    //                 ->whereDate('pd.departure_date', '>=', $today)
    //                 ->whereRaw('COALESCE(pd.quota,0) > COALESCE(pd.booked,0)');
    //         })

    //         ->leftJoin('paket_departure_prices as pdp', 'pd.id', '=', 'pdp.paket_departure_id')

    //         ->addSelect([
    //             /*
    //             |--------------------------------------------------------------------------
    //             | FINAL PRICE (PROMO)
    //             |--------------------------------------------------------------------------
    //             */
    //             \DB::raw('
    //                 MIN(
    //                     CASE
    //                         WHEN pdp.promo_type = "percent"
    //                         THEN pdp.price - (pdp.price * pdp.promo_value / 100)

    //                         WHEN pdp.promo_type = "fixed"
    //                         THEN pdp.price - pdp.promo_value

    //                         ELSE pdp.price
    //                     END
    //                 ) as base_price
    //             '),

    //             /*
    //             |--------------------------------------------------------------------------
    //             | ORIGINAL PRICE
    //             |--------------------------------------------------------------------------
    //             */
    //             \DB::raw('MIN(pdp.price) as original_price'),
    //         ])

    //         ->where('pakets.is_active', true)
    //         ->where('pakets.is_published', true)

    //         ->groupBy('pakets.id')

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 🔥 RELATION (FIX PROMO SOURCE)
    //         |--------------------------------------------------------------------------
    //         */
    //         ->with([
    //             'nextDeparture' => function ($query) use ($today) {

    //                 $query
    //                     ->where('is_active', true)
    //                     ->where('is_closed', false)
    //                     ->whereDate('departure_date', '>=', $today->toDateString())
    //                     ->whereRaw('COALESCE(quota,0) > COALESCE(booked,0)')
    //                     ->orderBy('departure_date') // next departure

    //                     ->with([
    //                         'prices' => fn ($q) => $q
    //                             ->select([
    //                                 'id',
    //                                 'paket_departure_id',
    //                                 'room_type',
    //                                 'price',
    //                                 'promo_type',
    //                                 'promo_value',
    //                                 'promo_label',
    //                                 'promo_expires_at',
    //                             ])
    //                             ->orderBy('price'),
    //                     ]);
    //             }
    //         ])

    //         ->withCount([
    //             'departures as departures_count' => function ($query) use ($today) {
    //                 $this->applyAvailableDepartureScope($query, $today);
    //             },
    //             'bookings',
    //         ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | SORT
    //     |--------------------------------------------------------------------------
    //     */

    //     $query = match ($sort) {

    //         'price_low' => $query->orderBy('base_price'),
    //         'price_high' => $query->orderByDesc('base_price'),
    //         'popular' => $query->orderByDesc('bookings_count'),
    //         default => $query->orderByDesc('pakets.created_at'),
    //     };

    //     return $query->paginate($perPage)->withQueryString();
    // }

    /*
    |--------------------------------------------------------------------------
    | DETAIL
    |--------------------------------------------------------------------------
    */

    public function publicDetail(string $slug): Paket
{
    return cache()->remember("paket:detail:$slug", 60, function () use ($slug) {

        $today = now()->startOfDay();

        return Paket::query()
            ->select([
                'pakets.*',

                \DB::raw('(
                    SELECT MIN(
                        CASE
                            WHEN pdp.promo_type = "percent"
                                AND (
                                    pdp.promo_expires_at IS NULL
                                    OR pdp.promo_expires_at > NOW()
                                )
                            THEN pdp.price - (pdp.price * pdp.promo_value / 100)

                            WHEN pdp.promo_type = "fixed"
                                AND (
                                    pdp.promo_expires_at IS NULL
                                    OR pdp.promo_expires_at > NOW()
                                )
                            THEN pdp.price - pdp.promo_value

                            ELSE pdp.price
                        END
                    )
                    FROM paket_departures pd
                    JOIN paket_departure_prices pdp
                        ON pd.id = pdp.paket_departure_id
                    WHERE pd.paket_id = pakets.id
                    AND pd.is_active = 1
                    AND pd.is_closed = 0
                    AND pd.departure_date >= NOW()
                    AND COALESCE(pd.quota,0) > COALESCE(pd.booked,0)
                ) as base_price'),

                \DB::raw('(
                    SELECT MIN(pdp.price)
                    FROM paket_departures pd
                    JOIN paket_departure_prices pdp
                        ON pd.id = pdp.paket_departure_id
                    WHERE pd.paket_id = pakets.id
                    AND pd.is_active = 1
                    AND pd.is_closed = 0
                    AND pd.departure_date >= NOW()
                    AND COALESCE(pd.quota,0) > COALESCE(pd.booked,0)
                ) as original_price')
            ])

            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)

            ->with([
                'hotels' => fn ($q) => $q->orderBy('id'),

                'itinerary' => fn ($q) => $q
                    ->with('destination')
                    ->orderBy('day_order'),

                'departures' => function ($query) use ($today) {
                    $this->applyAvailableDepartureScope($query, $today);

                    $query->with([
                        'prices' => fn ($q) => $q
                            ->orderByRaw("
                                CASE room_type
                                    WHEN 'double' THEN 1
                                    WHEN 'triple' THEN 2
                                    WHEN 'quad' THEN 3
                                    ELSE 4
                                END
                            ")
                            ->orderBy('price'),
                    ]);
                },
            ])

            ->withCount([
                'departures as departures_count' => function (Builder $query) use ($today) {
                    $this->applyAvailableDepartureScope($query, $today);
                },
                'bookings',
            ])

            ->firstOrFail();

    });
}

    // public function publicDetail(string $slug): Paket
    // {
    //     $today = now()->startOfDay();

    //     return Paket::query()
    //         ->select([
    //             'pakets.*',

    //             /*
    //             |--------------------------------------------------------------------------
    //             | 🔥 BASE PRICE (FINAL PRICE)
    //             |--------------------------------------------------------------------------
    //             */
    //             \DB::raw('(
    //                 SELECT MIN(
    //                     CASE
    //                         WHEN pdp.promo_type = "percent"
    //                             AND (
    //                                 pdp.promo_expires_at IS NULL
    //                                 OR pdp.promo_expires_at > NOW()
    //                             )
    //                         THEN pdp.price - (pdp.price * pdp.promo_value / 100)

    //                         WHEN pdp.promo_type = "fixed"
    //                             AND (
    //                                 pdp.promo_expires_at IS NULL
    //                                 OR pdp.promo_expires_at > NOW()
    //                             )
    //                         THEN pdp.price - pdp.promo_value

    //                         ELSE pdp.price
    //                     END
    //                 )
    //                 FROM paket_departures pd
    //                 JOIN paket_departure_prices pdp
    //                     ON pd.id = pdp.paket_departure_id
    //                 WHERE pd.paket_id = pakets.id
    //                 AND pd.is_active = 1
    //                 AND pd.is_closed = 0
    //                 AND pd.departure_date >= NOW()
    //                 AND COALESCE(pd.quota,0) > COALESCE(pd.booked,0)
    //             ) as base_price
    //             '),

    //             /*
    //             |--------------------------------------------------------------------------
    //             | 🔥 ORIGINAL PRICE
    //             |--------------------------------------------------------------------------
    //             */
    //             \DB::raw('(
    //                 SELECT MIN(pdp.price)
    //                 FROM paket_departures pd
    //                 JOIN paket_departure_prices pdp
    //                     ON pd.id = pdp.paket_departure_id
    //                 WHERE pd.paket_id = pakets.id
    //                 AND pd.is_active = 1
    //                 AND pd.is_closed = 0
    //                 AND pd.departure_date >= NOW()
    //                 AND COALESCE(pd.quota,0) > COALESCE(pd.booked,0)
    //             ) as original_price
    //             ')
    //         ])

    //         ->where('slug', $slug)
    //         ->where('is_active', true)
    //         ->where('is_published', true)

    //         ->with([
    //             'hotels' => fn ($q) => $q->orderBy('id'),

    //             'itinerary' => fn ($q) => $q
    //                 ->with('destination')
    //                 ->orderBy('day_order'),

    //             'departures' => function ($query) use ($today) {

    //                 $this->applyAvailableDepartureScope($query, $today);

    //                 $query->with([
    //                     'prices' => fn ($q) => $q
    //                         ->orderByRaw("
    //                             CASE room_type
    //                                 WHEN 'double' THEN 1
    //                                 WHEN 'triple' THEN 2
    //                                 WHEN 'quad' THEN 3
    //                                 ELSE 4
    //                             END
    //                         ")
    //                         ->orderBy('price'),
    //                 ]);
    //             },
    //         ])

    //         ->withCount([
    //             'departures as departures_count' => function (Builder $query) use ($today) {
    //                 $this->applyAvailableDepartureScope($query, $today);
    //             },
    //             'bookings',
    //         ])

    //         ->firstOrFail();
    // }

    /*
    |--------------------------------------------------------------------------
    | SHARED SCOPE
    |--------------------------------------------------------------------------
    */

    protected function applyAvailableDepartureScope(
        Builder|HasMany|HasOne $query,
        Carbon $today,
        ?Carbon $departureDate = null
    ): void {
        $query
            ->where('is_active', true)
            ->where('is_closed', false)
            ->whereDate('departure_date', '>=', $today->toDateString())
            ->whereRaw('COALESCE(quota, 0) > COALESCE(booked, 0)')
            ->when($departureDate, function ($query) use ($departureDate) {
                $query->whereDate('departure_date', $departureDate->toDateString());
            })
            ->orderBy('departure_date');
    }
}
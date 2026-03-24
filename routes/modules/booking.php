<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\BookingAddonController;
use App\Models\PaketDeparture;
use App\Models\BookingLock;

/*
|--------------------------------------------------------------------------
| BOOKING MODULE (ADMIN PANEL)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('bookings')
    ->name('bookings.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | INDEX
        |--------------------------------------------------------------------------
        */
        Route::get('/', [BookingController::class, 'index'])
            ->name('index')
            ->middleware('permission:booking.view');

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */
        Route::get('/create', [BookingController::class, 'create'])
            ->name('create')
            ->middleware('permission:booking.create');

        Route::post('/', [BookingController::class, 'store'])
            ->name('store')
            ->middleware('permission:booking.create');

        /*
        |--------------------------------------------------------------------------
        | SHOW
        |--------------------------------------------------------------------------
        */
        Route::get('/{booking}', [BookingController::class, 'show'])
            ->name('show')
            ->middleware('permission:booking.view');

        /*
        |--------------------------------------------------------------------------
        | EDIT
        |--------------------------------------------------------------------------
        */
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:booking.update');

        Route::put('/{booking}', [BookingController::class, 'update'])
            ->name('update')
            ->middleware('permission:booking.update');

        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */
        Route::delete('/{booking}', [BookingController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:booking.delete');

        /*
        |--------------------------------------------------------------------------
        | CONFIRM / CANCEL
        |--------------------------------------------------------------------------
        */
        Route::post('/{booking}/confirm', [BookingController::class, 'confirm'])
            ->name('confirm')
            ->middleware('permission:booking.approve');

        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])
            ->name('cancel')
            ->middleware('permission:booking.cancel');

        /*
        |--------------------------------------------------------------------------
        | ADDONS
        |--------------------------------------------------------------------------
        */
        Route::post('/{booking}/addons', [BookingAddonController::class, 'store'])
            ->name('addons.store')
            ->middleware('permission:booking.update');

        Route::put('/{booking}/addons/{bookingAddon}', [BookingAddonController::class, 'update'])
            ->name('addons.update')
            ->middleware('permission:booking.update');

        Route::delete('/{booking}/addons/{bookingAddon}', [BookingAddonController::class, 'destroy'])
            ->name('addons.destroy')
            ->middleware('permission:booking.update');

    });


/*
|--------------------------------------------------------------------------
| AJAX DEPARTURE (🔥 LOCK AWARE FINAL)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->get('/pakets/{id}/departures', function ($id) {

        return PaketDeparture::with([
                'prices:id,paket_departure_id,room_type,price'
            ])
            ->where('paket_id', $id)
            ->where('is_active', true)
            ->where('is_closed', false)
            ->orderBy('departure_date')
            ->get()
            ->map(function ($d) {

                // 🔥 HITUNG LOCK
                $locked = BookingLock::where('paket_departure_id', $d->id)
                    ->where('expired_at', '>', now())
                    ->sum('qty');

                // 🔥 AVAILABLE REAL
                $available = $d->quota - $d->booked - $locked;

                return [
                    'id' => $d->id,
                    'departure_date' => $d->departure_date,
                    'return_date' => $d->return_date,

                    'quota' => $d->quota,
                    'booked' => $d->booked,
                    'locked' => $locked,

                    // 🔥 INI YANG DIPAKAI UI
                    'available' => max(0, $available),
                ];
            });

    });
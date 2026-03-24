<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Booking\BookingController;
use App\Http\Controllers\Api\V1\Booking\BookingLockController;

Route::prefix('bookings')
    ->name('bookings.') // 🔥 INI KUNCI
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | 🔓 PUBLIC
        |--------------------------------------------------------------------------
        */
        Route::get('/availability', [BookingLockController::class, 'availability'])
            ->name('availability');

        /*
        |--------------------------------------------------------------------------
        | 🔐 PROTECTED
        |--------------------------------------------------------------------------
        */
        Route::middleware('auth:sanctum')->group(function () {

            Route::post('/lock', [BookingLockController::class, 'lock'])
                ->name('lock');

            Route::post('/extend-lock', [BookingLockController::class, 'extendLock'])
                ->name('extendLock');

            Route::get('/', [BookingController::class, 'index'])
                ->name('index');

            Route::post('/', [BookingController::class, 'store'])
                ->name('store');

            Route::get('/{booking}', [BookingController::class, 'show'])
                ->name('show');

            Route::post('/{booking}/confirm', [BookingController::class, 'confirm'])
                ->name('confirm');

            Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])
                ->name('cancel');
        });

    });
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ticketing\FlightController;
use App\Http\Controllers\Ticketing\SeatAllocationController;
use App\Http\Controllers\Ticketing\FlightManifestController;
use App\Http\Controllers\Ticketing\TicketingDashboardController;
use App\Http\Controllers\Ticketing\DepartureController;

Route::prefix('ticketing')
    ->middleware(['auth'])
    ->as('ticketing.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard',
            [TicketingDashboardController::class, 'index']
        )->name('dashboard')
         ->middleware('permission:flight.view');


        /*
        |--------------------------------------------------------------------------
        | DEPARTURES (paket_departures)
        |--------------------------------------------------------------------------
        */
        Route::resource('departures', DepartureController::class)
            ->middleware('permission:departure.view');

        Route::post(
            '/departures/{departure}/assign-flight',
            [DepartureController::class, 'assignFlight']
        )->name('departures.assign-flight')
         ->middleware('permission:departure.update');

        Route::get('/departures/{departure}/assign',
            [DepartureController::class, 'assignForm']
        )->name('departures.assign-form')
        ->middleware('permission:departure.update');

        Route::post('/departures/{departure}/assign',
            [DepartureController::class, 'assignFlight']
        )->name('departures.assign')
        ->middleware('permission:departure.update');
        
        /*
        |--------------------------------------------------------------------------
        | FLIGHTS
        |--------------------------------------------------------------------------
        */
        Route::resource('flights', FlightController::class)
            ->middleware('permission:flight.view');


        /*
        |--------------------------------------------------------------------------
        | SEAT ALLOCATION
        |--------------------------------------------------------------------------
        */
        Route::get('/seat-allocations',
            [SeatAllocationController::class, 'index']
        )->name('seat.index')
         ->middleware('permission:seat.view');

        Route::post('/seat-allocations/allocate',
            [SeatAllocationController::class, 'allocate']
        )->name('seat.allocate')
         ->middleware('permission:seat.update');

        Route::post('/seat-allocations/release',
            [SeatAllocationController::class, 'release']
        )->name('seat.release')
         ->middleware('permission:seat.update');


        /*
        |--------------------------------------------------------------------------
        | FLIGHT MANIFEST
        |--------------------------------------------------------------------------
        */
        Route::get('/manifests',
            [FlightManifestController::class, 'index']
        )->name('manifests.index')
         ->middleware('permission:manifest.view');

        Route::post('/manifests/generate',
            [FlightManifestController::class, 'generate']
        )->name('manifests.generate')
         ->middleware('permission:manifest.generate');
    });
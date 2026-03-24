<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Paket\PaketController;

Route::middleware(['auth'])
    ->prefix('pakets')
    ->group(function () {

        Route::get('/', [PaketController::class, 'index'])
            ->middleware('permission:paket.view')
            ->name('pakets.index');

        Route::get('/create', [PaketController::class, 'create'])
            ->middleware('permission:paket.create')
            ->name('pakets.create');

        Route::post('/', [PaketController::class, 'store'])
            ->middleware('permission:paket.create')
            ->name('pakets.store');

        /*
        |--------------------------------------------------------------------------
        | 🔥 LOAD DEPARTURES JSON (HARUS DI ATAS /{paket})
        |--------------------------------------------------------------------------
        */
        Route::get('/{paket}/departures', [PaketController::class, 'departures'])
            ->middleware('permission:paket.view')
            ->name('pakets.departures');

        Route::get('/{paket}/edit', [PaketController::class, 'edit'])
            ->middleware('permission:paket.update')
            ->name('pakets.edit');

        Route::put('/{paket}', [PaketController::class, 'update'])
            ->middleware('permission:paket.update')
            ->name('pakets.update');

        Route::delete('/{paket}', [PaketController::class, 'destroy'])
            ->middleware('permission:paket.delete')
            ->name('pakets.destroy');

        /*
        |--------------------------------------------------------------------------
        | SHOW (LETTAKKAN PALING BAWAH)
        |--------------------------------------------------------------------------
        */
        Route::get('/{paket}', [PaketController::class, 'show'])
            ->middleware('permission:paket.view')
            ->name('pakets.show');
    });
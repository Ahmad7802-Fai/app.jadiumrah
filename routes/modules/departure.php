<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Departure\DepartureController;

Route::middleware(['auth'])
    ->prefix('departures')
    ->name('departures.')
    ->group(function () {

        Route::get('/', [DepartureController::class, 'index'])->name('index');
        Route::get('/create', [DepartureController::class, 'create'])->name('create');
        Route::post('/', [DepartureController::class, 'store'])->name('store');
        Route::get('/{departure}/edit', [DepartureController::class, 'edit'])->name('edit');
        Route::put('/{departure}', [DepartureController::class, 'update'])->name('update');
        Route::delete('/{departure}', [DepartureController::class, 'destroy'])->name('destroy');
    });
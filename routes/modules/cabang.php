<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Cabang\Controllers\DashboardController;
use App\Modules\Cabang\Controllers\BookingController;

Route::prefix('cabang')
    ->name('cabang.')
    ->middleware(['auth','role:ADMIN_CABANG|KEUANGAN_CABANG'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('bookings', BookingController::class);

    });
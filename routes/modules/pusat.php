<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Pusat\Controllers\DashboardController;
use App\Modules\Pusat\Controllers\UserController;
use App\Modules\Pusat\Controllers\BranchController;
use App\Modules\Pusat\Controllers\BookingController;
use App\Modules\Pusat\Controllers\CommissionController;
use App\Modules\Pusat\Controllers\RoleController;

Route::prefix('pusat')
    ->name('pusat.')
    ->middleware(['auth','role:ADMIN_PUSAT'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('branches', BranchController::class);
        Route::resource('bookings', BookingController::class);
        Route::resource('commission', CommissionController::class);
        Route::resource('roles', RoleController::class);

    });
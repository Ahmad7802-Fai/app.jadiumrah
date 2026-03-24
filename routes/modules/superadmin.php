<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Commission\CommissionSchemeController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\Branch\BranchController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Booking\BookingController;

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::resource('users', UserController::class)
        ->middleware('permission:user.view');

    Route::resource('roles', RoleController::class)
        ->middleware('permission:role.view');

    Route::resource('branches', BranchController::class)
        ->middleware('permission:branch.view');

    Route::resource('agents', AgentController::class)
        ->middleware('permission:agent.view');

    Route::resource('bookings', BookingController::class)
        ->middleware('permission:booking.view');

    Route::resource('commission-schemes', CommissionSchemeController::class)
        ->middleware('permission:commission.view');
});

// use App\Modules\Superadmin\Controllers\DashboardController;
// use App\Modules\Superadmin\Controllers\UserController;
// use App\Modules\Superadmin\Controllers\BranchController;
// use App\Modules\Superadmin\Controllers\AgentController;
// use App\Modules\Superadmin\Controllers\BookingController;
// use App\Modules\Superadmin\Controllers\CommissionSchemeController;
// use App\Modules\Superadmin\Controllers\RoleController;

// Route::prefix('superadmin')
//     ->name('superadmin.')
//     ->middleware(['auth','role:SUPERADMIN'])
//     ->group(function () {

//         Route::get('/dashboard', [DashboardController::class, 'index'])
//             ->name('dashboard');

//         Route::resource('users', UserController::class);
//         Route::resource('branches', BranchController::class);
//         Route::resource('agents', AgentController::class);
//         Route::resource('bookings', BookingController::class);
//         Route::resource('commission-schemes', CommissionSchemeController::class);
//         Route::resource('roles', RoleController::class);

//     });
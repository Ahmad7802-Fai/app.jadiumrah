<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\TabunganTopupController;

Route::prefix('backoffice')
    ->middleware(['auth', 'role:ADMIN'])
    ->group(function () {

        Route::get('tabungan/topup', 
            [TabunganTopupController::class, 'index'])
            ->name('backoffice.tabungan.topup.index');

        Route::post('tabungan/topup/{id}/approve', 
            [TabunganTopupController::class, 'approve'])
            ->name('backoffice.tabungan.topup.approve');

        Route::post('tabungan/topup/{id}/reject', 
            [TabunganTopupController::class, 'reject'])
            ->name('backoffice.tabungan.topup.reject');
    });

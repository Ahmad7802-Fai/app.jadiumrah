<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Commission\CommissionSchemeController;
use App\Http\Controllers\Commission\BranchCommissionController;

/*
|--------------------------------------------------------------------------
| COMMISSION MODULE
|--------------------------------------------------------------------------
|
| Struktur:
| - /commission/schemes        → Master skema tahunan
| - /commission/config         → Config per branch
|
*/

Route::prefix('commission')
    ->middleware(['auth'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Commission Schemes (Master Tahunan)
        |--------------------------------------------------------------------------
        */

        Route::resource('schemes', CommissionSchemeController::class)
            ->names('commission.schemes')
            ->middleware('permission:commission.view');

        /*
        |--------------------------------------------------------------------------
        | Branch Commission Configuration
        |--------------------------------------------------------------------------
        */

        Route::prefix('config')
            ->middleware('permission:commission.update')
            ->group(function () {

                // Halaman utama config
                Route::get('/', [BranchCommissionController::class, 'index'])
                    ->name('commission.config.index');

                // Update Company → Branch (Nominal)
                Route::post('/{branch}/company', [BranchCommissionController::class, 'updateCompany'])
                    ->name('commission.config.company');

                // Update Branch → Agent (Percentage)
                Route::post('/{branch}/agent', [BranchCommissionController::class, 'updateAgent'])
                    ->name('commission.config.agent');
            });

        
        /*
        |--------------------------------------------------------------------------
        | COMMISSION PAYOUT (Finance)
        |--------------------------------------------------------------------------
        */

        Route::prefix('payouts')
            ->middleware('permission:commission.payout.view')
            ->group(function () {

                Route::get('/', 
                    [\App\Http\Controllers\Commission\CommissionPayoutController::class, 'index']
                )->name('commission.payouts.index');

                Route::post('/{payout}/approve', 
                    [\App\Http\Controllers\Commission\CommissionPayoutController::class, 'approve']
                )->name('commission.payouts.approve')
                ->middleware('permission:commission.payout.approve');

                Route::post('/{payout}/paid', 
                    [\App\Http\Controllers\Commission\CommissionPayoutController::class, 'markAsPaid']
                )->name('commission.payouts.paid')
                ->middleware('permission:commission.payout.pay');
            });

        /*
        |--------------------------------------------------------------------------
        | AGENT COMMISSION
        |--------------------------------------------------------------------------
        */

        Route::prefix('my-payouts')
            ->middleware(['permission:commission.payout.request'])
            ->group(function () {

                Route::get('/', 
                    [\App\Http\Controllers\Commission\MyCommissionController::class, 'index']
                )->name('commission.my.index');

                Route::post('/request', 
                    [\App\Http\Controllers\Commission\MyCommissionController::class, 'request']
                )->name('commission.my.request');
            });
            
    });

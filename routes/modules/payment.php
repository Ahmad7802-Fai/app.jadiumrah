<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Finance\RefundController;
use App\Http\Controllers\Finance\ReceivableController;
/*
|--------------------------------------------------------------------------
| PAYMENT ROUTES (FINANCE)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | PAYMENTS
        |--------------------------------------------------------------------------
        */

        Route::get('/payments', [PaymentController::class, 'index'])
            ->name('payments.index');

        Route::get('/bookings/{booking}/payments/create',
            [PaymentController::class, 'create']
        )->name('payments.create');

        Route::post('/payments',
            [PaymentController::class, 'store']
        )->name('payments.store');

        Route::get('/payments/{payment}/edit',
            [PaymentController::class, 'edit']
        )->name('payments.edit');

        Route::put('/payments/{payment}',
            [PaymentController::class, 'update']
        )->name('payments.update');

        Route::delete('/payments/{payment}',
            [PaymentController::class, 'destroy']
        )->name('payments.destroy');

        Route::post('/payments/{payment}/approve',
            [PaymentController::class, 'approve']
        )->name('payments.approve')
         ->middleware('permission:payment.approve');

        Route::post('/payments/{payment}/reject',
            [PaymentController::class, 'reject']
        )->name('payments.reject')
         ->middleware('permission:payment.approve');

        /*
        |--------------------------------------------------------------------------
        | RECEIPT
        |--------------------------------------------------------------------------
        */
        Route::get('/payments/{payment}/receipt',
            [PaymentController::class, 'receipt']
        )->name('payments.receipt');

        /*
        |--------------------------------------------------------------------------
        | INVOICE (FROM BOOKING)
        |--------------------------------------------------------------------------
        */
        Route::get('/bookings/{booking}/invoice',
            [PaymentController::class, 'invoice']
        )->name('payments.invoice');

    });
    
Route::prefix('finance')
    ->name('finance.')
    ->middleware('auth')
    ->group(function () {

        Route::get('/refunds', [RefundController::class,'index'])
            ->name('refunds.index');

        Route::get('/payments/{payment}/refund', [RefundController::class,'create'])
            ->name('refunds.create');

        Route::post('/refunds', [RefundController::class,'store'])
            ->name('refunds.store');

        Route::post('/refunds/{refund}/approve', [RefundController::class,'approve'])
            ->name('refunds.approve');

        Route::post('/refunds/{refund}/reject', [RefundController::class,'reject'])
            ->name('refunds.reject');

        Route::get('/refunds/{refund}/receipt',
                [RefundController::class, 'receipt']
            )->name('refunds.receipt');

        Route::get('/receivables',
                [ReceivableController::class, 'index']
            )->name('receivables.index')
            ->middleware('permission:receivable.view');


    });


Route::prefix('finance')
    ->middleware(['auth'])
    ->name('finance.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | COST MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::get('/costs',
            [\App\Http\Controllers\Finance\CostController::class, 'index']
        )->name('costs.index')
         ->middleware('permission:cost.view');

        Route::get('/costs/create',
            [\App\Http\Controllers\Finance\CostController::class, 'create']
        )->name('costs.create')
         ->middleware('permission:cost.create');

        Route::post('/costs',
            [\App\Http\Controllers\Finance\CostController::class, 'store']
        )->name('costs.store')
         ->middleware('permission:cost.create');

        Route::get('/costs/{cost}/edit',
            [\App\Http\Controllers\Finance\CostController::class, 'edit']
        )->name('costs.edit')
         ->middleware('permission:cost.update');

        Route::put('/costs/{cost}',
            [\App\Http\Controllers\Finance\CostController::class, 'update']
        )->name('costs.update')
         ->middleware('permission:cost.update');

        Route::post('/costs/{cost}/approve',
            [\App\Http\Controllers\Finance\CostController::class, 'approve']
        )->name('costs.approve')
         ->middleware('permission:cost.approve');

        Route::post('/costs/{cost}/reject',
            [\App\Http\Controllers\Finance\CostController::class, 'reject']
        )->name('costs.reject')
         ->middleware('permission:cost.approve');

        Route::delete('/costs/{cost}',
            [\App\Http\Controllers\Finance\CostController::class, 'destroy']
        )->name('costs.destroy')
         ->middleware('permission:cost.delete');

    });
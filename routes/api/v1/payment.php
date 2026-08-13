<?php

use App\Http\Controllers\Api\V1\Finance\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // ================= PAYMENT GLOBAL =================
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::patch('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.patch');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');


    // ================= BOOKING PAYMENT =================
    Route::get('/bookings/{booking}/payments', [PaymentController::class, 'byBooking'])
        ->name('payments.byBooking');

    Route::post('/bookings/{booking}/payments', [PaymentController::class, 'storeByBooking'])
        ->name('payments.storeByBooking');
});
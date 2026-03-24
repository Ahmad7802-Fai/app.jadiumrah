<?php

use App\Http\Controllers\Api\V1\Jamaah\JamaahController;
use Illuminate\Support\Facades\Route;

Route::prefix('jamaahs')->name('jamaahs.')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [JamaahController::class, 'index'])->name('index');
    Route::post('/', [JamaahController::class, 'store'])->name('store');
    Route::get('/{jamaah}', [JamaahController::class, 'show'])->name('show');
    Route::put('/{jamaah}', [JamaahController::class, 'update'])->name('update');
    Route::patch('/{jamaah}', [JamaahController::class, 'update'])->name('patch');
    Route::delete('/{jamaah}', [JamaahController::class, 'destroy'])->name('destroy');

    Route::post('/{jamaah}/approve', [JamaahController::class, 'approve'])->name('approve');
    Route::post('/{jamaah}/reject', [JamaahController::class, 'reject'])->name('reject');
});
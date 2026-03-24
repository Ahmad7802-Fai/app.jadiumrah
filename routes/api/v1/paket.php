<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Paket\PaketController;

Route::prefix('pakets')->name('pakets.')->group(function () {
    Route::get('/', [PaketController::class, 'index'])->name('index');
    Route::get('/{slug}', [PaketController::class, 'show'])->name('show');
});
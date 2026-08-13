<?php
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PendaftaranController;

Route::middleware('capture.referral')->group(function () {

    Route::get('/paket/{slug}', [PaketController::class, 'show'])
        ->name('paket.show');

    Route::get('/daftar', [PendaftaranController::class, 'create'])
        ->name('daftar.create');

    Route::post('/daftar', [PendaftaranController::class, 'store'])
        ->name('daftar.store');

});

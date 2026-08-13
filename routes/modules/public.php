<?php

use App\Http\Controllers\Website\PaketUmrahController;

Route::middleware(['web', 'capture.referral'])->group(function () {

    Route::get('/paket-umrah/{slug}', [PaketUmrahController::class, 'show'])
        ->name('website.paket.show');

});

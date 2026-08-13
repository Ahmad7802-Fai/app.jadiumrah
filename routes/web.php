<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KeberangkatanPaketController;

Route::get('/', function () {

    if (auth()->guard('jamaah')->check()) {
        return redirect()->route('jamaah.dashboard');
    }

    if (auth()->check()) {
        return redirect('/dashboard');
    }

    return redirect()->route('login');
});

Route::middleware(['auth'])
    ->get('/keberangkatan-paket/{keberangkatan}', 
        [KeberangkatanPaketController::class, 'show']
    )
    ->name('keberangkatan.paket');
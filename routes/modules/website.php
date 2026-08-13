<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\PaketUmrahController;
use App\Http\Controllers\Website\PendaftaranController;

Route::middleware('capture.referral')->group(function () {

    /**
     * ===============================
     * PENDAFTARAN (HARUS DI ATAS)
     * ===============================
     */
    Route::prefix('daftar')->name('website.daftar.')->group(function () {
        Route::get('/', [PendaftaranController::class, 'create'])
            ->name('create');

        Route::post('/', [PendaftaranController::class, 'store'])
            ->name('store')
            ->middleware(['prevent.duplicate.registration', 'throttle:3,10']);

        Route::get('/sukses', [PendaftaranController::class, 'success'])
            ->name('success');
    });

    /**
     * ===============================
     * PAKET UMRAH NORMAL
     * ===============================
     */
    Route::get('/paket-umrah/{slug}', [PaketUmrahController::class, 'show'])
        ->name('paket.umrah.show');

    /**
     * ===============================
     * PAKET UMRAH REFERRAL (PALING BAWAH)
     * ===============================
     */
    Route::get('/{agent}/{slug}', [PaketUmrahController::class, 'show'])
        ->name('paket.umrah.by-agent');
});


// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Website\PaketUmrahController;
// use App\Http\Controllers\Website\PendaftaranController;

// Route::middleware('capture.referral')->group(function () {

//     /**
//      * ===============================
//      * PAKET UMRAH
//      * ===============================
//      */

//     // Normal
//     Route::get('/paket-umrah/{slug}', [PaketUmrahController::class, 'show'])
//         ->name('paket.umrah.show');

//     // Dengan referral cantik
//     Route::get('/paket-umrah/{slug}/by/{agent}', [PaketUmrahController::class, 'show'])
//         ->name('paket.umrah.show.ref');
    
//         Route::get('/{agent}/{slug}', [PaketUmrahController::class, 'show'])
//     ->middleware('capture.referral')
//     ->name('paket.umrah.by-agent');

//     /**
//      * ===============================
//      * PENDAFTARAN (SELF REGISTRATION)
//      * ===============================
//      */
//     Route::prefix('daftar')->name('website.daftar.')->group(function () {

//         Route::get('/', [PendaftaranController::class, 'create'])
//             ->name('create');

//         Route::post('/', [PendaftaranController::class, 'store'])
//             ->name('store')
//             ->middleware([
//                 'prevent.duplicate.registration',
//                 'throttle:3,10',
//             ]);

//         Route::get('/sukses', [PendaftaranController::class, 'success'])
//             ->name('success');
//     });

// });

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Website\PaketUmrahController;
// use App\Http\Controllers\Website\PendaftaranController;

// Route::middleware('capture.referral')->group(function () {

//     /**
//      * ===============================
//      * PAKET UMRAH
//      * ===============================
//      */

//     // Normal
//     Route::get('/paket-umrah/{slug}', [PaketUmrahController::class, 'show'])
//         ->name('paket.umrah.show');

//     // Dengan referral cantik
//     Route::get('/paket-umrah/{slug}/by/{agent}', [PaketUmrahController::class, 'show'])
//         ->name('paket.umrah.show.ref');
    
//         Route::get('/{agent}/{slug}', [PaketUmrahController::class, 'show'])
//     ->middleware('capture.referral')
//     ->name('paket.umrah.by-agent');

//     /**
//      * ===============================
//      * PENDAFTARAN (SELF REGISTRATION)
//      * ===============================
//      */
//     Route::prefix('daftar')->name('website.daftar.')->group(function () {

//         Route::get('/', [PendaftaranController::class, 'create'])
//             ->name('create');

//         Route::post('/', [PendaftaranController::class, 'store'])
//             ->name('store')
//             ->middleware([
//                 'prevent.duplicate.registration',
//                 'throttle:3,10',
//             ]);

//         Route::get('/sukses', [PendaftaranController::class, 'success'])
//             ->name('success');
//     });

// });

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Website\PaketUmrahController;
// use App\Http\Controllers\Website\PendaftaranController;

// Route::middleware('capture.referral')->group(function () {

//     // ===============================
//     // PAKET
//     // ===============================
//     Route::get('/paket-umrah/{slug}', [PaketUmrahController::class, 'show'])
//         ->name('paket.umrah.show');

//     // ===============================
//     // PENDAFTARAN (SELF REGISTRATION)
//     // ===============================
//     Route::prefix('daftar')->name('website.daftar.')->group(function () {

//         // 👉 /daftar
//         Route::get('/', [PendaftaranController::class, 'create'])
//             ->name('create');

//         // 👉 POST /daftar
//         Route::post('/', [PendaftaranController::class, 'store'])
//             ->name('store')
//             ->middleware([
//                 'prevent.duplicate.registration',
//                 'throttle:3,10',
//             ]);

//         // 👉 /daftar/sukses
//         Route::get('/sukses', [PendaftaranController::class, 'success'])
//             ->name('success');
//     });

// });

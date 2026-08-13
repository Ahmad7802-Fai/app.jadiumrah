<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::middleware(['web','redirect.login'])->group(function () {

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.submit');
    
    
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});



// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Auth\LoginController;

// /*
// |--------------------------------------------------------------------------
// | AUTH — SINGLE LOGIN GATE
// |--------------------------------------------------------------------------
// | /login dipakai untuk SEMUA user:
// | - jamaah
// | - admin
// | - crm
// | - keuangan
// |--------------------------------------------------------------------------
// */

// Route::middleware(['web','redirect.login'])->group(function () {

//     Route::get('/login', function () {
//         return view('auth.login');
//     })->name('login');

//     Route::post('/login', [LoginController::class, 'login'])
//         ->name('login.submit');
// });

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\PaketUmrahController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TestimoniController;

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES — F7 PREMIUM CLEAN
|--------------------------------------------------------------------------
| Semua route di sini otomatis memakai prefix "admin" dan middleware:
| - web
| - auth
| - role:admin
|
| Ditangani dari web.php / RouteServiceProvider.
|--------------------------------------------------------------------------
*/

// =========================
// BERITA
// =========================
Route::resource('berita', BeritaController::class)
    ->names('admin.berita');

// =========================
// GALLERY
// =========================
Route::resource('gallery', GalleryController::class)
    ->names('admin.gallery');

// =========================
// PAKET UMRAH
// =========================
Route::resource('paket-umrah', PaketUmrahController::class)
    ->names('admin.paket-umrah');

// =========================
// PARTNER
// =========================
Route::resource('partner', PartnerController::class)
    ->names('admin.partner');

// =========================
// TEAM
// =========================
Route::resource('team', TeamController::class)
    ->names('admin.team');

// =========================
// TESTIMONI
// =========================
Route::resource('testimoni', TestimoniController::class)
    ->names('admin.testimoni');

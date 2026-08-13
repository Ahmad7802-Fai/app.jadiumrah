<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manifest\ManifestController;

Route::middleware(['auth'])
    ->prefix('manifests')
    ->name('manifests.')
    ->group(function () {

        Route::get('/', [ManifestController::class, 'index'])
            ->name('index')
            ->middleware('permission:manifest.view');

        Route::get('/{departure}', [ManifestController::class, 'show'])
            ->whereNumber('departure')
            ->name('show')
            ->middleware('permission:manifest.view');

        Route::get('/{departure}/export-pdf',
            [ManifestController::class, 'exportPdf'])
            ->whereNumber('departure')
            ->name('export.pdf')
            ->middleware('permission:manifest.view');

        Route::post('/{departure}/generate-seat', 
            [ManifestController::class, 'generateSeat'])
            ->name('generate.seat')
            ->middleware('permission:manifest.update');

        Route::get('/{departure}/nametag',
            [ManifestController::class,'exportNameTag'])
            ->name('nametag.pdf');
    });
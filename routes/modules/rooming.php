<?php 

use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\Rooming\RoomingController;

Route::middleware(['auth'])
    ->prefix('rooming')
    ->name('rooming.')
    ->group(function () {

        // LIST departure (menu masuk sini)
        Route::get('/', 
            [RoomingController::class,'index']
        )->name('index');

        // DETAIL per departure
        Route::get('/{departure}',
            [RoomingController::class,'show']
        )->name('show');

        Route::post('/{departure}/generate',
            [RoomingController::class,'generate']
        )->name('generate');

        Route::delete('/rooms/{room}',
            [RoomingController::class,'destroy']
        )->name('destroy');

        Route::delete('/{departure}/clear',
            [RoomingController::class,'clear']
        )->name('clear');

        Route::get('/{departure}/export-pdf',
            [RoomingController::class,'exportPdf']
        )->name('export.pdf');

        Route::post('/assign',
            [RoomingController::class,'assign']
        )->name('assign');

    });
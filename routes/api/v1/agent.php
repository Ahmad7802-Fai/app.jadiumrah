<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Agent\AgentController;

Route::middleware('auth:sanctum')
    ->prefix('agent')
    ->name('agent.')
    ->group(function () {
        Route::get('/stats', [AgentController::class, 'stats'])->name('stats');
    });
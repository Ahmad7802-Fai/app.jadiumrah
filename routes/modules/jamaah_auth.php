<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Jamaah\AuthController;

Route::get('/jamaah/login', fn () => view('jamaah.auth.login'))
    ->name('jamaah.login');

Route::post('/jamaah/login', [AuthController::class, 'login'])
    ->name('jamaah.login.post');

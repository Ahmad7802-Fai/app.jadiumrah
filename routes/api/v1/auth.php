<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

/*
|--------------------------------------------------------------------------
| AUTH PUBLIC
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class,'register']);
    Route::post('/verify-email', [AuthController::class,'verifyEmail']);
    Route::post('/login', [AuthController::class,'login']);

    Route::post('/forgot-password', [AuthController::class,'forgotPassword']);
    Route::post('/reset-password', [AuthController::class,'resetPassword']);

    Route::get('/google', [AuthController::class,'redirectGoogle']);
    Route::get('/google/callback', [AuthController::class,'handleGoogle']);

});

/*
|--------------------------------------------------------------------------
| AUTH PROTECTED
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class,'logout']);

});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::post('/register', [AuthController::class,'register']);
Route::post('/verify-email', [AuthController::class,'verifyEmail']);
Route::post('/login', [AuthController::class,'login']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/logout', [AuthController::class,'logout']);

Route::post('/forgot-password', [AuthController::class,'forgotPassword']);
Route::post('/reset-password', [AuthController::class,'resetPassword']);

Route::get('/auth/google', [AuthController::class,'redirectGoogle']);
Route::get('/auth/google/callback', [AuthController::class,'handleGoogle']);
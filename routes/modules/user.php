<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\RoleController;

Route::middleware(['auth'])
    ->group(function () {

        Route::resource('users', UserController::class)
            ->middleware('permission:user.view');

        Route::resource('roles', RoleController::class)
            ->middleware('permission:role.view');

    });
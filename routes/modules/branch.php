<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\BranchController;

Route::middleware(['auth'])
    ->group(function () {

        Route::resource('branches', BranchController::class)
            ->middleware('permission:branch.view');

    });
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\AgentController;
use App\Models\Agent;

Route::middleware(['auth'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | AJAX: Agent by Branch
        |--------------------------------------------------------------------------
        */

        Route::get('agents/by-branch/{branch}', function ($branchId) {

            return Agent::where('branch_id', $branchId)
                ->where('is_active', true)
                ->select('id','nama','branch_id')
                ->orderBy('nama')
                ->get();

        })->name('agents.byBranch');


        /*
        |--------------------------------------------------------------------------
        | RESOURCE
        |--------------------------------------------------------------------------
        */

        Route::resource('agents', AgentController::class)
            ->middleware('permission:agent.view');
    });
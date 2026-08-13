<?php

use Illuminate\Support\Facades\Route;

// CRM Controllers
use App\Http\Controllers\Crm\SalesDashboardController;
use App\Http\Controllers\Crm\LeadController;
use App\Http\Controllers\Crm\LeadClosingController;
use App\Http\Controllers\Crm\LeadActivityController;
use App\Http\Controllers\Crm\PipelineController;
use App\Http\Controllers\Crm\PipelineLogController;
use App\Http\Controllers\Crm\LeadSourceController;

/*
|--------------------------------------------------------------------------
| CRM ROUTES — PREMIUM FINAL v3
| prefix    : crm
| middleware: auth + role:crm,sales
|--------------------------------------------------------------------------
*/
Route::prefix('crm')
    ->middleware([
        'auth',
        'role:crm,sales',
        'not.agent' // 🔥 INI KUNCINYA
    ])
    ->name('crm.')
    ->group(function () {


        // DASHBOARD
        Route::get('dashboard', [SalesDashboardController::class, 'index'])
            ->name('dashboard.sales');

        // LEADS
        Route::resource('leads', LeadController::class)
            ->names('leads');

        // LEAD SOURCE (HALAMAN MINI)
        Route::get('lead-sources/create', [LeadSourceController::class, 'create'])
            ->name('lead-sources.create');

        Route::post('lead-sources', [LeadSourceController::class, 'store'])
            ->name('lead-sources.store');

        // PIPELINE ACTION
        Route::post('leads/{lead}/update-pipeline', [LeadController::class, 'updatePipeline'])
            ->name('leads.pipeline.update');

        // CLOSING
        Route::post('leads/{lead}/closing', [LeadClosingController::class, 'store'])
            ->name('lead-closings.store');

        Route::get('lead-closings', [LeadClosingController::class, 'index'])
            ->name('lead-closings.index');

        Route::post('lead-closings/{closing}/approve', [LeadClosingController::class, 'approve'])
            ->name('lead-closings.approve');

        Route::post('lead-closings/{closing}/reject', [LeadClosingController::class, 'reject'])
            ->name('lead-closings.reject');

        // FOLLOW UP
        Route::get('followup', [LeadActivityController::class, 'index'])
            ->name('followup.index');
            
        Route::get(
            'leads/{lead}/followup/create',
            [LeadActivityController::class, 'create']
        )->name('followup.create');

        Route::post(
            'leads/{lead}/followup',
            [LeadActivityController::class, 'store']
        )->name('followup.store');

        // PIPELINE
        Route::resource('pipeline', PipelineController::class)
            ->names('pipeline');

        Route::get('pipeline/logs', [PipelineLogController::class, 'index'])
            ->name('pipeline.logs');

        Route::get(
            'leads/{lead}/pipeline',
            [PipelineController::class, 'change']
        )->name('pipeline.change');

        Route::post(
            'leads/{lead}/pipeline',
            [PipelineController::class, 'updateForLead']
        )->name('pipeline.update-for-lead');

        // ===============================
        // LEAD CLOSING
        // ===============================
        Route::post(
            'leads/{lead}/closing/submit',
            [LeadClosingController::class, 'submit']
        )->name('leads.closing.submit');

        // ADMIN – CLOSING
        Route::get('closing', 
            [LeadClosingController::class, 'index']
        )->name('closing.index');

        Route::get('closing/{closing}', 
            [LeadClosingController::class, 'show']
        )->name('closing.show');

        Route::post('closing/{closing}/approve', 
            [LeadClosingController::class, 'approve']
        )->name('closing.approve');
        
    });

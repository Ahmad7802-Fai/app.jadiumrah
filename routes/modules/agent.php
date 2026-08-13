<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\ProfileController;
use App\Http\Controllers\Agent\LeadController;
use App\Http\Controllers\Agent\LeadActivityController;
use App\Http\Controllers\Agent\LeadPipelineController;
use App\Http\Controllers\Agent\LeadClosingController;
use App\Http\Controllers\Agent\JamaahController;
use App\Http\Controllers\Agent\ManualJamaahController;
use App\Http\Controllers\Agent\PaymentController;
use App\Http\Controllers\Agent\KomisiController;
use App\Http\Controllers\Agent\PayoutController;

/*
|--------------------------------------------------------------------------
| AGENT ROUTES
|--------------------------------------------------------------------------
| NOTE:
| - prefix: agent/
| - name  : agent.*
|   (sudah di RouteServiceProvider)
|--------------------------------------------------------------------------
*/


// ==================================================
// DASHBOARD
// ==================================================
Route::get('dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');


// ==================================================
// PROFILE
// ==================================================

Route::get('profile', [ProfileController::class, 'edit'])
    ->name('profile.index');
    
Route::get('profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

Route::post('profile', [ProfileController::class, 'update'])
    ->name('profile.update');


// ==================================================
// LEADS (CRM)
// ==================================================
Route::resource('leads', LeadController::class)
    ->except(['destroy']);

// Follow up
Route::post(
    'leads/{lead}/followup',
    [LeadActivityController::class, 'store']
)->name('leads.followup.store');

// Pipeline update
Route::post(
    'leads/{lead}/pipeline',
    [LeadPipelineController::class, 'update']
)->name('leads.pipeline.update');

// Pipeline move
Route::post(
    'leads/{lead}/pipeline/{pipeline}',
    [LeadPipelineController::class, 'move']
)->name('leads.pipeline.move');

// routes/agent.php

Route::post(
    'leads/{lead}/closing',
    [LeadClosingController::class, 'store']
)->name('leads.closing.submit');

// ==================================================
// JAMAAH
// ==================================================
Route::get('jamaah', [JamaahController::class, 'index'])
    ->name('jamaah.index');

Route::get('jamaah/create', [JamaahController::class, 'create'])
    ->name('jamaah.create');

Route::post('jamaah', [JamaahController::class, 'store'])
    ->name('jamaah.store');

Route::get('jamaah/{id}', [JamaahController::class, 'show'])
    ->name('jamaah.show');

Route::get('jamaah/{id}/edit', [JamaahController::class, 'edit'])
    ->name('jamaah.edit');

Route::put('jamaah/{id}', [JamaahController::class, 'update'])
    ->name('jamaah.update');

// Input payment oleh agent
Route::post(
    'jamaah/{jamaah}/payment',
    [PaymentController::class, 'store']
)->name('jamaah.payment');

// Manual jamaah
Route::get(
    'jamaah/manual/create',
    [ManualJamaahController::class, 'create']
)->name('jamaah.manual.create');

Route::post(
    'jamaah/manual',
    [ManualJamaahController::class, 'store']
)->name('jamaah.manual.store');

// WhatsApp
Route::get(
    'jamaah/{id}/whatsapp',
    [JamaahController::class, 'whatsapp']
)->name('jamaah.whatsapp');


// ==================================================
// KOMISI
// ==================================================
Route::get('komisi', [KomisiController::class, 'index'])
    ->name('komisi.index');


// ==================================================
// PAYOUT KOMISI (AGENT)
// ==================================================
Route::prefix('payout')->name('payout.')->group(function () {

    Route::get('/', [PayoutController::class, 'index'])
        ->name('index');

    Route::post('/request', [PayoutController::class, 'request'])
        ->name('request');
        
    Route::get('/confirm', [PayoutController::class, 'confirm'])
        ->name('confirm');
});

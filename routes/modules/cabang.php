<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cabang\DashboardController;
use App\Http\Controllers\Cabang\JamaahCabangController;
use App\Http\Controllers\Cabang\AgentController;
use App\Http\Controllers\Cabang\PaymentController;
use App\Http\Controllers\Cabang\LeadController;
use App\Http\Controllers\Cabang\LeadClosingController;
use App\Http\Controllers\Cabang\LeadActivityController;
/* =================================================
use App\Http\Controllers\Cabang\LeadController;====
 | DASHBOARD CABANG
 ===================================================== */
Route::get('dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/* =====================================================
 | JAMAAH CABANG (CRUD TERBATAS)
 ===================================================== */
Route::resource('jamaah', JamaahCabangController::class)
    ->except(['show','destroy']);

Route::get('jamaah/{jamaah}',
    [JamaahCabangController::class, 'show']
)->name('jamaah.show');

/* =====================================================
 | AGENT CABANG (FULL CRUD)
 ===================================================== */
Route::resource('agent', AgentController::class)->except(['show']);

Route::get('agent/{agent}',[AgentController::class, 'show'])->name('agent.show');

Route::patch('agent/{agent}/toggle',[AgentController::class, 'toggle'])->name('agent.toggle');

// 🔥 ROUTE PRINT DETAIL JAMAAH
Route::get('jamaah/{jamaah}/print', 
            [JamaahCabangController::class, 'print']
)->name('jamaah.print');

/* =====================================================
 | INPUT PEMBAYARAN (CABANG ONLY)
 ===================================================== */
Route::post(
    'jamaah/{jamaah}/payments',
    [PaymentController::class, 'store']
)->name('payments.store');
//  PRINT DETAIL JAMAAH
Route::get(
    'jamaah/{id}/print.detail',
    [JamaahCabangController::class, 'printDetail']
)->name('jamaah.print.detail');

// PRINT INVOICE JAMAAH
Route::get(
    'jamaah/{jamaah}/print-invoice',
    [JamaahCabangController::class, 'printInvoice']
)->name('jamaah.print.invoice');


/* =====================================================
 | LEAD MANAGEMENT (CABANG)
 ===================================================== */
Route::get(
    'leads',
    [LeadController::class, 'index']
)->name('leads.index');
/* =====================================================
 | LEAD CRUD (CABANG)
 ===================================================== */
Route::get('leads/create', [LeadController::class, 'create'])
    ->name('leads.create');

Route::post('leads', [LeadController::class, 'store'])
    ->name('leads.store');

Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])
    ->name('leads.edit');

Route::put('leads/{lead}', [LeadController::class, 'update'])
    ->name('leads.update');

Route::delete('leads/{lead}', [LeadController::class, 'destroy'])
    ->name('leads.destroy');

// DETAIL LEAD
Route::get(
    'leads/{lead}',
    [LeadController::class, 'show']
)->name('leads.show');
        // 🔥 CLOSING — CABANG (AJUKAN SAJA)
Route::post(
    'leads/{lead}/closing',
    [LeadClosingController::class, 'store']
  )->name('leads.closing.store');

// MOVE PIPELINE (KANBAN)

Route::post(
    'leads/{lead}/pipeline',
    [LeadController::class, 'movePipeline']
)->name('leads.pipeline');

// VIEW KANBAN LEAD
Route::get('leads-kanban',
    [LeadController::class, 'kanban']
)->name('leads.kanban');
// LOAD MORE LEAD (KANBAN / AJAX)
Route::get(
    'leads-kanban/load-more',
    [LeadController::class, 'loadMore']
)->name('leads.kanban.load-more');

// LOG ACTIVITY (WA / TELPON / MEETING)
Route::post(
    'leads/{lead}/activity',
    [LeadController::class, 'storeActivity']
)->name('leads.activity');

// SUBMIT CLOSING (DRAFT → PUSAT)
Route::post(
    'leads/{lead}/closing',
    [LeadController::class, 'submitClosing']
)->name('leads.closing');

Route::post(
    'leads/{lead}/followup',
    [LeadActivityController::class, 'store']
    )->name('leads.followup.store'); 
// routes/cabang.php
Route::get(
    'closing/{closing}',
    [LeadClosingController::class, 'show']
)->name('closing.show');
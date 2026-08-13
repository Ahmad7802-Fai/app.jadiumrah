<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| DASHBOARD ROUTES — FINAL CLEAN (NO DUPLICATE)
|--------------------------------------------------------------------------
*/

Route::middleware(['web','auth','access.context'])
    ->group(function () {

        // ===============================
        // MAIN DASHBOARD (SEMUA ROLE)
        // ===============================
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ===============================
        // CHART DATA (AJAX)
        // ===============================
        Route::get('/dashboard/chart', [DashboardController::class, 'chartData'])
            ->name('dashboard.chart');

        Route::get('/dashboard/chart-comparison', [DashboardController::class, 'chartComparison'])
            ->name('dashboard.chartComparison');
    });


// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\Keuangan\DashboardController as KeuanganDashboard;
// use App\Http\Controllers\Crm\SalesDashboardController;

// /*
// |--------------------------------------------------------------------------
// | DASHBOARD ROUTES — PREMIUM FINAL v3
// |--------------------------------------------------------------------------
// | Semua role tetap masuk lewat /dashboard (global).
// | Chart route TIDAK berada dalam prefix role.
// |--------------------------------------------------------------------------
// */

// /* ============================================================
//    1) GLOBAL DASHBOARD (semua role)
//    ============================================================ */

// // Route::middleware(['auth'])
// //     ->get('/dashboard', [DashboardController::class, 'index'])
// //     ->name('dashboard');

// Route::middleware(['auth', 'ensure.agent'])
//     ->get('/dashboard', [DashboardController::class, 'index'])
//     ->name('dashboard');

// /* ============================================================
//    2) GLOBAL CHART ROUTE (PALING PENTING)
//    Tidak berada dalam prefix manapun.
//    ============================================================ */
// Route::middleware(['auth'])
//     ->prefix('dashboard')
//     ->group(function () {
//         Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
//         Route::get('/chart', [DashboardController::class, 'chartData'])->name('chart.data');
//     });

// Route::get('/dashboard/chart', [DashboardController::class, 'chartData'])
//     ->name('dashboard.chart');



// /* ============================================================
//    3) ROLE-BASED DASHBOARD (masing-masing menu sidebar)
//    ============================================================ */

// /* SUPERADMIN */
// Route::prefix('superadmin')
//     ->middleware(['auth', 'role:superadmin'])
//     ->group(function () {
//         Route::get('/dashboard', [DashboardController::class, 'index'])
//             ->name('superadmin.dashboard');
//     });

// /* ADMIN */
// Route::prefix('admin')
//     ->middleware(['auth', 'role:admin'])
//     ->group(function () {
//         Route::get('/dashboard', [DashboardController::class, 'index'])
//             ->name('admin.dashboard');
//     });

// /* OPERATOR */
// Route::prefix('operator')
//     ->middleware(['auth', 'role:operator'])
//     ->group(function () {
//         Route::get('/dashboard', [DashboardController::class, 'index'])
//             ->name('operator.dashboard');
//     });

// /* INVENTORY */
// Route::prefix('inventory')
//     ->middleware(['auth', 'role:inventory'])
//     ->group(function () {
//         Route::get('/dashboard', [DashboardController::class, 'index'])
//             ->name('inventory.dashboard');
//     });

// /* CRM (Sales Dashboard Premium) */
// Route::prefix('crm')
//     ->middleware(['auth', 'role:crm,sales'])
//     ->group(function () {
//         Route::get('/dashboard', [SalesDashboardController::class, 'index'])
//             ->name('crm.dashboard.sales');
//     });

// /* KEUANGAN */
// Route::prefix('keuangan')
//     ->middleware(['auth', 'role:keuangan'])
//     ->group(function () {
//         Route::get('/dashboard', [KeuanganDashboard::class, 'index'])
//             ->name('keuangan.dashboard');
//     });


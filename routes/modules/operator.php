<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Operator\{
    DaftarJamaahController,
    KeberangkatanController,
    ManifestController,
    PaketMasterController,
    PassportJamaahController,
    UpdateJamaahController,
    VisaController,
    JamaahUserController,
    JamaahApprovalController,
    AjaxKeberangkatanController
};

/*
|--------------------------------------------------------------------------
| OPERATOR MODULE ROUTES
| Prefix   : /operator      (dari RouteServiceProvider)
| Middleware: web, auth, access.context, role:operator
|--------------------------------------------------------------------------
*/

/* ===========================
| DASHBOARD OPERATOR
=========================== */
Route::get('dashboard', function () {
    return view('dashboard');
})->name('operator.dashboard');

/* ===========================
| DAFTAR JAMAAH
=========================== */
Route::resource('daftar-jamaah', DaftarJamaahController::class)
    ->names('operator.daftar-jamaah');

// Route::get(
//     'daftar-jamaah/ajax-keberangkatan-paket/{id}',
//     [DaftarJamaahController::class, 'ajaxKeberangkatanPaket']
// )->name('operator.ajax.keberangkatan-paket');

Route::get(
    'daftar-jamaah/{id}/print',
    [DaftarJamaahController::class, 'print']
)->name('operator.daftar-jamaah.print');
// Export Excel & PDF
Route::get('daftar-jamaah/export/excel', [DaftarJamaahController::class, 'exportExcel'])
    ->name('operator.daftar-jamaah.export.excel');

Route::get('daftar-jamaah/export/pdf', [DaftarJamaahController::class, 'exportPdf'])
    ->name('operator.daftar-jamaah.export.pdf');

/* ===========================
| JAMAAH APPROVAL (PUSAT)
=========================== */
Route::prefix('jamaah-approval')
    ->as('operator.jamaah-approval.')
    ->group(function () {

        Route::get('/', [JamaahApprovalController::class, 'index'])
            ->name('index');

        Route::get('{id}', [JamaahApprovalController::class, 'show'])
            ->name('show');

        Route::post('{id}/approve', [JamaahApprovalController::class, 'approve'])
            ->name('approve');

        Route::post('{id}/reject', [JamaahApprovalController::class, 'reject'])
            ->name('reject');
    });

/* ===========================
| KEBERANGKATAN
=========================== */
Route::resource('keberangkatan', KeberangkatanController::class)
    ->names('operator.keberangkatan');

Route::get(
        'keberangkatan-paket/{id}',
        [AjaxKeberangkatanController::class, 'paket']
    )->name('operator.ajax.keberangkatan-paket');
/* ===========================
| MANIFEST
=========================== */
Route::resource('manifest', ManifestController::class)
    ->names('operator.manifest');

Route::get(
    'manifest/{keberangkatan_id}/print',
    [ManifestController::class, 'print']
)->name('operator.manifest.print');

/* ===========================
| MASTER PAKET
=========================== */
Route::resource('master-paket', PaketMasterController::class)
    ->names('operator.master-paket');

/* ===========================
| PASSPORT JAMAAH
=========================== */
Route::resource('passport', PassportJamaahController::class)
    ->names('operator.passport');

Route::get(
    'passport/{id}/print',
    [PassportJamaahController::class, 'print']
)->name('operator.passport.print');

Route::get(
    'passport/{id}/srp',
    [PassportJamaahController::class, 'srp']
)->name('operator.passport.srp');

/* ===========================
| UPDATE JAMAAH
=========================== */
Route::resource('update-jamaah', UpdateJamaahController::class)
    ->names('operator.update-jamaah');

/* ===========================
| VISA
=========================== */
Route::resource('visa', VisaController::class)
    ->names('operator.visa');

/* ===========================
| USER JAMAAH (AKUN LOGIN)
=========================== */
Route::prefix('jamaah-user')
    ->as('operator.jamaah-user.')
    ->group(function () {

        Route::get('/', [JamaahUserController::class, 'index'])
            ->name('index');

        Route::get('create', [JamaahUserController::class, 'create'])
            ->name('create');

        Route::post('/', [JamaahUserController::class, 'store'])
            ->name('store');

        Route::post('{id}/reset-password', [JamaahUserController::class, 'resetPassword'])
            ->name('reset-password');

        Route::patch('{id}/toggle', [JamaahUserController::class, 'toggleActive'])
            ->name('toggle');
    });

Route::prefix('jamaah-approval')
    ->as('operator.jamaah-approval.')
    ->group(function () {

        Route::get('/', [JamaahApprovalController::class, 'index'])
            ->name('index');

        Route::get('{id}', [JamaahApprovalController::class, 'show'])
            ->name('show');

        Route::post('{id}/approve', [JamaahApprovalController::class, 'approve'])
            ->name('approve');

        Route::post('{id}/reject', [JamaahApprovalController::class, 'reject'])
            ->name('reject');

        // ✅ FIXED BULK APPROVE
        Route::post('bulk-approve', [JamaahApprovalController::class, 'bulkApprove'])
            ->name('bulk-approve');
    });



// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Operator\{
//     DaftarJamaahController,
//     KeberangkatanController,
//     ManifestController,
//     PaketMasterController,
//     PassportJamaahController,
//     UpdateJamaahController,
//     VisaController,
//     JamaahUserController,
//     JamaahApprovalController
// };

// /*
// |--------------------------------------------------------------------------
// | OPERATOR ROUTES — FINAL STABLE
// |--------------------------------------------------------------------------
// */

// Route::prefix('operator')
//     ->name('operator.')
//     ->middleware(['auth']) // ⬅️ WAJIB
//     ->group(function () {

//     /* ===========================
//        DASHBOARD
//     ============================ */
//     Route::get('dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');


//     /* ===========================
//        DAFTAR JAMAAH
//     ============================ */
//     Route::resource('daftar-jamaah', DaftarJamaahController::class);

//     Route::get(
//         'daftar-jamaah/ajax-keberangkatan-paket/{id}',
//         [DaftarJamaahController::class, 'ajaxKeberangkatanPaket']
//     )->name('ajax.keberangkatan-paket');

//     Route::get(
//         'daftar-jamaah/export/excel',
//         [DaftarJamaahController::class, 'exportExcel']
//     )->name('daftar-jamaah.export.excel');

//     Route::get(
//         'daftar-jamaah/export/pdf',
//         [DaftarJamaahController::class, 'exportPdf']
//     )->name('daftar-jamaah.export.pdf');

//     Route::get(
//         'daftar-jamaah/{id}/print',
//         [DaftarJamaahController::class, 'print']
//     )->name('daftar-jamaah.print');


//     /* ===========================
//        JAMAAH APPROVAL (PUSAT)
//     ============================ */
//     Route::prefix('jamaah-approval')->group(function () {
//         Route::get('/', [JamaahApprovalController::class, 'index'])->name('jamaah-approval.index');
//         Route::get('{id}', [JamaahApprovalController::class, 'show'])->name('jamaah-approval.show');
//         Route::post('{id}/approve', [JamaahApprovalController::class, 'approve'])->name('jamaah-approval.approve');
//         Route::post('{id}/reject', [JamaahApprovalController::class, 'reject'])->name('jamaah-approval.reject');
//     });


//     /* ===========================
//        MASTER DATA
//     ============================ */
//     Route::resource('keberangkatan', KeberangkatanController::class);
//     Route::resource('manifest', ManifestController::class);
//     Route::resource('master-paket', PaketMasterController::class);
//     Route::resource('passport', PassportJamaahController::class);
//     Route::resource('update-jamaah', UpdateJamaahController::class);
//     Route::resource('visa', VisaController::class);


//     /* ===========================
//        USER JAMAAH
//     ============================ */
//     Route::prefix('jamaah-user')->group(function () {
//         Route::get('/', [JamaahUserController::class, 'index'])->name('jamaah-user.index');
//         Route::get('create', [JamaahUserController::class, 'create'])->name('jamaah-user.create');
//         Route::post('/', [JamaahUserController::class, 'store'])->name('jamaah-user.store');
//         Route::post('{id}/reset-password', [JamaahUserController::class, 'resetPassword'])->name('jamaah-user.reset-password');
//         Route::patch('{id}/toggle', [JamaahUserController::class, 'toggleActive'])->name('jamaah-user.toggle');
//     });

// });


// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Operator\DaftarJamaahController;
// use App\Http\Controllers\Operator\KeberangkatanController;
// use App\Http\Controllers\Operator\ManifestController;
// use App\Http\Controllers\Operator\PaketMasterController;
// use App\Http\Controllers\Operator\PassportJamaahController;
// use App\Http\Controllers\Operator\UpdateJamaahController;
// use App\Http\Controllers\Operator\VisaController;
// use App\Http\Controllers\Operator\JamaahUserController;
// use App\Http\Controllers\Operator\JamaahApprovalController;

// /*
// |--------------------------------------------------------------------------
// | OPERATOR ROUTES — F7 PREMIUM FINAL
// | prefix: operator
// | middleware: auth, role:operator
// |--------------------------------------------------------------------------
// */

// /* ===========================
//    DASHBOARD
// =========================== */
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->name('operator.dashboard');

// /* ===========================
//    DAFTAR JAMAAH
// =========================== */
// Route::resource('daftar-jamaah', DaftarJamaahController::class)
//     ->names('operator.daftar-jamaah');

// // Export Excel & PDF
// Route::get('daftar-jamaah/export/excel', [DaftarJamaahController::class, 'exportExcel'])
//     ->name('operator.daftar-jamaah.export.excel');

// Route::get('daftar-jamaah/export/pdf', [DaftarJamaahController::class, 'exportPdf'])
//     ->name('operator.daftar-jamaah.export.pdf');

// // PRINT (AMAN)
// Route::get('daftar-jamaah/{id}/print', [DaftarJamaahController::class, 'print'])
//     ->name('operator.daftar-jamaah.print');

//     /* ===========================
//    JAMAAH APPROVAL (PUSAT)
// =========================== */
// Route::prefix('jamaah-approval')
//     ->as('operator.jamaah-approval.')
//     ->group(function () {

//         // LIST PENDING
//         Route::get('/',
//             [JamaahApprovalController::class, 'index']
//         )->name('index');

//         // DETAIL (READ ONLY)
//         Route::get('{id}',
//             [JamaahApprovalController::class, 'show']
//         )->name('show');

//         // APPROVE
//         Route::post('{id}/approve',
//             [JamaahApprovalController::class, 'approve']
//         )->name('approve');

//         // REJECT
//         Route::post('{id}/reject',
//             [JamaahApprovalController::class, 'reject']
//         )->name('reject');
//     });

// /* ===========================
//    KEBERANGKATAN
// =========================== */
// Route::resource('keberangkatan', KeberangkatanController::class)
//     ->names('operator.keberangkatan');

// Route::get(
//     '/operator/daftar-jamaah/ajax-keberangkatan-paket/{id}',
//     [DaftarJamaahController::class, 'ajaxKeberangkatanPaket']
// )->name('operator.ajax.keberangkatan-paket');



// /* ===========================
//    MANIFEST
// =========================== */
// Route::resource('manifest', ManifestController::class)
//     ->names('operator.manifest');

// Route::get('manifest/{keberangkatan_id}/print', [ManifestController::class, 'print'])
//     ->name('operator.manifest.print');


// /* ===========================
//    MASTER PAKET (PAKET MASTER)
// =========================== */
// Route::resource('master-paket', PaketMasterController::class)
//     ->names('operator.master-paket');


// /* ===========================
//    PASSPORT JAMAAH
// =========================== */
// Route::resource('passport', PassportJamaahController::class)
//     ->names('operator.passport');

// Route::get('passport/{id}/print', [PassportJamaahController::class, 'print'])
//     ->name('operator.passport.print');

// Route::get('passport/{id}/srp', [PassportJamaahController::class, 'srp'])
//     ->name('operator.passport.srp');


// /* ===========================
//    UPDATE JAMAAH
// =========================== */
// Route::resource('update-jamaah', UpdateJamaahController::class)
//     ->names('operator.update-jamaah');


// /* ===========================
//    VISA
// =========================== */
// Route::resource('visa', VisaController::class)
//     ->names('operator.visa');

// /* ===========================
//    USER JAMAAH (AKUN LOGIN)
// =========================== */
// Route::prefix('jamaah-user')
//     ->as('operator.jamaah-user.')
//     ->group(function () {

//         Route::get('/', 
//             [JamaahUserController::class, 'index']
//         )->name('index');

//         Route::get('create', 
//             [JamaahUserController::class, 'create']
//         )->name('create');

//         Route::post('/', 
//             [JamaahUserController::class, 'store']
//         )->name('store');

//         Route::post('{id}/reset-password',
//             [JamaahUserController::class, 'resetPassword']
//         )->name('reset-password');

//         Route::patch('{id}/toggle',
//             [JamaahUserController::class, 'toggleActive']
//         )->name('toggle');
//     });

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Jamaah\JamaahController;
use App\Http\Controllers\Jamaah\JamaahDocumentController;
use App\Http\Controllers\Jamaah\JamaahApprovalController;
use App\Http\Controllers\Jamaah\JamaahAccountController;
/*
|--------------------------------------------------------------------------
| JAMAAH MODULE ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('jamaah')
    ->name('jamaah.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | STATIC ROUTES (NO PARAMETER)
        |--------------------------------------------------------------------------
        */

        Route::get('/', [JamaahController::class, 'index'])
            ->name('index')
            ->middleware('permission:jamaah.view');

        Route::get('/create', [JamaahController::class, 'create'])
            ->name('create')
            ->middleware('permission:jamaah.create');

        Route::post('/', [JamaahController::class, 'store'])
            ->name('store')
            ->middleware('permission:jamaah.create');

        Route::get('/documents', [JamaahDocumentController::class, 'index'])
            ->name('documents.index')
            ->middleware('permission:jamaah.document.view');

        Route::delete('/documents/{document}', [JamaahDocumentController::class, 'destroy'])
            ->whereNumber('document')
            ->name('documents.destroy')
            ->middleware('permission:jamaah.document.view');

        Route::get('/bookings-history', [JamaahController::class, 'bookingHistory'])
            ->name('bookings.history')
            ->middleware('permission:jamaah.booking.view');

        /*
        |--------------------------------------------------------------------------
        | APPROVAL LIST
        |--------------------------------------------------------------------------
        */
        Route::get('/approvals', [JamaahApprovalController::class, 'index'])
            ->name('approvals.index')
            ->middleware('permission:jamaah.approval.view');
            
        /*
        |--------------------------------------------------------------------------
        | PARAMETER ROUTES (SAFE)
        |--------------------------------------------------------------------------
        */

        Route::get('/{jamaah}', [JamaahController::class, 'show'])
            ->whereNumber('jamaah')
            ->name('show')
            ->middleware('permission:jamaah.view');

        Route::get('/{jamaah}/edit', [JamaahController::class, 'edit'])
            ->whereNumber('jamaah')
            ->name('edit')
            ->middleware('permission:jamaah.update');

        Route::put('/{jamaah}', [JamaahController::class, 'update'])
            ->whereNumber('jamaah')
            ->name('update')
            ->middleware('permission:jamaah.update');

        Route::delete('/{jamaah}', [JamaahController::class, 'destroy'])
            ->whereNumber('jamaah')
            ->name('destroy')
            ->middleware('permission:jamaah.delete');

        Route::post('/{jamaah}/documents', [JamaahDocumentController::class, 'store'])
            ->whereNumber('jamaah')
            ->name('documents.store')
            ->middleware('permission:jamaah.document.view');

        Route::post('/{jamaah}/approve', [JamaahApprovalController::class, 'approve'])
            ->whereNumber('jamaah')
            ->name('approve')
            ->middleware('permission:jamaah.approval.view');

        Route::post('/{jamaah}/reject', [JamaahApprovalController::class, 'reject'])
            ->whereNumber('jamaah')
            ->name('reject')
            ->middleware('permission:jamaah.approval.view');

        Route::get('/{jamaah}/bookings', [JamaahController::class, 'bookings'])
            ->whereNumber('jamaah')
            ->name('bookings')
            ->middleware('permission:jamaah.booking.view');
        /*
        |--------------------------------------------------------------------------
        | ACCOUNT MANAGEMENT
        |--------------------------------------------------------------------------
        */

        // 🔹 List akun jamaah
        Route::get('/accounts',
            [JamaahAccountController::class, 'index'])
            ->name('account.index')
            ->middleware('permission:jamaah.account.view');

        // 🔹 Create single account
        Route::post('/{jamaah}/account/create',
            [JamaahAccountController::class,'create'])
            ->whereNumber('jamaah')
            ->name('account.create')
            ->middleware('permission:jamaah.account.create');

        // 🔹 Reset password
        Route::post('/{jamaah}/account/reset',
            [JamaahAccountController::class,'reset'])
            ->whereNumber('jamaah')
            ->name('account.reset')
            ->middleware('permission:jamaah.account.reset');

        // 🔹 Deactivate account
        Route::post('/{jamaah}/account/deactivate',
            [JamaahAccountController::class,'deactivate'])
            ->whereNumber('jamaah')
            ->name('account.deactivate')
            ->middleware('permission:jamaah.account.update');

        // 🔹 Activate account
        Route::post('/{jamaah}/account/activate',
            [JamaahAccountController::class,'activate'])
            ->whereNumber('jamaah')
            ->name('account.activate')
            ->middleware('permission:jamaah.account.update');

        // 🔹 Bulk create account
        Route::post('/accounts/bulk-create',
            [JamaahAccountController::class,'bulkCreate'])
            ->name('account.bulk-create')
            ->middleware('permission:jamaah.account.create');    

        Route::post('/{jamaah}/account/send-wa',
            [JamaahAccountController::class,'sendWa'])
            ->name('account.send-wa')
            ->middleware('permission:jamaah.account.reset');
            
    });
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Jamaah\AuthController;
use App\Http\Controllers\Jamaah\DashboardController;
use App\Http\Controllers\Jamaah\ProfileController;
use App\Http\Controllers\Jamaah\TabunganController;
use App\Http\Controllers\Jamaah\NotificationController;

/*
|--------------------------------------------------------------------------
| JAMAAH MODULE ROUTES
|--------------------------------------------------------------------------
| Semua route khusus jamaah (mobile-first)
| Guard  : auth:jamaah
| Prefix : /jamaah
| Name   : jamaah.*
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:jamaah', 'jamaah.approved'])
    ->prefix('jamaah')
    ->as('jamaah.')
    ->group(function () {

        /* ================= DASHBOARD ================= */
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /* ================= TABUNGAN (BEBAS) ================= */
        Route::get('tabungan', [TabunganController::class, 'index'])
            ->name('tabungan.index');

        Route::get('tabungan/topup', [TabunganController::class, 'createTopup'])
            ->name('tabungan.topup');

        Route::post('tabungan/topup', [TabunganController::class, 'storeTopup'])
            ->name('tabungan.topup.store');

        Route::get('tabungan/topup/history', [TabunganController::class, 'history'])
            ->name('tabungan.topup.history');

                /* ================= KWITANSI SETORAN TABUNGAN ================= */

        Route::get(
            'tabungan/bukti/{bukti}',
            [\App\Http\Controllers\Jamaah\BuktiSetoranController::class, 'show']
        )->name('tabungan.bukti.show');

        /* ================= NOTIFIKASI ================= */
        Route::get('notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');

        Route::get('notifications/{id}', [NotificationController::class, 'show'])
            ->name('notifications.show');

        Route::post('notifications/read/{id}', [NotificationController::class, 'markAsRead'])
            ->name('notifications.read');

        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('notifications.read-all');

        /* ================= PROFILE (SENSITIVE) ================= */
        Route::middleware('password.fresh')->group(function () {

            Route::get('profile', [ProfileController::class, 'index'])
                ->name('profile');

            Route::post('profile', [ProfileController::class, 'update'])
                ->name('profile.update');

            Route::post('profile/password', [ProfileController::class, 'updatePassword'])
                ->name('profile.password');
        });

        /* ================= LOGOUT ================= */
        Route::post('logout', [AuthController::class, 'logout'])
            ->name('logout');
    });

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'ok',
            'time' => now(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});
/*
|--------------------------------------------------------------------------
| Redirect After Login
|--------------------------------------------------------------------------
*/

Route::get('/redirect-role', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('redirect.role');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/modules/dashboard.php';
require __DIR__.'/modules/user.php';
require __DIR__.'/modules/branch.php';
require __DIR__.'/modules/agent.php';
require __DIR__.'/modules/booking.php';
require __DIR__.'/modules/commission.php';
require __DIR__.'/modules/paket.php';
require __DIR__.'/modules/jamaah.php';
require __DIR__.'/modules/departure.php';
require __DIR__.'/modules/manifest.php';
require __DIR__.'/modules/rooming.php';
require __DIR__.'/modules/payment.php';
require __DIR__.'/modules/marketing.php';
require __DIR__.'/modules/ticketing.php';
require __DIR__.'/modules/visa.php';
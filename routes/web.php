<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

    $user = \App\Models\User::findOrFail($request->route('id'));

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return redirect('https://jadiumrah.cloud/verify-success');

})->middleware(['signed'])->name('verification.verify');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
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
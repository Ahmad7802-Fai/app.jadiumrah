<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    require __DIR__ . '/api/v1/paket.php';
    require __DIR__ . '/api/v1/auth.php';
    require __DIR__ . '/api/v1/booking.php';
    require __DIR__ . '/api/v1/agent.php';
    require __DIR__ . '/api/v1/visa.php';
    require __DIR__ . '/api/v1/payment.php';
    require __DIR__ . '/api/v1/jamaah.php';
});

// use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\Api\V1\PaketController;
// use App\Http\Controllers\Api\V1\BookingController;
// use App\Http\Controllers\Api\V1\JamaahController;
// use App\Http\Controllers\Api\V1\PaketDepartureController;
// use App\Http\Controllers\Api\V1\PaymentController;
// use App\Http\Controllers\Api\V1\BookingDocumentController;
// use App\Http\Controllers\Api\V1\SavingController;
// use App\Http\Controllers\Api\V1\ProfileController;
// use App\Http\Controllers\Api\V1\AgentController;
// /*
// |--------------------------------------------------------------------------
// | API V1
// |--------------------------------------------------------------------------
// */

// Route::prefix('v1')->name('api.')->group(function () {

//     /*
//     |--------------------------------------------------------------------------
//     | AUTH
//     |--------------------------------------------------------------------------
//     */

//     Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

//     Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

//     /*
//     |--------------------------------------------------------------------------
//     | PUBLIC DATA
//     |--------------------------------------------------------------------------
//     */

//     Route::get('/pakets', [PaketController::class, 'index'])->name('pakets.index');

//     Route::get('/pakets/{slug}', [PaketController::class, 'show'])->name('pakets.show');

//     Route::get('/pakets/{paket}/departures',[PaketDepartureController::class, 'byPaket'])->name('pakets.departures');

//     Route::get('/departures/{departure}',[PaketDepartureController::class, 'show'])->name('departures.show');

//     /*
//     |--------------------------------------------------------------------------
//     | PUBLIC DOCUMENTS
//     |--------------------------------------------------------------------------
//     */

//     Route::get('/bookings/{booking:booking_code}/invoice',[BookingDocumentController::class,'invoice'])->name('bookings.invoice');

//     Route::get('/bookings/{booking:booking_code}/receipt',[BookingDocumentController::class,'receipt'])->name('bookings.receipt');

//     /*
//     |--------------------------------------------------------------------------
//     | PROTECTED ROUTES
//     |--------------------------------------------------------------------------
//     */

//     Route::middleware('auth:sanctum')->group(function () {

//         /*
//         |--------------------------------------------------------------------------
//         | AUTH
//         |--------------------------------------------------------------------------
//         */

//         Route::post('/logout', [AuthController::class, 'logout'])
//             ->name('auth.logout');

//         Route::get('/me', [AuthController::class, 'me'])
//             ->name('auth.me');


//         /*
//         --------------------------------------------------------------------------
//         | PROFILE
//         |--------------------------------------------------------------------------
//         */

//         Route::get('/profile',[ProfileController::class,'show']);
//         Route::put('/profile',[ProfileController::class,'update']);
//         Route::post('/profile/change-password',[ProfileController::class,'changePassword']);
//         /*
//         |--------------------------------------------------------------------------
//         | AGENT
//         |--------------------------------------------------------------------------
//         */

//         Route::prefix('agent')->name('agent.')->group(function(){

//             Route::get('/stats',[AgentController::class,'stats']);

//         });

//         /*
//         |--------------------------------------------------------------------------
//         | JAMAAH
//         |--------------------------------------------------------------------------
//         */

//         Route::get('/jamaahs/me',
//             [JamaahController::class,'me']
//         )->name('jamaahs.me');

//         Route::apiResource('jamaahs', JamaahController::class)
//             ->names([
//                 'index'   => 'jamaahs.index',
//                 'store'   => 'jamaahs.store',
//                 'show'    => 'jamaahs.show',
//                 'update'  => 'jamaahs.update',
//                 'destroy' => 'jamaahs.destroy',
//             ]);

//         Route::post('/jamaahs/{jamaah}/documents',
//             [JamaahController::class,'uploadDocument']
//         )->name('jamaahs.documents.upload');



//         /*
//         |--------------------------------------------------------------------------
//         | BOOKINGS
//         |--------------------------------------------------------------------------
//         */

//         Route::apiResource('bookings', BookingController::class)
//             ->only(['index','store','show'])
//             ->names([
//                 'index' => 'bookings.index',
//                 'store' => 'bookings.store',
//                 'show'  => 'bookings.show',
//             ]);

//         Route::post('/bookings/{booking}/confirm',
//             [BookingController::class,'confirm']
//         )->name('bookings.confirm');

//         Route::post('/bookings/{booking}/cancel',
//             [BookingController::class,'cancel']
//         )->name('bookings.cancel');



//         /*
//         |--------------------------------------------------------------------------
//         | PAYMENTS
//         |--------------------------------------------------------------------------
//         */

//         Route::get('/bookings/{booking:booking_code}/payments',
//             [PaymentController::class,'byBooking']
//         )->name('bookings.payments.index');

//         Route::post('/bookings/{booking:booking_code}/payments',
//             [PaymentController::class,'store']
//         )->name('bookings.payments.store');

//         Route::get('/payments/{payment}/receipt',
//             [PaymentController::class,'receipt']
//         )->name('payments.receipt');

//         Route::post('/payments/{payment}/approve',
//             [PaymentController::class,'approve']
//         )->name('payments.approve');

//         Route::post('/payments/{payment}/reject',
//             [PaymentController::class,'reject']
//         )->name('payments.reject');

//         Route::delete('/payments/{payment}',
//             [PaymentController::class,'destroy']
//         )->name('payments.destroy');



//         /*
//         |--------------------------------------------------------------------------
//         | SAVING / TABUNGAN
//         |--------------------------------------------------------------------------
//         */

//         Route::prefix('saving')->name('saving.')->group(function(){

//             Route::get('/me',
//                 [SavingController::class,'me']
//             )->name('me');

//             Route::post('/open',
//                 [SavingController::class,'openAccount']
//             )->name('open');

//             Route::post('/deposit',
//                 [SavingController::class,'deposit']
//             )->name('deposit');

//             Route::post('/withdraw',
//                 [SavingController::class,'withdraw']
//             )->name('withdraw');

//             Route::get('/transactions',
//                 [SavingController::class,'transactions']
//             )->name('transactions');

//         });

//     });

// });
// <?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\AuthController;

// Route::prefix('v1')->name('api.')->group(function () {

//     Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
//     Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

//     /*
//     |--------------------------------------------------------------------------
//     | PROTECTED AUTH
//     |--------------------------------------------------------------------------
//     */

//     Route::middleware('auth:sanctum')->group(function () {
//         Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
//         Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
//     });

//     require __DIR__ . '/api/v1/booking.php';
//     require __DIR__ . '/api/v1/visa.php';
// });

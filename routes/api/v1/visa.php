<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Visa\VisaDocumentApiController;
use App\Http\Controllers\Api\V1\Visa\VisaOrderApiController;
use App\Http\Controllers\Api\V1\Visa\VisaPaymentApiController;
use App\Http\Controllers\Api\V1\Visa\VisaProductApiController;

Route::middleware(['auth:sanctum'])->prefix('visa')->name('api.visa.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */
    Route::get('/products', [VisaProductApiController::class, 'index'])
        ->middleware('permission:visa.product.view')
        ->name('products.index');

    Route::post('/products', [VisaProductApiController::class, 'store'])
        ->middleware('permission:visa.product.create')
        ->name('products.store');

    Route::get('/products/{visaProduct}', [VisaProductApiController::class, 'show'])
        ->middleware('permission:visa.product.view')
        ->name('products.show');

    Route::put('/products/{visaProduct}', [VisaProductApiController::class, 'update'])
        ->middleware('permission:visa.product.update')
        ->name('products.update');

    Route::delete('/products/{visaProduct}', [VisaProductApiController::class, 'destroy'])
        ->middleware('permission:visa.product.delete')
        ->name('products.destroy');

    Route::patch('/products/{visaProduct}/toggle-active', [VisaProductApiController::class, 'toggleActive'])
        ->middleware('permission:visa.product.update')
        ->name('products.toggle-active');

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */
    Route::get('/orders', [VisaOrderApiController::class, 'index'])
        ->middleware('permission:visa.order.view')
        ->name('orders.index');

    Route::post('/orders', [VisaOrderApiController::class, 'store'])
        ->middleware('permission:visa.order.create')
        ->name('orders.store');

    Route::get('/orders/{visaOrder}', [VisaOrderApiController::class, 'show'])
        ->middleware('permission:visa.order.view')
        ->name('orders.show');

    Route::put('/orders/{visaOrder}', [VisaOrderApiController::class, 'update'])
        ->middleware('permission:visa.order.update')
        ->name('orders.update');

    Route::delete('/orders/{visaOrder}', [VisaOrderApiController::class, 'destroy'])
        ->middleware('permission:visa.order.delete')
        ->name('orders.destroy');

    Route::patch('/orders/{visaOrder}/status', [VisaOrderApiController::class, 'updateStatus'])
        ->middleware('permission:visa.order.status.update')
        ->name('orders.update-status');

    Route::post('/orders/{visaOrder}/notes', [VisaOrderApiController::class, 'addNote'])
        ->middleware('permission:visa.order.note.create')
        ->name('orders.notes.store');

    Route::post('/orders/{visaOrder}/travelers', [VisaOrderApiController::class, 'addTraveler'])
        ->middleware('permission:visa.order.traveler.create')
        ->name('orders.travelers.store');

    Route::put('/orders/{visaOrder}/travelers/{traveler}', [VisaOrderApiController::class, 'updateTraveler'])
        ->middleware('permission:visa.order.traveler.update')
        ->name('orders.travelers.update');

    Route::delete('/orders/{visaOrder}/travelers/{traveler}', [VisaOrderApiController::class, 'deleteTraveler'])
        ->middleware('permission:visa.order.traveler.delete')
        ->name('orders.travelers.destroy');

    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/payments', [VisaPaymentApiController::class, 'index'])
        ->middleware('permission:visa.payment.view')
        ->name('payments.index');

    Route::post('/payments', [VisaPaymentApiController::class, 'store'])
        ->middleware('permission:visa.payment.create')
        ->name('payments.store');

    Route::get('/payments/{visaPayment}', [VisaPaymentApiController::class, 'show'])
        ->middleware('permission:visa.payment.view')
        ->name('payments.show');

    Route::put('/payments/{visaPayment}', [VisaPaymentApiController::class, 'update'])
        ->middleware('permission:visa.payment.update')
        ->name('payments.update');

    Route::delete('/payments/{visaPayment}', [VisaPaymentApiController::class, 'destroy'])
        ->middleware('permission:visa.payment.delete')
        ->name('payments.destroy');

    Route::patch('/payments/{visaPayment}/mark-paid', [VisaPaymentApiController::class, 'markPaid'])
        ->middleware('permission:visa.payment.approve')
        ->name('payments.mark-paid');

    Route::patch('/payments/{visaPayment}/mark-failed', [VisaPaymentApiController::class, 'markFailed'])
        ->middleware('permission:visa.payment.update')
        ->name('payments.mark-failed');

    Route::patch('/payments/{visaPayment}/mark-refunded', [VisaPaymentApiController::class, 'markRefunded'])
        ->middleware('permission:visa.payment.refund')
        ->name('payments.mark-refunded');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/documents', [VisaDocumentApiController::class, 'index'])
        ->middleware('permission:visa.document.view')
        ->name('documents.index');

    Route::post('/documents', [VisaDocumentApiController::class, 'store'])
        ->middleware('permission:visa.document.create')
        ->name('documents.store');

    Route::get('/documents/{visaDocument}', [VisaDocumentApiController::class, 'show'])
        ->middleware('permission:visa.document.view')
        ->name('documents.show');

    Route::put('/documents/{visaDocument}', [VisaDocumentApiController::class, 'update'])
        ->middleware('permission:visa.document.update')
        ->name('documents.update');

    Route::delete('/documents/{visaDocument}', [VisaDocumentApiController::class, 'destroy'])
        ->middleware('permission:visa.document.delete')
        ->name('documents.destroy');

    Route::patch('/documents/{visaDocument}/verify', [VisaDocumentApiController::class, 'verify'])
        ->middleware('permission:visa.document.verify')
        ->name('documents.verify');

    Route::patch('/documents/{visaDocument}/unverify', [VisaDocumentApiController::class, 'unverify'])
        ->middleware('permission:visa.document.verify')
        ->name('documents.unverify');

    Route::get('/documents/{visaDocument}/download', [VisaDocumentApiController::class, 'download'])
        ->middleware('permission:visa.document.download')
        ->name('documents.download');
});
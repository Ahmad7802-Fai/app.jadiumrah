<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Visa\VisaOrderController;
use App\Http\Controllers\Visa\VisaProductController;
use App\Http\Controllers\Visa\VisaPaymentController;
use App\Http\Controllers\Visa\VisaDocumentController;

Route::middleware(['auth'])->prefix('visa')->name('visa.')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */
    Route::get('/products', [VisaProductController::class, 'index'])
        ->middleware('permission:visa.product.view')
        ->name('products.index');

    Route::get('/products/create', [VisaProductController::class, 'create'])
        ->middleware('permission:visa.product.create')
        ->name('products.create');

    Route::post('/products', [VisaProductController::class, 'store'])
        ->middleware('permission:visa.product.create')
        ->name('products.store');

    Route::get('/products/{visaProduct}', [VisaProductController::class, 'show'])
        ->middleware('permission:visa.product.view')
        ->name('products.show');

    Route::get('/products/{visaProduct}/edit', [VisaProductController::class, 'edit'])
        ->middleware('permission:visa.product.update')
        ->name('products.edit');

    Route::put('/products/{visaProduct}', [VisaProductController::class, 'update'])
        ->middleware('permission:visa.product.update')
        ->name('products.update');

    Route::delete('/products/{visaProduct}', [VisaProductController::class, 'destroy'])
        ->middleware('permission:visa.product.delete')
        ->name('products.destroy');

    Route::patch('/products/{visaProduct}/toggle-active', [VisaProductController::class, 'toggleActive'])
        ->middleware('permission:visa.product.update')
        ->name('products.toggle-active');

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */
    Route::get('/orders', [VisaOrderController::class, 'index'])
        ->middleware('permission:visa.order.view')
        ->name('orders.index');

    Route::get('/orders/create', [VisaOrderController::class, 'create'])
        ->middleware('permission:visa.order.create')
        ->name('orders.create');

    Route::post('/orders', [VisaOrderController::class, 'store'])
        ->middleware('permission:visa.order.create')
        ->name('orders.store');

    Route::get('/orders/{visaOrder}', [VisaOrderController::class, 'show'])
        ->middleware('permission:visa.order.view')
        ->name('orders.show');

    Route::get('/orders/{visaOrder}/edit', [VisaOrderController::class, 'edit'])
        ->middleware('permission:visa.order.update')
        ->name('orders.edit');

    Route::put('/orders/{visaOrder}', [VisaOrderController::class, 'update'])
        ->middleware('permission:visa.order.update')
        ->name('orders.update');

    Route::delete('/orders/{visaOrder}', [VisaOrderController::class, 'destroy'])
        ->middleware('permission:visa.order.delete')
        ->name('orders.destroy');

    Route::patch('/orders/{visaOrder}/status', [VisaOrderController::class, 'updateStatus'])
        ->middleware('permission:visa.order.status.update')
        ->name('orders.update-status');

    Route::post('/orders/{visaOrder}/notes', [VisaOrderController::class, 'addNote'])
        ->middleware('permission:visa.order.note.create')
        ->name('orders.notes.store');

    Route::post('/orders/{visaOrder}/travelers', [VisaOrderController::class, 'addTraveler'])
        ->middleware('permission:visa.order.traveler.create')
        ->name('orders.travelers.store');

    Route::put('/orders/{visaOrder}/travelers/{traveler}', [VisaOrderController::class, 'updateTraveler'])
        ->middleware('permission:visa.order.traveler.update')
        ->name('orders.travelers.update');

    Route::delete('/orders/{visaOrder}/travelers/{traveler}', [VisaOrderController::class, 'deleteTraveler'])
        ->middleware('permission:visa.order.traveler.delete')
        ->name('orders.travelers.destroy');

    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/payments', [VisaPaymentController::class, 'index'])
        ->middleware('permission:visa.payment.view')
        ->name('payments.index');

    Route::get('/payments/create', [VisaPaymentController::class, 'create'])
        ->middleware('permission:visa.payment.create')
        ->name('payments.create');

    Route::post('/payments', [VisaPaymentController::class, 'store'])
        ->middleware('permission:visa.payment.create')
        ->name('payments.store');

    Route::get('/payments/{visaPayment}', [VisaPaymentController::class, 'show'])
        ->middleware('permission:visa.payment.view')
        ->name('payments.show');

    Route::get('/payments/{visaPayment}/edit', [VisaPaymentController::class, 'edit'])
        ->middleware('permission:visa.payment.update')
        ->name('payments.edit');

    Route::put('/payments/{visaPayment}', [VisaPaymentController::class, 'update'])
        ->middleware('permission:visa.payment.update')
        ->name('payments.update');

    Route::delete('/payments/{visaPayment}', [VisaPaymentController::class, 'destroy'])
        ->middleware('permission:visa.payment.delete')
        ->name('payments.destroy');

    Route::patch('/payments/{visaPayment}/mark-paid', [VisaPaymentController::class, 'markPaid'])
        ->middleware('permission:visa.payment.approve')
        ->name('payments.mark-paid');

    Route::patch('/payments/{visaPayment}/mark-failed', [VisaPaymentController::class, 'markFailed'])
        ->middleware('permission:visa.payment.update')
        ->name('payments.mark-failed');

    Route::patch('/payments/{visaPayment}/mark-refunded', [VisaPaymentController::class, 'markRefunded'])
        ->middleware('permission:visa.payment.refund')
        ->name('payments.mark-refunded');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/documents', [VisaDocumentController::class, 'index'])
        ->middleware('permission:visa.document.view')
        ->name('documents.index');

    Route::get('/documents/create', [VisaDocumentController::class, 'create'])
        ->middleware('permission:visa.document.create')
        ->name('documents.create');

    Route::post('/documents', [VisaDocumentController::class, 'store'])
        ->middleware('permission:visa.document.create')
        ->name('documents.store');

    Route::get('/documents/{visaDocument}', [VisaDocumentController::class, 'show'])
        ->middleware('permission:visa.document.view')
        ->name('documents.show');

    Route::get('/documents/{visaDocument}/edit', [VisaDocumentController::class, 'edit'])
        ->middleware('permission:visa.document.update')
        ->name('documents.edit');

    Route::put('/documents/{visaDocument}', [VisaDocumentController::class, 'update'])
        ->middleware('permission:visa.document.update')
        ->name('documents.update');

    Route::delete('/documents/{visaDocument}', [VisaDocumentController::class, 'destroy'])
        ->middleware('permission:visa.document.delete')
        ->name('documents.destroy');

    Route::patch('/documents/{visaDocument}/verify', [VisaDocumentController::class, 'verify'])
        ->middleware('permission:visa.document.verify')
        ->name('documents.verify');

    Route::patch('/documents/{visaDocument}/unverify', [VisaDocumentController::class, 'unverify'])
        ->middleware('permission:visa.document.verify')
        ->name('documents.unverify');

    Route::get('/documents/{visaDocument}/download', [VisaDocumentController::class, 'download'])
        ->middleware('permission:visa.document.download')
        ->name('documents.download');
});
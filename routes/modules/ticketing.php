<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ticketing\{
    TicketPnrController,
    TicketDepositController,
    TicketAllocationController,
    TicketInvoiceController,
    TicketPaymentController,
    TicketInvoicePdfController,
    TicketPaymentPdfController,
    TicketAuditController,
    TicketRefundController,
    TicketRefundApprovalController,
    TicketReportController
};

/*
|--------------------------------------------------------------------------
| TICKETING MODULE
|--------------------------------------------------------------------------
| Role access dikontrol di policy
| Superadmin & operator auto lewat Gate::before
*/

Route::prefix('ticketing')
    ->name('ticketing.')
    ->group(function () {

        /* =====================
         | PNR
         ===================== */
        Route::get('pnr', [TicketPnrController::class, 'index'])
            ->name('pnr.index');
        // FORM CREATE
        Route::get('pnr/create',
            [TicketPnrController::class, 'create'])
            ->name('pnr.create');
        // STORE
        Route::post('pnr',
            [TicketPnrController::class, 'store'])
            ->name('pnr.store');

        // FORM EDIT
        Route::get('pnr/{pnr}/edit',
            [TicketPnrController::class, 'edit'])
            ->name('pnr.edit');

        // UPDATE
        Route::put('pnr/{pnr}',
            [TicketPnrController::class, 'update'])
            ->name('pnr.update');
        /*
        |--------------------------------------------------------------------------
        | PNR ROUTES (SECTOR)
        |--------------------------------------------------------------------------
        */
        Route::get('pnr/{pnr}/routes/edit',
            [TicketPnrController::class, 'editRoutes'])
            ->name('pnr.routes.edit');

        Route::put('pnr/{pnr}/routes',
            [TicketPnrController::class, 'updateRoutes'])
            ->name('pnr.routes.update');

        Route::get('pnr/{pnr}', [TicketPnrController::class, 'show'])
            ->name('pnr.show');

        Route::post('pnr/{pnr}/status', [TicketPnrController::class, 'updateStatus'])
            ->name('pnr.updateStatus');
        // ✅ CONFIRM PNR
        Route::post(
            'pnr/{pnr}/confirm',
            [TicketPnrController::class, 'confirm']
        )->name('pnr.confirm');

        /* =====================
         | DEPOSIT
         ===================== */
        Route::post('deposit', [TicketDepositController::class, 'store'])
            ->name('deposit.store');

        Route::post('deposit/{deposit}/approve', [TicketDepositController::class, 'approve'])
            ->name('deposit.approve');

        /* =====================
         | ALLOCATION
         ===================== */
        Route::post('pnr/{pnr}/allocate', [TicketAllocationController::class, 'store'])
            ->name('allocation.store');

        /* =====================
        | INVOICE
        ===================== */
        Route::get('invoice',
            [TicketInvoiceController::class, 'index']
        )->name('invoice.index');

        Route::post(
            'invoice/from-pnr/{pnr}',
            [TicketInvoiceController::class, 'storeFromPnr'] // ✅ BENAR
        )->name('invoice.createFromPnr');


        Route::get('invoice/{invoice}',
            [TicketInvoiceController::class, 'show']
        )->name('invoice.show');

        Route::get('invoice/{invoice}/edit',
            [TicketInvoiceController::class, 'edit']
        )->name('invoice.edit');

        Route::put('invoice/{invoice}',
            [TicketInvoiceController::class, 'update']
        )->name('invoice.update');
        /* =====================
        | INVOICE PDF
        ===================== */
        Route::get(
            'invoice/{invoice}/pdf',
            [TicketInvoicePdfController::class, 'show']
        )->name('invoice.pdf');

        /* =====================
         | PAYMENT
         ===================== */
        Route::post('invoice/{invoice}/pay', [TicketPaymentController::class, 'store'])
            ->name('payment.store');

        Route::get('payment/{payment}/pdf',
            [TicketPaymentPdfController::class, 'receipt']
        )->name('payment.pdf');

        Route::get('audit', [TicketAuditController::class, 'index'])
        ->name('audit.index');

        Route::post('invoice/{invoice}/refund',
            [TicketRefundController::class, 'store']
        )->name('refund.store');
        
        /* =====================
        | REPORT
        ===================== */
        Route::get('report',
            [TicketReportController::class, 'index']
        )->name('report.index');

        Route::get('report/payment/pdf',
            [TicketReportController::class, 'paymentPdf']
        )->name('report.payment.pdf');

        Route::get('report/payment/excel',
            [TicketReportController::class, 'paymentExcel']
        )->name('report.payment.excel');

        Route::get('report/refund/pdf',
            [TicketReportController::class, 'refundPdf']
        )->name('report.refund.pdf');

        Route::get('report/refund/excel',
            [TicketReportController::class, 'refundExcel']
        )->name('report.refund.excel');

        // MAKER
        Route::post('invoice/{invoice}/refund',
            [TicketRefundController::class, 'store']
        )->name('refund.store');

        // CHECKER
        Route::get('refund/approval',
            [TicketRefundApprovalController::class, 'index']
        )->name('refund.approval');

        Route::post('refund/{refund}/approve',
            [TicketRefundApprovalController::class, 'approve']
        )->name('refund.approve');

        Route::post('refund/{refund}/reject',
            [TicketRefundApprovalController::class, 'reject']
        )->name('refund.reject');

        Route::post(
            'ticketing/pnr/store-json',
            [TicketPnrController::class, 'storeJson']
        )->name('pnr.storeJson');

    });

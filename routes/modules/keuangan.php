<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Backoffice\TabunganTopupController;
use App\Http\Controllers\Backoffice\BuktiSetoranController;
use App\Http\Controllers\Keuangan\TripExpenseController;
use App\Http\Controllers\Backoffice\TabunganClosingController;
use App\Http\Controllers\Keuangan\PayoutController;
use App\Http\Controllers\Keuangan\KomisiController;

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
// Route::get('/dashboard', [DashboardController::class, 'index'])
//     ->name('keuangan.dashboard');

// // API chart (dipanggil oleh frontend JS)
// Route::get('/dashboard/chart', [DashboardController::class, 'chartData'])
//     ->name('keuangan.dashboard.chart');

// Route::get('/dashboard/chart-comparison', 
//     [DashboardController::class, 'chartComparison'])
//     ->name('dashboard.chart-comparison');

/*
|--------------------------------------------------------------------------
| Clients (F7 Premium CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('clients')->group(function () {

    // LIST CLIENT
    Route::get('/', 
        [\App\Http\Controllers\Keuangan\ClientController::class, 'index'])
        ->name('keuangan.clients.index');

    // FORM TAMBAH
    Route::get('/create', 
        [\App\Http\Controllers\Keuangan\ClientController::class, 'create'])
        ->name('keuangan.clients.create');

    // SIMPAN CLIENT BARU
    Route::post('/', 
        [\App\Http\Controllers\Keuangan\ClientController::class, 'store'])
        ->name('keuangan.clients.store');

    // DETAIL CLIENT (OPSIONAL, kalau mas perlu)
    Route::get('/{id}', 
        [\App\Http\Controllers\Keuangan\ClientController::class, 'show'])
        ->whereNumber('id')
        ->name('keuangan.clients.show');

    // FORM EDIT CLIENT
    Route::get('/{id}/edit', 
        [\App\Http\Controllers\Keuangan\ClientController::class, 'edit'])
        ->whereNumber('id')
        ->name('keuangan.clients.edit');

    // UPDATE CLIENT
    Route::put('/{id}', 
        [\App\Http\Controllers\Keuangan\ClientController::class, 'update'])
        ->whereNumber('id')
        ->name('keuangan.clients.update');

    // HAPUS CLIENT
    Route::delete('/{id}', 
        [\App\Http\Controllers\Keuangan\ClientController::class, 'destroy'])
        ->whereNumber('id')
        ->name('keuangan.clients.destroy');
});


/*
|--------------------------------------------------------------------------
| Invoice Jamaah
|--------------------------------------------------------------------------
*/
Route::prefix('invoice-jamaah')->group(function () {

    Route::get('/', 
        [\App\Http\Controllers\Keuangan\InvoiceJamaahController::class, 'index'])
        ->name('keuangan.invoice-jamaah.index');

    Route::get('{id}/print-premium', 
        [\App\Http\Controllers\Keuangan\InvoiceJamaahController::class, 'printInvoicePremium'])
        ->whereNumber('id')
        ->name('keuangan.invoice-jamaah.print-premium');

    Route::get('{invoice_jamaah}', 
        [\App\Http\Controllers\Keuangan\InvoiceJamaahController::class, 'show'])
        ->whereNumber('invoice_jamaah')
        ->name('keuangan.invoice-jamaah.show');
});


/*
|--------------------------------------------------------------------------
| Invoice Layanan
|--------------------------------------------------------------------------
*/
Route::prefix('invoice-layanan')->group(function () {

    Route::get('/', [\App\Http\Controllers\Keuangan\InvoiceLayananController::class, 'index'])
        ->name('keuangan.invoice-layanan.index');

    Route::get('/generate/{transaksi_id}', [\App\Http\Controllers\Keuangan\InvoiceLayananController::class, 'generate'])
        ->whereNumber('transaksi_id')
        ->name('keuangan.invoice-layanan.generate');

    Route::get('{id}/payment', [\App\Http\Controllers\Keuangan\InvoiceLayananController::class, 'createPayment'])
        ->whereNumber('id')
        ->name('keuangan.payment-layanan.create');

    Route::post('{id}/payment', [\App\Http\Controllers\Keuangan\InvoiceLayananController::class, 'storePayment'])
        ->whereNumber('id')
        ->name('keuangan.payment-layanan.store');
    /* ==========================================================
    APPROVAL PEMBAYARAN LAYANAN
    ========================================================== */
    Route::post('{invoice_id}/payment/{payment_id}/approve',
        [\App\Http\Controllers\Keuangan\InvoiceLayananController::class, 'approvePayment'])
        ->whereNumber('invoice_id')
        ->whereNumber('payment_id')
        ->name('keuangan.invoice-layanan.payment.approve');

    Route::post('{invoice_id}/payment/{payment_id}/reject',
        [\App\Http\Controllers\Keuangan\InvoiceLayananController::class, 'rejectPayment'])
        ->whereNumber('invoice_id')
        ->whereNumber('payment_id')
        ->name('keuangan.invoice-layanan.payment.reject');

    Route::get('{id}/print', [\App\Http\Controllers\Keuangan\InvoiceLayananController::class, 'print'])
        ->whereNumber('id')
        ->name('keuangan.invoice-layanan.print');

    Route::get('{invoice_layanan}', [\App\Http\Controllers\Keuangan\InvoiceLayananController::class, 'show'])
        ->whereNumber('invoice_layanan')
        ->name('keuangan.invoice-layanan.show');
});


/*
|--------------------------------------------------------------------------
| Biaya Keberangkatan (Resource)
|--------------------------------------------------------------------------
*/
Route::resource('biaya-keberangkatan', \App\Http\Controllers\Keuangan\BiayaKeberangkatanController::class)
    ->names('keuangan.biaya-keberangkatan');


/*
|--------------------------------------------------------------------------
| Biaya Operasional (Resource + Export)
|--------------------------------------------------------------------------
*/
Route::get('biaya-operasional/export/excel', [\App\Http\Controllers\Keuangan\OperationalExpenseController::class, 'exportExcel'])
    ->name('keuangan.operasional.excel');

Route::get('biaya-operasional/export/pdf', [\App\Http\Controllers\Keuangan\OperationalExpenseController::class, 'exportPdf'])
    ->name('keuangan.operasional.pdf');

Route::resource('biaya-operasional', \App\Http\Controllers\Keuangan\OperationalExpenseController::class)
    ->names('keuangan.operasional');

/*
|--------------------------------------------------------------------------
| Pembayaran — AJAX (HARUS DI ATAS RESOURCE)
|--------------------------------------------------------------------------
*/
Route::get('pembayaran/search-jamaah',
    [\App\Http\Controllers\Keuangan\PaymentController::class, 'searchJamaah']
)->name('keuangan.payments.search-jamaah');

Route::get('pembayaran/ajax-invoice/{jamaah_id}',
    [\App\Http\Controllers\Keuangan\PaymentController::class, 'ajaxInvoice']
)->whereNumber('jamaah_id')
 ->name('keuangan.payments.ajax-invoice');


/*
|--------------------------------------------------------------------------
| Pembayaran — Approval Workflow (BANK GRADE)
|--------------------------------------------------------------------------
*/
Route::post('pembayaran/{id}/approve',
    [\App\Http\Controllers\Keuangan\PaymentController::class, 'approve']
)->whereNumber('id')
 ->name('keuangan.payments.approve');

Route::post('pembayaran/{id}/reject',
    [\App\Http\Controllers\Keuangan\PaymentController::class, 'reject']
)->whereNumber('id')
 ->name('keuangan.payments.reject');


/*
|--------------------------------------------------------------------------
| Pembayaran — Export & Dokumen
|--------------------------------------------------------------------------
*/
Route::get('pembayaran/export/excel',
    [\App\Http\Controllers\Keuangan\PaymentController::class, 'exportExcel']
)->name('keuangan.payments.export.excel');

Route::get('pembayaran/{id}/kwitansi',
    [\App\Http\Controllers\Keuangan\PaymentController::class, 'printKwitansi']
)->whereNumber('id')
 ->name('keuangan.payments.kwitansi');

Route::get('pembayaran/{id}/kwitansi-premium',
    [\App\Http\Controllers\Keuangan\PaymentController::class, 'printKwitansiPremium']
)->whereNumber('id')
 ->name('keuangan.payments.kwitansi.premium');


/*
|--------------------------------------------------------------------------
| Pembayaran — Resource (INPUT & VIEW ONLY)
|--------------------------------------------------------------------------
*/
Route::resource(
    'pembayaran',
    \App\Http\Controllers\Keuangan\PaymentController::class
)->names([
    'index'   => 'keuangan.payments.index',
    'create'  => 'keuangan.payments.create',
    'store'   => 'keuangan.payments.store',
    'show'    => 'keuangan.payments.show',
    'edit'    => 'keuangan.payments.edit',
    'update'  => 'keuangan.payments.update',
    'destroy' => 'keuangan.payments.destroy',
]);

// /*
// |--------------------------------------------------------------------------
// | Pembayaran (Resource)
// |--------------------------------------------------------------------------
// */

// // AJAX SEARCH JAMAAH — HARUS ADA!!
// Route::get('pembayaran/search-jamaah', 
//     [\App\Http\Controllers\Keuangan\PaymentController::class, 'searchJamaah'])
//     ->name('keuangan.payments.search-jamaah');
// Route::get('pembayaran/ajax-invoice/{jamaah_id}',
//     [\App\Http\Controllers\Keuangan\PaymentController::class, 'ajaxInvoice'])
//     ->name('keuangan.payments.ajax-invoice');
// /*
// |--------------------------------------------------------------------------
// | Pembayaran — Validasi (F7)
// |--------------------------------------------------------------------------
// */
// Route::post('pembayaran/{id}/validate', 
//     [\App\Http\Controllers\Keuangan\PaymentController::class, 'validatePayment']
// )->name('keuangan.payments.validate');

// Route::post('pembayaran/{id}/reject', 
//     [\App\Http\Controllers\Keuangan\PaymentController::class, 'rejectPayment']
// )->name('keuangan.payments.reject');



// Route::get('pembayaran/export/excel', [\App\Http\Controllers\Keuangan\PaymentController::class, 'exportExcel'])
//     ->name('keuangan.payments.export.excel');

// Route::get('pembayaran/export/pdf', [\App\Http\Controllers\Keuangan\PaymentController::class, 'exportPdf'])
//     ->name('keuangan.payments.export.pdf');

// Route::get('pembayaran/{id}/kwitansi', [\App\Http\Controllers\Keuangan\PaymentController::class, 'printKwitansi'])
//     ->whereNumber('id')
//     ->name('keuangan.payments.kwitansi');

// Route::get('pembayaran/{id}/kwitansi-premium', [\App\Http\Controllers\Keuangan\PaymentController::class, 'printKwitansiPremium'])
//     ->whereNumber('id')
//     ->name('keuangan.payments.kwitansi.premium');

// Route::resource('pembayaran', \App\Http\Controllers\Keuangan\PaymentController::class)
//     ->names('keuangan.payments');

/*
|--------------------------------------------------------------------------
| Transaksi Layanan (Explicit Route)
|--------------------------------------------------------------------------
*/

Route::prefix('transaksi-layanan')->group(function () {

    Route::get('/', [\App\Http\Controllers\Keuangan\TransaksiLayananController::class,'index'])
        ->name('keuangan.transaksi-layanan.index');

    Route::get('/create', [\App\Http\Controllers\Keuangan\TransaksiLayananController::class,'create'])
        ->name('keuangan.transaksi-layanan.create');

    Route::post('/', [\App\Http\Controllers\Keuangan\TransaksiLayananController::class,'store'])
        ->name('keuangan.transaksi-layanan.store');

    Route::get('/{id}', [\App\Http\Controllers\Keuangan\TransaksiLayananController::class,'show'])
        ->whereNumber('id')
        ->name('keuangan.transaksi-layanan.show');

    Route::get('/{id}/edit', [\App\Http\Controllers\Keuangan\TransaksiLayananController::class,'edit'])
        ->whereNumber('id')
        ->name('keuangan.transaksi-layanan.edit');

    Route::put('/{id}', [\App\Http\Controllers\Keuangan\TransaksiLayananController::class,'update'])
        ->whereNumber('id')
        ->name('keuangan.transaksi-layanan.update');

    Route::delete('/{id}', [\App\Http\Controllers\Keuangan\TransaksiLayananController::class,'destroy'])
        ->whereNumber('id')
        ->name('keuangan.transaksi-layanan.destroy');
});

/*
|--------------------------------------------------------------------------
| Trip Expenses
|--------------------------------------------------------------------------
*/
Route::prefix('trip/{paket_id}/expenses')
    ->whereNumber('paket_id')
    ->group(function () {

        // ================= PRINT PDF =================
        Route::get('print/pdf', 
            [\App\Http\Controllers\Keuangan\TripExpenseController::class, 'printPdf']
        )->name('keuangan.trip.expenses.print.pdf');

        // ================= DETAIL PER KEBERANGKATAN 🔥 =================
        Route::get(
            'keberangkatan/{keberangkatan_id}',
            [\App\Http\Controllers\Keuangan\TripExpenseController::class, 'byKeberangkatan']
        )->whereNumber('keberangkatan_id')
         ->name('keuangan.trip.expenses.by-keberangkatan');

        // ================= CRUD =================
        Route::resource(
            '',
            \App\Http\Controllers\Keuangan\TripExpenseController::class
        )->parameters([
            '' => 'expense'
        ])->names('keuangan.trip.expenses');
});

// /*
// |--------------------------------------------------------------------------
// | Trip Expenses (Resource + Print)
// |--------------------------------------------------------------------------
// */
// Route::prefix('trip/{paket_id}/expenses')->whereNumber('paket_id')->group(function () {

//     Route::get('/print/pdf', [\App\Http\Controllers\Keuangan\TripExpenseController::class, 'printPdf'])
//         ->name('keuangan.trip.expenses.print.pdf');

//     Route::resource('/', \App\Http\Controllers\Keuangan\TripExpenseController::class)
//         ->parameters(['' => 'expense'])
//         ->names('keuangan.trip.expenses');
    
//     // routes/web.php
//     Route::get(
//         'keuangan/trip/{paket}/expenses/keberangkatan/{keberangkatan}',
//         [TripExpenseController::class, 'byKeberangkatan']
//     )->name('keuangan.trip.expenses.by-keberangkatan');

// });


/*
|--------------------------------------------------------------------------
| Vendor Payments
|--------------------------------------------------------------------------
*/
Route::resource('vendor-payments', \App\Http\Controllers\Keuangan\VendorPaymentController::class)
    ->names('keuangan.vendor-payments');


/*
|--------------------------------------------------------------------------
| Laporan Keuangan
|--------------------------------------------------------------------------
*/
Route::prefix('laporan')->group(function () {

    Route::get('/', [\App\Http\Controllers\Keuangan\LaporanController::class, 'index'])
        ->name('keuangan.laporan.index');

    Route::get('/cashflow', [\App\Http\Controllers\Keuangan\LaporanController::class, 'cashflow'])
        ->name('keuangan.laporan.cashflow');

    Route::get('/cashflow/excel', [\App\Http\Controllers\Keuangan\LaporanController::class, 'cashflowExcel'])
        ->name('keuangan.laporan.cashflow.excel');

    Route::get('/cashflow/pdf', [\App\Http\Controllers\Keuangan\LaporanController::class, 'cashflowPdf'])
        ->name('keuangan.laporan.cashflow.pdf');

    Route::get('/pnl', [\App\Http\Controllers\Keuangan\LaporanController::class, 'monthlyPnl'])
        ->name('keuangan.laporan.pnl');

    Route::get('/pnl/excel', [\App\Http\Controllers\Keuangan\LaporanController::class, 'pnlExcel'])
        ->name('keuangan.laporan.pnl.excel');

    Route::get('/pnl/pdf', [\App\Http\Controllers\Keuangan\LaporanController::class, 'pnlPdf'])
        ->name('keuangan.laporan.pnl.pdf');

    Route::get('/trip-profit', [\App\Http\Controllers\Keuangan\LaporanController::class, 'tripProfit'])
        ->name('keuangan.laporan.trip-profit');

    Route::get('/trip-profit/pdf', [\App\Http\Controllers\Keuangan\LaporanController::class, 'tripProfitPdf'])
        ->name('keuangan.laporan.trip-profit.pdf');
});


/*
/*
|--------------------------------------------------------------------------
| Layanan Master + Items (F7 Premium + Backward Compatible)
|--------------------------------------------------------------------------
*/
Route::prefix('layanan')->group(function () {

    // MASTER
    Route::get('/', [\App\Http\Controllers\Keuangan\LayananController::class, 'index'])
        ->name('keuangan.layanan.index');

    Route::get('/create', [\App\Http\Controllers\Keuangan\LayananController::class, 'create'])
        ->name('keuangan.layanan.create');

    Route::post('/', [\App\Http\Controllers\Keuangan\LayananController::class, 'store'])
        ->name('keuangan.layanan.store');

    Route::get('/{id}', [\App\Http\Controllers\Keuangan\LayananController::class, 'show'])
        ->whereNumber('id')
        ->name('keuangan.layanan.show');


    /*
    |--------------------------------------------------------------------------
    | ITEMS (NEW OFFICIAL ROUTES)
    |--------------------------------------------------------------------------
    */
    Route::prefix('{id_master}/items')->whereNumber('id_master')->group(function () {

        Route::get('/create', 
            [\App\Http\Controllers\Keuangan\LayananItemController::class, 'create'])
            ->name('keuangan.layanan.items.create');
    });

    Route::post('/items', 
        [\App\Http\Controllers\Keuangan\LayananItemController::class, 'store'])
        ->name('keuangan.layanan.items.store');

    Route::get('/items/{id}/edit', 
        [\App\Http\Controllers\Keuangan\LayananItemController::class, 'edit'])
        ->whereNumber('id')
        ->name('keuangan.layanan.items.edit');

    Route::put('/items/{id}', 
        [\App\Http\Controllers\Keuangan\LayananItemController::class, 'update'])
        ->whereNumber('id')
        ->name('keuangan.layanan.items.update');

    Route::delete('/items/{id}', 
        [\App\Http\Controllers\Keuangan\LayananItemController::class, 'destroy'])
        ->whereNumber('id')
        ->name('keuangan.layanan.items.destroy');

    Route::patch('/items/{id}/toggle',
    [\App\Http\Controllers\Keuangan\LayananItemController::class, 'toggleStatus'])
    ->whereNumber('id')
    ->name('keuangan.layanan.items.toggle');

    /*
    |--------------------------------------------------------------------------
    | ITEM STATUS TOGGLE (AKTIF / NONAKTIF)
    |--------------------------------------------------------------------------
    */
    Route::post('/items/{id}/status',
        [\App\Http\Controllers\Keuangan\LayananItemController::class, 'toggleStatus'])
        ->whereNumber('id')
        ->name('keuangan.layanan.items.status');

    /*
    |--------------------------------------------------------------------------
    | BACKWARD COMPATIBILITY ROUTES (Supaya Blade lama tidak error)
    |--------------------------------------------------------------------------
    */
    Route::get('/{id_master}/item/create', 
        function($id_master){ return redirect()->route('keuangan.layanan.items.create', $id_master); })
        ->name('keuangan.layanan.item.create');

    Route::post('/item', 
        function(){ return redirect()->route('keuangan.layanan.items.store'); })
        ->name('keuangan.layanan.item.store');

    Route::get('/item/{id}/edit', 
        function($id){ return redirect()->route('keuangan.layanan.items.edit', $id); })
        ->name('keuangan.layanan.item.edit');

    Route::put('/item/{id}', 
        function($id){ return redirect()->route('keuangan.layanan.items.update', $id); })
        ->name('keuangan.layanan.item.update');

    Route::delete('/item/{id}', 
        function($id){ return redirect()->route('keuangan.layanan.items.destroy', $id); })
        ->name('keuangan.layanan.item.delete');

}); 
    /*
    |--------------------------------------------------------------------------
    | Tabungan Umrah (Backoffice – Keuangan)
    |--------------------------------------------------------------------------
    */
   
Route::prefix('tabungan')
    ->as('keuangan.tabungan.')
    ->group(function () {

        /* ================= TOPUP TABUNGAN ================= */

        Route::get('topup',
            [TabunganTopupController::class, 'index'])
            ->name('topup.index');

        Route::post('topup/{id}/approve',
            [TabunganTopupController::class, 'approve'])
            ->whereNumber('id')
            ->name('topup.approve');

        Route::post('topup/{id}/reject',
            [TabunganTopupController::class, 'reject'])
            ->whereNumber('id')
            ->name('topup.reject');

        Route::get('topup/{id}/download',
            [TabunganTopupController::class, 'download'])
            ->whereNumber('id')
            ->name('topup.download');

        Route::post('topup/{id}/resend-wa',
            [TabunganTopupController::class, 'resendWa'])
            ->whereNumber('id')
            ->name('topup.resend-wa');

        Route::get(
                    'bukti-setoran/{id}/download',
                    [\App\Http\Controllers\Backoffice\BuktiSetoranController::class, 'download']
                )
                ->whereNumber('id')
                ->name('bukti-setoran.download');

        
    });

    Route::prefix('tabungan')->group(function () {

    Route::get('rekap',
        [\App\Http\Controllers\Backoffice\TabunganRekapController::class, 'index']
    )->name('keuangan.tabungan.rekap.index');

    Route::get('rekap/pdf',
        [\App\Http\Controllers\Backoffice\TabunganRekapPdfController::class, 'export']
    )->name('keuangan.tabungan.rekap.pdf');

    Route::get('rekap/excel',
        [\App\Http\Controllers\Backoffice\TabunganRekapExcelController::class, 'export']
    )->name('keuangan.tabungan.rekap.excel');


    Route::get(
        'rekap/{tabungan}',
        [\App\Http\Controllers\Backoffice\TabunganRekapDetailController::class, 'index']
    )->name('keuangan.tabungan.rekap.detail');

    Route::get(
            'rekap/{tabungan}/pdf',
            [\App\Http\Controllers\Backoffice\TabunganRekapDetailPdfController::class, 'export']
    )->name('keuangan.tabungan.rekap.detail.pdf');

    

});

Route::prefix('tabungan')
    ->as('keuangan.tabungan.')
    ->group(function () {

        /* ================= MONTHLY CLOSING ================= */

        Route::post(
            'closing',
            [TabunganClosingController::class, 'close']
        )->name('closing');


        // 🔓 BUKA BULAN (FIX)
        Route::post(
            'closing/open',
            [TabunganClosingController::class, 'open']
        )->name('closing.open');
    });

    // ***
    //  PAYOUT
    // ***

Route::prefix('payout')
    ->name('keuangan.payout.')
    ->group(function () {

        Route::get('/', [PayoutController::class, 'index'])->name('index');
        Route::get('/{payout}', [PayoutController::class, 'show'])->name('show');
        Route::post('/{payout}/approve', [PayoutController::class, 'approve'])->name('approve');
        Route::post('/{payout}/pay', [PayoutController::class, 'pay'])->name('pay');
        Route::post('/{payout}/reject', [PayoutController::class, 'reject'])->name('reject');
        Route::get('/{payout}/export-pdf', [PayoutController::class, 'exportPdf'])->name('export-pdf');
    });


    // ***
    //  KOMISI
    // ***
Route::prefix('komisi')
    ->name('keuangan.komisi.')
    ->group(function () {

        // LIST
        Route::get('/', [KomisiController::class, 'index'])->name('index');
        // SHOW (DETAIL)
        Route::get('{id}', [KomisiController::class, 'show'])->name('show');
        // APPROVE
        Route::post('{id}/approve', [KomisiController::class, 'approve'])->name('approve');
        // REJECT
        Route::post('{id}/reject', [KomisiController::class, 'reject'])->name('reject');
    });


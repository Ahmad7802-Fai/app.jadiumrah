<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Marketing\MarketingExpenseController;

/*
|--------------------------------------------------------------------------
| MARKETING MODULE
|--------------------------------------------------------------------------
| Campaign & Marketing Expenses
| Admin & Sales
*/

Route::prefix('marketing')
    ->name('marketing.')
    ->group(function () {

        /* =====================
         | MARKETING EXPENSES
         ===================== */
        Route::get('expenses',
            [MarketingExpenseController::class, 'index']
        )->name('expenses.index');

        Route::get('expenses/create',
            [MarketingExpenseController::class, 'create']
        )->name('expenses.create');

        Route::post('expenses',
            [MarketingExpenseController::class, 'store']
        )->name('expenses.store');

        Route::get('expenses/{expense}/edit',
            [MarketingExpenseController::class, 'edit']
        )->name('expenses.edit');

        Route::put('expenses/{expense}',
            [MarketingExpenseController::class, 'update']
        )->name('expenses.update');

        Route::delete('expenses/{expense}',
            [MarketingExpenseController::class, 'destroy']
        )->name('expenses.destroy');

        Route::get('expenses/{expense}',
            [MarketingExpenseController::class, 'show']
        )->name('expenses.show');

    });

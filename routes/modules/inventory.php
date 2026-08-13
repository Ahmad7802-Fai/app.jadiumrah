<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\DistribusiController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\LogMutasiController;
use App\Http\Controllers\Inventory\StokBarangController;

/*
|--------------------------------------------------------------------------
| INVENTORY ROUTES — F7 PREMIUM FINAL
| prefix: inventory
| middleware: auth, role:inventory
|--------------------------------------------------------------------------
*/

/* ===========================
   DISTRIBUSI BARANG
=========================== */
Route::resource('distribusi', DistribusiController::class)
    ->names('inventory.distribusi');


/* ===========================
   ITEM (BARANG MASTER)
=========================== */
Route::resource('items', ItemController::class)
    ->names('inventory.items');

// Export
Route::get('items/export/excel', [ItemController::class, 'exportExcel'])
    ->name('items.excel');

Route::get('items/export/pdf', [ItemController::class, 'exportPdf'])
    ->name('items.pdf');


/* ===========================
   MUTASI (STOCK MOVEMENT)
=========================== */
Route::resource('mutasi', LogMutasiController::class)
    ->names('inventory.mutasi');


/* ===========================
   STOK BARANG
=========================== */
Route::resource('stok', StokBarangController::class)
    ->names('inventory.stok');

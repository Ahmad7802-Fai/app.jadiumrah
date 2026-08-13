<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\TopupApprovalController;

/*
|--------------------------------------------------------------------------
| WA APPROVAL — TOP UP TABUNGAN
|--------------------------------------------------------------------------
| - Signed URL (anti forward / tamper)
| - Expired otomatis
| - Aman walau link bocor
*/
Route::get('topup/approve/{token}', 
    [TopupApprovalController::class, 'approve']
)
->middleware('signed')      // 🔐 WAJIB
->name('topup.approve');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\RoleController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\BranchController;
use App\Http\Controllers\Superadmin\AgentController;
use App\Http\Controllers\Superadmin\CompanyProfileController;
use App\Http\Controllers\Superadmin\CompanyBankAccountController;

/*
|--------------------------------------------------------------------------
| SUPERADMIN ROUTES — FINAL CLEAN
|--------------------------------------------------------------------------
| Prefix     : /superadmin
| Middleware : web, auth, role:superadmin
|--------------------------------------------------------------------------
| RULE:
| - SUPERADMIN = master data owner
| - Semua CRUD pusat di sini
|--------------------------------------------------------------------------
*/

// =========================
// ROLE MANAGEMENT
// =========================
Route::resource('roles', RoleController::class)
    ->names('superadmin.roles');

// =========================
// USER MANAGEMENT
// =========================
Route::resource('users', UserController::class)
    ->names('superadmin.users');

// =========================
// BRANCH (CABANG)
// =========================
Route::resource('branch', BranchController::class)
    ->names('superadmin.branch');

Route::patch('branch/{branch}/toggle',[BranchController::class, 'toggle']
    )->name('superadmin.branch.toggle');

// =========================
// AGENT (MASTER AGENT)
// =========================
Route::resource('agent', AgentController::class)
    ->names('superadmin.agent');

Route::patch('agent/{agent}/toggle',[AgentController::class, 'toggle']
    )->name('superadmin.agent.toggle');

// =========================
// COMPANY PROFILE
// =========================
Route::get('company-profile',[CompanyProfileController::class, 'index']
    )->name('superadmin.company-profile.index');

Route::post('company-profile',[CompanyProfileController::class, 'store']
    )->name('superadmin.company-profile.store');

// upload logo (logo | invoice | bw)
Route::post('company-profile/logo/{type}',[CompanyProfileController::class, 'uploadLogo']
    )->name('superadmin.company-profile.logo');

// =========================
// COMPANY BANK ACCOUNT
// =========================
Route::get('company-bank',[CompanyBankAccountController::class, 'index']
    )->name('superadmin.company-bank.index');

Route::post('company-bank',[CompanyBankAccountController::class, 'store']
    )->name('superadmin.company-bank.store');

Route::patch('company-bank/{bank}/default',[CompanyBankAccountController::class, 'setDefault']
    )->name('superadmin.company-bank.default');

Route::patch('company-bank/{bank}/deactivate',[CompanyBankAccountController::class, 'deactivate']
    )->name('superadmin.company-bank.deactivate');
Route::patch('company-bank/{bank}/activate',[CompanyBankAccountController::class, 'activate']
    )->name('superadmin.company-bank.activate');

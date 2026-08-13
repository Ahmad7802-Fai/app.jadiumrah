<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        $this->routes(function () {

        
            Route::middleware('web')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            /*
            |--------------------------------------------------------------------------
            | PUBLIC
            |--------------------------------------------------------------------------
            */
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            /*
            |--------------------------------------------------------------------------
            | AUTH
            |--------------------------------------------------------------------------
            */
            Route::middleware('web')
                ->group(base_path('routes/modules/auth.php'));

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD GLOBAL (factory-based)
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web','auth','access.context'])
                ->group(base_path('routes/modules/dashboard.php'));

            /*
            |--------------------------------------------------------------------------
            | SUPERADMIN
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web','auth','access.context','role:SUPERADMIN'])
                ->prefix('superadmin')
                ->group(base_path('routes/modules/superadmin.php'));

            /*
            |--------------------------------------------------------------------------
            | ADMIN PUSAT & CABANG
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web','auth','access.context','role:ADMIN'])
                ->prefix('admin')
                ->group(base_path('routes/modules/admin.php'));

            Route::middleware(['web','auth','access.context','role:ADMIN'])
                ->prefix('cabang')
                ->name('cabang.')
                ->group(base_path('routes/modules/cabang.php'));

            /*
            |--------------------------------------------------------------------------
            | 🔥 AGENT / SALES
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web','auth','access.context','role:SALES'])
                ->prefix('agent')
                ->name('agent.')
                ->group(base_path('routes/modules/agent.php'));

            /*
            |--------------------------------------------------------------------------
            | OPERATOR
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web','auth','access.context','role:OPERATOR'])
                ->prefix('operator')
                ->group(base_path('routes/modules/operator.php'));

            /*
            |--------------------------------------------------------------------------
            | KEUANGAN
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web','auth','access.context','role:KEUANGAN'])
                ->prefix('keuangan')
                ->group(base_path('routes/modules/keuangan.php'));

            /*
            |--------------------------------------------------------------------------
            | INVENTORY
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web','auth','access.context','role:INVENTORY'])
                ->prefix('inventory')
                ->group(base_path('routes/modules/inventory.php'));

            /*
            |--------------------------------------------------------------------------
            | CRM (ADMIN + SALES)
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web','auth','access.context','role:ADMIN,SALES'])
                ->group(base_path('routes/modules/crm.php'));

            /*
            |--------------------------------------------------------------------------
            | 🎫 TICKETING
            |--------------------------------------------------------------------------
            | Multi role (admin, operator, keuangan, sales)
            | Akses dikontrol policy
            */
            Route::middleware(['web','auth','access.context'])
                ->group(base_path('routes/modules/ticketing.php'));
            
            /*
            |--------------------------------------------------------------------------
            | 📣 MARKETING
            |--------------------------------------------------------------------------
            | Campaign, Leads, Expenses
            */
            Route::middleware(['web','auth','access.context'])
                ->group(base_path('routes/modules/marketing.php'));


            /*
            |--------------------------------------------------------------------------
            | JAMAAH (ISOLATED)
            |--------------------------------------------------------------------------
            */
            Route::middleware('web')
                ->group(base_path('routes/modules/jamaah_auth.php'));

            Route::middleware('web')
                ->group(base_path('routes/modules/jamaah.php'));

            /*
            |--------------------------------------------------------------------------
            | 🌍 WEBSITE PUBLIC
            |--------------------------------------------------------------------------
            */
            Route::middleware('web')
                ->group(base_path('routes/modules/website.php'));

        });

    }
}


// namespace App\Providers;

// use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
// use Illuminate\Support\Facades\Route;

// class RouteServiceProvider extends ServiceProvider
// {
//     public const HOME = '/dashboard';

//     public function boot(): void
//     {
//         $this->routes(function () {

//             // ===============================
//             // PUBLIC
//             // ===============================
//             Route::middleware('web')
//                 ->group(base_path('routes/web.php'));

//             // ===============================
//             // AUTH
//             // ===============================
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/auth.php'));

//             // ===============================
//             // DASHBOARD GLOBAL
//             // ===============================
//             Route::middleware(['web','auth','access.context'])
//                 ->group(base_path('routes/modules/dashboard.php'));

//             // ===============================
//             // SUPERADMIN
//             // ===============================
//             Route::middleware(['web','auth','access.context','role:superadmin'])
//                 ->prefix('superadmin')
//                 ->group(base_path('routes/modules/superadmin.php'));

//             // ===============================
//             // ADMIN (PUSAT)
//             // ===============================
//             Route::middleware(['web','auth','access.context','role:admin'])
//                 ->prefix('admin')
//                 ->group(base_path('routes/modules/admin.php'));

//             // ===============================
//             // 🔥 CABANG (ADMIN + branch_id)
//             // ===============================
//             Route::middleware(['web','auth','access.context','role:admin'])
//                 ->group(base_path('routes/modules/cabang.php'));

//             // ===============================
//             // OPERATOR
//             // ===============================
//             Route::middleware(['web','auth','access.context','role:operator'])
//                 ->prefix('operator')
//                 ->group(base_path('routes/modules/operator.php'));

//             // ===============================
//             // KEUANGAN
//             // ===============================
//             Route::middleware(['web','auth','access.context','role:keuangan'])
//                 ->prefix('keuangan')
//                 ->group(base_path('routes/modules/keuangan.php'));

//             // ===============================
//             // INVENTORY
//             // ===============================
//             Route::middleware(['web','auth','access.context','role:inventory'])
//                 ->prefix('inventory')
//                 ->group(base_path('routes/modules/inventory.php'));

//             // ===============================
//             // CRM
//             // ===============================
//             Route::middleware(['web','auth','access.context','role:crm,sales'])
//                 ->group(base_path('routes/modules/crm.php'));

//             // ===============================
//             // JAMAAH (ISOLATED)
//             // ===============================
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/jamaah_auth.php'));

//             Route::middleware('web')
//                 ->group(base_path('routes/modules/jamaah.php'));
//         });
//     }
// }

// namespace App\Providers;

// use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
// use Illuminate\Support\Facades\Route;

// class RouteServiceProvider extends ServiceProvider
// {
//     /**
//      * Default redirect setelah login
//      */
//     public const HOME = '/dashboard';

//     public function boot(): void
//     {
//         $this->routes(function () {

//             /*
//             |--------------------------------------------------------------------------
//             | PUBLIC WEB ROUTES
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/web.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | AUTH (LOGIN / LOGOUT – TANPA ROLE)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/auth.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | DASHBOARD (REDIRECT ONLY – TANPA access.context)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth'])
//                 ->group(base_path('routes/modules/dashboard.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | SUPERADMIN
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:superadmin'])
//                 ->prefix('superadmin')
//                 ->group(base_path('routes/modules/superadmin.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | ADMIN
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:admin'])
//                 ->prefix('admin')
//                 ->group(base_path('routes/modules/admin.php'));

//             // API khusus admin
//             if (file_exists(base_path('routes/api/admin.php'))) {
//                 Route::middleware(['api', 'auth:sanctum', 'access.context', 'role:admin'])
//                     ->prefix('api/admin')
//                     ->group(base_path('routes/api/admin.php'));
//             }


//             /*
//             |--------------------------------------------------------------------------
//             | OPERATOR
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:operator'])
//                 ->prefix('operator')
//                 ->group(base_path('routes/modules/operator.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | KEUANGAN
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:keuangan'])
//                 ->prefix('keuangan')
//                 ->group(base_path('routes/modules/keuangan.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | INVENTORY
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:inventory'])
//                 ->prefix('inventory')
//                 ->group(base_path('routes/modules/inventory.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | CRM (ADMIN + SALES / AGENT)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:admin,sales'])
//                 ->group(base_path('routes/modules/crm.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | OPTIONAL — LAYANAN (jika masih dipakai)
//             |--------------------------------------------------------------------------
//             */
//             if (file_exists(base_path('routes/modules/layanan.php'))) {
//                 Route::middleware(['web', 'auth', 'access.context', 'role:keuangan'])
//                     ->prefix('keuangan')
//                     ->group(base_path('routes/modules/layanan.php'));
//             }


//             /*
//             |--------------------------------------------------------------------------
//             | API GENERAL
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('api')
//                 ->prefix('api')
//                 ->group(base_path('routes/api.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | JAMAAH AUTH & JAMAAH AREA (ISOLATED)
//             | ❗ TIDAK pakai access.context
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/jamaah_auth.php'));

//             Route::middleware('web')
//                 ->group(base_path('routes/modules/jamaah.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | WEBHOOK / SYSTEM (PUBLIC)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/webhook.php'));
//         });
//     }
// }


// namespace App\Providers;

// use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
// use Illuminate\Support\Facades\Route;

// class RouteServiceProvider extends ServiceProvider
// {
//     /**
//      * Default redirect setelah login
//      */
//     public const HOME = '/dashboard';

//     public function boot(): void
//     {
//         $this->routes(function () {

//             /*
//             |--------------------------------------------------------------------------
//             | PUBLIC WEB ROUTES
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/web.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | AUTH (LOGIN / LOGOUT – TANPA ROLE)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/auth.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | DASHBOARD (MULTI ROLE)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context'])
//                 ->group(base_path('routes/modules/dashboard.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | SUPERADMIN
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:superadmin'])
//                 ->prefix('superadmin')
//                 ->group(base_path('routes/modules/superadmin.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | ADMIN
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:admin'])
//                 ->prefix('admin')
//                 ->group(base_path('routes/modules/admin.php'));

//             // API Admin
//             if (file_exists(base_path('routes/api/admin.php'))) {
//                 Route::middleware(['api', 'auth:sanctum', 'access.context', 'role:admin'])
//                     ->prefix('api/admin')
//                     ->group(base_path('routes/api/admin.php'));
//             }


//             /*
//             |--------------------------------------------------------------------------
//             | OPERATOR
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:operator'])
//                 ->prefix('operator')
//                 ->group(base_path('routes/modules/operator.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | KEUANGAN
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:keuangan'])
//                 ->prefix('keuangan')
//                 ->group(base_path('routes/modules/keuangan.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | INVENTORY
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:inventory'])
//                 ->prefix('inventory')
//                 ->group(base_path('routes/modules/inventory.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | CRM (ADMIN + SALES / AGENT)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'access.context', 'role:crm,sales'])
//                 ->group(base_path('routes/modules/crm.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | OPTIONAL — LAYANAN
//             |--------------------------------------------------------------------------
//             */
//             if (file_exists(base_path('routes/modules/layanan.php'))) {
//                 Route::middleware(['web', 'auth', 'access.context', 'role:keuangan'])
//                     ->prefix('keuangan')
//                     ->group(base_path('routes/modules/layanan.php'));
//             }


//             /*
//             |--------------------------------------------------------------------------
//             | API GENERAL
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('api')
//                 ->prefix('api')
//                 ->group(base_path('routes/api.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | JAMAAH AUTH & JAMAAH AREA
//             | (❗ TANPA access.context – ISOLATED)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/jamaah_auth.php'));

//             Route::middleware('web')
//                 ->group(base_path('routes/modules/jamaah.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | WEBHOOK / SYSTEM (PUBLIC)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/webhook.php'));
//         });
//     }
// }

// namespace App\Providers;

// use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
// use Illuminate\Support\Facades\Route;

// class RouteServiceProvider extends ServiceProvider
// {
//     public const HOME = '/dashboard';

//     public function boot()
//     {
//         $this->routes(function () {

//             /*
//             |--------------------------------------------------------------------------
//             | PUBLIC WEB ROUTES
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/web.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | AUTH (tanpa role)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/auth.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | DASHBOARD MULTI ROLE
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth'])
//                 ->group(base_path('routes/modules/dashboard.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | SUPERADMIN (No API)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'role:superadmin'])
//                 ->prefix('superadmin')
//                 ->group(base_path('routes/modules/superadmin.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | ADMIN (Web + API)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'role:admin'])
//                 ->prefix('admin')
//                 ->group(base_path('routes/modules/admin.php'));

//             // API khusus admin
//             if (file_exists(base_path('routes/api/admin.php'))) {
//                 Route::middleware(['api', 'auth:sanctum', 'role:admin'])
//                     ->prefix('api/admin')
//                     ->group(base_path('routes/api/admin.php'));
//             }


//             /*
//             |--------------------------------------------------------------------------
//             | OPERATOR (Web only)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'role:operator'])
//                 ->prefix('operator')
//                 ->group(base_path('routes/modules/operator.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | KEUANGAN (Web only)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'role:keuangan'])
//                 ->prefix('keuangan')
//                 ->group(base_path('routes/modules/keuangan.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | INVENTORY (Web only)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'role:inventory'])
//                 ->prefix('inventory')
//                 ->group(base_path('routes/modules/inventory.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | CRM (Web only)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware(['web', 'auth', 'role:crm,sales'])
//                 ->group(base_path('routes/modules/crm.php'));


//             /*
//             |--------------------------------------------------------------------------
//             | OPTIONAL — LAYANAN (jika masih dipakai)
//             |--------------------------------------------------------------------------
//             */
//             if (file_exists(base_path('routes/modules/layanan.php'))) {
//                 Route::middleware(['web', 'auth', 'role:keuangan'])
//                     ->prefix('keuangan')
//                     ->group(base_path('routes/modules/layanan.php'));
//             }


//             /*
//             |--------------------------------------------------------------------------
//             | API GENERAL (Jika diperlukan)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('api')
//                 ->prefix('api')
//                 ->group(base_path('routes/api.php'));

//             /*
//             |--------------------------------------------------------------------------
//             | JAMAAH
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/jamaah_auth.php'));

//             Route::middleware('web')
//                 ->group(base_path('routes/modules/jamaah.php'));

//             /*
//             |--------------------------------------------------------------------------
//             | WEBHOOK / SYSTEM (PUBLIC)
//             |--------------------------------------------------------------------------
//             */
//             Route::middleware('web')
//                 ->group(base_path('routes/modules/webhook.php'));


//         });
//     }
// }

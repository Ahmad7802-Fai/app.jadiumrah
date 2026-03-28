<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Contracts
|--------------------------------------------------------------------------
*/
use App\Services\Contracts\RoleServiceInterface;
use App\Services\Contracts\UserRoleServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\UserQueryServiceInterface;

/*
|--------------------------------------------------------------------------
| Implementations
|--------------------------------------------------------------------------
*/
use App\Services\Roles\RoleService;
use App\Services\Roles\UserRoleService;
use App\Services\Users\UserService;
use App\Services\Users\UserQueryService;
use App\Services\Agents\AgentService;

/*
|--------------------------------------------------------------------------
| Observer
|--------------------------------------------------------------------------
*/
use App\Models\Booking;
use App\Observers\BookingObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(UserRoleServiceInterface::class, UserRoleService::class);

        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(UserQueryServiceInterface::class, UserQueryService::class);

        $this->app->bind(AgentService::class, AgentService::class);
    }

    public function boot(): void
    {
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

        Booking::observe(BookingObserver::class);

        /*
        |--------------------------------------------------------------------------
        | 🔥 PROTECTION MODE (ONLY WEB, NOT ARTISAN)
        |--------------------------------------------------------------------------
        */
        if (
            app()->environment('local') &&
            env('DB_READONLY') &&
            !app()->runningInConsole() // 🔥 INI KUNCI
        ) {

            DB::listen(function ($query) {
                $sql = strtolower($query->sql);

                if (
                    str_starts_with($sql, 'insert') ||
                    str_starts_with($sql, 'update') ||
                    str_starts_with($sql, 'delete') ||
                    str_starts_with($sql, 'truncate') ||
                    str_starts_with($sql, 'drop') ||
                    str_starts_with($sql, 'alter')
                ) {
                    throw new \Exception("🔥 READ ONLY MODE AKTIF - QUERY DIBLOK!");
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 🚫 BLOCK MIGRATION (TETAP AKTIF)
        |--------------------------------------------------------------------------
        */
        if (app()->environment('local') && env('DB_READONLY')) {
            if (app()->runningInConsole()) {
                $command = implode(' ', $_SERVER['argv'] ?? []);

                if (str_contains($command, 'migrate')) {
                    die("🔥 MIGRATION DIBLOK (LOCAL → PRODUCTION DB)");
                }
            }
        }
    }

}

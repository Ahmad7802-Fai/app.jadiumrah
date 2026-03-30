<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

use App\Events\BookingConfirmed;
use App\Listeners\GenerateInvoiceListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings.
     *
     * @var array
     */
    protected $listen = [

        // 🔥 EMAIL VERIFICATION (WAJIB)
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BookingConfirmed::class => [
        GenerateInvoiceListener::class,
        ],
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
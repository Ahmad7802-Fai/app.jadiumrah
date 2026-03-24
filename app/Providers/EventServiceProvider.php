<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
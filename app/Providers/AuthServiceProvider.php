<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/

use App\Models\Jamaah;
use App\Models\Booking;
use App\Models\JamaahDocument;
use App\Models\PaketDeparture;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\CommissionPayout;
use App\Models\MarketingCampaign;
use App\Models\MarketingBanner;
use App\Models\Voucher;

/*
|--------------------------------------------------------------------------
| POLICIES
|--------------------------------------------------------------------------
*/

use App\Policies\JamaahPolicy;
use App\Policies\BookingPolicy;
use App\Policies\JamaahDocumentPolicy;
use App\Policies\DeparturePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\RefundPolicy;
use App\Policies\CommissionPayoutPolicy;
Use App\Policies\CampaignPolicy;
Use App\Policies\BannerPolicy;
use App\Policies\VoucherPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Policy mappings
     */
    protected $policies = [
        Jamaah::class => JamaahPolicy::class,
        Booking::class => BookingPolicy::class,
        JamaahDocument::class => JamaahDocumentPolicy::class,
        PaketDeparture::class => DeparturePolicy::class,
        Payment::class => PaymentPolicy::class,
        Refund::class => RefundPolicy::class,
        CommissionPayout::class => CommissionPayoutPolicy::class,
        MarketingCampaign::class => CampaignPolicy::class,
        MarketingBanner::class => BannerPolicy::class, 
        Voucher::class => VoucherPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL GLOBAL SUPERADMIN BYPASS (Alternative)
        |--------------------------------------------------------------------------
        | Kalau mau bypass global tanpa BasePolicy juga bisa pakai ini.
        | Tapi karena kita sudah pakai BasePolicy, ini sebenarnya optional.
        */

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('SUPERADMIN')) {
                return true;
            }
        });
    }
}
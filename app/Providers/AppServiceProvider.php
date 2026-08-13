<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

/* ===============================
 | MODELS
=============================== */
use App\Models\JamaahNotification;
use App\Models\TicketInvoice;
use App\Models\TicketRefund;
use App\Models\LeadActivity;
use App\Models\LeadClosing;
use App\Models\TicketPayment;
use App\Models\TicketAllocation;
/* ===============================
 | OBSERVERS
=============================== */
use App\Observers\TicketInvoiceObserver;
use App\Observers\TicketRefundObserver;
use App\Observers\LeadActivityObserver;
use App\Observers\LeadClosingObserver;
use App\Observers\TicketAllocationObserver;
use App\Observers\TicketPaymentObserver;
use App\Models\Payments;
use App\Observers\PaymentObserver;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /* =================================================
         | 🔥 LOAD HELPERS
         ================================================= */
        foreach (glob(app_path('Helpers/*.php')) as $file) {
            require_once $file;
        }

        /* =================================================
         | PAGINATION
         ================================================= */
        Paginator::useBootstrapFive();

        /* =================================================
         | 💰 BLADE DIRECTIVE
         ================================================= */
        Blade::directive('money', function ($expression) {
            return "<?php echo 'Rp ' . number_format($expression, 0, ',', '.'); ?>";
        });

        /* =================================================
         | BLADE COMPONENT NAMESPACE
         ================================================= */
        Blade::componentNamespace(
            'resources.views.ticketing.components',
            'ticketing'
        );

        /* =================================================
         | 🔔 JAMAAH NOTIFICATION
         ================================================= */
        View::composer('jamaah.*', function ($view) {
            if (Auth::guard('jamaah')->check()) {
                $jamaahId = Auth::guard('jamaah')->user()->jamaah_id;

                $unreadCount = JamaahNotification::where('jamaah_id', $jamaahId)
                    ->unread()
                    ->count();

                $view->with('jamaahUnreadNotifCount', $unreadCount);
            }
        });

        /* =================================================
         | 🔒 OBSERVERS (WAJIB)
         ================================================= */

        // Ticketing
        TicketInvoice::observe(TicketInvoiceObserver::class);
        TicketRefund::observe(TicketRefundObserver::class);
        TicketPayment::observe(TicketPaymentObserver::class);

        // CRM Lead
        LeadActivity::observe(LeadActivityObserver::class);
        LeadClosing::observe(LeadClosingObserver::class);
        TicketAllocation::observe(TicketAllocationObserver::class);
        Payments::observe(PaymentObserver::class);

    }
}

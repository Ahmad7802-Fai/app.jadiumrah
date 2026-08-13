<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/* =====================
 | EXISTING MODELS
 ===================== */
use App\Models\Payments;
use App\Policies\PaymentPolicy;
use App\Models\Jamaah;
use App\Policies\JamaahPolicy;
use App\Models\Branch;
use App\Policies\BranchPolicy;
use App\Models\Agent;
use App\Policies\AgentPolicy;
use App\Models\Invoices;
use App\Policies\InvoicePolicy;
use App\Models\Lead;
use App\Policies\LeadPolicy;
use App\Models\LeadClosing;
use App\Policies\LeadClosingPolicy;
use App\Models\Pipeline;
use App\Policies\PipelinePolicy;
use App\Models\LeadActivity;
use App\Policies\LeadActivityPolicy;

/* =====================
 | NEW TICKETING MODELS
 ===================== */
use App\Models\TicketPnr;
use App\Models\TicketInvoice;
use App\Policies\TicketPnrPolicy;
use App\Policies\TicketInvoicePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [

        /* ===== EXISTING ===== */
        Payments::class     => PaymentPolicy::class,
        Jamaah::class       => JamaahPolicy::class,
        Branch::class       => BranchPolicy::class,
        Agent::class        => AgentPolicy::class,
        Invoices::class     => InvoicePolicy::class,
        Lead::class         => LeadPolicy::class,
        LeadClosing::class  => LeadClosingPolicy::class,
        Pipeline::class     => PipelinePolicy::class,
        LeadActivity::class => LeadActivityPolicy::class,

        /* ===== TICKETING ===== */
        TicketPnr::class     => TicketPnrPolicy::class,
        TicketInvoice::class => TicketInvoicePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * 🔑 GLOBAL OVERRIDE
         * SUPERADMIN & OPERATOR PUSAT
         */
        Gate::before(function ($user, $ability) {

            if (in_array($user->role, [
                'SUPERADMIN',
                'OPERATOR',
                'KEUANGAN'
            ])) {
                return true;
            }

            return null; // lanjut ke policy model
        });
    }
}


// namespace App\Providers;
// use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// use App\Models\Payments;
// use App\Policies\PaymentPolicy;
// use App\Models\Jamaah;
// use App\Policies\JamaahPolicy;
// use App\Models\Branch;
// use App\Policies\BranchPolicy;
// use App\Models\Agent;
// use App\Policies\AgentPolicy;
// use App\Models\Invoices;
// use App\Policies\InvoicePolicy;
// use App\Models\Lead;
// use App\Policies\LeadPolicy;
// use App\Models\LeadClosing;
// use App\Policies\LeadClosingPolicy;
// use App\Models\Pipeline;
// use App\Policies\PipelinePolicy;
// use App\Models\LeadActivity;
// use App\Policies\LeadActivityPolicy;
// use Illuminate\Support\Facades\Gate;
// class AuthServiceProvider extends ServiceProvider
// {
//     protected $policies = [
//         Payments::class => PaymentPolicy::class,
//         Jamaah::class   => JamaahPolicy::class,
//         Branch::class   => BranchPolicy::class,
//         Agent::class    => AgentPolicy::class,
//         Invoices::class => InvoicePolicy::class,
//         Lead::class => LeadPolicy::class,
//         LeadClosing::class => LeadClosingPolicy::class,
//         Pipeline::class => PipelinePolicy::class,
//         LeadActivity::class => LeadActivityPolicy::class,   
//     ];

//     public function boot(): void
//     {
//         $this->registerPolicies();

//         Gate::before(function ($user, $ability) {

//             // PUSAT SELALU BOLEH
//             if (in_array($user->role, ['SUPERADMIN', 'OPERATOR'])) {
//                 return true;
//             }

//             return null; // lanjut ke policy lain
//         });
//     }

// }

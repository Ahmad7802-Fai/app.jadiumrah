<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD
            |--------------------------------------------------------------------------
            */
            'dashboard.view',

            /*
            |--------------------------------------------------------------------------
            | USERS
            |--------------------------------------------------------------------------
            */
            'user.view','user.create','user.update','user.delete',

            /*
            |--------------------------------------------------------------------------
            | ROLES
            |--------------------------------------------------------------------------
            */
            'role.view','role.create','role.update','role.delete',

            /*
            |--------------------------------------------------------------------------
            | BRANCHES
            |--------------------------------------------------------------------------
            */
            'branch.view','branch.create','branch.update','branch.delete',

            /*
            |--------------------------------------------------------------------------
            | AGENTS
            |--------------------------------------------------------------------------
            */
            'agent.view','agent.create','agent.update','agent.delete',

            /*
            |--------------------------------------------------------------------------
            | JAMAAH
            |--------------------------------------------------------------------------
            */
            'jamaah.view','jamaah.create','jamaah.update','jamaah.delete',
            'jamaah.document.view',
            'jamaah.approval.view',
            'jamaah.booking.view',
            'jamaah.approve',
            'jamaah.account.create',
            'jamaah.account.reset',
            'jamaah.document.view',
            'jamaah.document.upload',   // 🔥 TAMBAHKAN
            'jamaah.document.delete',   // 🔥 TAMBAHKAN

            /*
            |--------------------------------------------------------------------------
            | PAKET
            |--------------------------------------------------------------------------
            */
            'paket.view','paket.create','paket.update','paket.delete',

            /*
            |--------------------------------------------------------------------------
            | MANIFEST
            |--------------------------------------------------------------------------
            */
            'manifest.view','manifest.export',
            /*
            |--------------------------------------------------------------------------
            | ROOMING
            |--------------------------------------------------------------------------
            */
            'rooming.view',
            'rooming.generate',
            'rooming.assign',
            'rooming.export',

            /*
            |--------------------------------------------------------------------------
            | DEPARTURE
            |--------------------------------------------------------------------------
            */
            'departure.view',
            'departure.create',
            'departure.update',
            'departure.delete',

            /*
            |--------------------------------------------------------------------------
            | BOOKING
            |--------------------------------------------------------------------------
            */
            'booking.view','booking.create','booking.update','booking.approve','booking.cancel',

            /*
            |--------------------------------------------------------------------------
            | COMMISSION
            |--------------------------------------------------------------------------
            */
            'scheme.view','scheme.create','scheme.update','scheme.delete',

            /*
            |--------------------------------------------------------------------------
            | CONFIG
            |--------------------------------------------------------------------------
            */
            'config.view','config.update',

            /*
            |--------------------------------------------------------------------------
            | FINANCE DASHBOARD
            |--------------------------------------------------------------------------
            */
            'finance.dashboard.view',


            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */
            'payment.view',
            'payment.create',
            'payment.update',
            'payment.delete',
            'payment.approve',
            'payment.receipt.view',

                        /*
            |--------------------------------------------------------------------------
            | REFUND
            |--------------------------------------------------------------------------
            */
            'refund.view',
            'refund.create',
            'refund.update',
            'refund.delete',
            'refund.approve',

            /*
            |--------------------------------------------------------------------------
            | RECEIVABLE
            |--------------------------------------------------------------------------
            */
            'receivable.view',

            /*
            |--------------------------------------------------------------------------
            | COMMISSION
            |--------------------------------------------------------------------------
            */

            'commission.view',
            'commission.update',

            /*
            |--------------------------------------------------------------------------
            | COMMISSION PAYOUT
            |--------------------------------------------------------------------------
            */

            'commission.payout.view',
            'commission.payout.request',
            'commission.payout.approve',
            'commission.payout.pay',

            /*
            |--------------------------------------------------------------------------
            | REPORT
            |--------------------------------------------------------------------------
            */
            'finance.report.view',
            'finance.report.export',
            /*
            |--------------------------------------------------------------------------
            | COST
            |--------------------------------------------------------------------------
            */
            'cost.view',
            'cost.create',
            'cost.update',
            'cost.delete',
            'cost.approve',
            /*
            |--------------------------------------------------------------------------
            | MARKETING
            |--------------------------------------------------------------------------
            */
            'campaign.view',
            'campaign.create',
            'campaign.update',
            'campaign.delete',

            'addon.view',
            'addon.create',
            'addon.update',
            'addon.delete',

            /*
            |--------------------------------------------------------------------------
            | MARKETING BANNER
            |--------------------------------------------------------------------------
            */
            'banner.view',
            'banner.create',
            'banner.update',
            'banner.delete',

            'voucher.view',
            'voucher.create',
            'voucher.update',
            'voucher.delete',
            
            // Flash Sale
            'flashsale.view',
            'flashsale.create',
            'flashsale.update',
            'flashsale.delete',

            /*
            |--------------------------------------------------------------------------
            | TICKETING - FLIGHT
            |--------------------------------------------------------------------------
            */
            'flight.view',
            'flight.create',
            'flight.update',
            'flight.delete',

            /*
            |--------------------------------------------------------------------------
            | TICKETING - SEAT ALLOCATION
            |--------------------------------------------------------------------------
            */
            'seat.view',
            'seat.update',

            /*
            |--------------------------------------------------------------------------
            | TICKETING - MANIFEST
            |--------------------------------------------------------------------------
            */
            'manifest.generate',

            /*
            |--------------------------------------------------------------------------
            | VISA PRODUCTS
            |--------------------------------------------------------------------------
            */
            'visa.product.view',
            'visa.product.create',
            'visa.product.update',
            'visa.product.delete',

            /*
            |--------------------------------------------------------------------------
            | VISA ORDERS
            |--------------------------------------------------------------------------
            */
            'visa.order.view',
            'visa.order.create',
            'visa.order.update',
            'visa.order.delete',
            'visa.order.status.update',
            'visa.order.note.create',
            'visa.order.traveler.create',
            'visa.order.traveler.update',
            'visa.order.traveler.delete',

            /*
            |--------------------------------------------------------------------------
            | VISA PAYMENTS
            |--------------------------------------------------------------------------
            */
            'visa.payment.view',
            'visa.payment.create',
            'visa.payment.update',
            'visa.payment.delete',
            'visa.payment.approve',
            'visa.payment.refund',

            /*
            |--------------------------------------------------------------------------
            | VISA DOCUMENTS
            |--------------------------------------------------------------------------
            */
            'visa.document.view',
            'visa.document.create',
            'visa.document.update',
            'visa.document.delete',
            'visa.document.verify',
            'visa.document.download',

            
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
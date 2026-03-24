<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN
        |--------------------------------------------------------------------------
        */
        $superadmin = Role::findByName('SUPERADMIN');
        $superadmin->syncPermissions(Permission::all());

        /*
        |--------------------------------------------------------------------------
        | ADMIN PUSAT
        |--------------------------------------------------------------------------
        */
        $adminPusat = Role::findByName('ADMIN_PUSAT');
        $adminPusat->syncPermissions([
            'dashboard.view',

            'jamaah.view',
            'booking.view','booking.approve',
            'paket.view',
            'scheme.view',

            'payment.view',
            'refund.view',

            'visa.product.view',
            'visa.product.create',
            'visa.product.update',
            'visa.product.delete',

            'visa.order.view',
            'visa.order.create',
            'visa.order.update',
            'visa.order.delete',
            'visa.order.status.update',
            'visa.order.note.create',
            'visa.order.traveler.create',
            'visa.order.traveler.update',
            'visa.order.traveler.delete',

            'visa.payment.view',
            'visa.payment.create',
            'visa.payment.update',
            'visa.payment.delete',
            'visa.payment.approve',
            'visa.payment.refund',

            'visa.document.view',
            'visa.document.create',
            'visa.document.update',
            'visa.document.delete',
            'visa.document.verify',
            'visa.document.download',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADMIN CABANG
        |--------------------------------------------------------------------------
        */
        $adminCabang = Role::findByName('ADMIN_CABANG');
        $adminCabang->syncPermissions([
            'dashboard.view',

            'jamaah.view','jamaah.create','jamaah.update',

            'jamaah.document.view',
            'jamaah.document.upload',
            'jamaah.document.delete',

            'booking.view','booking.create','booking.update',

            'agent.view','agent.create','agent.update',

            'paket.view',

            'payment.view',
            'payment.create',

            'visa.product.view',

            'visa.order.view',
            'visa.order.create',
            'visa.order.update',
            'visa.order.status.update',
            'visa.order.note.create',
            'visa.order.traveler.create',
            'visa.order.traveler.update',
            'visa.order.traveler.delete',

            'visa.payment.view',
            'visa.payment.create',
            'visa.payment.update',

            'visa.document.view',
            'visa.document.create',
            'visa.document.update',
            'visa.document.download',
        ]);

        /*
        |--------------------------------------------------------------------------
        | OPERATOR CABANG
        |--------------------------------------------------------------------------
        */
        $operatorCabang = Role::findByName('OPERATOR_CABANG');
        $operatorCabang->syncPermissions([
            'jamaah.view',
            'booking.view',

            'visa.order.view',
            'visa.document.view',
            'visa.document.create',
            'visa.document.download',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FINANCE
        |--------------------------------------------------------------------------
        */
        $finance = Role::findByName('FINANCE');
        $finance->syncPermissions([
            'dashboard.view',

            'payment.view',
            'payment.approve',
            'payment.delete',

            'refund.view',
            'refund.create',
            'refund.approve',
            'refund.delete',

            'commission.view',

            'commission.payout.view',
            'commission.payout.approve',
            'commission.payout.pay',
            
            'cost.view',
            'cost.create',
            'cost.update',
            'cost.delete',
            'cost.approve',

            'visa.order.view',
            'visa.payment.view',
            'visa.payment.create',
            'visa.payment.update',
            'visa.payment.delete',
            'visa.payment.approve',
            'visa.payment.refund',
            'visa.document.view',
            'visa.document.download',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FINANCE CABANG
        |--------------------------------------------------------------------------
        */
        $keuanganCabang = Role::findByName('KEUANGAN_CABANG');
        $keuanganCabang->syncPermissions([
            'dashboard.view',
            'commission.view',
            'commission.payout.view',

            'visa.order.view',
            'visa.payment.view',
            'visa.document.view',
            'visa.document.download',
        ]);

        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        */
        $agent = Role::findByName('AGENT');
        $agent->syncPermissions([
            'dashboard.view',

            'jamaah.create',
            'jamaah.update',
            'jamaah.view',

            'jamaah.document.view',
            'jamaah.document.upload',
            'jamaah.document.delete',

            'booking.view',
            'booking.create',

            'commission.payout.request',
        ]);

        /*
        |--------------------------------------------------------------------------
        | JAMAAH
        |--------------------------------------------------------------------------
        */
        $jamaah = Role::findByName('JAMAAH');
        $jamaah->syncPermissions([
            'dashboard.view',

            'booking.view',
            'booking.create',
            'payment.update',
            'payment.view',
            'payment.create',

            'jamaah.document.view',
            'jamaah.document.upload',
        ]);
    }
}
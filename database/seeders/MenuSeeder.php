<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | RESET ALL MENU & RELATIONS
        |--------------------------------------------------------------------------
        */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_menu')->truncate();
        DB::table('company_menu')->truncate();
        DB::table('menus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | PARENT MENUS
        |--------------------------------------------------------------------------
        */
        $commissionParent = $this->createMenu([
            'label'      => 'Commission',
            'route'      => null,
            'icon'       => 'currency-dollar',
            'permission' => null,
            'section'    => 'MANAGEMENT',
            'order'      => 1,
        ]);

        $jamaahParent = $this->createMenu([
            'label'      => 'Jamaah',
            'route'      => null,
            'icon'       => 'users',
            'permission' => null,
            'section'    => 'OPERATIONS',
            'order'      => 2,
        ]);

        $financeParent = $this->createMenu([
            'label'      => 'Finance',
            'route'      => null,
            'icon'       => 'banknotes',
            'permission' => null,
            'section'    => 'FINANCE',
            'order'      => 3,
        ]);

        $visaParent = $this->createMenu([
            'label'      => 'Visa',
            'route'      => null,
            'icon'       => 'identification',
            'permission' => null,
            'section'    => 'VISA',
            'order'      => 4,
        ]);

        /*
        |--------------------------------------------------------------------------
        | CHILD & SINGLE MENUS
        |--------------------------------------------------------------------------
        */
        $menus = [

            /*
            |--------------------------------------------------------------------------
            | MANAGEMENT
            |--------------------------------------------------------------------------
            */
            [
                'label'      => 'Users',
                'route'      => 'users.index',
                'icon'       => 'user',
                'permission' => 'user.view',
                'section'    => 'MANAGEMENT',
                'order'      => 1,
            ],
            [
                'label'      => 'Roles',
                'route'      => 'roles.index',
                'icon'       => 'shield-check',
                'permission' => 'role.view',
                'section'    => 'MANAGEMENT',
                'order'      => 2,
            ],
            [
                'label'      => 'Branches',
                'route'      => 'branches.index',
                'icon'       => 'building-office',
                'permission' => 'branch.view',
                'section'    => 'MANAGEMENT',
                'order'      => 3,
            ],
            [
                'label'      => 'Agents',
                'route'      => 'agents.index',
                'icon'       => 'user-group',
                'permission' => 'agent.view',
                'section'    => 'MANAGEMENT',
                'order'      => 4,
            ],

            /*
            |--------------------------------------------------------------------------
            | COMMISSION
            |--------------------------------------------------------------------------
            */
            [
                'label'      => 'Schemes',
                'route'      => 'commission.schemes.index',
                'icon'       => 'chart-bar',
                'permission' => 'scheme.view',
                'section'    => 'MANAGEMENT',
                'parent_id'  => $commissionParent->id,
                'order'      => 1,
            ],
            [
                'label'      => 'Config',
                'route'      => 'commission.config.index',
                'icon'       => 'cog-6-tooth',
                'permission' => 'config.view',
                'section'    => 'MANAGEMENT',
                'parent_id'  => $commissionParent->id,
                'order'      => 2,
            ],

            /*
            |--------------------------------------------------------------------------
            | OPERATIONS
            |--------------------------------------------------------------------------
            */
            [
                'label'      => 'Master Paket',
                'route'      => 'pakets.index',
                'icon'       => 'briefcase',
                'permission' => 'paket.view',
                'section'    => 'OPERATIONS',
                'order'      => 1,
            ],
            [
                'label'      => 'Keberangkatan',
                'route'      => 'departures.index',
                'icon'       => 'paper-airplane',
                'permission' => 'departure.view',
                'section'    => 'OPERATIONS',
                'order'      => 2,
            ],
            [
                'label'      => 'Bookings',
                'route'      => 'bookings.index',
                'icon'       => 'clipboard-document-list',
                'permission' => 'booking.view',
                'section'    => 'OPERATIONS',
                'order'      => 3,
            ],
            [
                'label'      => 'Manifest',
                'route'      => 'manifests.index',
                'icon'       => 'document-text',
                'permission' => 'manifest.view',
                'section'    => 'OPERATIONS',
                'order'      => 4,
            ],
            [
                'label'      => 'Rooming List',
                'route'      => 'rooming.index',
                'icon'       => 'home-modern',
                'permission' => 'rooming.view',
                'section'    => 'OPERATIONS',
                'order'      => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | JAMAAH
            |--------------------------------------------------------------------------
            */
            [
                'label'      => 'Data Jamaah',
                'route'      => 'jamaah.index',
                'icon'       => 'identification',
                'permission' => 'jamaah.view',
                'section'    => 'OPERATIONS',
                'parent_id'  => $jamaahParent->id,
                'order'      => 1,
            ],
            [
                'label'      => 'Dokumen',
                'route'      => 'jamaah.documents.index',
                'icon'       => 'document-text',
                'permission' => 'jamaah.document.view',
                'section'    => 'OPERATIONS',
                'parent_id'  => $jamaahParent->id,
                'order'      => 2,
            ],
            [
                'label'      => 'Riwayat Booking',
                'route'      => 'jamaah.bookings.history',
                'icon'       => 'clock',
                'permission' => 'jamaah.booking.view',
                'section'    => 'OPERATIONS',
                'parent_id'  => $jamaahParent->id,
                'order'      => 3,
            ],
            [
                'label'      => 'Approval',
                'route'      => 'jamaah.approvals.index',
                'icon'       => 'check-badge',
                'permission' => 'jamaah.approval.view',
                'section'    => 'OPERATIONS',
                'parent_id'  => $jamaahParent->id,
                'order'      => 4,
            ],
            [
                'label'      => 'Akun Jamaah',
                'route'      => 'jamaah.account.index',
                'icon'       => 'user-circle',
                'permission' => 'jamaah.account.view',
                'section'    => 'OPERATIONS',
                'parent_id'  => $jamaahParent->id,
                'order'      => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | FINANCE
            |--------------------------------------------------------------------------
            */
            [
                'label'      => 'Dashboard Finance',
                'route'      => 'finance.dashboard',
                'icon'       => 'chart-pie',
                'permission' => 'finance.dashboard.view',
                'section'    => 'FINANCE',
                'parent_id'  => $financeParent->id,
                'order'      => 1,
            ],
            [
                'label'      => 'Pembayaran',
                'route'      => 'finance.payments.index',
                'icon'       => 'credit-card',
                'permission' => 'payment.view',
                'section'    => 'FINANCE',
                'parent_id'  => $financeParent->id,
                'order'      => 2,
            ],
            [
                'label'      => 'Refund',
                'route'      => 'finance.refunds.index',
                'icon'       => 'arrow-uturn-left',
                'permission' => 'refund.view',
                'section'    => 'FINANCE',
                'parent_id'  => $financeParent->id,
                'order'      => 3,
            ],
            [
                'label'      => 'Piutang',
                'route'      => 'finance.receivables.index',
                'icon'       => 'clock',
                'permission' => 'receivable.view',
                'section'    => 'FINANCE',
                'parent_id'  => $financeParent->id,
                'order'      => 4,
            ],
            [
                'label'      => 'Komisi',
                'route'      => 'commission.payouts.index',
                'icon'       => 'currency-dollar',
                'permission' => 'commission.payout.view',
                'section'    => 'FINANCE',
                'parent_id'  => $financeParent->id,
                'order'      => 5,
            ],
            [
                'label'      => 'Cost Management',
                'route'      => 'finance.costs.index',
                'icon'       => 'banknotes',
                'permission' => 'cost.view',
                'section'    => 'FINANCE',
                'parent_id'  => $financeParent->id,
                'order'      => 6,
            ],
            [
                'label'      => 'Laporan',
                'route'      => 'finance.reports.index',
                'icon'       => 'document-chart-bar',
                'permission' => 'finance.report.view',
                'section'    => 'FINANCE',
                'parent_id'  => $financeParent->id,
                'order'      => 7,
            ],

            /*
            |--------------------------------------------------------------------------
            | MARKETING
            |--------------------------------------------------------------------------
            */
            [
                'label'      => 'Promo Banner',
                'route'      => 'marketing.banners.index',
                'icon'       => 'photo',
                'permission' => 'banner.view',
                'section'    => 'MARKETING',
                'order'      => 1,
            ],
            [
                'label'      => 'Campaign',
                'route'      => 'marketing.campaigns.index',
                'icon'       => 'rocket-launch',
                'permission' => 'campaign.view',
                'section'    => 'MARKETING',
                'order'      => 2,
            ],
            [
                'label'      => 'Produk Add-On',
                'route'      => 'marketing.addons.index',
                'icon'       => 'plus-circle',
                'permission' => 'addon.view',
                'section'    => 'MARKETING',
                'order'      => 3,
            ],
            [
                'label'      => 'Komisi Agent',
                'route'      => 'marketing.agent-commissions.index',
                'icon'       => 'chart-bar',
                'permission' => 'agent.performance.view',
                'section'    => 'MARKETING',
                'order'      => 4,
            ],
            [
                'label'      => 'Voucher',
                'route'      => 'marketing.vouchers.index',
                'icon'       => 'ticket',
                'permission' => 'voucher.view',
                'section'    => 'MARKETING',
                'order'      => 5,
            ],
            [
                'label'      => 'Flash Sale',
                'route'      => 'marketing.flash-sales.index',
                'icon'       => 'bolt',
                'permission' => 'flashsale.view',
                'section'    => 'MARKETING',
                'order'      => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | TICKETING
            |--------------------------------------------------------------------------
            */
            [
                'label'      => 'Dashboard',
                'route'      => 'ticketing.dashboard',
                'icon'       => 'chart-bar',
                'permission' => 'flight.view',
                'section'    => 'TICKETING',
                'order'      => 1,
            ],
            [
                'label'      => 'Data Flight',
                'route'      => 'ticketing.flights.index',
                'icon'       => 'paper-airplane',
                'permission' => 'flight.view',
                'section'    => 'TICKETING',
                'order'      => 2,
            ],
            [
                'label'      => 'Departures',
                'route'      => 'ticketing.departures.index',
                'icon'       => 'calendar-days',
                'permission' => 'departure.view',
                'section'    => 'TICKETING',
                'order'      => 3,
            ],
            [
                'label'      => 'Seat Allocation',
                'route'      => 'ticketing.seat.index',
                'icon'       => 'chair',
                'permission' => 'seat.view',
                'section'    => 'TICKETING',
                'order'      => 4,
            ],
            [
                'label'      => 'Manifest Flight',
                'route'      => 'ticketing.manifests.index',
                'icon'       => 'document-text',
                'permission' => 'manifest.view',
                'section'    => 'TICKETING',
                'order'      => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | VISA
            |--------------------------------------------------------------------------
            */
            [
                'label'      => 'Visa Orders',
                'route'      => 'visa.orders.index',
                'icon'       => 'clipboard-document-check',
                'permission' => 'visa.order.view',
                'section'    => 'VISA',
                'parent_id'  => $visaParent->id,
                'order'      => 1,
            ],
            [
                'label'      => 'Visa Products',
                'route'      => 'visa.products.index',
                'icon'       => 'identification',
                'permission' => 'visa.product.view',
                'section'    => 'VISA',
                'parent_id'  => $visaParent->id,
                'order'      => 2,
            ],
            [
                'label'      => 'Visa Payments',
                'route'      => 'visa.payments.index',
                'icon'       => 'credit-card',
                'permission' => 'visa.payment.view',
                'section'    => 'VISA',
                'parent_id'  => $visaParent->id,
                'order'      => 3,
            ],
            [
                'label'      => 'Visa Documents',
                'route'      => 'visa.documents.index',
                'icon'       => 'document-check',
                'permission' => 'visa.document.view',
                'section'    => 'VISA',
                'parent_id'  => $visaParent->id,
                'order'      => 4,
            ],
        ];

        foreach ($menus as $data) {
            $menu = $this->createMenu($data);
            $this->attachToRoles($menu);
            $this->attachToCompanies($menu);
        }

        $this->attachToRoles($commissionParent);
        $this->attachToCompanies($commissionParent);

        $this->attachToRoles($jamaahParent);
        $this->attachToCompanies($jamaahParent);

        $this->attachToRoles($financeParent);
        $this->attachToCompanies($financeParent);

        $this->attachToRoles($visaParent);
        $this->attachToCompanies($visaParent);
    }

    private function createMenu(array $data): Menu
    {
        return Menu::create(array_merge($data, [
            'is_active' => true,
        ]));
    }

    private function attachToRoles(Menu $menu): void
    {
        foreach (Role::all() as $role) {
            DB::table('role_menu')->insert([
                'role_id' => $role->id,
                'menu_id' => $menu->id,
            ]);
        }
    }

    private function attachToCompanies(Menu $menu): void
    {
        foreach (Company::all() as $company) {
            DB::table('company_menu')->insert([
                'company_id' => $company->id,
                'menu_id' => $menu->id,
            ]);
        }
    }
}
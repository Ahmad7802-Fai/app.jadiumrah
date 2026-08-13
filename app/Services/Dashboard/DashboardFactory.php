<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Services\Dashboard\Contracts\DashboardContract;

class DashboardFactory
{
    public static function make(User $user): DashboardContract
    {
        $role = strtoupper($user->role);

        return match (true) {

            // SUPERADMIN
            $role === 'SUPERADMIN' =>
                app(SuperadminDashboard::class),

            // OPERATOR (PUSAT)
            $role === 'OPERATOR' =>
                app(OperatorDashboard::class),

            // KEUANGAN (PUSAT)
            $role === 'KEUANGAN' =>
                app(KeuanganDashboard::class),

            // ADMIN CABANG
            $role === 'ADMIN' && ! empty($user->branch_id) =>
                app(CabangDashboard::class),

            // ADMIN PUSAT / CMS
            $role === 'ADMIN' =>
                app(AdminDashboard::class),

            // INVENTORY
            $role === 'INVENTORY' =>
                app(InventoryDashboard::class),

            // SALES → AGENT
            $role === 'SALES' && $user->isAgent() =>
                app(AgentDashboard::class),

            // SALES → CRM / PUSAT
            $role === 'SALES' =>
                app(CrmDashboard::class),

            // ❌ TIDAK ADA FALLBACK SILENT
            default =>
                throw new \LogicException(
                    "Dashboard belum didefinisikan untuk role: {$role}"
                ),
        };
    }
}

// namespace App\Services\Dashboard;

// use App\Models\User;
// use App\Services\Dashboard\Contracts\DashboardContract;

// class DashboardFactory
// {
//     public static function make(User $user): DashboardContract
//     {
//         $role = strtoupper($user->role);

//         return match (true) {

//             // SUPERADMIN
//             $role === 'SUPERADMIN' =>
//                 app(SuperadminDashboard::class),

//             // KEUANGAN (PUSAT)
//             $role === 'KEUANGAN' =>
//                 app(KeuanganDashboard::class),

//             // ADMIN CABANG
//             $role === 'ADMIN' && !empty($user->branch_id) =>
//                 app(CabangDashboard::class),

//             // ADMIN PUSAT / CMS
//             $role === 'ADMIN' =>
//                 app(AdminDashboard::class),

//             // INVENTORY / CMS
//             $role === 'INVENTORY' =>
//                 app(InventoryDashboard::class),

//             // SALES → AGENT
//             $role === 'SALES'
//                 && method_exists($user, 'isAgent')
//                 && $user->isAgent() =>
//                 app(AgentDashboard::class),

//             // SALES → CRM
//             $role === 'SALES' =>
//                 app(CrmDashboard::class),

//             // FALLBACK (AMAN)
//             default =>
//                 app(DefaultDashboard::class),
//         };
//     }
// }

// namespace App\Services\Dashboard;

// use App\Models\User;
// use App\Services\Dashboard\Contracts\DashboardContract;

// class DashboardFactory
// {
//     public static function make(User $user): DashboardContract
//     {
//         $role = strtoupper($user->role);

//         return match (true) {

//             // SUPERADMIN
//             $role === 'SUPERADMIN' =>
//                 app(SuperadminDashboard::class),

//             // KEUANGAN (PUSAT)
//             $role === 'KEUANGAN' =>
//                 app(KeuanganDashboard::class),

//             // ADMIN CABANG
//             $role === 'ADMIN' && !empty($user->branch_id) =>
//                 app(CabangDashboard::class),

//             // SALES → AGENT
//             $role === 'SALES'
//                 && method_exists($user, 'isAgent')
//                 && $user->isAgent() =>
//                 app(AgentDashboard::class),

//             // SALES → PUSAT
//             $role === 'SALES' =>
//                 app(CrmDashboard::class),

//             // FALLBACK (OPERATOR / INVENTORY / DLL)
//             default =>
//                 app(DefaultDashboard::class),
//         };
//     }
// }

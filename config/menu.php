<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SUPERADMIN
    |--------------------------------------------------------------------------
    */
    'superadmin' => [

        [
            'section' => 'MAIN',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'superadmin.dashboard',
                    'permission' => null,
                    'icon' => 'home',
                ],
            ],
        ],

        [
            'section' => 'SUPERADMIN',
            'items' => [
                [
                    'label' => 'Manajemen User',
                    'route' => 'superadmin.users.index',
                    'permission' => 'user.view',
                    'icon' => 'users',
                ],
                [
                    'label' => 'Cabang',
                    'route' => 'superadmin.branches.index',
                    'permission' => 'branch.view',
                    'icon' => 'branch',
                ],
                [
                    'label' => 'Agent',
                    'route' => 'superadmin.agents.index',
                    'permission' => 'agent.view',
                    'icon' => 'agent',
                ],
                [
                    'label' => 'Commission Scheme',
                    'route' => 'superadmin.commission-schemes.index',
                    'permission' => null,
                    'icon' => 'percent',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | ADMIN PUSAT
    |--------------------------------------------------------------------------
    */
    'admin_pusat' => [

        [
            'section' => 'MAIN',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'pusat.dashboard',
                    'permission' => null,
                    'icon' => 'home',
                ],
            ],
        ],

        [
            'section' => 'MANAGEMENT',
            'items' => [
                [
                    'label' => 'Booking',
                    'route' => 'pusat.bookings.index',
                    'permission' => 'booking.view',
                    'icon' => 'calendar',
                ],
                [
                    'label' => 'Approve Booking',
                    'route' => 'pusat.bookings.approval',
                    'permission' => 'booking.approve',
                    'icon' => 'key',
                ],
                [
                    'label' => 'Commission',
                    'route' => 'pusat.commission.index',
                    'permission' => 'commission.view',
                    'icon' => 'percent',
                ],
                [
                    'label' => 'Reports',
                    'route' => 'pusat.reports.index',
                    'permission' => 'report.view',
                    'icon' => 'building',
                ],
            ],
        ],

    ],

];
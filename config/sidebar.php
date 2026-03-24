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
            'section' => 'MANAGEMENT',
            'items' => [
                [
                    'label' => 'Users',
                    'route' => 'superadmin.users.index',
                    'permission' => null,
                    'icon' => 'users',
                ],
                [
                    'label' => 'Branches',
                    'route' => 'superadmin.branches.index',
                    'permission' => null,
                    'icon' => 'branch',
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
            'section' => 'OPERATIONS',
            'items' => [
                [
                    'label' => 'Bookings',
                    'route' => 'pusat.bookings.index',
                    'permission' => 'booking.view',
                    'icon' => 'calendar',
                ],
                [
                    'label' => 'Commission',
                    'route' => 'pusat.commission.index',
                    'permission' => 'commission.view',
                    'icon' => 'money',
                ],
                [
                    'label' => 'Reports',
                    'route' => 'pusat.reports.index',
                    'permission' => 'report.view',
                    'icon' => 'chart',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | ADMIN CABANG
    |--------------------------------------------------------------------------
    */
    'admin_cabang' => [

        [
            'section' => 'MAIN',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'cabang.dashboard',
                    'permission' => null,
                    'icon' => 'home',
                ],
            ],
        ],

        [
            'section' => 'OPERATIONS',
            'items' => [
                [
                    'label' => 'Jamaah',
                    'route' => 'cabang.jamaah.index',
                    'permission' => 'jamaah.view',
                    'icon' => 'users',
                ],
                [
                    'label' => 'Bookings',
                    'route' => 'cabang.bookings.index',
                    'permission' => 'booking.view',
                    'icon' => 'calendar',
                ],
            ],
        ],

    ],

];
<?php 

return [

    [
        'section' => 'MAIN',
        'items' => [
            [
                'label' => 'Dashboard',
                'route' => 'superadmin.dashboard',
                'permission' => 'dashboard.view',
            ],
        ],
    ],

    [
        'section' => 'MANAGEMENT',
        'items' => [
            [
                'label' => 'User',
                'route' => 'superadmin.users.index',
                'permission' => 'user.view',
            ],
            [
                'label' => 'Branch',
                'route' => 'superadmin.branches.index',
                'permission' => 'branch.view',
            ],
            [
                'label' => 'Agent',
                'route' => 'superadmin.agents.index',
                'permission' => 'agent.view',
            ],
            [
                'label' => 'Commission',
                'route' => 'superadmin.commission-schemes.index',
                'permission' => 'commission.view',
            ],
        ],
    ],
];
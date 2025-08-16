<?php
return [
    'roles' => ['admin','kasir'], //available role,
    'menus' => [
        [
            'id'    => 1,
            'icon'  => 'Home',
            'label' => 'Beranda',
            'route' => '/',
            'order' => 1,
        ],
        [
            'id'    => 2,
            'icon'  => 'CreditCard',
            'label' => 'POS',
            'route' => '/pos',
            'order' => 2
        ],
        [
            'id'    => 3,
            'icon'  => 'Settings',
            'label' => 'Pengaturan',
            'route' => '#',
            'order' => 3
        ],
        [
            'id'    => 4,
            'icon'  => '',
            'label' => 'Umum',
            'route' => '/setting/general',
            'order' => 0,
            'parent'=> 3
        ],
        [
            'id'    => 5,
            'icon'  => '',
            'label' => 'Basis Data',
            'route' => '/setting/database',
            'order' => 1,
            'parent'=> 3
        ],
        [
            'id'    => 6,
            'icon'  => '',
            'label' => 'Hak Akses',
            'route' => '/setting/roles',
            'order' => 2,
            'parent'=> 3
        ]
    ]
];
<?php
return [
    'roles' => ['admin','kasir','keuangan'], //available role,
    'payment_types' => [
        'cash'          => [
            'label' => 'Lunas',
            'class' => 'green-700'
        ],
        'credit'        => [
            'label' => 'Cicilan',
            'class' => 'orange-600'
        ],
        'tempo'         => [
            'label' => 'Hutang',
            'class' => 'red-600'
        ]
    ],
    'payment_methods' => [
        'receive' => [
            'cash_payment'  => [
                'label' => 'Tunai',
                'class' => 'green-700',
            ],
            'bank_transfer' => [
                'label' => 'Transfer Bank',
                'class' => 'blue-600'
            ],
        ],
        'sale' => [
            'cash_payment'  => [
                'label' => 'Tunai',
                'class' => 'green-700',
            ],
            'bank_transfer' => [
                'label' => 'Transfer Bank',
                'class' => 'red-500'
            ],
            'qris' => [
                'label' => 'QRIS',
                'class' => 'blue-600'
            ],
        ]
    ],
    'payment_status' => [
        'unpaid'            => [
            'label' => 'Belum Dibayar',
            'class' => 'gray-800'
        ],
        'partially_paid'    => [
            'label' => 'Terbayar Sebagian',
            'class' => 'orange-500'
        ],
        'paid'              => [
            'label' => 'Lunas',
            'class' => 'green-700'
        ],
        'overdue'           => [
            'label'  => 'Jatuh Tempo',
            'class'  => 'red-400'
        ],
        'canceled'          => [
            'label'   => 'Dibatalkan',
            'class'   => 'red-800'
        ],
        'refunded'          => [
            'label'  => 'Dikembalikan',
            'class'  => 'blue-600'
        ]
    ],
    'menus' => [
        [
            'id'    => 1,
            'icon'  => 'Home',
            'label' => 'Beranda',
            'route' => '/',
            'order' => 1,
            'module'=>'dashboard'
        ],
        [
            'id'    => 2,
            'icon'  => 'CreditCard',
            'label' => 'POS',
            'route' => '/pos',
            'order' => 2,
            'module'=>'pos'
        ],
        [
            'id'    => 3,
            'icon'  => 'Settings',
            'label' => 'Pengaturan',
            'route' => '#',
            'order' => 6,
            'module'=>'setting'
        ],
        [
            'id'    => 4,
            'icon'  => '',
            'label' => 'Umum',
            'route' => '/setting/general',
            'order' => 0,
            'parent'=> 3,
            'module'=>'setting.general'
        ],
        [
            'id'    => 5,
            'icon'  => '',
            'label' => 'Basis Data',
            'route' => '/setting/database',
            'order' => 1,
            'parent'=> 3,
            'module'=>'setting.database'
        ],
        [
            'id'    => 6,
            'icon'  => '',
            'label' => 'Hak Akses',
            'route' => '/setting/role',
            'order' => 2,
            'parent'=> 3,
            'module'=>'setting.role'
        ],
        [
            'id'    => 7,
            'icon'  => 'Package',
            'label' => 'Master Data',
            'route' => '#',
            'order' => 3,
            'module'=>'master'
        ],
        [
            'id'    => 8,
            'icon'  => '',
            'label' => 'Satuan Barang',
            'route' => '/master/unit',
            'order' => 0,
            'parent'=> 7,
            'module'=>'master.unit'
        ],
        [
            'id'    => 9,
            'icon'  => '',
            'label' => 'Barang',
            'route' => '/master/item',
            'order' => 1,
            'parent'=> 7,
            'module'=>'master.item'
        ],
        [
            'id'    => 14,
            'icon'  => '',
            'label' => 'Kontak',
            'route' => '/master/contact',
            'order' => 2,
            'parent'=> 7,
            'module'=>'master.contact'
        ],
        [
            'id'    => 15,
            'icon'  => '',
            'label' => 'Pengguna',
            'route' => '/master/user',
            'order' => 3,
            'parent'=> 7,
            'module'=>'master.user'
        ],
        [
            'id'    => 16,
            'icon'  => '',
            'label' => 'Menu',
            'route' => '/setting/menu',
            'order' => 4,
            'parent'=> 3,
            'module'=>'setting.menu'
        ],
        [
            'id'    => 10,
            'icon'  => 'ShoppingCart',
            'label' => 'Transaksi',
            'route' => '#',
            'order' => 3,
            'module'=>'transaction'
        ],
        [
            'id'    => 11,
            'icon'  => '',
            'label' => 'Gudang',
            'route' => '#',
            'order' => 2,
            'parent'=> 10,
            'module'=>'transaction.warehouse'
        ],
        [
            'id'    => 12,
            'icon'  => '',
            'label' => 'Penerimaan',
            'route' => '/transaction/receive',
            'order' => 0,
            'parent'=> 10,
            'module'=>'transaction.receive'
        ],
        [
            'id'    => 13,
            'icon'  => '',
            'label' => 'Penjualan',
            'route' => '/transaction/sale',
            'order' => 1,
            'parent'=> 10,
            'module'=>'transaction.sale'
        ],
        [
            'id'    => 17,
            'icon'  => 'DollarSign',
            'label' => 'Keuangan',
            'route' => '#',
            'order' => 4,
            'module'=>'finance'
        ],
        [
            'id'    => 18,
            'icon'  => '',
            'label' => 'Hutang',
            'route' => '/finance/payable',
            'order' => 0,
            'parent'=> 17,
            'module'=>'finance.payable'
        ],
        [
            'id'    => 19,
            'icon'  => '',
            'label' => 'Piutang',
            'route' => '/finance/receivable',
            'order' => 1,
            'parent'=> 17,
            'module'=>'finance.receivable'
        ],
        [
            'id'    => 20,
            'icon'  => '',
            'label' => 'Stok',
            'route' => '/transaction/warehouse/stock',
            'order' => 0,
            'parent'=> 11,
            'module'=>'transaction.warehouse.stock'
        ],
         [
            'id'    => 21,
            'icon'  => '',
            'label' => 'Penyesuaian',
            'route' => '/transaction/warehouse/adjustment',
            'order' => 1,
            'parent'=> 11,
            'module'=>'transaction.warehouse.adjustment'
        ],
        [
            'id'    => 22,
            'icon'  => 'ScrollText',
            'label' => 'Laporan',
            'route' => '#',
            'order' => 5,
            'module'=>'report'
        ],
        [
            'id'    => 23,
            'icon'  => '',
            'label' => 'Kas',
            'route' => '/report/cash',
            'order' => 0,
            'parent'=> 22,
            'module'=>'report.cash'
        ],
        [
            'id'    => 24,
            'icon'  => '',
            'label' => 'Pendapatan',
            'route' => '/report/income',
            'order' => 1,
            'parent'=> 22,
            'module'=>'report.income'
        ],
        [
            'id'    => 25,
            'icon'  => '',
            'label' => 'Pengeluaran',
            'route' => '/report/expense',
            'order' => 2,
            'parent'=> 22,
            'module'=>'report.expense'
        ],
        [
            'id'    => 26,
            'icon'  => '',
            'label' => 'Laba Rugi',
            'route' => '/report/profitloss',
            'order' => 3,
            'parent'=> 22,
            'module'=>'report.profitloss'
        ],
        [
            'id'    => 27,
            'icon'  => '',
            'label' => 'Piutang',
            'route' => '/report/receivable',
            'order' => 4,
            'parent'=> 22,
            'module'=>'report.receivable'
        ],
        [
            'id'    => 28,
            'icon'  => '',
            'label' => 'Hutang',
            'route' => '/report/payable',
            'order' => 5,
            'parent'=> 22,
            'module'=>'report.payable'
        ],
        [
            'id'    => 29,
            'icon'  => '',
            'label' => 'Persediaan',
            'route' => '/report/inventory',
            'order' => 6,
            'parent'=> 22,
            'module'=>'report.inventory'
        ],
        [
            'id'    => 30,
            'icon'  => '',
            'label' => 'Pengeluaran',
            'route' => '/finance/expense',
            'order' => 2,
            'parent'=> 17,
            'module'=>'finance.expense'
        ],
        //last_id:28
    ],
    'units' => [
        'pcs'      => [1, 'Piece', 1, 'pcs'],
        'pak4'     => [2, 'Pack 4', 4, 'pcs'],
        'pak6'     => [3, 'Pack 6', 6, 'pcs'],
        'pak10'    => [4, 'Pack 10', 10, 'pcs'],
        'pak12'    => [5, 'Pack 12', 12, 'pcs'],
        'doz'      => [6, 'Lusin', 12, 'pcs'],
        'grs'      => [7, 'Gross', 144, 'pcs'],
        'kd'       => [8, 'Kodi', 20, 'pcs'],
        'rim'      => [9, 'Rim', 500, 'lbr'],

        'kg'       => [10, 'Kilogram', 1000, 'g'],
        'ons'      => [11, 'Ons', 100, 'g'],
        'ton'      => [12, 'Ton', 1000, 'kg'],
        'lb'       => [13, 'Pound', 0.4536, 'kg'],
        'oz'       => [14, 'Ounce', 28.35, 'g'],

        'L'        => [15, 'Liter', 1000, 'ml'],
        'ml'       => [16, 'Mililiter', 0.001, 'L'],
        'gln'      => [17, 'Galon (Indonesia)', 19, 'L'],
        'galUS'    => [18, 'Gallon (US)', 3.785, 'L'],
        'galUK'    => [19, 'Gallon (UK)', 4.546, 'L'],

        'm'        => [20, 'Meter', 100, 'cm'],
        'yd'       => [21, 'Yard', 0.9144, 'm'],
        'in'       => [22, 'Inch', 2.54, 'cm'],

        'W'        => [23, 'Watt', 1, 'J/s'],
        'kW'       => [24, 'Kilowatt', 1000, 'W'],
        'A'        => [25, 'Ampere', 1, 'C/s'],
        'V'        => [26, 'Volt', 1, 'J/C'],
        'Hz'       => [27, 'Hertz', 1, 's^-1'],

        'B'        => [28, 'Byte', 8, 'b'],
        'KB'       => [29, 'Kilobyte', 1024, 'B'],
        'MB'       => [30, 'Megabyte', 1024, 'KB'],
        'GB'       => [31, 'Gigabyte', 1024, 'MB'],
        'TB'       => [32, 'Terabyte', 1024, 'GB'],

        'px'       => [33, 'Pixel', 1, 'px'],
        'MP'       => [34, 'Megapixel', 1000000, 'px'],
        'in_layar' => [35, 'Inch (layar)', 2.54, 'cm'],
    ],
    'basic_units' => [
        'pcs'      => [36, 'Piece'],
        'g'        => [37, 'Gram'],
        'kg'       => [38, 'Kilogram'],
        'ml'       => [39, 'Mililiter'],
        'L'        => [40, 'Liter'],
        'cm'       => [41, 'Centimeter'],
        'm'        => [42, 'Meter'],
        'lbr'      => [43, 'Lembar'],
        'J/s'      => [44, 'Joule per detik'],
        'W'        => [45, 'Watt'],
        'C/s'      => [46, 'Coulomb per detik'],
        'J/C'      => [47, 'Joule per Coulomb'],
        's^-1'     => [48, 'Siklus per detik'],
        'b'        => [49, 'Bit'],
        'B'        => [50, 'Byte'],
        'px'       => [51, 'Pixel'],
        'btg'      => [52, 'Batang']
    ],
    'status' => [
        '0' => [
            'label' => 'Tidak Aktif',
            'color' => 'red-800'
        ],
        '1' => [
            'label' => 'Aktif',
            'color' => 'green-800'
        ]
    ],
    'adjustment_types' => [
        'lost' => [
            'label' => 'Hilang',
            'class' => 'black-800',
        ],
        'broke' => [
            'label' => 'Rusak',
            'class' => 'red-600'
        ],
        'return_in' => [
            'label' => 'Retur Penjualan',
            'class' =>  'blue-600'
        ],
        'return_out' => [
            'label' => 'Retur Pembelian',
            'class' =>  'green-800'
        ]
    ]

];
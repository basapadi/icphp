<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
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
            ]
        ];

        \DB::beginTransaction();
        Menu::truncate();
        $preInsertMenu = [];
        foreach($menus as $k => $menu){
            array_push($preInsertMenu, [
                'id' => $menu['id'],
                'icon' => $menu['icon']??null,
                'label' => $menu['label'],
                'route' => $menu['route'],
                'order' => $menu['order'],
                'parent_id' => $menu['parent']??null
            ]);
        }

        Menu::insert($preInsertMenu);

        \DB::commit();
    }
}

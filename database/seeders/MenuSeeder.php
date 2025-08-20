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
        $menus = config('ihandcashier.menus');

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
                'parent_id' => $menu['parent']??null,
                'module'    => $menu['module']??null
            ]);
        }
        Menu::insert($preInsertMenu);

        \DB::commit();
    }
}

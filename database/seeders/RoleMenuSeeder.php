<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\RoleMenu;

class RoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = config('ihandcashier.roles');
        $menus = Menu::all();
        $roleMenus = [];
        foreach($roles as $r => $role){
            $all = $role == 'admin' ? 1: 0;
            foreach($menus as $m => $menu){
                array_push($roleMenus, [
                    'role'      => $role,
                    'menu_id'   => $menu->id,
                    'view'      => $all,
                    'create'    => $all,
                    'edit'    => $all,
                    'update'    => $all,
                    'delete'    => $all,
                    'download'  => $all,
                ]);
            }
        }
        RoleMenu::truncate();
        RoleMenu::insert($roleMenus);
    }
}

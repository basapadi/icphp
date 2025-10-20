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
                $view = $all;
                $create = $all;
                $edit = $all;
                $update = $all;
                $delete = $all;
                $download = $all;
                if(in_array($menu->id,[31])){ //trash
                    $create = 0;
                    $edit = 0;
                    $update = 0;
                    $download = 0;
                }

                if($menu->id == 20){ //trash
                    $create = 0;
                    $edit = 0;
                    $update = 0;
                    $delete = 0;
                }
                if($menu->route == '#'){
                    $create = 0;
                    $edit = 0;
                    $update = 0;
                    $delete = 0;
                    $download = 0;
                }
                if(in_array($menu->id,[37,38])){ //invoice purchase and sale
                    $create = 0;
                    $edit = 1;
                    $update = 1;
                    $delete = 1;
                    $download = 1;
                }
                array_push($roleMenus, [
                    'role'      => $role,
                    'menu_id'   => $menu->id,
                    'view'      => $view,
                    'create'    => $create,
                    'edit'      => $edit,
                    'update'    => $update,
                    'delete'    => $delete,
                    'download'  => $download,
                ]);
            }
        }
        RoleMenu::truncate();
        RoleMenu::insert($roleMenus);
    }
}

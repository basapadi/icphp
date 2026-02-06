<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\{
    RoleMenu,
    Role
};

class RoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = config('ihandcashier.roles');
        $configMenus = collect(config('ihandcashier.menus'));
        $menus = Menu::all();
        $roleMenus = [];
        foreach ($roles as $r => $role) {
            $all = $role == 'admin' ? 1 : 0;
            foreach ($menus as $m => $menu) {
                $access = [
                    'view'      => $all,
                    'create'    => $all,
                    'edit'      => $all,
                    'update'    => $all,
                    'delete'    => $all,
                    'download'  => $all
                ];

                $cm = $configMenus->where('id', $menu->id)->first();

                if ($cm && isset($cm['hide'])) {
                    foreach ($cm['hide'] as $v) {
                        $access[$v] = 0;
                    }
                }

                if ($menu->route == '#') {
                    $access = [
                        'view'      => $all,
                        'create'    => 0,
                        'edit'      => 0,
                        'update'    => 0,
                        'delete'    => 0,
                        'download'  => 0
                    ];
                }

                array_push($roleMenus, [
                    'role'      => $role,
                    'menu_id'   => $menu->id,
                    'view'      => $access['view'],
                    'create'    => $access['create'],
                    'edit'      => $access['edit'],
                    'update'    => $access['update'],
                    'delete'    => $access['delete'],
                    'download'  => $access['download']
                ]);
            }
        }
        $dataroles = [];
        foreach ($roles as $key => $r) {
            $isAdmin = $r == 'admin' ? true : false;
            array_push($dataroles, [
                'key' => $r,
                'name' => str_replace('_', ' ', strtoupper($r)),
                'is_admin' => $isAdmin
            ]);
        }

        RoleMenu::truncate();
        Role::truncate();
        RoleMenu::insert($roleMenus);
        Role::insert($dataroles);
    }
}

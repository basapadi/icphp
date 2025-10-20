<?php

namespace App\Http\Controllers;

use App\Models\{
    Menu,
    RoleMenu
};
use Illuminate\Http\Request;
use Native\Laravel\Facades\App as ICApp;

class MenuController extends BaseController
{
    public function getMenu()
    {
        $role = (string) auth()->user()->role;

        // Ambil hanya menu_id yang punya izin view = 1
        $menuIds = RoleMenu::where('role', $role)
            ->where('view', true)
            ->whereHas('menu', function ($q) {
                $q->where('side_menu', true);
            })
            ->pluck('menu_id');
        // Query menu induk + subItems secara rekursif
        $menus = Menu::with([
            'subItems' => function ($q) use ($role) {
                $q->whereIn('id', function ($subQ) use ($role) {
                    $subQ->select('menu_id')
                        ->from('role_menus')
                        ->join('menus', 'menus.id', '=', 'role_menus.menu_id')
                        ->where('role_menus.role', $role)
                        ->where('role_menus.view', 1)
                        ->where('menus.side_menu', 1);
                })
                ->with(['subItems' => function ($q2) use ($role) {
                    $q2->whereIn('id', function ($subQ2) use ($role) {
                        $subQ2->select('menu_id')
                            ->from('role_menus')
                            ->join('menus', 'menus.id', '=', 'role_menus.menu_id')
                            ->where('role_menus.role', $role)
                            ->where('role_menus.view', 1)
                            ->where('menus.side_menu', 1);
                    });
                }])
                ->orderBy('order');
            },
        ])
        ->whereIn('id', $menuIds)
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get()
        ->map(fn($menu) => $this->setOpenRecursive($menu));

        return [
            'menus'      => $menus,
            'app'        => [
                'version'   => config('nativephp.version'),
                'copyright' => config('nativephp.copyright'),
            ],
        ];
    }

    private function setOpenRecursive($menu)
    {
        // Rekursif ke setiap subItem
        $menu->subItems->transform(fn($sub) => $this->setOpenRecursive($sub));

        // Tentukan apakah menu induk perlu 'open' berdasarkan subItems
        $menu->open = $menu->subItems->contains(fn($sub) => $sub->active || $sub->open);

        return $menu;
    }


    
}

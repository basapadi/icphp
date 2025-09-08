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
    public function getMenu(){
        $this->allowAccessModule('setting.menu', 'view');
        $role = (string) auth()->user()->role;
        
        $menuIds = RoleMenu::where('role', $role)->where('view', true)->pluck('menu_id');
        $menus = Menu::with('subItems.subItems')
        ->whereIn('id', $menuIds)
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get()
        ->map(fn($menu) => $this->setOpenRecursive($menu));
        $menuRoles = RoleMenu::join('menus', 'role_menus.menu_id', '=', 'menus.id')
            ->where('role_menus.role', $role)
            ->select('role_menus.id','menus.label','menus.route','menus.parent_id','view','create','delete','download')
            ->get()->toArray();
        return [
            'menus' => $menus,
            'menu_roles' => $menuRoles,
            'app' => [
                'version' => config('nativephp.version'),
                'copyright' => config('nativephp.copyright')
            ]
        ];
    }

    function setOpenRecursive($menu)
    {
        $menu->subItems->transform(fn($sub) => $this->setOpenRecursive($sub));
        $menu->open = $menu->subItems->contains(fn($sub) => $sub->active || $sub->open);
        return $menu;
    }

    
}

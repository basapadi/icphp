<?php

namespace App\Http\Controllers;

use App\Models\{
    Menu,
    RoleMenu
};
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    public function getMenu(){
       
        $role = auth()->user()->role;
        $menuIds = RoleMenu::where('role', $role)->where('view', true)->pluck('menu_id');
        $menus = Menu::with(['subItems' => function ($query) use ($menuIds) {
            $query->whereIn('id', $menuIds)->orderBy('order');
        }])
        ->whereIn('id', $menuIds)
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get()
        ->map(function ($item) {
            $item->open = $item->subItems->contains(fn ($sub) => $sub->active);
            return $item;
        });
        $menuRoles = $roleMenus = RoleMenu::join('menus', 'role_menus.menu_id', '=', 'menus.id')
            ->where('role_menus.role', $role)
            ->select('role_menus.id','menus.label','menus.route','menus.parent_id','view','create','delete','download')
            ->get()->toArray();
        return [
            'menus' => $menus,
            'menu_roles' => $menuRoles
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    public function getMenu(Request $request){
        $menus = Menu::with(['subItems' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->whereNull('parent_id')->get();
        return $menus;
    }
}

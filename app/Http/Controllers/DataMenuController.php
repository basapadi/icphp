<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class DataMenuController extends BaseController
{
    public function __construct(){
        $this->setModel(Menu::class)->with('parent');
        $this->setModule('setting.menu');
        $this->setMultipleSelectGrid(false);
        $this->setGridProperties([
            'advanceFilter' => false
        ]);
        $this->setFilterColumnsLike(['label','route'],request('q')??'');
    }
}
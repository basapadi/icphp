<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class DataMenuController extends BaseController
{
    public function __construct(){
        $this->setModel(Menu::class)->with('parent');
        $this->setModule('setting.menu');
        $this->setMultipleSelectGrid(false);
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'actions', 'label'=> 'Aksi', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]],
            ['value' => 'module', 'label'=> 'Module', 'align' => 'left'],
            ['value' => 'icon', 'label'=> 'Icon', 'align' => 'left'],
            ['value' => 'label', 'label'=> 'Label', 'align' => 'left'],
            ['value' => 'route', 'label'=> 'Route', 'align' => 'left'],
            ['value' => 'parent__label', 'label'=> 'Parent Label', 'align' => 'left'],
            ['value' => 'order', 'label'=> 'Order', 'align' => 'left']
        ]);
        $this->setGridProperties([
            'advanceFilter' => false
        ]);
        $this->setFilterColumnsLike(['label','route'],request('q')??'');
    }
}
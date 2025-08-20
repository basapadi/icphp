<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master;

class UnitController extends BaseController
{
    public function __construct(){
        $this->setModel(Master::class)->where('type','UNIT');
        $this->setModule('master.unit');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'status_label', 'label'=> 'Status Aktif', 'align' => 'left'],
            ['value' => 'type', 'label'=> 'Type', 'align' => 'left'],
            ['value' => 'kode', 'label'=> 'Kode', 'align' => 'left'],
            ['value' => 'nama', 'label'=> 'Nama', 'align' => 'left'],
            ['value' => 'actions', 'label'=> 'Aksi', 'align' => 'left','options' => ['edit','delete']]
        ]);
        $this->setFilterColumnsLike(['kode','nama'],request('q')??'');
    }
}

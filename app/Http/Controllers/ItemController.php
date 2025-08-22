<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends BaseController
{
    public function __construct(){
        $this->setModel(Item::class);
        $this->setModule('master.item');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'kode_barang', 'label'=> 'Kode Barang', 'align' => 'left'],
            ['value' => 'nama', 'label'=> 'Nama', 'align' => 'left'],
            // ['value' => 'gambar', 'label'=> 'Gambar', 'align' => 'left'],
            ['value' => 'kategori', 'label'=> 'Kategori', 'align' => 'left'],
            ['value' => 'status_label', 'label'=> 'Status', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]]
        ]);
        $this->setFilterColumnsLike(['kode_barang','nama'],request('q')??'');
    }
}

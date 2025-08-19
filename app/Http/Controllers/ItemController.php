<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends BaseController
{
    public function __construct(){
        $this->setModel(Item::class);
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'kode_barang', 'label'=> 'Kode Barang', 'align' => 'left'],
            ['value' => 'nama', 'label'=> 'Nama', 'align' => 'left'],
            ['value' => 'gambar', 'label'=> 'Gambar', 'align' => 'left'],
            ['value' => 'kategori', 'label'=> 'Kategori', 'align' => 'left'],
            ['value' => 'aktif', 'label'=> 'Aktif', 'align' => 'left'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => ['edit','delete']]
        ]);
        $this->setFilterColumnsLike(['kode_barang','nama'],request('q')??'');
    }
}

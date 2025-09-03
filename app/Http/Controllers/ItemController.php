<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends BaseController
{
    public function __construct(){
        $this->setModel(Item::class);
        $this->setModule('master.item');
        $this->setFilterColumnsLike(['kode_barang','nama'],request('q')??'');
        // $this->setMultipleSelectGrid(false);
    }
}

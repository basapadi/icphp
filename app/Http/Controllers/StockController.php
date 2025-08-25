<?php

namespace App\Http\Controllers;

use App\Models\{
    Item
};
use Illuminate\Http\Request;

class StockController extends BaseController
{
    public function __construct(){
        $this->setModel(Item::class);
        $this->setModule('transaction.warehouse.stock');
        $this->setColumns([
            ['value' => 'nama', 'label'=> 'Nama Barang', 'align' => 'left','option_filter' => true],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [
                $this->allowAccess('view'),
                $this->allowAccess('edit'),
                $this->allowAccess('delete')
            ]]
        ]);
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'nama'
        ]);
        $this->setFilterColumnsLike(['nama'],request('q')??'');
    }
}

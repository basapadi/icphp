<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemStock
};
use Illuminate\Http\Request;

class StockController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemStock::class)->with(['item','unit'])
            ->leftJoin('masters', 'masters.id', '=', 'item_stocks.unit_id');
        $this->setModule('transaction.warehouse.stock');
        $this->setColumns([
            ['value' => 'item__nama', 'label'=> 'Nama Barang', 'align' => 'left','option_filter' => true],
            ['value' => 'unit__nama', 'label'=> 'Satuan', 'align' => 'left','option_filter' => true],
            ['value' => 'jumlah', 'label'=> 'Jumlah', 'align' => 'right','styles' => 'width:50px','class' => 'font-mono font-bold','option_filter' => true],
            ['value' => 'minimum_stock', 'label'=> 'Stok Minimal', 'align' => 'right','styles' => 'width:50px','class' => 'font-mono font-bold','option_filter' => true],
        ]);
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'nama',
            'multipleSelect' => false
        ]);
        $this->setFilterColumnsLike(['masters.nama'],request('q')??'');
    }
}

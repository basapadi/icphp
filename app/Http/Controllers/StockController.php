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
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'nama',
            'multipleSelect' => false
        ]);
        $this->setFilterColumnsLike(['masters.nama'],request('q')??'');
    }
}

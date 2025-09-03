<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemStockAdjustment
};
use Illuminate\Http\Request;

class StockAdjustmentController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemStockAdjustment::class)->select('item_stock_adjustments.*')->with(['item','unit'])
            ->leftJoin('masters', 'masters.id', '=', 'item_stock_adjustments.unit_id')
            ->leftJoin('items', 'items.id', '=', 'item_stock_adjustments.item_id');
        $this->setModule('transaction.warehouse.adjustment');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'created_at',
            'multipleSelect' => false
        ]);
        $this->setFilterColumnsLike(['masters.nama','items.nama'],request('q')??'');
    }
}

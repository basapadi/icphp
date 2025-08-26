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
        $this->setColumns([
            ['value' => 'item__nama', 'label'=> 'Barang', 'align' => 'left','option_filter' => true],
            ['value' => 'unit__nama', 'label'=> 'Satuan', 'align' => 'left','option_filter' => true],
            ['value' => 'adjustment_type', 'label'=> 'Tipe Penyesuaian', 'align' => 'left','option_filter' => true,'show' => false],
            ['value' => 'adjustment_type_label', 'label'=> 'Tipe Penyesuaian', 'align' => 'left','option_filter' => false,'type'=> 'badge'],
            ['value' => 'system_stock', 'label'=> 'Jlh.Disistem', 'align' => 'right','styles' => 'width:50px','class' => 'font-mono font-bold','option_filter' => true],
            ['value' => 'actual_stock', 'label'=> 'Jlh.Sebenarnya', 'align' => 'right','styles' => 'width:50px','class' => 'font-mono font-bold','option_filter' => true],
            ['value' => 'adjustment_stock', 'label'=> 'Jlh.Penyesuaian', 'align' => 'right','styles' => 'width:50px','class' => 'font-mono font-bold','option_filter' => true],
            ['value' => 'catatan', 'label'=> 'Catatan', 'align' => 'left'],
            ['value' => 'created_at_formatted', 'label'=> 'Tanggal', 'align' => 'left','option_filter' => false, 'type' => 'date_range'],
            ['value' => 'created_at', 'label'=> 'Tanggal', 'align' => 'left','option_filter' => true, 'show' => false,'type' => 'date_range'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [
                $this->allowAccess('view'),
                $this->allowAccess('edit'),
                $this->allowAccess('delete')
            ]]
        ]);
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'created_at',
            'multipleSelect' => false
        ]);
        $this->setFilterColumnsLike(['masters.nama','items.nama'],request('q')??'');
    }
}

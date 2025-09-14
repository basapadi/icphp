<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemStockAdjustment,
    Master,
    ItemReceivedDetail
};
use Illuminate\Http\Request;
use App\Http\Response;

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
            'multipleSelect' => true
        ]);
        $this->setFilterColumnsLike(['masters.nama','items.nama'],request('q')??'');

        $unitReceivedDetail = ItemReceivedDetail::select('unit_id')->distinct()->get()->pluck('unit_id')->toArray();
        $itemReceivedDetail = ItemReceivedDetail::select('item_id')->distinct()->get()->pluck('item_id')->toArray();
        $items = Item::select(['id','nama'])->where('status',true)->whereIn('id',$itemReceivedDetail)->get()->pluck('nama','id')->toArray();
        $units = Master::select('id','nama')->where('type','UNIT')->whereIn('id',$unitReceivedDetail)->get()->pluck('nama','id')->toArray();
        $config = config('ihandcashier.adjustment_types');
        $types = [];
        foreach($config as $k => $c ){
            $types[$k] = $c['label'];
        }

        $form = $this->getResourceForm('item_adjustment');
        injectData($form, [
            'items' => $items,
            'units' => $units,
            'types' => $types
        ]);
        $this->setForm($form);
    }

    public function store(Request $request) {
        $rules = [
            'type' => 'required|string|in:UNIT,BASIC_UNIT',
            'kode' => 'required|string',
            'nama' => 'required|string',
            'status' => 'required|numeric|in:0,1',
            'to' => 'nullable|numeric',
            'conversion' => 'nullable|numeric',
            'id' => 'nullable|numeric',
        ];

    }

    public function edit(Request $request,$id){
        $this->allowAccessModule('transaction.warehouse.adjustment', 'edit');
        $id = $this->decodeId($id);
        $data = ItemStockAdjustment::where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        $unitReceivedDetail = ItemReceivedDetail::select('unit_id')->distinct()->get()->pluck('unit_id')->toArray();
        $itemReceivedDetail = ItemReceivedDetail::select('item_id')->distinct()->get()->pluck('item_id')->toArray();
        $items = Item::select(['id','nama'])->where('status',true)->whereIn('id',$itemReceivedDetail)->get()->pluck('nama','id')->toArray();
        $units = Master::select('id','nama')->where('type','UNIT')->whereIn('id',$unitReceivedDetail)->get()->pluck('nama','id')->toArray();
        $config = config('ihandcashier.adjustment_types');
        $types = [];
        foreach($config as $k => $c ){
            $types[$k] = $c['label'];
        }
        $form = $this->getResourceForm('item_adjustment');
        injectData($form, [
            'items' => $items,
            'units' => $units,
            'types' => $types
        ]);
        return Response::ok('loaded',[
            'data' => $data,
            'sections' => $form
        ]);

    }
}

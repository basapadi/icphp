<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemStockAdjustment,
    Master,
    ItemReceivedDetail,
    ItemStock
};
use Illuminate\Http\Request;
use App\Http\Response;
use Exception;

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
        $adjustmentTypes = config('ihandcashier.adjustment_types');
        $types = [];
        foreach ($adjustmentTypes as $key => $at) {
            array_push($types,$key);
        }
        $rules = [
            'adjustment_type' => 'required|string|in:'.implode(',',$types),
            'item_id' => 'required|numeric',
            'adjustment_stock' => 'required|numeric',
            'unit_id' => 'required|numeric',
            'catatan' => 'nullable|string'
        ];

        $data = $this->validate($rules);
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;
        $stock = ItemStock::where('item_id',$data['item_id'])->where('unit_id',$data['unit_id'])->first();
        if(empty($stock)) return Response::badRequest('Barang belum terdaftar di stock');
        try {
            if(!isset($request->id)){
                $this->allowAccessModule('transaction.warehouse.adjustment', 'create');

                
                $preInsert = [
                    'adjustment_type' => trim($data['adjustment_type']),
                    'item_id' => trim($data['item_id']),
                    'adjustment_stock' => trim($data['adjustment_stock']),
                    'unit_id' => trim($data['unit_id']),
                    'catatan' => trim($data['catatan']),
                    'system_stock' => @$stock->jumlah ?? 0,
                    'actual_stock' => 0,
                    'final_stock' => 0,
                    'created_by' => auth()->user()->id
                ];
                ItemStockAdjustment::insert($preInsert);
                return $this->setAlert('info','Berhasil',$preInsert['nama'].' berhasil disimpan');
            } else {

            }
        }catch(Exception $e){
            return $this->setAlert('error','Gagal',$e->getMessage());
        }

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

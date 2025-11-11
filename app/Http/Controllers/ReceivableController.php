<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemDelivery,
    ItemStock
};
use Illuminate\Http\Request;
use App\Objects\{DataArray,MergeData};

class ReceivableController extends BaseController
{
    public function __construct(){
        $this->setModule('finance.receivable');
        $exceptStatus = ['paid','cancelled','refunded'];
        $mergeData = new MergeData();
        $mergeData->attribute = 'details';
        $mergeData->class = ItemDelivery::class;
        $mergeData->key = 'contact_id';
        $mergeData->relations = [];
        $mergeData->whereNotIn = new DataArray('status_pembayaran',$exceptStatus );
        $this->setMergeData($mergeData);
        $query = $this->setModel(ItemDelivery::class)
            ->select(
                'contact_id',
                $this->raw('SUM(total_harga) as total_harga'),
                $this->raw('COALESCE(SUM(trx_sale_payment_items.jumlah),0) as terbayar'),
                $this->raw('SUM(trx_delivery_items.total_harga) - COALESCE(SUM(trx_sale_payment_items.jumlah),0) as sisa_bayar')
            )
            ->whereNotIn('status_pembayaran',$exceptStatus)
            ->leftJoin('trx_sale_payment_items', 'trx_sale_payment_items.trx_sale_item_id', '=', 'trx_delivery_items.id')
            ->havingRaw('SUM(trx_delivery_items.total_harga) - COALESCE(SUM(trx_sale_payment_items.jumlah),0) > 0');
            
        $this->setQuery($query)
            ->with(['contact'])
            ->groupBy('contact_id');
        $this->setGridProperties([
            'simpleFilter' => false,
            'multipleSelect' => false
        ]);
        $this->setExceptContextMenu(['create','edit','delete']);
    }
}

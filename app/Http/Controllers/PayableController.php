<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemReceived,
    ItemStock
};
use App\Objects\{DataArray,MergeData};
use Illuminate\Http\Request;

class PayableController extends BaseController
{
    public function __construct(){
        $this->setModule('finance.payable');
        $status = ['partially_paid','unpaid','overdue'];
        $mergeData = new MergeData();
        $mergeData->attribute = 'details';
        $mergeData->class = ItemReceived::class;
        $mergeData->key = 'contact_id';
        $mergeData->relations = [];
        $mergeData->whereIn = new DataArray('status_pembayaran',$status );
        $this->setMergeData($mergeData);

        $query = $this->setModel(ItemReceived::class)
            ->select(
                'contact_id',
                $this->raw('SUM(total_harga) as total_harga'),
                $this->raw('COALESCE(SUM(trx_received_payment_items.jumlah),0) as terbayar'),
                $this->raw('SUM(trx_received_items.total_harga) - COALESCE(SUM(trx_received_payment_items.jumlah),0) as sisa_bayar')
            )
            ->whereIn('status_pembayaran',$status)
            ->leftJoin('trx_received_payment_items', 'trx_received_payment_items.trx_received_item_id', '=', 'trx_received_items.id')
            ->havingRaw('SUM(trx_received_items.total_harga) - COALESCE(SUM(trx_received_payment_items.jumlah),0) > 0');
        $this->setQuery($query)->with(['contact'])->groupBy('contact_id');
        $this->setGridProperties([
            'simpleFilter' => false
        ]);
    }
}

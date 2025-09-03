<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived
};
use Illuminate\Http\Request;

class ReceivedItemController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemReceived::class)
            ->select('trx_received_items.*')
            ->with(['details','details.item','details.unit','contact','payments','payments.createdBy','createdBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_received_items.contact_id')->orderBy('tanggal_terima','desc');
        $this->setModule('transaction.receive');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal_terima'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');
    }
}

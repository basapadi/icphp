<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemSale
};
use Illuminate\Http\Request;

class SaleItemController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemSale::class)
            ->select('trx_sale_items.*')
            ->with(['details','details.item','details.unit','contact','payments','payments.createdBy','createdBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_sale_items.contact_id')->orderBy('tanggal_jual','desc');
        $this->setModule('transaction.sale');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal_jual'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived,
    SaleOrder,
    SaleOrderDetail,
    Contact,
    Item,
    Master
};
use Illuminate\Http\Request;

class SaleOrderController extends BaseController
{
    public function __construct(){
        $this->setModel(SaleOrder::class)
            ->select(['trx_sale_orders.*', 'trx_sale_orders.status as so_status'])
            ->with(['details','details.item','details.unit','contact','createdBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_sale_orders.contact_id')->orderBy('tanggal','desc');
        $this->setModule('transaction.order.sale');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode'],request('q')??'');
    }

    private function getContactToSelect(){
        $data = Contact::where('status', true)->where('type','pelanggan')->get();
        $contacts = [];

        foreach ($data as $key => $c) {
            $contacts[$c->id] = $c->nama.' - '.$c->email;
        }
        return $contacts;
    }

    private function getItemToSelect(){
        $data = Item::where('status', true)->get();
        $items = [];

        foreach ($data as $key => $c) {
            $items[$c->id] = $c->nama;
        }
        return $items;
    }

    private function getUnitToSelect(){
        $data = Master::where('status', true)->where('type','UNIT')->get();
        $items = [];

        foreach ($data as $key => $c) {
            $items[$c->id] = $c->nama;
        }
        return $items;
    }
}

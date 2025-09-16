<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived,
    Contact,
    Item,
    Master
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

        $form = $this->getResourceForm('receive');
        injectData($form, [
            'contacts'          => $this->getContactToSelect(),
            'payment_status'    => ihandCashierConfigToSelect('payment_status'),
            'payment_type'      => ihandCashierConfigToSelect('payment_types'),
            'payment_method'    => ihandCashierConfigToSelect('payment_methods.receive'),
            'items'             => $this->getItemToSelect(),
            'units'             => $this->getUnitToSelect()
        ]);
        $this->setForm($form);
    }


    private function getContactToSelect(){
        $data = Contact::where('status', true)->where('type','pemasok')->get();
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

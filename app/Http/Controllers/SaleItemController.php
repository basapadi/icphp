<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemSale
};
use Illuminate\Http\Request;

class SaleItemController extends BaseController
{
    private $_form = null;
    public function __construct(){
        $this->setModel(ItemSale::class)
            ->select('trx_sale_items.*')
            ->with(['details','details.item','details.unit','contact','createdBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_sale_items.contact_id')->orderBy('tanggal_jual','desc');
        $this->setModule('transaction.item.sale');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal_jual'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');
        $saleStatus = ihandCashierConfigToSelect('sale_item_status');
        $this->setInjectDataColumn([
            'status' => $saleStatus,
        ]);

         //ambil form json
        $this->_form = $this->getResourceForm('sale');

        //inject data ke form
        $form = $this->_form;
        injectData($form, [
            'kode_disabled'     => false,
            'contacts'          => getContactToSelect('pelanggan'),
            'status'            => ihandCashierConfigToSelect('sale_item_status', ['invoiced','partial_invoiced','cancelled','void']),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect(),
            'status_readonly'   => false,
            'contact_readonly'  => false
        ]);

        //set default value
        $this->setForm($form,[
            'kode_transaksi' => generateTransactionCode('DO'),
            'tanggal_jual' => now(),
            'dijual_oleh' => auth()->user()->name,
            'status' => 'draft'
        ]);
    }
}

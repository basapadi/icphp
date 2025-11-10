<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemSale
};
use Illuminate\Http\Request;
use App\Objects\ContextMenu;
use App\Http\Response;
use stdClass;

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
        $this->setDataDefaultForm($form,[
            'kode_transaksi' => generateTransactionCode('DO'),
            'tanggal_jual' => now(),
            'dijual_oleh' => auth()->user()->name,
            'status' => 'draft'
        ]);

        //buat pengiriman
        $createDelivery = new ContextMenu('createdelivery','Kirim Barang');
        $createDelivery->conditions = ['status' => ['draft']];
        $createDelivery->type = 'form_dialog';
        $createDelivery->apiUrl = route('api.sale.createDelivery');
        $createDelivery->icon = 'Truck';
        $createDelivery->color = '#6D94C5';
        $createDelivery->onClick = 'getFormDialog';
        $createDelivery->formUrl = route('api.sale.deliveryForm');

        $contextMenus = [$createDelivery];
        $this->setContextMenu($contextMenus);
    }

    public function createDelivery(Request $request){

    }

    public function deliveryForm(Request $request){
        $this->allowAccessModule('transaction.item.sale', 'create');
        $id = $this->decodeId($request->id);

        $data = ItemSale::with(['details'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        $delivery = new stdClass;
        $delivery->biaya_pengiriman = 0;
        $delivery->tipe_pengiriman = 'internal';

        $form = $this->getResourceForm('sale_delivery');
        injectData($form, [
            'delivery_types'     => ihandCashierConfigToSelect('delivery_types'),
        ]);
        
        $form = serializeform($form);

        return Response::ok('loaded',[
            'data' => $delivery,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 

    }
}

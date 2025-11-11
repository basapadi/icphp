<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemDelivery,
    SaleShipment
};
use Illuminate\Http\Request;
use App\Objects\ContextMenu;
use App\Http\Response;
use stdClass;
use Exception;
use Illuminate\Support\Facades\DB;

class SaleItemController extends BaseController
{
    private $_form = null;
    public function __construct(){
        $this->setModel(ItemDelivery::class)
            ->select('trx_delivery_items.*')
            ->with(['shipment','details','details.item','details.unit','contact','createdBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_delivery_items.contact_id')->orderBy('tanggal_jual','desc');
        $this->setModule('transaction.item.delivery');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal_jual'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');
        $saleStatus = ihandCashierConfigToSelect('delivery_item_status');
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
            'status'            => ihandCashierConfigToSelect('delivery_item_status', ['invoiced','partial_invoiced','cancelled','void']),
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

        //buat faktur
        $createInvoice = new ContextMenu('createinvoice','Buat Faktur');
        $createInvoice->conditions = ['status' => ['sent','partial_invoiced']];
        $createInvoice->type = 'form_dialog';
        $createInvoice->apiUrl = route('api.sale.invoice.createInvoice');
        $createInvoice->icon = 'Receipt';
        $createInvoice->color = '#6D94C5';
        $createInvoice->onClick = 'getFormDialog';
        $createInvoice->formUrl = route('api.sale.invoice.form');

        $contextMenus = [$createDelivery,$createInvoice];
        $this->setContextMenu($contextMenus);
    }

    public function edit(Request $request,$id){
        $this->allowAccessModule('transaction.item.delivery', 'edit');
        $id = $this->decodeId($id);
        $data = ItemDelivery::with(['contact','details'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        injectData($this->_form, [
            'kode_disabled'     => true,
            'contacts'          => getContactToSelect('pelanggan'),
            'status'            => ihandCashierConfigToSelect('delivery_item_status',['invoiced','partial_invoiced','cancelled']),
            'status_readonly'   => false,
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect()
        ]);
        
        $form = serializeform($this->_form);
        return Response::ok('loaded',[
            'data' => $data,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 

    }

    public function createDelivery(Request $request){
        $this->allowAccessModule('transaction.item.delivery', 'create');

        $rules = [
            'tipe_pengiriman'           => 'required|string',
            'tanggal_kirim'             => 'required|string',
            'biaya_pengiriman'          => 'required|numeric|min:0',
            'jasa_kirim'                => 'nullable|string',
            'catatan'                   => 'nullable|string',
            'no_resi'                   => 'nullable|string',
            'driver'                    => 'nullable|string',
            'telepon'                   => 'nullable|string',
            'no_kendaraan'              => 'nullable|string',
            'item_delivery_id'              => 'required|numeric'
        ];

        try {
            $data = $this->validate($rules);
            if ($data instanceof \Illuminate\Http\JsonResponse) return $data;
            $is = ItemDelivery::where('id', $data['item_delivery_id'])->first();
            if(empty($is))  return $this->setAlert('error','Gagal', 'Data pengiriman tidak ditemukan.');

            if($data['tipe_pengiriman'] == 'ekspedisi'){
                if(empty($data['jasa_kirim']) || empty($data['no_resi'])) return $this->setAlert('error','Gagal', 'Jasa kirim dan No.Resi tidak boleh kosong apabila tipe pengiriman adalah ekspedisi.');
            }

            begin();
            $preInsert = [
                'tipe_pengiriman'           => trim($data['tipe_pengiriman']),
                'tanggal_kirim'             => trim($data['tanggal_kirim']),
                'biaya_pengiriman'          => trim($data['biaya_pengiriman']),
                'jasa_kirim'                => trim(@$data['jasa_kirim']??null),
                'no_resi'                   => trim(@$data['no_resi']??null),
                'driver'                    => trim(@$data['driver']??null),
                'telepon'                   => trim(@$data['telepon']??null),
                'no_kendaraan'              => trim(@$data['no_kendaraan']??null),
                'item_delivery_id'              => trim($data['item_delivery_id']),
                'catatan'                   => @trim($data['catatan'])??null,
                'created_by'                => auth()->user()->id,
                'created_at'                => now()
            ];
            SaleShipment::create($preInsert);

            $is->status = 'sent';
            $is->save();

            commit();
            return $this->setAlert('info','Berhasil','Data Pengiriman berhasil disimpan');

        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function deliveryForm(Request $request){
        $this->allowAccessModule('transaction.item.delivery', 'create');
        $id = $this->decodeId($request->id);

        $data = ItemDelivery::with(['details'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        $delivery = new stdClass;
        $delivery->biaya_pengiriman = 0;
        $delivery->tipe_pengiriman = 'internal';
        $delivery->tanggal_kirim = date('Y-m-d');
        $delivery->item_delivery_id = $data->id;

        $form = $this->getResourceForm('sale_delivery');
        injectData($form, [
            'delivery_types'        => ihandCashierConfigToSelect('delivery_types'),
        ]);
        
        $form = serializeform($form);

        return Response::ok('loaded',[
            'data' => $delivery,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 

    }

    public function createInvoice(Request $request){

    }

    public function invoiceForm(Request $request){
        $this->allowAccessModule('transaction.invoice.sale', 'view');
        $id = $this->decodeId($request->id);
        $newInvoice = new \stdClass;
        $newInvoice->kode = generateTransactionCode('INV');
        $item = ItemDelivery::select('sale_order_id','contact_id')->where('id',$id)->first();
        if(empty($item)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        if($item->sale_order_id != null) $data = ItemDelivery::with(['details'])->where('sale_order_id',$item->sale_order_id)->get();
        else $data = ItemDelivery::with(['details'])->where('id',$id)->get();

        $details = [];
        foreach ($data as $key => $ir) {
            foreach ($ir->details as $detail) {
                // Hitung total jumlah yang sudah difakturkan untuk item ini
                $invoicedQty = DB::table('trx_sale_invoice_details')
                    ->join('trx_sale_invoices','trx_sale_invoices.id','=','trx_sale_invoice_details.sale_invoice_id')
                    ->join('trx_sale_invoice_item_deliveries', 'trx_sale_invoice_item_deliveries.sale_invoice_id', '=', 'trx_sale_invoice_details.sale_invoice_id')
                    ->where('trx_sale_invoice_item_deliveries.item_delivery_id', $ir->id)
                    ->where('trx_sale_invoices.status','!=', 'void')
                    ->where('trx_sale_invoice_details.item_id', $detail->item_id)
                    ->sum('trx_sale_invoice_details.jumlah');

                $remaining = $detail->jumlah - $invoicedQty;

                // Kalau masih ada sisa belum difakturkan
                if ($remaining > 0) {
                    $detailArr = $detail->toArray();
                    $detailArr['kode_pengiriman'] = $ir->kode_transaksi;
                    $detailArr['jumlah'] = $remaining;
                    $detailArr['diskon_nominal'] = 0;
                    $detailArr['pajak_persen'] = 11;
                    $detailArr['item_delivery_id'] = $ir->id;
                    $details[] = $detailArr;
                }
            }
        }

        $newInvoice->details = $details;
        $newInvoice->contact_id = $item->contact_id;
        $newInvoice->tanggal = date('Y-m-d');
        $newInvoice->tipe_bayar = 'tempo';
        $form = $this->getResourceForm('sale_invoice');

        injectData($form, [
            'contacts'          => getContactToSelect('pelanggan'),
            'tipe_bayar'        => ihandCashierConfigToSelect('payment_types'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect('UNIT'),
            'taxes'             => getTaxToSelect()
        ]);
        $form = serializeform($form);
        return Response::ok('loaded',[
            'data' => $newInvoice,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 
    }
}

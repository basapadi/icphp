<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived,
    Contact,
    Item,
    Master
};
use Illuminate\Http\Request;
use Exception;
use App\Http\Response;

class ReceivedItemController extends BaseController
{
    private $_form = null;
    public function __construct(){
        $this->setModel(ItemReceived::class)
            ->select('trx_received_items.*')
            ->with(['details','details.item','details.unit','contact','payments','payments.createdBy','createdBy','purchase_order'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_received_items.contact_id')
            ->orderBy('tanggal_terima','desc');
        $this->setModule('transaction.item.receive');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal_terima'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');

        //ambil form json
        $this->_form = $this->getResourceForm('receive');

        //inject data ke form
        $form = $this->_form;
        injectData($form, [
            'kode_disabled'     => false,
            'contacts'          => getContactToSelect('pemasok'),
            'payment_status'    => ihandCashierConfigToSelect('payment_status'),
            'payment_type'      => ihandCashierConfigToSelect('payment_types'),
            'payment_method'    => ihandCashierConfigToSelect('payment_methods.receive'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect()
        ]);

        //set default value
        $this->setForm($form,[
            'kode_transaksi' => generateTransactionCode('TR'),
            'tanggal_terima' => now(),
            'diterima_oleh' => auth()->user()->name,
            'status_pembayaran' => 'unpaid'
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'addtable.details'  => 'required|array|min:1',
            'contact_id'        => 'required|numeric',
            'tanggal_terima'    => 'required|string',
            'diterima_oleh'     => 'nullable|string',
            'potongan_harga'    => 'nullable|numeric:min:0',
            'status_pembayaran' => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('payment_status')),
            'tipe_pembayaran'   => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('payment_types')),
            'metode_pembayaran' => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('payment_methods.receive')),
            'syarat_pembayaran' => 'nullable|string',
            'catatan'           => 'nullable|string',
            'id'                => 'nullable|numeric',

            'addtable.details.*.item_id'    => 'required|integer|exists:items,id',
            'addtable.details.*.unit_id'    => 'required|integer|exists:masters,id',
            'addtable.details.*.jumlah'     => 'required|numeric|min:1',
            'addtable.details.*.harga'      => 'required|numeric|min:0',
            'addtable.details.*.kedaluarsa' => 'nullable|string',
            'addtable.details.*.batch'      => 'nullable|string'
        ];

        try {
            if(!isset($request->id)){
                $this->allowAccessModule('transaction.item.receive', 'create');

                $rules['kode'] = 'required|string|unique:trx_received_items,kode_transaksi';
                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                dd($data);
                begin();

            } else {
                $this->allowAccessModule('transaction.item.receive', 'update');

                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                dd($data);
                begin();
            }
        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function edit(Request $request,$id){
        $this->allowAccessModule('transaction.item.receive', 'edit');
        $id = $this->decodeId($id);
        $data = ItemReceived::with(['contact','details'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        injectData($this->_form, [
            'kode_disabled'     => true,
            'contacts'          => getContactToSelect('pemasok'),
            'payment_status'    => ihandCashierConfigToSelect('payment_status'),
            'payment_type'      => ihandCashierConfigToSelect('payment_types'),
            'payment_method'    => ihandCashierConfigToSelect('payment_methods.receive'),
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
}

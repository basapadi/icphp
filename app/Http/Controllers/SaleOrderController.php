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
use Exception;
use App\Http\Response;

class SaleOrderController extends BaseController
{
    private $_form = null;
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

        $this->_form = $this->getResourceForm('sale_order');

        $form = $this->_form;
        //inject data ke form
        injectData($form, [
            'kode_disabled'     => false,
            'contacts'          => getContactToSelect('pelanggan'),
            'so_status'         => ihandCashierConfigToSelect('sale_order_status'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect('UNIT'),
            'payment_status'    => ihandCashierConfigToSelect('payment_status'),
        ]);

         $this->setForm($form,[
            'kode' => generateTransactionCode('SO'),
            'tanggal' => date('d-m-Y'),
            'status' => 'draft',
            'status_pembayaran' => 'unpaid'
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'addtable.details'       => 'required|array|min:1',
            'contact_id'            => 'required|numeric',
            'tanggal'               => 'required|string',
            'tanggal_permintaan'    => 'required|string',
            'status'                => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('sale_order_status')),
            'status_pembayaran'     => 'nullable|string|in:'.implode(',',ihandCashierConfigKeyToArray('payment_status')),
            'catatan'               => 'nullable|string',
            'id'                    => 'nullable|numeric',

            'addtable.details.*.item_id'     => 'required|integer|exists:items,id',
            'addtable.details.*.unit_id'     => 'required|integer|exists:masters,id',
            'addtable.details.*.jumlah'      => 'required|numeric|min:1',
            'addtable.details.*.harga'       => 'required|numeric|min:0',
            'addtable.details.*.discount'    => 'nullable|numeric|min:0'
        ];

        try {
            if(!isset($request->id)){
                $this->allowAccessModule('transaction.order.sale', 'create');

                $rules['kode'] = 'required|string|unique:trx_sale_orders,kode';
                $data = $this->validate($rules);    
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                begin();

                $preInsert = [
                    'kode' => trim($data['kode']),
                    'contact_id' => trim($data['contact_id']),
                    'tanggal' => trim($data['tanggal']),
                    'tanggal_permintaan' => trim($data['tanggal_permintaan']),
                    'status' => trim($data['status']),
                    'status_pembayaran' => trim($data['status_pembayaran']),
                    'catatan' => @trim($data['catatan'])??null,
                    'created_by' => auth()->user()->id,
                    'created_at' => now()
                ];

                $so = SaleOrder::create($preInsert);

                $perInsertDetails = [];
                $total = 0;

                if(count($data['addtable']['details']) > 0){
                    foreach ($data['addtable']['details'] as $key => $d) {
                        $t = (double) (trim($d['harga']) * trim($d['jumlah']));
                        $discount = (double) trim($d['discount'])??0;
                        array_push($perInsertDetails,[
                            'sale_order_id' => $so->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (double) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'discount'            => $discount,
                            'sub_total'         => $t - $discount,
                        ]);
                        $total += ($t - $discount);
                    }
                }

                $so->total = $total;
                $so->save();
                SaleOrderDetail::insert($perInsertDetails);
                commit();
                return $this->setAlert('info','Berhasil','Pesanan penjualan '.$so->kode.' berhasil disimpan');

            } else {
                $this->allowAccessModule('transaction.order.sale', 'update');

                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                $exist = SaleOrder::with(['details'])->where('id',$data['id'])->first();
                if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');

                if(!in_array($exist->status,['draft','cancelled'])) return $this->setAlert('error','Galat!','Data sudah tidak dapat diubah karena status sudah '. config('ihandcashier.sale_order_status')[$exist->status]['label']);
                
                begin();

                $exist->contact_id = trim($data['contact_id']);
                $exist->tanggal = trim($data['tanggal']);
                $exist->tanggal_permintaan = trim($data['tanggal_permintaan']);
                $exist->status = trim($data['status']);
                $exist->status_pembayaran = trim($data['status_pembayaran']);
                $exist->catatan = trim($data['catatan']);
                $exist->updated_by = auth()->user()->id;
                $exist->updated_at = now();

                $perInsertDetails = [];
                $total = 0;

                if(count($data['addtable']['details']) > 0){
                    foreach ($data['addtable']['details'] as $key => $d) {
                        $t = (double) (trim($d['harga']) * trim($d['jumlah']));
                        $discount = (double) trim($d['discount'])??0;
                        array_push($perInsertDetails,[
                            'sale_order_id' => $exist->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (double) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'discount'            => $discount,
                            'sub_total'         => $t - $discount,
                        ]);
                        $total += ($t - $discount);
                    }
                }

                $exist->total = $total;
                $exist->details()->delete();
                $exist->save();

                SaleOrderDetail::insert($perInsertDetails);
                commit();
                return $this->setAlert('info','Berhasil','Pesanan penjualan '.$exist->kode.' berhasil diubah');

            }
        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function edit(Request $request,$id){
        $this->allowAccessModule('transaction.order.sale', 'edit');
        $id = $this->decodeId($id);
        $data = SaleOrder::with(['contact','details'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        injectData($this->_form, [
            'kode_disabled'     => true,
            'contacts'          => getContactToSelect('pelanggan'),
            'so_status'         => ihandCashierConfigToSelect('sale_order_status'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect('UNIT'),
            'payment_status'    => ihandCashierConfigToSelect('payment_status'),
        ]);
        
        $form = serializeform($this->_form);
        return Response::ok('loaded',[
            'data' => $data,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 

    }
}

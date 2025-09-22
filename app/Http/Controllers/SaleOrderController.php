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

        $form = $this->getResourceForm('sale_order');

        //inject data ke form
        injectData($form, [
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
            'addtable.detail' => 'required|array|min:1',
            'kode' => 'required|string|unique:trx_sale_orders,kode',
            'contact_id' => 'required|numeric',
            'tanggal' => 'required|date_format:d-m-Y',
            'tanggal_permintaan' => 'required|date_format:d-m-Y',
            'status' => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('sale_order_status')),
            'status_pembayaran' => 'nullable|string|in:'.implode(',',ihandCashierConfigKeyToArray('payment_status')),
            'catatan' => 'nullable|string',
            'id' => 'nullable|numeric',

            'addtable.detail.*.item_id' => 'required|integer|exists:items,id',
            'addtable.detail.*.unit_id' => 'required|integer|exists:masters,id',
            'addtable.detail.*.jumlah' => 'required|numeric|min:1',
            'addtable.detail.*.harga' => 'required|numeric|min:0',
            'addtable.detail.*.discount' => 'required|numeric|min:0'
        ];

        $data = $this->validate($rules);    
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

        try {
            if(!isset($data['id'])){
                $this->allowAccessModule('transaction.order.sale', 'create');
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

                if(count($data['addtable']['detail']) > 0){
                    foreach ($data['addtable']['detail'] as $key => $d) {
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
                //TODO:: Update SO

            }
        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }
}

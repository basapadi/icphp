<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived,
    PurchaseOrder,
    PurchaseOrderDetail,
    Contact,
    Item,
    Master,
    User
};
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends BaseController
{
    public function __construct(){
        $this->setModel(PurchaseOrder::class)
            ->select(['trx_purchase_orders.*', 'trx_purchase_orders.status as po_status'])
            ->with(['details','details.item','details.unit','contact','createdBy','approvalBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_purchase_orders.contact_id')->orderBy('tanggal','desc');
        $this->setModule('transaction.order.purchase');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode'],request('q')??'');

        $form = $this->getResourceForm('purchase_order');

        //inject data ke form
        injectData($form, [
            'contacts'          => $this->getContactToSelect(),
            'po_status'         => ihandCashierConfigToSelect('purchase_order_status'),
            'items'             => $this->getItemToSelect(),
            'units'             => $this->getUnitToSelect(),
            'users'             => $this->getUserToSelect()
        ]);

        //set default value
        $this->setForm($form,[
            'kode' => generateTransactionCode('PO'),
            'tanggal' => date('d-m-Y'),
            'status' => 'draft',
            'approval_by' => auth()->user()->id
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'addtable.detail' => 'required|array|min:1',
            'kode' => 'required|string|unique:trx_purchase_orders,kode',
            'contact_id' => 'required|numeric',
            'tanggal' => 'required|date_format:d-m-Y',
            'approval_by' => 'required|numeric',
            'tanggal_perkiraan_datang' => 'required|string',
            'status' => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('purchase_order_status')),
            'catatan' => 'nullable|string',
            'id' => 'nullable|numeric',

            'addtable.detail.*.item_id' => 'required|integer|exists:items,id',
            'addtable.detail.*.unit_id' => 'required|integer|exists:masters,id',
            'addtable.detail.*.jumlah' => 'required|numeric|min:1',
            'addtable.detail.*.harga' => 'required|numeric|min:0'
        ];

        $data = $this->validate($rules);
        
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

        try {
            if(!isset($data['id'])){
                $this->allowAccessModule('transaction.order.purchase', 'create');

                DB::beginTransaction();
                $preInsert = [
                    'kode' => trim($data['kode']),
                    'contact_id' => trim($data['contact_id']),
                    'tanggal' => trim($data['tanggal']),
                    'tanggal_perkiraan_datang' => trim($data['tanggal_perkiraan_datang']),
                    'status' => trim($data['status']),
                    'catatan' => @trim($data['catatan'])??null,
                    'approval_by' => trim($data['approval_by']),
                    'approval_status' => 'pending',
                    'created_by' => auth()->user()->id,
                    'created_at' => now()
                ];

                $po = PurchaseOrder::create($preInsert);

                $perInsertDetails = [];
                $total = 0;
                if(count($data['addtable']['detail']) > 0){
                    foreach ($data['addtable']['detail'] as $key => $d) {
                        $t = (double) (trim($d['harga']) * trim($d['jumlah']));
                        array_push($perInsertDetails,[
                            'purchase_order_id' => $po->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (int) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'sub_total'         => $t
                        ]);

                        $total += $t;
                    }
                }

                $po->total = $total;
                $po->save();
                
                PurchaseOrderDetail::insert($perInsertDetails);
                DB::commit();
                return $this->setAlert('info','Berhasil','Pesanan pembelian '.$po->kode.' berhasil disimpan');

            }else {

            }
        }catch(Exception $e){
            DB::rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    private function getContactToSelect(){
        $data = Contact::where('status', true)->where('type','pemasok')->get();
        $contacts = [];

        foreach ($data as $key => $c) {
            $contacts[$c->id] = $c->nama.' - '.$c->email;
        }
        return $contacts;
    }

    private function getUserToSelect(){
        $data = User::where('active', true)->get();
        $users = [];

        foreach ($data as $key => $c) {
            $users[$c->id] = $c->name.' - '.$c->email;
        }
        return $users;
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

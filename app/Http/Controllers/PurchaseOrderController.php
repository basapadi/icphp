<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived,
    PurchaseOrder,
    PurchaseOrderDetail,
    Contact,
    Item,
    ItemReceivedDetail,
    Master,
    User
};
use App\Objects\ContextMenu;
use Illuminate\Http\Request;
use Exception;
use App\Http\Response;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends BaseController
{
    private $_form = null;
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

        $this->_form = $this->getResourceForm('purchase_order');
        //inject data ke form
        $form = $this->_form;
        injectData($form, [
            'kode_disabled'     => false,
            'contacts'          => getContactToSelect('pemasok'),
            'po_status'         => ihandCashierConfigToSelect('purchase_order_status'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect('UNIT'),
            'users'             => getUserToSelect()
        ]);


        //set default value
        $this->setForm($form,[
            'kode' => generateTransactionCode('PO'),
            'tanggal' => date('d-m-Y'),
            'status' => 'draft',
            'approval_by' => auth()->user()->id
        ]);

        $sendEmailContextMenu = new ContextMenu('sendpo','Kirim PO via Email');
        $sendEmailContextMenu->conditions = ['status' => 'approved'];
        $sendEmailContextMenu->type = 'confirm';
        $sendEmailContextMenu->apiUrl = route('api.purhcase.sendEmail');
        $sendEmailContextMenu->icon = 'SendHorizontal';
        $sendEmailContextMenu->color = '#2196F3';

        $createReceivedItem = new ContextMenu('createreceived','Buat Penerimaan');
        $createReceivedItem->conditions = ['status' => ['sended','partial_received']];
        $createReceivedItem->type = 'form_dialog';
        $createReceivedItem->apiUrl = route('api.purhcase.createReceivedItem');
        $createReceivedItem->icon = 'HandHelping';
        $createReceivedItem->color = '#6D94C5';
        $createReceivedItem->onClick = 'getFormDialog';
        $createReceivedItem->formUrl = route('api.purhcase.receivedForm');

        $contextMenus = [$sendEmailContextMenu,$createReceivedItem];
        $this->setContextMenu($contextMenus);
    }

    public function store(Request $request)
    {
        $rules = [
            'addtable.details'          => 'required|array|min:1',
            'contact_id'                => 'required|numeric',
            'tanggal'                   => 'required|string',
            'approval_by'               => 'required|numeric',
            'tanggal_perkiraan_datang'  => 'required|string',
            'status'                    => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('purchase_order_status')),
            'catatan'                   => 'nullable|string',
            'id'                        => 'nullable|numeric',

            'addtable.details.*.item_id'    => 'required|integer|exists:items,id',
            'addtable.details.*.unit_id'    => 'required|integer|exists:masters,id',
            'addtable.details.*.jumlah'     => 'required|numeric|min:1',
            'addtable.details.*.harga'      => 'required|numeric|min:0'
        ];

        try {
            if(!isset($request->id)){
                $this->allowAccessModule('transaction.order.purchase', 'create');

                $rules['kode'] = 'required|string|unique:trx_purchase_orders,kode';
                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                begin();
                $preInsert = [
                    'kode'                      => trim($data['kode']),
                    'contact_id'                => trim($data['contact_id']),
                    'tanggal'                   => trim($data['tanggal']),
                    'tanggal_perkiraan_datang'  => trim($data['tanggal_perkiraan_datang']),
                    'status'                    => trim($data['status']),
                    'catatan'                   => @trim($data['catatan'])??null,
                    'approval_by'               => trim($data['approval_by']),
                    'approval_status'           => 'pending',
                    'created_by'                => auth()->user()->id,
                    'created_at'                => now()
                ];

                $po = PurchaseOrder::create($preInsert);

                $perInsertDetails = [];
                $total = 0;
                if(count($data['addtable']['details']) > 0){
                    foreach ($data['addtable']['details'] as $key => $d) {
                        $t = (double) (trim($d['harga']) * trim($d['jumlah']));
                        array_push($perInsertDetails,[
                            'purchase_order_id' => $po->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (double) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'sub_total'         => $t
                        ]);

                        $total += $t;
                    }
                }

                $po->total = $total;
                $po->save();
                
                PurchaseOrderDetail::insert($perInsertDetails);
                commit();
                return $this->setAlert('info','Berhasil','Pesanan pembelian '.$po->kode.' berhasil disimpan');

            }else {
                $this->allowAccessModule('transaction.order.sale', 'update');
                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                $exist = PurchaseOrder::with(['details'])->where('id',$data['id'])->first();
                if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');
                
                if(!in_array($exist->status,['draft','rejected','canceled'])) return $this->setAlert('error','Galat!','Data sudah tidak dapat diubah karena status sudah '. config('ihandcashier.purchase_order_status')[$exist->status]['label']);

                begin();
                $exist->contact_id = trim($data['contact_id']);
                $exist->tanggal = trim($data['tanggal']);
                $exist->tanggal_perkiraan_datang = trim($data['tanggal_perkiraan_datang']);
                $exist->status = trim($data['status']);
                $exist->catatan = trim($data['catatan']);
                $exist->updated_by = auth()->user()->id;
                $exist->updated_at = now();

                $perInsertDetails = [];
                $total = 0;
                if(count($data['addtable']['details']) > 0){
                    foreach ($data['addtable']['details'] as $key => $d) {
                        $t = (double) (trim($d['harga']) * trim($d['jumlah']));
                        array_push($perInsertDetails,[
                            'purchase_order_id' => $exist->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (double) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'sub_total'         => $t
                        ]);

                        $total += $t;
                    }
                }

                $exist->total = $total;
                $exist->details()->delete();
                $exist->save();
                PurchaseOrderDetail::insert($perInsertDetails);
                commit();
                return $this->setAlert('info','Berhasil','Pesanan pembelian '.$exist->kode.' berhasil diubah');
            }
        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function edit(Request $request,$id){
        $this->allowAccessModule('transaction.order.purchase', 'edit');
        $id = $this->decodeId($id);
        $data = PurchaseOrder::with(['contact','details'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        injectData($this->_form, [
            'kode_disabled'     => true,
            'contacts'          => getContactToSelect('pemasok'),
            'po_status'         => ihandCashierConfigToSelect('purchase_order_status'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect('UNIT'),
            'users'             => getUserToSelect()
        ]);
        
        $form = serializeform($this->_form);
        return Response::ok('loaded',[
            'data' => $data,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 

    }

    public function receivedForm(Request $request){
        $this->allowAccessModule('transaction.item.receive', 'create');
        $id = $this->decodeId($request->id);

        $data = PurchaseOrder::with(['details'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');
        $data->kode_po = $data->kode;
        $data->kode_transaksi = generateTransactionCode('TR');
        $data->tanggal_terima = date('Y-m-d');
        $data->diterima_oleh = auth()->user()->name;
        $data->status_pembayaran = 'unpaid';
        $data->po_id = $data->id;
        unset($data->id);
        $newDetails = [];
        //hitung sisa belum diterima dari penerimaan barang
        foreach ($data->details as $detail) {
            $receivedQty = DB::table('trx_received_item_details as ird')
                ->join('trx_received_items as ir', 'ir.id', '=', 'ird.item_received_id')
                ->where('ir.purchase_order_id', $data->po_id)
                ->where('ird.item_id', $detail->item_id)
                ->sum('ird.jumlah');
            $sisa = $detail->jumlah - $receivedQty;

            if ($sisa > 0) {
                $detail->jumlah = $sisa;
                $newDetails[] = $detail;
            }
        }

        $data->setRelation('details', collect($newDetails));

        $form = $this->getResourceForm('receive');
        injectData($form, [
            'kode_disabled'     => false,
            'contacts'          => getContactToSelect('pemasok'),
            'payment_status'    => ihandCashierConfigToSelect('payment_status'),
            'payment_type'      => ihandCashierConfigToSelect('payment_types'),
            'payment_method'    => ihandCashierConfigToSelect('payment_methods.receive'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect()
        ]);

        $form = serializeform($form);
        return Response::ok('loaded',[
            'data' => $data,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 
    }

    public function createReceivedItem(Request $request){
        $this->allowAccessModule('transaction.item.receive', 'create');

        $rules = [
            'addtable.details'  => 'required|array|min:1',
            'kode_transaksi'    => 'required|string',
            'contact_id'        => 'required|numeric',
            'tanggal_terima'    => 'required|string',
            'diterima_oleh'     => 'nullable|string',
            'potongan_harga'    => 'nullable|numeric:min:0',
            'status_pembayaran' => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('payment_status')),
            'tipe_pembayaran'   => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('payment_types')),
            'metode_pembayaran' => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('payment_methods.receive')),
            'syarat_pembayaran' => 'nullable|string',
            'catatan'           => 'nullable|string',
            'po_id'             => 'required|numeric',

            'addtable.details.*.item_id'    => 'required|integer|exists:items,id',
            'addtable.details.*.unit_id'    => 'required|integer|exists:masters,id',
            'addtable.details.*.jumlah'     => 'required|numeric|min:1',
            'addtable.details.*.harga'      => 'required|numeric|min:0',
            'addtable.details.*.kedaluarsa' => 'nullable|string',
            'addtable.details.*.batch'      => 'nullable|string'
        ];

        $data = $this->validate($rules);
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

        try {

            begin();
            $preInsert = [
                'purchase_order_id'         => trim($data['po_id']),
                'kode_transaksi'            => trim($data['kode_transaksi']),
                'contact_id'                => trim($data['contact_id']),
                'tanggal_terima'            => trim($data['tanggal_terima']),
                'diterima_oleh'             => trim($data['diterima_oleh']),
                'potongan_harga'            => trim(@$data['potongan_harga']??null),
                'status_pembayaran'         => trim($data['status_pembayaran']),
                'tipe_pembayaran'           => trim($data['tipe_pembayaran']),
                'metode_pembayaran'         => trim($data['metode_pembayaran']),
                'syarat_pembayaran'         => trim(@$data['syarat_pembayaran']??null),
                'catatan'                   => trim(@$data['catatan'])??null,
                'created_by'                => auth()->user()->id,
                'created_at'                => now()
            ];

            $po = PurchaseOrder::with(['details'])->where('id', $data['po_id'])->first();
            if(empty($po))return $this->setAlert('error','Gagal', 'PO dengan id '.$data['po_id'].' tidak ditemukan');

            $received = ItemReceived::create($preInsert);

            $perInsertDetails = [];
            $total = 0;
            if(count($data['addtable']['details']) > 0){
                foreach ($data['addtable']['details'] as $key => $d) {
                    $t = (double) (trim($d['harga']) * trim($d['jumlah']));
                    array_push($perInsertDetails,[
                        'item_received_id'  => $received->id,
                        'item_id'           => (int) trim($d['item_id']),
                        'unit_id'           => (int) trim($d['unit_id']),
                        'harga'             => (double) trim($d['harga']),
                        'jumlah'            => (int) trim($d['jumlah']),
                        'kedaluarsa'        => (int) trim(@$d['kedaluarsa']??null),
                        'batch'             => (int) trim(@$d['batch']??null)
                    ]);

                    $total += $t;
                }
            }
            ItemReceivedDetail::insert($perInsertDetails);

            //compare detail PO dengan detail penerimaan
            $allFull = true;
            $poDetails = $po->details;

            foreach ($poDetails as $d) {
                // total yang sudah diterima (termasuk penerimaan kali ini)
                $receivedQty = ItemReceivedDetail::join('trx_received_items as ir', 'ir.id', '=', 'trx_received_item_details.item_received_id')
                    ->where('ir.purchase_order_id', $po->id)
                    ->where('trx_received_item_details.item_id', $d->item_id)
                    ->sum('trx_received_item_details.jumlah');

                if ($receivedQty < $d->jumlah) {
                    $allFull = false;
                }
            }

            $po->status = $allFull ? 'received': 'partial_received';
            $received->total_harga = $total;
            $received->purchase_order_id = $po->id;
            $received->save();
            $po->save();
            
            commit();
            return $this->setAlert('info','Berhasil','Penerimaan '.$received->kode.' berhasil disimpan');
        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }
}

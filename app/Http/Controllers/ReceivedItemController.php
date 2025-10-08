<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived,
    ItemReceivedDetail,
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
            ->with(['details','details.item','details.unit','contact','createdBy','purchase_order'])
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
            'status'            => ihandCashierConfigToSelect('receive_item_status'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect(),
            'status_readonly'   => false
        ]);

        //set default value
        $this->setForm($form,[
            'kode_transaksi' => generateTransactionCode('TR'),
            'tanggal_terima' => now(),
            'diterima_oleh' => auth()->user()->name,
            'status' => 'draft'
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'addtable.details'  => 'required|array|min:1',
            'contact_id'        => 'required|numeric',
            'tanggal_terima'    => 'required|string',
            'diterima_oleh'     => 'nullable|string',
            'status'            => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('receive_item_status')),
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

                $rules['kode_transaksi'] = 'required|string|unique:trx_received_items,kode_transaksi';
                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                begin();
                $preInsert = [
                    'kode_transaksi'            => trim($data['kode_transaksi']),
                    'contact_id'                => trim($data['contact_id']),
                    'tanggal_terima'            => trim($data['tanggal_terima']),
                    'diterima_oleh'             => trim(@$data['diterima_oleh']??null),
                    'catatan'                   => @trim($data['catatan'])??null,
                    'status'                    => trim($data['status']),
                    'created_by'                => auth()->user()->id,
                    'created_at'                => now()
                ];

                $trx = ItemReceived::create($preInsert);

                $perInsertDetails = [];
                $total = 0;
                if(count($data['addtable']['details']) > 0){
                    foreach ($data['addtable']['details'] as $key => $d) {
                        $t = (double) (trim($d['harga']) * (int)trim($d['jumlah']));
                        array_push($perInsertDetails,[
                            'item_received_id' => $trx->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (double) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'kedaluarsa'        => @trim($d['kedaluarsa'])??null,
                            'batch'             => trim($d['batch'])??null,
                            'sub_total'         => $t
                        ]);

                        $total += $t;
                    }
                }

                $trx->total_harga = $total;
                $trx->save();
                
                ItemReceivedDetail::insert($perInsertDetails);
                commit();
                return $this->setAlert('info','Berhasil','Penerimaan '.$trx->kode_transaksi.' berhasil disimpan');

            } else {
                $this->allowAccessModule('transaction.item.receive', 'update');

                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                $exist = ItemReceived::with(['details'])->where('id',$data['id'])->first();
                if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');
                begin();

                $exist->contact_id = trim($data['contact_id']);
                $exist->tanggal_terima = trim($data['tanggal_terima']);
                $exist->diterima_oleh = trim($data['diterima_oleh']);
                $exist->status = trim($data['status']);
                $exist->catatan = @trim($data['catatan'])??null;
                $exist->updated_by = auth()->user()->id;
                $exist->updated_at = now();

                $perInsertDetails = [];
                $total = 0;
                if(count($data['addtable']['details']) > 0){
                    foreach ($data['addtable']['details'] as $key => $d) {
                        $t = (double) (trim($d['harga']) * (int) trim($d['jumlah']));
                        array_push($perInsertDetails,[
                            'item_received_id' => $exist->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (double) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'kedaluarsa'        => @trim($d['kedaluarsa'])??null,
                            'batch'             => trim($d['batch'])??null,
                            'sub_total'         => $t
                        ]);

                        $total += $t;
                    }
                }

                $exist->total_harga = $total;
                $exist->details()->delete();
                $exist->save();

                ItemReceivedDetail::insert($perInsertDetails);
                commit();
                return $this->setAlert('info','Berhasil','Penerimaan '.$exist->kode_transaksi.' berhasil diubah');

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
            'status'            => ihandCashierConfigToSelect('receive_item_status'),
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
}

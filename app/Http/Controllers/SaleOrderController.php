<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived,
    SaleOrder,
    SaleOrderDetail,
    Contact,
    Item,
    ItemSale,
    ItemSaleDetail,
    Master,
    ItemStock
};
use Illuminate\Http\Request;
use Exception;
use App\Http\Response;
use App\Objects\ContextMenu;
use Illuminate\Support\Facades\{DB,Mail,Log};

class SaleOrderController extends BaseController
{
    private $_form = null;
    public function __construct(){
        $this->setModel(SaleOrder::class)
            ->select(['trx_sale_orders.*', 'trx_sale_orders.status as so_status'])
            ->with(['details','details.item','details.unit','contact','createdBy','approvalBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_sale_orders.contact_id')->orderBy('tanggal','desc');
        $this->setModule('transaction.order.sale');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode'],request('q')??'');
        $saleStatus = ihandCashierConfigToSelect('sale_order_status');

        $this->_form = $this->getResourceForm('sale_order');
        $form = $this->_form;
        //inject data ke form
        injectData($form, [
            'kode_disabled'     => false,
            'contacts'          => getContactToSelect('pelanggan'),
            'so_status'         => ihandCashierConfigToSelect('sale_order_status'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect('UNIT'),
            'status_disabled'   => true,
            'approval_by_disabled' => false,
            'users'             => getUserToSelect()
        ]);

        $this->setInjectDataColumn([
            'so_options' => $saleStatus,
        ]);

        $this->setDataDefaultForm($form,[
            'kode' => generateTransactionCode('SO'),
            'tanggal' => date('Y-m-d'),
            'status' => 'draft',
            'tanggal_permintaan' => date('Y-m-d')
        ]);


        //butuh persetujuan
        $needApproval = new ContextMenu('needapproval','Minta Persetujuan');
        $needApproval->conditions = ['status' => ['draft','rejected']];
        $needApproval->type = 'confirm';
        $needApproval->apiUrl = route('api.sale.order.needApproval');
        $needApproval->icon = 'BadgeCheck';
        $needApproval->color = '#6D94C5';
        $needApproval->onClick = 'confirmPopup';
        $needApproval->title = 'Meminta Persetujuan';
        $needApproval->message = 'Apakah anda yakin meminta persetujuan untuk pesanan ini?.';

        //buat pengiriman baru
        $createSaleItem = new ContextMenu('createsale','Buat Pengiriman');
        $createSaleItem->conditions = ['status' => ['confirmed','partial_sent']];
        $createSaleItem->type = 'form_dialog';
        $createSaleItem->apiUrl = route('api.sale.order.createSaleItem');
        $createSaleItem->icon = 'HandHelping';
        $createSaleItem->color = '#6D94C5';
        $createSaleItem->onClick = 'getFormDialog';
        $createSaleItem->formUrl = route('api.sale.order.saleForm');

        $contextMenus = [$needApproval,$createSaleItem];
        $this->setContextMenu($contextMenus);
    }

    public function store(Request $request)
    {
        $rules = [
            'addtable.details'      => 'required|array|min:1',
            'contact_id'            => 'required|numeric',
            'tanggal'               => 'required|string',
            'tanggal_permintaan'    => 'required|string',
            'status'                => 'required|string|in:'.implode(',',ihandCashierConfigKeyToArray('sale_order_status')),
            'catatan'               => 'nullable|string',
            'approval_by'           => 'required|numeric',
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
                    'kode'                  => trim($data['kode']),
                    'contact_id'            => trim($data['contact_id']),
                    'tanggal'               => trim($data['tanggal']),
                    'tanggal_permintaan'    => trim($data['tanggal_permintaan']),
                    'status'                => trim($data['status']),
                    'catatan'               => @trim($data['catatan'])??null,
                    'approval_by'           => trim($data['approval_by']),
                    'approval_status'       => 'pending',
                    'created_by'            => auth()->user()->id,
                    'created_at'            => now()
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
                            'sale_order_id'     => $exist->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (double) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'discount'          => $discount,
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
            'status_disabled'   => true,
            'approval_by_disabled' => true,
            'users'             => getUserToSelect()
        ]);
        
        $form = serializeform($this->_form);
        return Response::ok('loaded',[
            'data' => $data,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 

    }

    public function needApproval(Request $request){

        try {
            $id = $this->decodeId($request->id);
            $so = SaleOrder::where('id',$id)->first();
            if(empty($so)) return $this->setAlert('error','Gagal','Data tidak ditemukan');

            if(!in_array($so->status,['draft','rejected'])) return $this->setAlert('error','Gagal','Meminta persetujuan hanya bisa dilakukan apabila statusnya adalah Draft atau Ditolak');

            $so->status = 'need_approval';
            $so->save();
            return $this->setAlert('info','Berhasil','Permintaan terkirim, persetujuan pesanan sedang diproses.');
        }catch(Exception $e){
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function saleForm(Request $request){
        $this->allowAccessModule('transaction.item.sale', 'create');
        $id = $this->decodeId($request->id);

        $data = SaleOrder::with(['details'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        $data->kode_so = $data->kode;
        $data->kode_transaksi = generateTransactionCode('DO');
        $data->tanggal_jual = date('Y-m-d');
        $data->status = 'sent';
        $data->dijual_oleh = auth()->user()->name;
        $data->so_id = $data->id;
        unset($data->id);

        $newDetails = [];

        foreach ($data->details as $detail) {
            $saleQty = DB::table('trx_sale_item_details as sid')
                ->join('trx_sale_items as si', 'si.id', '=', 'sid.item_sale_id')
                ->where('si.sale_order_id', $data->so_id)
                ->where('sid.item_id', $detail->item_id)
                ->sum('sid.jumlah');
            $sisa = $detail->jumlah - $saleQty;

            if ($sisa > 0) {
                $detail->jumlah = $sisa;
                $newDetails[] = $detail;
            }
        }
        // dd($data);
        $data->setRelation('details', collect($newDetails));
        $form = $this->getResourceForm('sale');
        injectData($form, [
            'kode_disabled'     => false,
            'contacts'          => getContactToSelect('pelanggan'),
            'status'            => ihandCashierConfigToSelect('sale_item_status', ['invoiced','partial_invoiced','cancelled']),
            'status_readonly'   => true,
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect(),
            'contact_readonly'  => true
        ]);
        $form = serializeform($form);

        return Response::ok('loaded',[
            'data' => $data,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 
    }

    public function createSaleItem(Request $request){
        $this->allowAccessModule('transaction.item.sale', 'create');

        $rules = [
            'addtable'          => 'required|array',
            'addtable.details'  => 'required|array|min:1',
            'kode_transaksi'    => 'required|string',
            'contact_id'        => 'required|numeric',
            'tanggal_jual'      => 'required|string',
            'dijual_oleh'       => 'nullable|string',
            'catatan'           => 'nullable|string',
            'so_id'             => 'required|numeric',

            'addtable.details.*.item_id'    => 'required|integer|exists:items,id|distinct',
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
                'sale_order_id'             => trim($data['so_id']),
                'kode_transaksi'            => trim($data['kode_transaksi']),
                'contact_id'                => trim($data['contact_id']),
                'tanggal_jual'              => trim($data['tanggal_jual']),
                'dijual_oleh'               => trim($data['dijual_oleh']),
                'status'                    => 'draft',
                'catatan'                   => trim(@$data['catatan'])??null,
                'created_by'                => auth()->user()->id,
                'created_at'                => now()
            ];

            $so = SaleOrder::with(['details','details.item'])->where('id', $data['so_id'])->first();
            if(empty($so))return $this->setAlert('error','Gagal', 'SO dengan id '.$data['so_id'].' tidak ditemukan');

            $sent = ItemSale::create($preInsert);

            $perInsertDetails = [];
            $total = 0;
            if(count($data['addtable']['details']) > 0){
                foreach ($data['addtable']['details'] as $key => $d) {
                    $t = (double) (trim($d['harga']) * trim($d['jumlah']));
                    array_push($perInsertDetails,[
                        'item_sale_id'      => $sent->id,
                        'item_id'           => (int) trim($d['item_id']),
                        'unit_id'           => (int) trim($d['unit_id']),
                        'harga'             => (double) trim($d['harga']),
                        'jumlah'            => (int) trim($d['jumlah']),
                        'kedaluarsa'        => !empty(trim(@$d['kedaluarsa'])) ? trim($d['kedaluarsa']) : null,
                        'batch'             => (string) !empty(trim(@$d['batch'])?trim(@$d['batch']):null),
                    ]);

                    $total += $t;
                }
            }
            ItemSaleDetail::insert($perInsertDetails);
            $allFull = true;
            $soDetails = $so->details;
            foreach ($perInsertDetails as $key => $ird) {
                $receivedQty = ItemSaleDetail::whereHas('sale', function ($q) use ($so) {
                    $q->where('sale_order_id', $so->id);
                })
                ->where('item_id', $ird['item_id'])
                ->sum('jumlah');

                $soItem = $soDetails->where('item_id',$ird['item_id'])->first();
                if(empty($soItem)){
                    rollBack();
                    return $this->setAlert('error','Gagal', 'Barang yang anda masukkan tidak ada di pemesanan');
                }
                if ($receivedQty < $soItem->jumlah) {
                    $allFull = false;
                } else if( $receivedQty > $soItem->jumlah){
                    rollBack();
                    return $this->setAlert('error','Gagal', 'Total jumlah pengiriman pada barang '.$soItem->item->nama.' lebih besar sebanyak '.($receivedQty - $soItem->jumlah).' dari jumlah pemesanan, silahkan masukkan jumlah yang sesuai dengan jumlah pesanan.');
                }
            }

            // $stocks = ItemStock::get();
            // foreach ($perInsertDetails as $key => $item) {
            //     $mitem = Item::where('id', $item['item_id'])->first();
            //     $munit = Master::where('id', $item['unit_id'])->first();
            //     $stock = $stocks->where('item_id',$item['item_id'])->where('unit_id',$item['unit_id'])->first();
            //     if(!empty($stock) && $stock->jumlah >= $item['jumlah']) {
            //         $stock->jumlah -= (int) $item['jumlah'];
            //         $stock->tanggal_pembaruan = now();
            //         $stock->save();
            //     } else {
            //         return $this->setAlert('error','Gagal', 'Stok '. $mitem->nama .' tidak tersedia sebanyak '. $item['jumlah'] . ' '. $munit->nama);
            //     }
            // }

            //cek apakah ada barang yang belum dikirim?
            foreach ($soDetails as $d) {
                $receivedQty = ItemSaleDetail::whereHas('sale', function ($q) use ($so) {
                        $q->where('sale_order_id', $so->id);
                    })
                    ->where('item_id', $d->item_id)
                    ->sum('jumlah');

                if ($receivedQty < $d->jumlah) {
                    $allFull = false;
                }
            }

            $so->status = $allFull ? 'sent': 'partial_sent';
            $sent->total_harga = $total;
            $sent->sale_order_id = $so->id;
            $sent->save();
            $so->save();

            commit();
            return $this->setAlert('info','Berhasil','Pengiriman '.$sent->kode.' berhasil disimpan');
        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }
}

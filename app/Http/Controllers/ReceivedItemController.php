<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived,
    ItemReceivedDetail,
    Contact,
    Item,
    Master,
    PurchaseInvoice,
    PurchaseInvoiceDetail,
    PurchaseInvoiceItemReceived,
    ItemStock
};
use Illuminate\Http\Request;
use Exception;
use App\Http\Response;
use App\Objects\ContextMenu;
use Illuminate\Support\Facades\DB;

class ReceivedItemController extends BaseController
{
    private $_form = null;
    public function __construct(){
        $this->setModel(ItemReceived::class)
            ->select(['trx_received_items.*'])
            ->with(['details','contact','details.item','details.unit','createdBy','purchase_order'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_received_items.contact_id')
            ->orderBy('created_at','desc');
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
            'status'            => ihandCashierConfigToSelect('receive_item_status', ['invoiced','partial_invoiced','canceled']),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect(),
            'status_readonly'   => false,
            'contact_readonly'  => false
        ]);

        $this->setInjectDataColumn([
            'status' => ihandCashierConfigToSelect('receive_item_status')
        ]);

        //set default value
        $this->setForm($form,[
            'kode_transaksi' => generateTransactionCode('TR'),
            'tanggal_terima' => now(),
            'diterima_oleh' => auth()->user()->name,
            'status' => 'draft'
        ]);

        //buat faktur
        $createInvoice = new ContextMenu('createinvoice','Buat Faktur');
        $createInvoice->conditions = ['status' => ['received','partial_invoiced']];
        $createInvoice->type = 'form_dialog';
        $createInvoice->apiUrl = route('api.purchase.invoice.createInvoice');
        $createInvoice->icon = 'Receipt';
        $createInvoice->color = '#6D94C5';
        $createInvoice->onClick = 'getFormDialog';
        $createInvoice->formUrl = route('api.purchase.invoice.form');

        $contextMenus = [$createInvoice];
        $this->setContextMenu($contextMenus);
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
                            'kedaluarsa'        => !empty(trim(@$d['kedaluarsa'])) ? trim($d['kedaluarsa']) : null,
                            'batch'             => trim($d['batch'])??null,
                            'sub_total'         => $t
                        ]);

                        $total += $t;
                    }
                }

                $trx->total_harga = $total;
                $trx->save();
                
                ItemReceivedDetail::insert($perInsertDetails);

                //Update stocks
                if(in_array($data['status'],['received','partial_received'])){
                    $stocks = ItemStock::get();
                    foreach ($perInsertDetails as $key => $item) {
                        $preInsertStock = [
                            'item_id'           => $item['item_id'],
                            'unit_id'           => $item['unit_id'],
                            'jumlah'            => $item['jumlah'],
                            'tanggal_pembaruan' => now(),
                            'minimum_stock'     => 10
                        ];
                        $stock = $stocks->where('item_id',$item['item_id'])->where('unit_id',$item['unit_id'])->first();
                        if(empty($stock)) ItemStock::create($preInsertStock);
                        else {
                            $stock->jumlah += (int) $item['jumlah'];
                            $stock->tanggal_pembaruan = now();
                            $stock->save();
                        }
                    }
                }
                commit();
                return $this->setAlert('info','Berhasil','Penerimaan '.$trx->kode_transaksi.' berhasil disimpan');

            } else {
                $this->allowAccessModule('transaction.item.receive', 'update');

                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

                $exist = ItemReceived::with(['details'])->where('id',$data['id'])->first();
                if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');
                begin();

                $oldStatus = $exist->status;
                $newStatus = $data['status'];

                $exist->contact_id      = trim($data['contact_id']);
                $exist->tanggal_terima  = trim($data['tanggal_terima']);
                $exist->diterima_oleh   = trim($data['diterima_oleh']);
                $exist->status          = trim($data['status']);
                $exist->catatan = isset($data['catatan']) ? trim($data['catatan']) : null;
                $exist->updated_by      = auth()->user()->id;
                $exist->updated_at      = now();

                //kurang stock sebanyak data lama
                if (in_array($oldStatus, ['received', 'partial_received'])) {
                    $oldDetails = $exist->details()->get();
                    foreach ($oldDetails as $old) {
                        $stock = ItemStock::where('item_id', $old->item_id)
                            ->where('unit_id', $old->unit_id)
                            ->first();
                        if ($stock) {
                            $stock->jumlah -= (int) $old->jumlah;
                            $stock->save();
                        }
                    }
                }
                
                $exist->details()->delete();

                $perInsertDetails = [];
                $total = 0;
                if(count($data['addtable']['details']) > 0){
                    foreach ($data['addtable']['details'] as $key => $d) {
                        $t = (double) (trim($d['harga']) * (int) trim($d['jumlah']));
                        array_push($perInsertDetails,[
                            'item_received_id'  => $exist->id,
                            'item_id'           => (int) trim($d['item_id']),
                            'unit_id'           => (int) trim($d['unit_id']),
                            'harga'             => (double) trim($d['harga']),
                            'jumlah'            => (int) trim($d['jumlah']),
                            'kedaluarsa'        => !empty(trim(@$d['kedaluarsa'])) ? trim($d['kedaluarsa']) : null,
                            'batch'             => trim($d['batch'])??null,
                            'sub_total'         => $t
                        ]);

                        $total += $t;
                    }
                }

                ItemReceivedDetail::insert($perInsertDetails);
                $exist->update(['total_harga' => $total]);

                //tambah stock sebanyak data baru
                if (in_array($newStatus, ['received', 'partial_received'])) {
                    foreach ($perInsertDetails as $item) {
                        $stock = ItemStock::firstOrNew([
                            'item_id' => $item['item_id'],
                            'unit_id' => $item['unit_id']
                        ]);

                        $stock->jumlah = ($stock->jumlah ?? 0) + (int) $item['jumlah'];
                        $stock->tanggal_pembaruan = now();
                        $stock->minimum_stock = $stock->minimum_stock ?? 10;
                        $stock->save();
                    }
                }

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
            'status'            => ihandCashierConfigToSelect('receive_item_status',['invoiced','partial_invoiced','canceled']),
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

    public function invoiceForm(Request $request){
        $this->allowAccessModule('transaction.invoice.purchase', 'view');
        $id = $this->decodeId($request->id);
        $newInvoice = new \stdClass;
        $newInvoice->kode = generateTransactionCode('INV');
        $item = ItemReceived::select('purchase_order_id','contact_id')->where('id',$id)->first();
        if(empty($item)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        if($item->purchase_order_id != null) $data = ItemReceived::with(['details'])->where('purchase_order_id',$item->purchase_order_id)->get();
        else $data = ItemReceived::with(['details'])->where('id',$id)->get();

        $details = [];
        foreach ($data as $key => $ir) {
            foreach ($ir->details as $detail) {
                // Hitung total jumlah yang sudah difakturkan untuk item ini
                $invoicedQty = DB::table('trx_purchase_invoice_details')
                    ->join('trx_purchase_invoice_item_receiveds', 'trx_purchase_invoice_item_receiveds.purchase_invoice_id', '=', 'trx_purchase_invoice_details.purchase_invoice_id')
                    ->where('trx_purchase_invoice_item_receiveds.item_received_id', $ir->id)
                    ->where('trx_purchase_invoice_details.item_id', $detail->item_id)
                    ->sum('trx_purchase_invoice_details.jumlah');

                $remaining = $detail->jumlah - $invoicedQty;

                // Kalau masih ada sisa belum difakturkan
                if ($remaining > 0) {
                    $detailArr = $detail->toArray();
                    $detailArr['kode_penerimaan'] = $ir->kode_transaksi;
                    $detailArr['jumlah'] = $remaining;
                    $detailArr['diskon_nominal'] = 0;
                    $detailArr['pajak_persen'] = 11;
                    $detailArr['item_received_id'] = $ir->id;
                    $details[] = $detailArr;
                }
            }
        }

        $newInvoice->details = $details;
        $newInvoice->contact_id = $item->contact_id;
        $newInvoice->tanggal = date('Y-m-d');
        $newInvoice->tipe_bayar = 'tempo';
        $form = $this->getResourceForm('purchase_invoice');

        injectData($form, [
            'contacts'          => getContactToSelect('pemasok'),
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

    public function createInvoice(Request $request){
        $this->allowAccessModule('transaction.invoice.purchase', 'view');
        $rules = [
            'addtable'          => 'required|array',
            'addtable.details'  => 'required|array|min:1',
            'kode'              => 'required|string',
            'contact_id'        => 'required|numeric',
            'tanggal'           => 'required|string',
            'tipe_bayar'        => 'required|string',
            'no_referensi'      => 'nullable|string',
            'catatan'           => 'nullable|string',
            'jatuh_tempo'       => 'nullable|string',
            'syarat_bayar'      => 'nullable|string',

            'addtable.details.*.id'                 => 'required|integer|exists:trx_received_item_details,id|distinct',
            'addtable.details.*.item_id'            => 'required|integer|exists:items,id|distinct',
            'addtable.details.*.unit_id'            => 'required|integer|exists:masters,id',
            'addtable.details.*.jumlah'             => 'required|numeric|min:1',
            'addtable.details.*.harga'              => 'required|numeric|min:0',
            'addtable.details.*.diskon_nominal'     => 'required|numeric|min:0',
            'addtable.details.*.pajak_persen'       => 'required|numeric|min:0',
            'addtable.details.*.item_received_id'   => 'required|numeric',
        ];  

        $data = $this->validate($rules);
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

        try {
            begin();

            $preInsert = [
                'kode'                      => trim($data['kode']),
                'contact_id'                => trim($data['contact_id']),
                'tanggal'                   => trim($data['tanggal']),
                'no_referensi'              => trim(@$data['no_referensi'])??null,
                'tipe_bayar'                => trim($data['tipe_bayar']),
                'syarat_bayar'              => trim(@$data['syarat_bayar'])??null,
                'jatuh_tempo'               => !empty(trim(@$data['kedaluarsa'])) ? trim($data['kedaluarsa']) : null,
                'status_pembayaran'         => 'unpaid',
                'status'                    => 'draft',
                'catatan'                   => trim(@$data['catatan'])??null,
                'created_by'                => auth()->user()->id,
                'created_at'                => now()
            ];

            $invoice = PurchaseInvoice::create($preInsert);
            $perInsertDetails = [];
            $grandTotal = 0;
            $totalDiskon = 0;
            $totalPajak = 0;
            $total = 0;
            $pivotInvoice = [];
            if(count($data['addtable']['details']) > 0){
                foreach ($data['addtable']['details'] as $key => $d) {
                    $t = (double) (trim($d['harga']) * trim($d['jumlah']));
                    $diskon = (double) trim(@$d['diskon_nominal'])??0;
                    $pajakPersen = (int) trim(@$d['pajak_persen'])??0;
                    $pajakNominal = (double) ($pajakPersen/100) * ($t - $diskon);

                    array_push($perInsertDetails,[
                        'purchase_invoice_id'   => $invoice->id,
                        'item_received_id'      => (int) trim($d['item_received_id']),
                        'item_id'               => (int) trim($d['item_id']),
                        'unit_id'               => (int) trim($d['unit_id']),
                        'harga'                 => (double) trim($d['harga']),
                        'jumlah'                => (int) trim($d['jumlah']),
                        'diskon_persen'         => (int) trim(@$d['diskon_persen'])??0,
                        'diskon_nominal'        => $diskon,
                        'pajak_persen'          => $pajakPersen,
                        'pajak_nominal'         => $pajakNominal,
                    ]);
                    $totalDiskon += $diskon;
                    $total += $t;
                    $totalPajak += $pajakNominal;
                    $totalFaktur = $t - $diskon + $pajakNominal;    
                    $grandTotal += $totalFaktur;

                    if(!isset($pivotInvoice[$d['item_received_id']])){
                        $pivotInvoice[$d['item_received_id']] = [
                            'purchase_invoice_id'   => $invoice->id,
                            'item_received_id'      => $d['item_received_id'],
                            'total_terfaktur'       => (double) $totalFaktur,
                            'created_at'            => now() 
                        ];
                    } else {
                        $pivotInvoice[$d['item_received_id']]['total_terfaktur'] += (double) $totalFaktur;
                    }
                }
            }

            $invoice->update([
                'subtotal'      => $total,
                'total_diskon'  => $totalDiskon,
                'total_pajak'   => $totalPajak,
                'grand_total'   => ($total - $totalDiskon + $totalPajak),
            ]);

            PurchaseInvoiceDetail::insert($perInsertDetails);
            PurchaseInvoiceItemReceived::insert(array_values($pivotInvoice));

            foreach ($pivotInvoice as $irId => $totalBaru) {
                $pivot = PurchaseInvoiceItemReceived::where('item_received_id', $irId)->first();

                if ($pivot) {
                    $pivot->total_terfaktur += $totalBaru['total_terfaktur'];
                    $pivot->save();
                } else {
                    $pivot = PurchaseInvoiceItemReceived::create([
                        'purchase_invoice_id' => $invoice->id,
                        'item_received_id'    => $irId,
                        'total_terfaktur'     => $totalBaru['total_terfaktur'],
                    ]);
                }

                // --- LOGIKA CEK FAKTUR SEBAGIAN BERDASARKAN DETAIL ---
                $ir = ItemReceived::with('details')->find($irId);
                $allFull = true;

                foreach ($ir->details as $detail) {
                    // hitung total jumlah yang sudah difakturkan utk detail ini
                    $totalInvoicedQty = PurchaseInvoiceDetail::whereHas('invoice', function ($q) use ($irId) {
                            $q->whereHas('itemReceiveds', function ($r) use ($irId) {
                                $r->where('item_received_id', $irId);
                            });
                        })
                        ->where('item_id', $detail->item_id)
                        ->sum('jumlah');

                    // jika masih ada qty yang belum difaktur → belum penuh
                    if ($totalInvoicedQty < $detail->jumlah) {
                        $allFull = false;
                        break;
                    }
                }

                $statusIR = $allFull ? 'invoiced' : 'partial_invoiced';
                $ir->update(['status' => $statusIR]);
            }
            commit();
            return $this->setAlert('info','Berhasil','Faktur '.$invoice->kode.' berhasil disimpan');

        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }
}

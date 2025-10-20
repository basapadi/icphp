<?php

namespace App\Http\Controllers;

use App\Models\{
    PurchaseInvoice,
    PurchaseInvoiceDetail,
    ItemReceived,
    PurchaseInvoiceItemReceived
};
use App\Objects\ContextMenu;
use Illuminate\Http\Request;
use Exception;
use App\Http\Response;

class PurchaseInvoiceController extends BaseController
{
    private $_form = null;
    public function __construct(){
       $this->setModel(PurchaseInvoice::class)
            ->select(['trx_purchase_invoices.*'])
            ->with(['details','details.item','details.unit','contact','createdBy'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_purchase_invoices.contact_id')->orderBy('tanggal','desc');
        $this->setModule('transaction.invoice.purchase');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode'],request('q')??'');
        $this->_form = $this->getResourceForm('purchase_invoice');
        $this->setInjectDataColumn([
            'status' => ihandCashierConfigToSelect('purchase_invoice_status'),
            'status_pembayaran' => ihandCashierConfigToSelect('payment_status')
        ]);

        //buat payment
        $createPayment = new ContextMenu('createPayment','Pembayaran');
        $createPayment->conditions = ['status' => ['draft']];
        $createPayment->type = 'form_dialog';
        $createPayment->apiUrl = route('api.purchase.invoice.createPayment');
        $createPayment->icon = 'BanknoteArrowUp';
        $createPayment->color = '#da8300ff';
        $createPayment->onClick = 'getFormDialog';
        $createPayment->formUrl = route('api.purchase.invoice.paymentForm');

        $contextMenus = [$createPayment];
        $this->setContextMenu($contextMenus);
    }

    public function edit(Request $request,$id){
        $this->allowAccessModule($this->_module, 'edit');
        $id = $this->decodeId($id);
        $data = PurchaseInvoice::with(['contact','details','details.received'])->where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        injectData($this->_form, [
            'kode_disabled'     => true,
            'contacts'          => getContactToSelect('pemasok'),
            'tipe_bayar'        => ihandCashierConfigToSelect('payment_types'),
            'items'             => getItemToSelect(),
            'units'             => getUnitToSelect('UNIT'),
            'taxes'             => getTaxToSelect()
        ]);
        
        $form = serializeform($this->_form);
        return Response::ok('loaded',[
            'data' => $data,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]); 

    }

    public function store(Request $request){
        $this->allowAccessModule($this->_module, 'update');
        $rules = [
            'addtable'          => 'required|array',
            'addtable.details'  => 'required|array|min:1',
            'kode'              => 'required|string',
            'contact_id'        => 'required|numeric',
            'tanggal'           => 'required|string',
            'tipe_bayar'        => 'required|string',
            'catatan'           => 'nullable|string',
            'no_referensi'      => 'nullable|string',
            'syarat_bayar'      => 'nullable|string',
            'id'                => 'nullable|numeric',

            'addtable.details.*.id'                 => 'required|integer|exists:trx_purchase_invoice_details,id|distinct',
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

        begin();
        try {
            $exist = PurchaseInvoice::with(['details'])->where('id',$data['id'])->first();
            if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');
            
            if(in_array($exist->status,['posted','void'])) return $this->setAlert('error','Galat!','Data sudah tidak dapat diubah karena status sudah '. config('ihandcashier.purchase_invoice_status')[$exist->status]['label']);

            $exist->tanggal             = trim($data['tanggal']);
            $exist->no_referensi        = trim(@$data['no_referensi'])??'';
            $exist->tipe_bayar          = trim($data['tipe_bayar']);
            $exist->syarat_bayar        = trim(@$data['syarat_bayar'])??null;
            $exist->jatuh_tempo         = trim(@$data['jatuh_tempo'])??null;
            $exist->catatan             = trim(@$data['catatan'])??'';


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
                        'purchase_invoice_id'   => $exist->id,
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
                            'purchase_invoice_id'   => $exist->id,
                            'item_received_id'      => $d['item_received_id'],
                            'total_terfaktur'       => (double) $totalFaktur,
                            'created_at'            => now() 
                        ];
                    } else {
                        $pivotInvoice[$d['item_received_id']]['total_terfaktur'] += (double) $totalFaktur;
                    }
                }
            }

            $exist->update([
                'subtotal'      => $total,
                'total_diskon'  => $totalDiskon,
                'total_pajak'   => $totalPajak,
                'grand_total'   => ($total - $totalDiskon + $totalPajak),
            ]);

            //delete relation
            $exist->details()->delete();
            PurchaseInvoiceItemReceived::where('purchase_invoice_id',$exist->id)->delete();

            PurchaseInvoiceDetail::insert($perInsertDetails);
            PurchaseInvoiceItemReceived::insert(array_values($pivotInvoice));

            foreach ($pivotInvoice as $irId => $totalBaru) {
                $pivot = PurchaseInvoiceItemReceived::where('item_received_id', $irId)->first();

                if ($pivot) {
                    $pivot->total_terfaktur += $totalBaru['total_terfaktur'];
                    $pivot->save();
                } else {
                    $pivot = PurchaseInvoiceItemReceived::create([
                        'purchase_invoice_id' => $exist->id,
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
            return $this->setAlert('info','Berhasil','Faktur '.$exist->kode.' berhasil diubah');

        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function paymentForm(Request $request){
        $this->allowAccessModule('transaction.invoice.purchase.payment', 'create');

        $id = $this->decodeId($request->id);
        $newPayment = new \stdClass;
        $newPayment->kode = generateTransactionCode('PAY');

        $item = PurchaseInvoice::select('id')->where('id',$id)->first();
        if(empty($item)) return $this->setAlert('error','Galat!','Data tidak ditemukan!.');

        $form = $this->getResourceForm('purchase_invoice_payment');

        injectData($form, [
            'metode_bayar'        => ihandCashierConfigToSelect('payment_methods.receive'),
        ]);
        $form = serializeform($form);
        return Response::ok('loaded',[
            'data' => $newPayment,
            'dialog' => $form['dialog'],
            'sections' => $form['sections']
        ]);
    }
}
<?php

namespace App\Http\Controllers\Tasks;

use App\Models\Item;
use Exception;
use Illuminate\Http\Request;
use App\Http\Response;
use App\Http\Controllers\BaseController;
use App\Objects\ContextMenu;
use App\Models\PurchaseOrder;

class ApprovalPurchaseOrderController extends BaseController
{
    public function __construct(){
        $this->setModel(PurchaseOrder::class)
            ->where('approval_by', auth()->user()->id)
            ->where('trx_purchase_orders.status','need_approval')
            ->select(['trx_purchase_orders.*', 'trx_purchase_orders.status as po_status'])
            ->with(['details','details.item','details.unit','contact','createdBy','approvalBy','receiveds'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_purchase_orders.contact_id')->orderBy('tanggal','desc');
        $this->setModule('transaction.order.purchase');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal',
            'multipleSelect' => false
        ]);
        $this->setExceptContextMenu(['create','edit','delete']);
        $this->setFilterColumnsLike(['contacts.nama','kode'],request('q')??'');

        //approved
        $approved = new ContextMenu('approved','Setujui');
        $approved->conditions = ['status' => ['need_approval']];
        $approved->type = 'confirm';
        $approved->apiUrl = route('api.task.approval-purchase-order.approval').'?status=approved';
        $approved->icon = 'BadgeCheck';
        $approved->color = '#388E3C';
        $approved->onClick = 'confirmPopup';
        $approved->title = 'Setujui Permintaan';
        $approved->message = 'Apakah anda yakin menyetujui permintaan ini?.';
        $approved->forms = [
            [
                'name' => 'approval_note',
                'type' => 'textarea',
                'label' => 'Komentar',
                'required' => true,
                "hint" => "Masukkan Catatan"
            ]
        ];

        //rejected
        $rejected = new ContextMenu('rejected','Tolak');
        $rejected->conditions = ['status' => ['need_approval']];
        $rejected->type = 'confirm';
        $rejected->apiUrl = route('api.task.approval-purchase-order.approval').'?status=rejected';
        $rejected->icon = 'BadgeX';
        $rejected->color = '#BF360C';
        $rejected->onClick = 'confirmPopup';
        $rejected->title = 'Tolak Permintaan';
        $rejected->message = 'Apakah anda yakin menolak permintaan ini?.';
        $rejected->forms = [
            [
                'name' => 'approval_note',
                'type' => 'textarea',
                'label' => 'Komentar',
                'required' => true,
                "hint" => "Masukkan Catatan"
            ]
        ];

        $contextMenus = [$approved,$rejected];
        $this->setContextMenu($contextMenus);
    }

    public function approval(Request $request){
        try {
            $id = $this->decodeId($request->id);
            $po = PurchaseOrder::where('id',$id)->first();
            if(empty($po)) return $this->setAlert('error','Gagal','Data tidak ditemukan');
            if(!in_array($po->status,['need_approval'])) return $this->setAlert('error','Gagal','Aksi ini hanya bisa dilakukan apabila statusnya adalah Approval Diproses');

            if(!isset($request->status)) return $this->setAlert('error','Gagal','Tidak dapat melakukan approval untuk saat ini karena status tidak didefenisikan');
            if(!in_array($request->status,['approved','rejected'])) return $this->setAlert('error','Gagal','Status yang dikirim tidak dapat diproses');

            $po->status = trim($request->status);
            $po->approval_status = trim($request->status);
            $po->approved_at = now();
            $po->approval_note = @$request->approval_note??null;
            $po->save();
            return $this->setAlert('info','Berhasil','Data berhasil dperbarui, periksa hasilnya di grid Pesanan Pembelian.');
        }catch(Exception $e){
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }
}
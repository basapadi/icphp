<?php

namespace App\Http\Controllers\Tasks;

use App\Models\Item;
use Exception;
use Illuminate\Http\Request;
use App\Http\Response;
use App\Http\Controllers\BaseController;
use App\Objects\ContextMenu;
use App\Models\SaleOrder;

class ApprovalSaleOrderController extends BaseController
{
    public function __construct(){
        $this->setModel(SaleOrder::class)
            ->where('approval_by', auth()->user()->id)
            ->whereIn('trx_sale_orders.status',['need_approval','approved','rejected'])
            ->select(['trx_sale_orders.*', 'trx_sale_orders.status as po_status'])
            ->with(['details','details.item','details.unit','contact','createdBy','approvalBy','sales'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_sale_orders.contact_id')->orderBy('tanggal','desc');
        $this->setModule('task.sale.order');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal',
            'multipleSelect' => false
        ]);
        $this->setExceptContextMenu(['create','edit','delete']);
        $this->setFilterColumnsLike(['contacts.nama','kode'],request('q')??'');

        $this->setInjectDataColumn([
            'approval_status' => ihandCashierConfigToSelect('sale_approval_status')
        ]);

        //approved
        $approved = new ContextMenu('approved','Setujui');
        $approved->conditions = ['approval_status' => ['pending','rejected']];
        $approved->type = 'confirm';
        $approved->apiUrl = route('api.task.approval-sale-order.approval').'?status=approved';
        $approved->icon = 'BadgeCheck';
        $approved->color = '#388E3C';
        $approved->onClick = 'confirmPopup';
        $approved->title = 'Setujui Permintaan';
        $approved->message = 'Apakah anda yakin menyetujui dan mengkonfirmasi pesanan penjualan ini?. <br> <span class="text-xs text-orange-600">* Pastikan stok barang tersedia.</span>';
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
        $rejected->conditions = ['approval_status' => ['pending','approved']];
        $rejected->type = 'confirm';
        $rejected->apiUrl = route('api.task.approval-sale-order.approval').'?status=rejected';
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
            $so = SaleOrder::where('id',$id)->first();
            if(empty($so)) return $this->setAlert('error','Gagal','Data tidak ditemukan');
            if(!in_array($so->status,['need_approval','rejected','approved'])) return $this->setAlert('error','Gagal','Aksi ini hanya bisa dilakukan apabila statusnya adalah Approval Diproses,Ditolak atau Disetujui');

            if(!isset($request->status)) return $this->setAlert('error','Gagal','Tidak dapat melakukan approval untuk saat ini karena status tidak didefenisikan');
            if(!in_array($request->status,['approved','rejected'])) return $this->setAlert('error','Gagal','Status yang dikirim tidak dapat diproses');

            $so->status = trim($request->status);
            if ($request->status == 'approved') $so->status = 'confirmed';
            $so->approval_status = trim($request->status);
            $so->approved_at = now();
            $so->approval_note = @$request->approval_note??null;
            $so->save();
            return $this->setAlert('info','Berhasil','Data berhasil dperbarui, periksa hasilnya di grid Pesanan Penjualan.');
        }catch(Exception $e){
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }
}

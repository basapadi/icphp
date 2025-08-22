<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived
};
use Illuminate\Http\Request;

class ReceivedController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemReceived::class)
            ->with(['details','contact'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_received_items.contact_id');
        $this->setModule('transaction.receive');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'detail', 'label'=> 'Detil', 'align' => 'center','styles' => 'width:50px; item-align: center;'],
            ['value' => 'kode_transaksi', 'label'=> 'Kode Trx', 'align' => 'left'],
            ['value' => 'contact__nama', 'label'=> 'Pemasok', 'align' => 'left'],
            ['value' => 'total_harga_formatted', 'label'=> 'Total Harga', 'align' => 'right', 'class' => 'font-mono font-bold'],
            // ['value' => 'potongan_harga', 'label'=> 'Potongan Harga', 'align' => 'right'],
            ['value' => 'tanggal_terima_formatted', 'label'=> 'Tanggal Terima', 'align' => 'left'],
            // ['value' => 'diterima_oleh', 'label'=> 'Diterima Oleh', 'align' => 'left'],
            ['value' => 'status_pembayaran_label', 'label'=> 'Status Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'tipe_pembayaran_label', 'label'=> 'Tipe Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'metode_pembayaran_label', 'label'=> 'Metode Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'syarat_pembayaran', 'label'=> 'Syarat', 'align' => 'left','class' => 'font-mono font-bold'],
            ['value' => 'catatan', 'label'=> 'Catatan', 'align' => 'left'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [
                $this->allowAccess('view'),
                $this->allowAccess('edit'),
                $this->allowAccess('delete')
            ]]
        ]);
        $this->setGridProperties([
            'filterDateRange' => true
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');
    }
}

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
            ['value' => 'kode_transaksi', 'label'=> 'Kode Trx', 'align' => 'left'],
            ['value' => 'contact__nama', 'label'=> 'Pemasok', 'align' => 'left'],
            ['value' => 'total_harga', 'label'=> 'Total Harga', 'align' => 'right'],
            // ['value' => 'potongan_harga', 'label'=> 'Potongan Harga', 'align' => 'right'],
            ['value' => 'tanggal_terima_formatted', 'label'=> 'Tanggal Terima', 'align' => 'left'],
            // ['value' => 'diterima_oleh', 'label'=> 'Diterima Oleh', 'align' => 'left'],
            ['value' => 'status_pembayaran_label', 'label'=> 'Status Bayar', 'align' => 'left'],
            ['value' => 'tipe_pembayaran_label', 'label'=> 'Tipe Bayar', 'align' => 'left'],
            ['value' => 'metode_pembayaran_label', 'label'=> 'Metode Bayar', 'align' => 'left'],
            ['value' => 'syarat_pembayaran', 'label'=> 'Syarat', 'align' => 'left'],
            ['value' => 'catatan', 'label'=> 'Catatan', 'align' => 'left'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]]
        ]);
        $this->setGridProperties([
            'filterDateRange' => true
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');
    }
}

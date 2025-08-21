<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceivedDetail
};
use Illuminate\Http\Request;

class ReceivedController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemReceivedDetail::class)->with('details');
        $this->setModule('transaction.receive');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'kode_transaksi', 'label'=> 'Kode Trx', 'align' => 'left'],
            ['value' => 'contact__nama', 'label'=> 'Pemasok', 'align' => 'left'],
            ['value' => 'total_harga', 'label'=> 'Total Harga', 'align' => 'right'],
            ['value' => 'potongan_harga', 'label'=> 'Potongan Harga', 'align' => 'right'],
            ['value' => 'tanggal_terima', 'label'=> 'Tanggal Terima', 'align' => 'left'],
            ['value' => 'diterima_oleh', 'label'=> 'Diterima Oleh', 'align' => 'left'],
            ['value' => 'status_pembayaran', 'label'=> 'Status Bayar', 'align' => 'left'],
            ['value' => 'metode_pembayaran', 'label'=> 'Metode Bayar', 'align' => 'left'],
            ['value' => 'syarat_pembayaran', 'label'=> 'Syarat', 'align' => 'left'],
            ['value' => 'catatan', 'label'=> 'Catatan', 'align' => 'left'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]]
        ]);
        $this->setFilterColumnsLike(['kode','nama'],request('q')??'');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemReceived
};
use Illuminate\Http\Request;

class ReceivedItemController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemReceived::class)
            ->select('trx_received_items.*')
            ->with(['details','contact','payments'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_received_items.contact_id')->orderBy('tanggal_terima','desc');
        $this->setModule('transaction.receive');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'actions', 'label'=> 'Aksi', 'align' => 'left','options' => [
                'detail',
                $this->allowAccess('edit'),
                $this->allowAccess('delete')
            ]],
            ['value' => 'kode_transaksi', 'label'=> 'Kode Trx', 'align' => 'left','option_filter' => true],
            ['value' => 'contact__nama', 'label'=> 'Pemasok', 'align' => 'left','option_filter' => true],
            ['value' => 'total_harga_formatted', 'label'=> 'Total Harga', 'align' => 'right', 'class' => 'font-mono font-bold'],
            // ['value' => 'potongan_harga', 'label'=> 'Potongan Harga', 'align' => 'right'],
            ['value' => 'tanggal_terima_formatted', 'label'=> 'Tanggal Terima', 'align' => 'left'],
            ['value' => 'tanggal_terima', 'label'=> 'Tanggal Terima', 'align' => 'left','option_filter' => true,'show' => false, 'type' => 'date_range'],
            // ['value' => 'diterima_oleh', 'label'=> 'Diterima Oleh', 'align' => 'left'],
            ['value' => 'status_pembayaran_label', 'label'=> 'Status Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'status_pembayaran', 'label'=> 'Status Bayar', 'align' => 'left','show' => false, 'option_filter' => true, 'type'=> 'select', 'options' => ihandCashierConfigToOptions('payment_status')],
            ['value' => 'tipe_pembayaran_label', 'label'=> 'Tipe Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'tipe_pembayaran', 'label'=> 'Tipe Bayar','type' => 'select','show' => false, 'option_filter' => true,'options' => ihandCashierConfigToOptions('payment_types')],
            ['value' => 'metode_pembayaran_label', 'label'=> 'Metode Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'metode_pembayaran', 'label'=> 'Metode Bayar', 'type' => 'select','show' => false, 'option_filter' => true,'options' => ihandCashierConfigToOptions('payment_methods.receive')],
            ['value' => 'syarat_pembayaran', 'label'=> 'Syarat', 'align' => 'left','class' => 'font-mono text-red-500'],
            ['value' => 'tanggal_jatuh_tempo', 'label'=> 'Jatuh Tempo', 'align' => 'left'],
            ['value' => 'catatan', 'label'=> 'Catatan', 'align' => 'left'],
        ]);
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal_terima'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');
    }
}

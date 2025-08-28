<?php

namespace App\Http\Controllers;

use App\Models\{
    ItemSale
};
use Illuminate\Http\Request;

class SaleItemController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemSale::class)
            ->select('trx_sale_items.*')
            ->with(['details','contact','payments'])
            ->leftJoin('contacts', 'contacts.id', '=', 'trx_sale_items.contact_id')->orderBy('tanggal_jual','desc');
        $this->setModule('transaction.sale');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'actions', 'label'=> 'Aksi', 'align' => 'left','options' => [
                'detail',
                $this->allowAccess('edit'),
                $this->allowAccess('delete')
            ]], 
            ['value' => 'kode_transaksi', 'label'=> 'Kode Trx', 'align' => 'left','option_filter' => true],
            ['value' => 'contact__nama', 'label'=> 'Pelanggan', 'align' => 'left','option_filter' => true],
            ['value' => 'total_harga_formatted', 'label'=> 'Total Harga', 'align' => 'right', 'class' => 'font-mono font-bold'],
            // ['value' => 'potongan_harga', 'label'=> 'Potongan Harga', 'align' => 'right'],
            ['value' => 'tanggal_jual_formatted', 'label'=> 'Tanggal Jual', 'align' => 'left'],
            ['value' => 'tanggal_jual', 'label'=> 'Tanggal Jual', 'align' => 'left','option_filter' => true,'show' => false, 'type' => 'date_range'],
            ['value' => 'status_pembayaran_label', 'label'=> 'Status Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'status_pembayaran', 'label'=> 'Status Bayar', 'align' => 'left','show' => false, 'option_filter' => true, 'type'=> 'select', 'options' => ihandCashierConfigToOptions('payment_status')],
            ['value' => 'tipe_pembayaran_label', 'label'=> 'Tipe Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'tipe_pembayaran', 'label'=> 'Tipe Bayar','type' => 'select','show' => false, 'option_filter' => true,'options' => ihandCashierConfigToOptions('payment_types')],
            ['value' => 'metode_pembayaran_label', 'label'=> 'Metode Bayar', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'metode_pembayaran', 'label'=> 'Metode Bayar', 'type' => 'select','show' => false, 'option_filter' => true,'options' => ihandCashierConfigToOptions('payment_methods.sale')],
            ['value' => 'syarat_pembayaran', 'label'=> 'Syarat', 'align' => 'left','class' => 'font-mono text-red-500'],
            ['value' => 'tanggal_jatuh_tempo', 'label'=> 'Jatuh Tempo', 'align' => 'left'],
            ['value' => 'catatan', 'label'=> 'Catatan', 'align' => 'left']
        ]);
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal_jual'
        ]);
        $this->setFilterColumnsLike(['contacts.nama','kode_transaksi'],request('q')??'');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemSale,
    ItemStock
};
use Illuminate\Http\Request;
use App\Objects\{DataArray,MergeData};

class ReceivableController extends BaseController
{
    public function __construct(){
        $this->setModule('finance.receivable');
        $exceptStatus = ['paid','canceled','refunded'];
        $mergeData = new MergeData();
        $mergeData->attribute = 'details';
        $mergeData->class = ItemSale::class;
        $mergeData->key = 'contact_id';
        $mergeData->relations = [];
        $mergeData->whereNotIn = new DataArray('status_pembayaran',$exceptStatus );
        $this->setMergeData($mergeData);
        $query = $this->setModel(ItemSale::class)
            ->select(
                'contact_id',
                $this->raw('SUM(total_harga) as total_harga'),
                $this->raw('COALESCE(SUM(trx_sale_payment_items.jumlah),0) as terbayar'),
                $this->raw('SUM(trx_sale_items.total_harga) - COALESCE(SUM(trx_sale_payment_items.jumlah),0) as sisa_bayar')
            )
            ->whereNotIn('status_pembayaran',$exceptStatus)
            ->leftJoin('trx_sale_payment_items', 'trx_sale_payment_items.trx_sale_item_id', '=', 'trx_sale_items.id')
            ->havingRaw('SUM(trx_sale_items.total_harga) - COALESCE(SUM(trx_sale_payment_items.jumlah),0) > 0');
        
        $this->setQuery($query)
            ->with(['contact'])
            ->groupBy('contact_id');
        
        $this->setColumns([
            ['value' => 'actions', 'label'=> 'Aksi', 'align' => 'left','options' => [ 'detail' ] ],
            ['value' => 'contact__nama', 'label'=> 'Pemasok', 'align' => 'left','option_filter' => true],
            ['value' => 'total_harga_formatted', 'label'=> 'Total Transaksi', 'align' => 'right', 'class' => 'font-mono'],
            ['value' => 'terbayar_formatted', 'label'=> 'Total Dibayar', 'align' => 'right','class' => 'font-mono'],
            ['value' => 'sisa_bayar_formatted', 'label'=> 'Sisa Bayar', 'align' => 'right','class' => 'font-mono'],
        ]);

        $this->setDetailSchema([
            'main' => [
                'type'=> 'object',
                'title' => 'Transaksi',
                'fields' => [
                    'contact__nama' => ['label' => 'Nama Pelanggan'],
                    'contact__email' => ['label' => 'Email'],
                    'contact__alamat' => ['label' => 'Alamat'],
                    'contact__telepon' => ['label' => 'Telepon'],
                    'total_harga_formatted' => ['label' => 'Total Harga','class' => 'font-mono'],
                    'terbayar_formatted' => ['label' => 'Total Dibayar','class' => 'font-mono'],
                    'sisa_bayar_formatted' => ['label' => 'Sisa Bayar', 'class' => 'font-mono font-bold'],
                ]
            ],
            'details' => [
                'type' => 'array',
                'title' => 'Detail Transaksi',
                'fields' => [
                    'no' => ['label' => 'No', 'style' => 'width: 20px'],
                    'kode_transaksi' => ['label' => 'Kode Transaksi'],
                    'tanggal_jual_formatted' => ['label' => 'Tanggal Jual'],
                    'syarat_pembayaran' => ['label' => 'Syarat'],
                    'tanggal_jatuh_tempo' => ['label' => 'Jatuh Tempo'],
                    'metode_pembayaran_label' => ['label' => 'Metode Bayar'],
                    'tipe_pembayaran_label' => ['label' => 'Tipe Bayar'],
                    'metode_pembayaran_label' => ['label' => 'Metode Bayar'],
                    'status_pembayaran_label' => ['label' => 'Status Bayar'],
                    'total_harga_formatted' => ['label' => 'Total Harga','class' => 'font-mono font-bold text-right'],
                ]
            ]
        ]);
    }
}

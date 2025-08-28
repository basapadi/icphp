<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Btx\Http\Response;
use Btx\Http\Libraries\ApiResponse;
class DashboardController extends BaseController
{
    public function __construct(){
        $this->setModel(Item::class);
        $this->setModule('master.item');
    }

    public function data(Request $request){
        $this->allowAccessModule('dashboard','view');
        
        //TODO:: ganti value cards dan grids dibawah dengan real value
        $cards = [
            [
                'title' => 'Total Penjualan Bulan '.date('M'), //total nilai penjualan barang periode bulan berjalan (bruto)
                'value' => 'Rp.157.002.000',
                'change' => '+2.05%',
                'trend' => 'up',
                'icon' => 'DollarSign'
            ],
            [
                'title' => 'Total Laba Bersih Penjualan '.date('M'), //total nilai netto (harga jual - harga beli) dari semua brang terjual bulan berjalan
                'value' => 'Rp.57.579.000',
                'change' => '+1.05%',
                'trend' => 'up',
                'icon' => 'DollarSign'
            ],
            // [
            //     'title' => 'Total Pengeluaran Bulan '.date('M'), //ini diskip karena belum ada modul transaksi pengeluaran
            //     'value' => 'Rp.0',
            //     'change' => '0%',
            //     'trend' => '-',
            //     'icon' => 'DollarSign'
            // ],
            [
                'title' => 'Total Piutang Bulan '.date('M'), //ini diskip, tunggu modul piutang selesai kita buat
                'value' => 'Rp.25.970.000',
                'change' => '+1.09%',
                'trend' => 'up',
                'icon' => 'DollarSign'
            ],
            [
                'title' => 'Total Hutang Bulan '.date('M'), // ini diskip, tunggu selesai modul hutang kita buat
                'value' => 'Rp.15.240.000',
                'change' => '+1.09%',
                'trend' => 'up',
                'icon' => 'DollarSign'
            ]
        ];

        $grids = [
            'receive_duedate' => [ //listkan semua penerimaan barang yang type pembayarannya adalah tempo (Hutang) 2 minggu kedepan
                ['kode_transaksi' => 'Kode Transaksi','contact_name' => 'Nama Kontak', 'tanggal_duedate' => '2025/08/26', 'syarat' => '2/15 N30']
            ],
            'sale_duedate' => [ //listkan semua penjualan barang yang type pembayarannya adalah tempo (Hutang) 2 minggu kedepan
                ['kode_transaksi' => 'Kode Transaksi','contact_name' => 'Nama Kontak', 'tanggal_duedate' => '2025/08/26', 'syarat' => '2/15 N30']
            ],
            'top_products' => [ //listkan 10 barang paling laku selama bulan ini order by jumlah penjualan desc
                ['nama_barang' => 'Nama Barang', 'revenue' => '15.000.000']
            ],
            'minimum_stocks' => [ //listkan 10 barang dengan stok dibawah minimum_stock (lihat di tabel item_stocks) order by jumlah stock asc
                ['nama_barang' => 'Nama Barang', 'stock' => '7']
            ]
        ];
        return Response::ok('Loaded', [
            'cards' => $cards,
            'grids' => $grids
        ]);
    }
}

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
        
        //TODO:: ganti value dibawah dengan real value
        $cards = [
            [
                'title' => 'Total Penjualan Bulan '.date('M'),
                'value' => 'Rp.157.002.000',
                'change' => '+2.05%',
                'trend' => 'up',
                'icon' => 'DollarSign'
            ],
            [
                'title' => 'Total Pengeluaran Bulan '.date('M'),
                'value' => 'Rp.57.002.000',
                'change' => '-2.05%',
                'trend' => 'down',
                'icon' => 'DollarSign'
            ],
            [
                'title' => 'Total Piutang Bulan '.date('M'),
                'value' => 'Rp.25.970.000',
                'change' => '+1.09%',
                'trend' => 'up',
                'icon' => 'DollarSign'
            ],
            [
                'title' => 'Total Hutang Bulan '.date('M'),
                'value' => 'Rp.15.240.000',
                'change' => '+1.09%',
                'trend' => 'up',
                'icon' => 'DollarSign'
            ]
        ];
        return Response::ok('Loaded', [
            'cards' => $cards,
        ]);
    }
}

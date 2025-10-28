<?php

namespace App\Http\Controllers;

use App\Models\{
    PurchasePayment
};
use App\Objects\ContextMenu;
use Illuminate\Http\Request;
use Exception;
use App\Http\Response;

class PurchasePaymentController extends BaseController
{
    private $_form = null;
    public function __construct(){
        $this->setModel(PurchasePayment::class)
            ->with(['invoice','creator','updater'])->orderBy('id','desc');
        $this->setModule('transaction.payment.purchase');

        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'tanggal'
        ]);

        $this->setFilterColumnsLike(['kode','catatan'],request('q')??'');
        
        $this->setInjectDataColumn([
            'payment_methods' => ihandCashierConfigToSelect('payment_methods.receive')
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemSale,
    ItemStock
};
use Illuminate\Http\Request;

class ReceivableController extends BaseController
{
    public function __construct(){
        $this->setModel(ItemSale::class);
        $this->setModule('finance.payable');
        $this->setColumns([]);
    }
}

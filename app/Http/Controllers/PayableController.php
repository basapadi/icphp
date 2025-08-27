<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemReceived,
    ItemStock
};
use Illuminate\Http\Request;

class PayableController extends BaseController
{
    public function __construct(){
       $this->setModel(ItemReceived::class);
       $this->setModule('finance.payable');
       $this->setColumns([]);
    }
}

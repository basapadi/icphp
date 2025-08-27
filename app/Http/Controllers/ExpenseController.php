<?php

namespace App\Http\Controllers;

use App\Models\{
    Item,
    ItemStock
};
use Illuminate\Http\Request;

class ExpenseController extends BaseController
{
    public function __construct(){
       $this->setModule('finance.expense');
    }
}

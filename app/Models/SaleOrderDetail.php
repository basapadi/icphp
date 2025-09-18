<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrderDetail extends BaseModel
{
    public $table = 'trx_sale_order_details';

    protected $fillable = [
        'sale_order_id',
        'item_id',
        'unit_id',
        'unit_price',
        'discount',
        'qty',
        'sub_total'
    ];
}

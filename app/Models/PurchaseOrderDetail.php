<?php

namespace App\Models;

class PurchaseOrderDetail extends BaseModel
{
    public $table = 'trx_purchase_order_details';

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'unit_id',
        'unit_price',
        'qty',
        'sub_total'
    ];
}

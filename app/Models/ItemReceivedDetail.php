<?php

namespace App\Models;

class ItemReceivedDetail extends BaseModel
{
    public $timestamps = false;
    public $table = 'trx_received_item_details';
    protected $fillable = [
        'item_received_id',
        'item_id',
        'jumlah',
        'unit_id'
    ];

}

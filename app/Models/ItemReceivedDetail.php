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

    public function details(){
        return $this->hasMany(ItemReceivedDetail::class,'item_received_id','id');
    }
}

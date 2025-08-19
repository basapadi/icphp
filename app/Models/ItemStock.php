<?php

namespace App\Models;
class ItemStock extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'unit_id',
        'jumlah',
        'tanggal_pembaruan'
    ];
}

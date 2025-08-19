<?php

namespace App\Models;
class ItemPrice extends BaseModel
{
    protected $fillable = [
        'item_id',
        'harga',
        'tanggal_berlaku'
    ];
}

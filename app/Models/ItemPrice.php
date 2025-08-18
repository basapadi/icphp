<?php

namespace App\Models;

use Btx\Query\Model;

class ItemPrice extends Model
{
    protected $fillable = [
        'item_id',
        'harga',
        'tanggal_berlaku'
    ];
}

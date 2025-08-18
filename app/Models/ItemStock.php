<?php

namespace App\Models;

use Btx\Query\Model;

class ItemStock extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'unit_id',
        'jumlah',
        'tanggal_pembaruan'
    ];
}

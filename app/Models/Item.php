<?php

namespace App\Models;

use Btx\Query\Model;

class Item extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'nama',
        'gambar',
        'kategori',
        'aktif'
    ];
}

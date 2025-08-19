<?php

namespace App\Models;
class Item extends BaseModel
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

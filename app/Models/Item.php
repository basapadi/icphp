<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

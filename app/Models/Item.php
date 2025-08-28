<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Item extends BaseModel
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'barcode',
        'nama',
        'gambar',
        'kategori',
        'status'
    ];
}

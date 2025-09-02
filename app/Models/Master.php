<?php

namespace App\Models;

use Exception;

class Master extends BaseModel
{
    public $timestamps = false;
    protected $fillable = [
        'type',
        'kode',
        'nama',
        'status',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'object',
    ];

    public function itemReceivedDetails()
    {
        return $this->hasMany(ItemReceivedDetail::class,'unit_id');
    }

    public function itemSaleDetails()
    {
        return $this->hasMany(ItemSaleDetail::class,'unit_id');
    }

    public function itemStocks()
    {
        return $this->hasMany(ItemStock::class,'unit_id');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            if ($data->itemReceivedDetails()->exists()) {
                throw new Exception('Tidak dapat menghapus data ini karena sudah digunakan di data penerimaan barang');
            }

            if ($data->itemSaleDetails()->exists()) {
                throw new Exception('Tidak dapat menghapus data ini karena sudah digunakan di data penjualan barang');
            }
        });
    }
}

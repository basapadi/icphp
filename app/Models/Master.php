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

    public function received_item_details()
    {
        return $this->hasMany(ItemReceivedDetail::class,'unit_id');
    }

    public function sale_item_details()
    {
        return $this->hasMany(ItemSaleDetail::class,'unit_id');
    }

    protected static function booted()
    {
        static::deleting(function ($satuan) {
            if ($satuan->received_item_details()->exists()) {
                throw new Exception('Tidak dapat menghapus data ini karena sudah digunakan di penerimaan barang');
            }

            if ($satuan->sale_item_details()->exists()) {
                throw new Exception('Tidak dapat menghapus data ini karena sudah digunakan di penjualan barang');
            }
        });
    }
}

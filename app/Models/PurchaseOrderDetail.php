<?php

namespace App\Models;

class PurchaseOrderDetail extends BaseModel
{
    public $table = 'trx_purchase_order_details';
    protected $appends = ['total_harga','harga_formatted','total_harga_formatted'];
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'unit_id',
        'harga',
        'jumlah',
        'sub_total'
    ];

    public function item(){
       return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function unit(){
       return $this->belongsTo(Master::class, 'unit_id', 'id');
    }

    public function getTotalHargaFormattedAttribute()
    {
        return currency($this->sub_total);
    }

    public function getHargaFormattedAttribute()
    {
        return currency($this->harga);
    }

    public function getTotalHargaAttribute()
    {
        return (double) $this->jumlah * (double) $this->harga;
    }
}

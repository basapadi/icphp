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
        'qty',
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
        return 'IDR '.number_format($this->sub_total, 0, ',', '.');
    }

    public function getHargaFormattedAttribute()
    {
        return 'IDR '.number_format($this->harga, 0, ',', '.');
    }

    public function getTotalHargaAttribute()
    {
        return (double) $this->qty * (double) $this->harga;
    }
}

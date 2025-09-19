<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrderDetail extends BaseModel
{
    public $table = 'trx_sale_order_details';
    protected $appends = ['total_harga','harga_formatted','total_harga_formatted','discount_formatted'];
    protected $fillable = [
        'sale_order_id',
        'item_id',
        'unit_id',
        'harga',
        'discount',
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
        return 'IDR '.number_format($this->sub_total, 0, ',', '.');
    }

    public function getHargaFormattedAttribute()
    {
        return 'IDR '.number_format($this->harga, 0, ',', '.');
    }

    public function getTotalHargaAttribute()
    {
        return (double) $this->jumlah * (double) $this->harga;
    }

    public function getDiscountFormattedAttribute()
    {
        return 'IDR '.number_format($this->discount, 0, ',', '.');
    }
}

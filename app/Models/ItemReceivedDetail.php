<?php

namespace App\Models;

class ItemReceivedDetail extends BaseModel
{
    public $timestamps = false;
    public $table = 'trx_received_item_details';
    protected $appends = ['total_harga'];
    protected $fillable = [
        'item_received_id',
        'item_id',
        'jumlah',
        'harga',
        'unit_id',
        'keladuarsa',
        'batch'
    ];

    public function getTotalHargaAttribute()
    {
        return (double) $this->jumlah * (double) $this->harga;
    }

    public function item(){
       return $this->hasOne(Item::class, 'item_id', 'id');
    }

    public function unit(){
       return $this->belongsTo(Master::class, 'unit_id', 'id');
    }

}

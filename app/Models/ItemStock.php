<?php

namespace App\Models;
class ItemStock extends BaseModel
{
    public $timestamps = false;
    protected $fillable = [
        'item_id',
        'unit_id',
        'jumlah',
        'tanggal_pembaruan'
    ];

    public function item(){
        return $this->belongsTo(Item::class,'item_id','id');
    }

    public function unit(){
        return $this->belongsTo(Master::class,'unit_id','id');
    }
}

<?php

namespace App\Models;

class ItemReceivedDetail extends BaseModel
{
    public $timestamps = false;
    public $table = 'trx_received_item_details';
    protected $appends = ['total_harga','harga_formatted','total_harga_formatted'];
    protected $fillable = [
        'item_received_id',
        'item_id',
        'jumlah',
        'harga',
        'unit_id',
        'keladuarsa',
        'batch'
    ];

    public function getTotalHargaFormattedAttribute()
    {
        return 'Rp.'.number_format($this->total_harga, 0, ',', '.');
    }

    public function getHargaFormattedAttribute()
    {
        return 'Rp.'.number_format($this->harga, 0, ',', '.');
    }

    public function getTotalHargaAttribute()
    {
        return (double) $this->jumlah * (double) $this->harga;
    }

    public function item(){
       return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function unit(){
       return $this->belongsTo(Master::class, 'unit_id', 'id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
class ItemSaleDetail extends BaseModel
{
    public $table = 'trx_sale_item_details';
    protected $appends = ['total_harga','harga_formatted','total_harga_formatted'];
    public $timestamps = false;

    protected $fillable = [
        'item_sale_id',
        'item_id',
        'jumlah',
        'harga',
        'unit_id'
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

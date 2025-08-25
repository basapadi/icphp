<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
class ItemSaleDetail extends BaseModel
{
    public $table = 'trx_sale_item_details';
    protected $appends = ['total_harga'];
    public $timestamps = false;

    protected $fillable = [
        'item_sale_id',
        'item_id',
        'jumlah',
        'harga',
        'unit_id'
    ];

    public function getTotalHargaAttribute()
    {
        return (double) $this->jumlah * (double) $this->harga;
    }

    
}

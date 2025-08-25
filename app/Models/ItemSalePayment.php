<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
class ItemSalePayment extends BaseModel
{
    use SoftDeletes;

    public $table = 'trx_sale_payment_items';

    protected $fillable = [
        'trx_sale_item_id',
        'jumlah',
        'metode_pembayaran',
        'dijual_oleh',
        'tanggal_pembayaran',
        'bukti_bayar',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function item(){
        return $this->belongsTo(ItemSale::class,'trx_sale_item_id','id');
    }
}

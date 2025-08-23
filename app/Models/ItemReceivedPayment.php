<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceivedPayment extends BaseModel
{
    use SoftDeletes;

    public $table = 'trx_received_payment_items';
    protected $fillable = [
        'trx_received_item_id',
        'jumlah',
        'metode_pembayaran',
        'dibayar_oleh',
        'tanggal_pembayaran',
        'bukti_bayar',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function item(){
        return $this->belongsTo(ItemReceived::class,'trx_received_item_id','id');
    }
}

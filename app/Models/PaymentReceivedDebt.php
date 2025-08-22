<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentReceivedDebt extends BaseModel
{
    use SoftDeletes;

    public $table = 'trx_payment_received_debts';
    protected $fillable = [
        'contact_id',
        'jumlah',
        'type_pembayaran',
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
}

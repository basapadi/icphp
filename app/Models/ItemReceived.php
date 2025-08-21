<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceived extends BaseModel
{
    use SoftDeletes;
    public $table = 'trx_received_items';
    protected $fillable = [
        'kode_transaksi',
        'contact_id',
        'tanggal_terima',
        'diterima_oleh',
        'catatan',
        'total_harga',
        'potongan_harga',
        'status_pembayaran',
        'metode_pembayaran',
        'syarat_pembayaran',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];
}

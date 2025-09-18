<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleOrder extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_sale_orders';

    protected $fillable = [
        'kode',
        'contact_id',
        'tanggal',
        'tanggal_permintaan',
        'status',
        'total',
        'status_pembayaran',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];
}

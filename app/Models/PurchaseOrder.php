<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_purchase_orders';

    protected $fillable = [
        'kode',
        'contact_id',
        'tanggal',
        'tanggal_perkiraan_datang',
        'status',
        'total',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

}

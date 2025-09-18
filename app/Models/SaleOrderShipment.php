<?php

namespace App\Models;

class SaleOrderShipment extends BaseModel
{
    public $table = 'trx_sale_order_shipments';

    protected $fillable = [
        'sale_order_id',
        'tanggal_pengiriman',
        'nomor_resi',
        'status',
        'dikirim_oleh',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];
}

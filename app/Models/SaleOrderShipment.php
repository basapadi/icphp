<?php

namespace App\Models;

class SaleOrderShipment extends BaseModel
{
    public $table = 'trx_sale_shipments';

    protected $fillable = [
        'item_sale_id',
        'tanggal_pengiriman',
        'tipe_pengiriman',
        'biaya_pengiriman',
        'no_resi',
        'jasa_kirim',
        'driver',
        'telepon',
        'catatan',
        'no_kendaraan',
        'created_by',
        'updated_by'
    ];
}

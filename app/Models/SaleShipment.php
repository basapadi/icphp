<?php

namespace App\Models;

class SaleShipment extends BaseModel
{
    public $table = 'trx_sale_shipments';
    protected $appends = ['tipe_pengiriman_label','tanggal_kirim_formatted','biaya_pengiriman_formatted'];
    protected $fillable = [
        'item_delivery_id',
        'tanggal_kirim',
        'tipe_pengiriman',
        'biaya_pengiriman',
        'no_resi',
        'jasa_kirim',
        'driver',
        'telepon',
        'catatan',
        'no_kendaraan',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function getTipePengirimanLabelAttribute(){
        return isset($this->tipe_pengiriman) ? config('ihandcashier.delivery_types')[$this->tipe_pengiriman]['label'] : null;
    }

    public function getTanggalKirimFormattedAttribute()
    {
        return formattedDate($this->tanggal_kirim);
    }

    public function getBiayaPengirimanFormattedAttribute()
    {
        return currency($this->biaya_pengiriman);
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class SaleInvoiceDetail extends BaseModel
{
    public $table = 'trx_sale_invoice_details';
    protected $appends = [
        'kode_pengiriman',
        'harga_formatted',
        'sub_total_formatted',
        'diskon_nominal_formatted',
        'pajak_nominal_formatted',
        'pajak_persen_formatted',
        'total_formatted',
        'jumlah_formatted'
    ];
    protected $fillable = [
        'sale_invoice_id',
        'item_sale_id',
        'item_id',
        'catatan',
        'jumlah',
        'harga',
        'diskon_persen',
        'diskon_nominal',
        'pajak_persen',
        'pajak_nominal',
        'total'
    ];

    /* ========================
     * 🔗 RELASI
     * ======================== */

    // Faktur induk
    public function invoice()
    {
        return $this->belongsTo(SaleInvoice::class, 'sale_invoice_id');
    }

    public function sale()
    {
        return $this->belongsTo(ItemReceived::class,'item_sale_id');
    }

    public function getKodePengirimanAttribute()
    {
        return $this->sale()->first()->kode_transaksi;   
    }

    public function getHargaFormattedAttribute()
    {
        return currency($this->harga);
    }

    public function getSubTotalAttribute()
    {
        return ($this->harga * $this->jumlah);
    }

    public function getSubTotalFormattedAttribute()
    {
        return currency(($this->harga * $this->jumlah));
    }

    public function getDiskonNominalFormattedAttribute()
    {
        return currency($this->diskon_nominal);
    }

    public function getPajakNominalFormattedAttribute()
    {
        return currency($this->pajak_nominal);
    }

    public function getPajakPersenFormattedAttribute()
    {
        return $this->pajak_persen.'%';
    }

    public function getTotalFormattedAttribute()
    {
        return currency($this->sub_total + $this->pajak_nominal - $this->diskon_nominal);
    }

    public function getJumlahFormattedAttribute(){
        return (int) $this->jumlah;
    }

    // Barang / item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
    public function unit(){
       return $this->belongsTo(Master::class, 'unit_id', 'id');
    }

}
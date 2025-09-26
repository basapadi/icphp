<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Exception;
class Item extends BaseModel
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'barcode',
        'nama',
        'gambar',
        'kategori',
        'status'
    ];

    public function itemReceivedDetails()
    {
        return $this->hasMany(ItemReceivedDetail::class,'item_id');
    }

    public function itemSaleDetails()
    {
        return $this->hasMany(ItemSaleDetail::class,'item_id');
    }

    public function purchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class,'item_id');
    }

    public function saleOrderDetails()
    {
        return $this->hasMany(SaleOrderDetail::class,'item_id');
    }

    public function prices()
    {
        return $this->hasMany(ItemPrice::class,'item_id');
    }

    public function stock()
    {
        return $this->hasOne(ItemStock::class,'item_id');
    }

    public function stockAdjustments()
    {
        return $this->hasOne(ItemStockAdjustment::class,'item_id');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            if ($data->itemReceivedDetails()->exists()) {
                throw new Exception('Tidak dapat menghapus barang ini karena sudah digunakan di data penerimaan barang');
            }

            if ($data->itemSaleDetails()->exists()) {
                throw new Exception('Tidak dapat menghapus barang ini karena sudah digunakan di data penjualan barang');
            }

            if ($data->purchaseOrderDetails()->exists()) {
                throw new Exception('Tidak dapat menghapus barang ini karena sudah digunakan di data pesanan pembelian barang');
            }

            if ($data->saleOrderDetails()->exists()) {
                throw new Exception('Tidak dapat menghapus barang ini karena sudah digunakan di data pesanan penjualan barang');
            }

            $data->prices()->delete();
            $data->stock()->delete();
            $data->stockAdjustments()->delete();
        });
    }
}

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Exception;
class Contact extends BaseModel
{
    use HasFactory;
    public $timestamps = false;
    protected $appends = [
        'status_label',
    ];
    protected $fillable = [
        'type',
        'nama',
        'alamat',
        'telepon',
        'email',
        'contact_status'
    ];

    public function itemReceiveds()
    {
        return $this->hasMany(ItemReceived::class,'contact_id');
    }

    public function itemSales()
    {
        return $this->hasMany(ItemDelivery::class,'contact_id');
    }

    public function getStatusLabelAttribute()
    {
        if(isset($this->contact_status)){
            return $this->contact_status ? 'Aktif' : 'Tidak Aktif';
        } else return null;
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            if ($data->itemReceiveds()->exists()) {
                throw new Exception('Tidak dapat menghapus kontak ini karena sudah pernah melakukan transaski penerimaan barang');
            }

            if ($data->itemSales()->exists()) {
                throw new Exception('Tidak dapat menghapus kontak ini karena sudah pernah melakukan transaksi penjualan barang');
            }
        });
    }
}

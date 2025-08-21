<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
class ItemReceived extends BaseModel
{
    use SoftDeletes, HasFactory;
    public $table = 'trx_received_items';
    protected $appends = ['status_pembayaran_label','tipe_pembayaran_label','metode_pembayaran_label','tanggal_terima_formatted'];
    protected $fillable = [
        'kode_transaksi',
        'contact_id',
        'tanggal_terima',
        'diterima_oleh',
        'catatan',
        'total_harga',
        'potongan_harga',
        'status_pembayaran',
        'tipe_pembayaran',
        'metode_pembayaran',
        'syarat_pembayaran',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function getStatusPembayaranLabelAttribute(){
        return isset($this->status_pembayaran) ? config('ihandcashier.payment_status')[$this->status_pembayaran] : null;
    }

    public function getTipePembayaranLabelAttribute(){
        return isset($this->tipe_pembayaran) ? config('ihandcashier.payment_types')[$this->tipe_pembayaran] : null;
    }

    public function getMetodePembayaranLabelAttribute(){
        return isset($this->metode_pembayaran) ? config('ihandcashier.payment_methods.receive')[$this->metode_pembayaran] : null;
    }

    public function getTanggalTerimaFormattedAttribute()
    {
        return $this->tanggal_terima ? Carbon::parse($this->tanggal_terima)->locale('id')->translatedFormat('d F Y H:i') : null;
    }

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function details(){
        return $this->hasMany(ItemReceivedDetail::class,'item_received_id','id');
    }
}

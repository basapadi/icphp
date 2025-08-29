<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class ItemReceivedPayment extends BaseModel
{
    use SoftDeletes;

    public $table = 'trx_received_payment_items';
    protected $appends = ['metode_pembayaran_label','tanggal_pembayaran_formatted','jumlah_formatted'];
    protected $fillable = [
        'trx_received_item_id',
        'jumlah',
        'metode_pembayaran',
        'dibayar_oleh',
        'tanggal_pembayaran',
        'bukti_bayar',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function item(){
        return $this->belongsTo(ItemReceived::class,'trx_received_item_id','id');
    }

    public function getMetodePembayaranLabelAttribute(){
        return isset($this->metode_pembayaran) ? config('ihandcashier.payment_methods.sale')[$this->metode_pembayaran] ['label']: null;
    }

    public function getTanggalPembayaranFormattedAttribute()
    {
        return $this->tanggal_pembayaran ? Carbon::parse($this->tanggal_pembayaran)->locale('id')->translatedFormat('d M Y H:i') : null;
    }

    public function getJumlahFormattedAttribute()
    {
        return 'Rp.'.number_format($this->jumlah, 0, ',', '.');
    }

    public function createdBy(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }
}

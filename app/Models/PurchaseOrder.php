<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
class PurchaseOrder extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_purchase_orders';
    protected $appends = ['total_formatted','status_label','tanggal_perkiraan_formatted','tanggal_formatted'];
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

     public function getTotalFormattedAttribute()
    {
        return 'IDR '.number_format($this->total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(){
        return isset($this->status) ? config('ihandcashier.purchase_order_status')[$this->status]['label'] : null;
    }

    public function getTanggalPerkiraanFormattedAttribute()
    {
        return $this->tanggal_perkiraan_datang ? Carbon::parse($this->tanggal_perkiraan_datang)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal ? Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function details(){
        return $this->hasMany(PurchaseOrderDetail::class,'purchase_order_id','id');
    }

    public function createdBy(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(){
       return $this->belongsTo(User::class, 'updated_by', 'id');
    }

}

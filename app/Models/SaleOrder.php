<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;

class SaleOrder extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_sale_orders';
    protected $appends = ['total_formatted','status_label','color_status_label','tanggal_formatted','tanggal_permintaan_formatted', 'total_terbilang','status_pembayaran_label','color_status_pembayaran_label'];
    protected $fillable = [
        'kode',
        'contact_id',
        'tanggal',
        'tanggal_permintaan',
        'status',
        'total',
        'status_pembayaran',
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
        return isset($this->status) ? config('ihandcashier.sale_order_status')[$this->status]['label'] : null;
    }

    public function getColorStatusLabelAttribute()
    {
        $statuses = config('ihandcashier.sale_order_status');
        if(isset($this->active)){
            return $statuses[$this->active]['color'];
        }else if(isset($this->status)){
            return $statuses[$this->status]['color'];
        } else return '';
        
    }

    public function getStatusPembayaranLabelAttribute(){
        return isset($this->status_pembayaran) ? config('ihandcashier.payment_status')[$this->status_pembayaran]['label'] : null;
    }

    public function getColorStatusPembayaranLabelAttribute(){
        return isset($this->status_pembayaran) ? config('ihandcashier.payment_status')[$this->status_pembayaran]['class'] : null;
    }

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function details(){
        return $this->hasMany(SaleOrderDetail::class,'sale_order_id','id');
    }

    public function createdBy(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(){
       return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function getTotalTerbilangAttribute()
    {
        return strtoupper(SpellNumber::generate($this->total));
    }

    public function getTanggalPermintaanFormattedAttribute()
    {
        return $this->tanggal_permintaan ? Carbon::parse($this->tanggal_permintaan)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal ? Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            $statuses = config('ihandcashier.sale_order_status');
            if (in_array($data->status,['confirmed','shipped','partial_shipped','completed'])) {
                throw new Exception('Pesanan ini tidak dapat dihapus karena sudah '.$statuses[$data->status]['label']);
            }

            $data->details()->delete();
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class PurchaseOrder extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_purchase_orders';
    protected $appends = ['total_formatted','status_label','tanggal_perkiraan_formatted','tanggal_formatted','total_terbilang','approval_status_label','color_approval_status_label','approved_at_formatted'];
    protected $fillable = [
        'kode',
        'contact_id',
        'tanggal',
        'tanggal_perkiraan_datang',
        'status',
        'total',
        'catatan',
        'approval_by',
        'approval_status',
        'approved_at',
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

    public function getColorStatusLabelAttribute()
    {
        $statuses = config('ihandcashier.purchase_order_status');
        if(isset($this->active)){
            return $statuses[$this->active]['color'];
        }else if(isset($this->status)){
            return $statuses[$this->status]['color'];
        } else return '';
        
    }

    public function getApprovalStatusLabelAttribute(){
        return isset($this->approval_status) ? config('ihandcashier.purchase_approval_status')[$this->approval_status]['label'] : null;
    }

    public function getColorApprovalStatusLabelAttribute()
    {
        $statuses = config('ihandcashier.purchase_approval_status');
        return isset($this->approval_status) ? $statuses[$this->approval_status]['color']: null;
    }

    public function getTotalTerbilangAttribute()
    {
        return strtoupper(SpellNumber::generate($this->total));
    }

    public function getTanggalPerkiraanFormattedAttribute()
    {
        return $this->tanggal_perkiraan_datang ? Carbon::parse($this->tanggal_perkiraan_datang)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function getApprovedAtFormattedAttribute()
    {
        return $this->approved_at ? Carbon::parse($this->approved_at)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal ? Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function approvalBy(){
       return $this->belongsTo(User::class, 'approval_by', 'id');
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

    public function receiveds(){
        return $this->hasMany(ItemReceived::class,'purchase_order_id','id');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            $statuses = config('ihandcashier.purchase_order_status');
            if (in_array($data->status,['approved','sended','received','partial_received'])) {
                throw new Exception('Pesanan ini tidak dapat dihapus karena sudah '.$statuses[$data->status]['label']);
            }

            if ($data->receiveds()->exists()) {
                throw new Exception('Tidak dapat menghapus barang ini karena sudah penerimaan barang');
            }

            $data->details()->delete();
        });
    }

}

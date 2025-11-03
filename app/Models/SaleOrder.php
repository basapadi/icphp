<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Btx\Common\SpellNumber;
use Exception;

class SaleOrder extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_sale_orders';
    protected $appends = ['total_formatted','status_label','color_status_label','tanggal_formatted','tanggal_permintaan_formatted', 'total_terbilang','approval_status_label','color_approval_status_label','approved_at_formatted'];
    protected $fillable = [
        'kode',
        'contact_id',
        'tanggal',
        'tanggal_permintaan',
        'status',
        'total',
        'catatan',
        'approval_by',
        'approval_status',
        'approved_at',
        'approval_note',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function getTotalFormattedAttribute()
    {
        return currency($this->total);
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

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function details(){
        return $this->hasMany(SaleOrderDetail::class,'sale_order_id','id');
    }

    public function sales(){
        return $this->hasMany(ItemSale::class,'sale_order_id','id');
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
        return formattedDate($this->tanggal_permintaan, 'l, d M Y');
    }

    public function getTanggalFormattedAttribute()
    {
        return formattedDate($this->tanggal);
    }

    public function getApprovalStatusLabelAttribute(){
        return isset($this->approval_status) ? config('ihandcashier.sale_approval_status')[$this->approval_status]['label'] : null;
    }

    public function getColorApprovalStatusLabelAttribute()
    {
        $statuses = config('ihandcashier.sale_approval_status');
        return isset($this->approval_status) ? $statuses[$this->approval_status]['color']: null;
    }

    public function getApprovedAtFormattedAttribute()
    {
        return formattedDate($this->approved_at);
    }

    public function approvalBy(){
       return $this->belongsTo(User::class, 'approval_by', 'id');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            $statuses = config('ihandcashier.sale_order_status');
            if (in_array($data->status,['confirmed','shipped','partial_shipped','completed'])) {
                throw new Exception('Pesanan ini tidak dapat dihapus karena sudah '.$statuses[$data->status]['label']);
            }

            if ($data->sales()->exists()) {
                throw new Exception('Tidak dapat menghapus barang ini karena sudah dilakukan penjualan barang');
            }

            $data->details()->delete();
        });
    }
}

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class ItemSale extends BaseModel
{
    use SoftDeletes, HasFactory;
    public $table = 'trx_sale_items';

    protected $appends = [
        'status_label',
        'tanggal_jual_formatted',
        'total_harga_formatted',
        'color_status_label',
        'total_terbilang',
        'terbayar_formatted'
    ];

    protected $fillable = [
        'kode_transaksi',
        'sale_order_id',
        'contact_id',
        'tanggal_jual',
        'dijual_oleh',
        'catatan',
        'total_harga',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function getStatusLabelAttribute(){
        return isset($this->status) ? config('ihandcashier.payment_status')[$this->status]['label'] : null;
    }

    public function getTanggalJualFormattedAttribute()
    {
        return $this->tanggal_jual ? Carbon::parse($this->tanggal_jual)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    public function getTotalHargaFormattedAttribute()
    {
        return 'IDR '.number_format($this->total_harga, 0, ',', '.');
    }

    public function getColorStatusPembayaranLabelAttribute()
    {
        $paymentStatus = config('ihandcashier.payment_status');
        if(isset($this->status_pembayaran)) return $paymentStatus[$this->status_pembayaran]['class'];
        else return null;
    }

    public function getTerbayarFormattedAttribute()
    {
        return 'IDR '.number_format($this->terbayar, 0, ',', '.');
    }

    public function getTotalTerbilangAttribute()
    {
        return strtoupper(SpellNumber::generate($this->total_harga));
    }

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function details(){
        return $this->hasMany(ItemSaleDetail::class,'item_sale_id','id');
    }

    public function createdBy(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(){
       return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            if (!in_array($data->status,['draft','cancelled'])) {
                throw new Exception('Transaksi ini tidak dapat dihapus karena sudah melakukan penjualan.');
            }

            // //memperbarui stok:: stok ditambahkan
            // $details = $data->details()->get();
            // foreach ($details as $key => $d) {
            //    $stock = ItemStock::where('item_id',$d->item_id)->first();
            //    if(empty($stock)) continue;

            //    $stock->jumlah += (double) $d->jumlah;
            //    $stock->save();
            // }

            $data->details()->delete();
        });
    }

}

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
        'terbayar_formatted',
        'kode_so'
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
        return isset($this->status) ? config('ihandcashier.sale_item_status')[$this->status]['label'] : null;
    }

    public function getColorStatusLabelAttribute(){
        return isset($this->status) ? config('ihandcashier.sale_item_status')[$this->status]['color'] : null;
    }

    public function getTanggalJualFormattedAttribute()
    {
        return formattedDate($this->tanggal_jual,'l, d M Y');
    }

    public function getTotalHargaFormattedAttribute()
    {
        return currency($this->total_harga);
    }

    public function getTerbayarFormattedAttribute()
    {
        return currency($this->terbayar);
    }

    public function getTotalTerbilangAttribute()
    {
        return strtoupper(SpellNumber::generate($this->total_harga));
    }

    public function getKodeSoAttribute()
    {
        return $this->sale_order()->first()?->kode;
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

    public function sale_order(){
       return $this->belongsTo(SaleOrder::class, 'sale_order_id', 'id');
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

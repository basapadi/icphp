<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Btx\Common\SpellNumber;
use Exception;
class ItemReceived extends BaseModel
{
    use SoftDeletes, HasFactory;
    public $table = 'trx_received_items';
    protected $appends = [
        'status_label',
        'tanggal_terima_formatted',
        'total_harga_formatted',
        'total_terbilang',
        'kode_po',
        'item_received_status'
    ];
    protected $fillable = [
        'kode_transaksi',
        'purchase_order_id',
        'contact_id',
        'tanggal_terima',
        'diterima_oleh',
        'catatan',
        'total_harga',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function getStatusLabelAttribute(){
        return isset($this->status) ? config('ihandcashier.receive_item_status')[$this->status]['label'] : null;
    }

    public function getTanggalTerimaFormattedAttribute()
    {
        return formattedDate($this->tanggal_terima,'l, d M Y');
    }

    public function getTotalHargaFormattedAttribute()
    {
       return currency($this->total_harga);
    }

    public function getTotalTerbilangAttribute()
    {
        return strtoupper(SpellNumber::generate($this->total_harga));
    }

    public function getColorStatusLabelAttribute()
    {
        $status = config('ihandcashier.receive_item_status');
        if(isset($this->status)) return $status[$this->status]['class'];
        else return null;
    }

    public function getKodePoAttribute()
    {
        return $this->purchase_order()->first()?->kode;
    }

    public function getItemReceivedStatusAttribute()
    {
        return $this->status;
    }

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function details(){
        return $this->hasMany(ItemReceivedDetail::class,'item_received_id','id');
    }

    public function createdBy(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(){
       return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function purchase_order(){
       return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function invoices()
    {
        return $this->belongsToMany(PurchaseInvoice::class, 'trx_purchase_invoice_item_receiveds');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            if (!in_array($data->status,['draft','canceled'])) {
                throw new Exception('Transaksi ini tidak dapat dihapus karena sudah melakukan penerimaan.');
            }

            if(!empty($data->purchase_order_id)){
                $po = PurchaseOrder::where('id',$data->purchase_order_id)->first();
                if(!empty($po)){
                    $po->update(['status' => 'sended']);
                }
            }

            //memperbarui stok:: stok dikurangi
            // $details = $data->details()->get();
            // foreach ($details as $key => $d) {
            //    $stock = ItemStock::where('item_id',$d->item_id)->first();
            //    if(empty($stock)) continue;

            //    $stock->jumlah -= (double) $d->jumlah;
            //    $stock->save();
            // }

            $data->details()->delete();
        });
    }
}

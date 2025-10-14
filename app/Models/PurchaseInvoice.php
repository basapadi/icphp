<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class PurchaseInvoice extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_purchase_invoices';
    protected $appends = [
        'status_label',
        'color_status_label',
        'status_pembayaran_label',
        'color_status_pembayaran_label',
        'tanggal_formatted'
    ];
    protected $fillable = [
        'contact_id',
        'item_received_id',
        'kode',
        'tanggal',
        'no_referensi',
        'tipe_bayar',
        'syarat_bayar',
        'jatuh_tempo',
        'status_pembayaran',
        'subtotal',
        'total_diskon',
        'total_pajak',
        'biaya_pengiriman',
        'grand_total',
        'nominal_terbayar',
        'status',
        'catatan',
        'created_by',
        'updated_by',
    ];

    /* ========================
     * 🔗 RELASI
     * ======================== */

    // Kontak / supplier
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // Purchase Order asal (jika ada)
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Penerimaan barang (item_received)
    public function itemReceiveds()
    {
        return $this->belongsToMany(ItemReceived::class, 'trx_purchase_invoice_item_receiveds');
    }

    // Detail item faktur
    public function details()
    {
        return $this->hasMany(PurchaseInvoiceDetail::class);
    }

    // Pembayaran faktur
    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    // User pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // User pengubah terakhir
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdBy(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(){
       return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function getStatusLabelAttribute(){
        return isset($this->status) ? config('ihandcashier.purchase_invoice_status')[$this->status]['label'] : null;
    }

    public function getColorStatusLabelAttribute()
    {
        $statuses = config('ihandcashier.purchase_invoice_status');
        if(isset($this->status)){
            return $statuses[$this->status]['color'];
        }else return '';
        
    }

    public function getStatusPembayaranLabelAttribute(){
        return isset($this->status_pembayaran) ? config('ihandcashier.payment_status')[$this->status_pembayaran]['label'] : null;
    }

    public function getColorStatusPembayaranLabelAttribute()
    {
        $statuses = config('ihandcashier.payment_status');
        if(isset($this->status_pembayaran)){
            return $statuses[$this->status_pembayaran]['class'];
        }else if(isset($this->status)){
            return $statuses[$this->status_pembayaran]['class'];
        } else return '';
        
    }

    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal ? Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            $statuses = config('ihandcashier.purchase_invoice_status');
            if (in_array($data->status,['posted','void'])) {
                throw new Exception('Faktur ini tidak dapat dihapus karena sudah '.$statuses[$data->status]['label']);
            }
            // dd($data->itemReceiveds()->get());
            foreach ($data->itemReceiveds()->get() as $key => $ir) {
                $ir->status = 'received';
                $ir->save();
            }
            $data->details()->delete();
            PurchaseInvoiceItemReceived::where('purchase_invoice_id',$data->id)->delete();
        });
    }

}
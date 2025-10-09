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

    ];
    protected $fillable = [
        'contact_id',
        'purchase_order_id',
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

}
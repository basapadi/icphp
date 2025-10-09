<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class PurchaseInvoiceDetail extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_purchase_invoice_details';
    protected $appends = [

    ];
    protected $fillable = [
        'purchase_invoice_id',
        'item_id',
        'catatan',
        'jumlah',
        'harga',
        'diskon_persen',
        'diskon_nominal',
        'pajak_persen',
        'pajak_nominal',
        'total'
    ];

    /* ========================
     * 🔗 RELASI
     * ======================== */

    // Faktur induk
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    // Barang / item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
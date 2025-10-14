<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class PurchaseInvoiceItemReceived extends BaseModel
{
    public $table = 'trx_purchase_invoice_item_receiveds';
    protected $appends = [

    ];
    protected $fillable = [
        'purchase_invoice_id',
        'item_received_id',
        'total_terfaktur'
    ];

    /* ========================
     * 🔗 RELASI
     * ======================== */

    // Faktur induk
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    // Penerimaan
    public function received()
    {
        return $this->belongsTo(ItemReceived::class, 'item_received_id');
    }

}
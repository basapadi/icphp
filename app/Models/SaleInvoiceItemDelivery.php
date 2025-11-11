<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class SaleInvoiceItemDelivery extends BaseModel
{
    public $table = 'trx_sale_invoice_item_deliveries';
    protected $appends = [

    ];
    protected $fillable = [
        'sale_invoice_id',
        'item_delivery_id',
        'total_terfaktur'
    ];

    /* ========================
     * 🔗 RELASI
     * ======================== */

    // Faktur induk
    public function invoice()
    {
        return $this->belongsTo(SaleInvoice::class, 'sale_invoice_id');
    }

    // pengiriman
    public function sale()
    {
        return $this->belongsTo(ItemDelivery::class, 'item_delivery_id');
    }

}
<?php

namespace App\Models;

use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class PurchasePayment extends BaseModel
{
    public $table = 'trx_purchase_payments';
    protected $appends = [

    ];
    protected $fillable = [
        'purchase_invoice_id',
        'kode',
        'tanggal',
        'metode_bayar',
        'no_referensi',
        'jumlah',
        'diskon',
        'catatan',
    ];

    /* ========================
     * 🔗 RELASI
     * ======================== */

    // Faktur yang dibayar
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

}
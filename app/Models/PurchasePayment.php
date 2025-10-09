<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
use Exception;
class PurchasePayment extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_purchase_payments';
    protected $appends = [

    ];
    protected $fillable = [
        'purchase_invoice_id',
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
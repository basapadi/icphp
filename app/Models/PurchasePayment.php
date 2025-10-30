<?php

namespace App\Models;

use Btx\Common\SpellNumber;
use Exception;
class PurchasePayment extends BaseModel
{
    public $table = 'trx_purchase_payments';
    protected $appends = [
        'tanggal_formatted',
        'metode_bayar_label',
        'color_metode_bayar_label',
        'jumlah_formatted',
        'diskon_formatted',
        'invoice_status_label',
        'color_invoice_status_label'
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
        'created_by',
        'updated_by'
    ];

    // Faktur yang dibayar
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function creator(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater(){
       return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    //attributes
    public function getTanggalFormattedAttribute()
    {
        return formattedDate($this->tanggal,'l, d M Y');
    }

    public function getMetodeBayarLabelAttribute(){
        return isset($this->metode_bayar) ? config('ihandcashier.payment_methods.receive')[$this->metode_bayar]['label'] : null;
    }

    public function getColorMetodeBayarLabelAttribute()
    {
        $statuses = config('ihandcashier.payment_methods.receive');
        return isset($this->metode_bayar) ? $statuses[$this->metode_bayar]['class']: null;
    }

    public function getInvoiceStatusAttribute(){
        $statuses = config('ihandcashier.purchase_invoice_status');
        return $this->invoice->status;
    }

    public function getInvoiceStatusLabelAttribute(){
        $statuses = config('ihandcashier.purchase_invoice_status');
        return isset($this->invoice->status) ? $statuses[$this->invoice->status]['label']: null;
    }

    public function getColorInvoiceStatusLabelAttribute(){
        $statuses = config('ihandcashier.purchase_invoice_status');
        return isset($this->invoice->status) ? $statuses[$this->invoice->status]['class']: null;
    }

    public function getJumlahFormattedAttribute()
    {
        return currency($this->jumlah);
    }

    public function getDiskonFormattedAttribute()
    {
        return currency($this->diskon);
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            $invoice = PurchaseInvoice::where('id',$data->purchase_invoice_id)->first();
            if($invoice){
                $invoice->total_diskon -= (double) $data->diskon;
                $invoice->nominal_terbayar -= (double) $data->jumlah;
                if($invoice->nominal_terbayar <= 0) $invoice->status_pembayaran = 'unpaid';
                else if($invoice->nominal_terbayar < ($invoice->grand_total + $invoice->total_diskon)) $invoice->status_pembayaran = 'partially_paid';
                $invoice->save();
            }
        });
    }

}
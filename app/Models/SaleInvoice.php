<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Btx\Common\SpellNumber;
use Exception;
class SaleInvoice extends BaseModel
{
    use SoftDeletes,HasFactory;
    public $table = 'trx_sale_invoices';
    protected $appends = [
        'status_label',
        'color_status_label',
        'status_pembayaran_label',
        'color_status_pembayaran_label',
        'tanggal_formatted',
        'tipe_bayar_label',
        'jatuh_tempo_formatted',
        'subtotal_formatted',
        'total_diskon_formatted',
        'total_pajak_formatted',
        'biaya_pengiriman_formatted',
        'grand_total_formatted',
        'nominal_terbayar_formatted',
        'sisa_bayar_formatted'
    ];
    protected $fillable = [
        'contact_id',
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

    // Kontak / pelanggan
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // sale Order asal (jika ada)
    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    // Pengiriman barang (item_sale)
    public function itemSales()
    {
        return $this->belongsToMany(ItemSale::class, 'trx_sale_invoice_item_sales');
    }

    // Detail item faktur
    public function details()
    {
        return $this->hasMany(SaleInvoiceDetail::class);
    }

    // Pembayaran faktur
    public function payments()
    {
        return $this->hasMany(SalePayment::class);
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

    public function getTipeBayarLabelAttribute(){
        return isset($this->tipe_bayar) ? config('ihandcashier.payment_types')[$this->tipe_bayar]['label'] : null;
    }

    public function getJatuhTempoFormattedAttribute(){
        return formattedDate($this->jatuh_tempo,'l, d M Y');
    }

    public function getSubtotalFormattedAttribute()
    {
        return currency($this->subtotal);
    }

    public function getTotalDiskonFormattedAttribute()
    {
        return currency($this->total_diskon);
    }

    public function getTotalPajakFormattedAttribute()
    {
        return currency($this->total_pajak);
    }

    public function getGrandTotalFormattedAttribute()
    {
        return currency($this->grand_total);
    }

    public function getBiayaPengirimanFormattedAttribute()
    {
        return currency($this->biaya_pengiriman);
    }

    public function getNominalTerbayarFormattedAttribute()
    {
        return currency($this->nominal_terbayar);
    }

    public function getSisaBayarFormattedAttribute()
    {
        return currency($this->grand_total - ($this->nominal_terbayar + $this->total_diskon));
    }

    public function getStatusLabelAttribute(){
        return isset($this->status) ? config('ihandcashier.sale_invoice_status')[$this->status]['label'] : null;
    }

    public function getColorStatusLabelAttribute()
    {
        $statuses = config('ihandcashier.sale_invoice_status');
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
        return formattedDate($this->tanggal,'l, d M Y');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            $statuses = config('ihandcashier.sale_invoice_status');
            if (in_array($data->status,['posted','void'])) {
                throw new Exception('Faktur ini tidak dapat dihapus karena sudah '.$statuses[$data->status]['label']);
            }
            foreach ($data->itemSales()->get() as $key => $ir) {
                $ir->status = 'sent';
                $ir->save();
            }

            $details = $data->details();
            // dd($details);

            $data->details()->delete();
            SaleInvoiceItemSale::where('sale_invoice_id',$data->id)->delete();
        });
    }

}
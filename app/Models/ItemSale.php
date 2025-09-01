<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
class ItemSale extends BaseModel
{
    use SoftDeletes, HasFactory;
    public $table = 'trx_sale_items';

    protected $appends = [
        'status_pembayaran_label',
        'tipe_pembayaran_label',
        'metode_pembayaran_label',
        'tanggal_jual_formatted',
        'total_harga_formatted',
        'color_status_pembayaran_label',
        'color_tipe_pembayaran_label',
        'color_metode_pembayaran_label',
        'tanggal_jatuh_tempo',
        'total_terbilang',
        'sisa_bayar_formatted',
        'terbayar_formatted'
    ];

    protected $fillable = [
        'kode_transaksi',
        'contact_id',
        'tanggal_jual',
        'dijual_oleh',
        'catatan',
        'total_harga',
        'potongan_harga',
        'status_pembayaran',
        'tipe_pembayaran',
        'metode_pembayaran',
        'syarat_pembayaran',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    public function getStatusPembayaranLabelAttribute(){
        return isset($this->status_pembayaran) ? config('ihandcashier.payment_status')[$this->status_pembayaran]['label'] : null;
    }

    public function getTipePembayaranLabelAttribute(){
        return isset($this->tipe_pembayaran) ? config('ihandcashier.payment_types')[$this->tipe_pembayaran]['label'] : null;
    }

    public function getMetodePembayaranLabelAttribute(){
        return isset($this->metode_pembayaran) ? config('ihandcashier.payment_methods.sale')[$this->metode_pembayaran] ['label']: null;
    }

    public function getTanggalJualFormattedAttribute()
    {
        return $this->tanggal_jual ? Carbon::parse($this->tanggal_jual)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }
    public function getTanggalJatuhTempoAttribute(){
        $tgl = '';
        if($this->tipe_pembayaran == 'tempo'){
            $syarats = explode(' ',$this->syarat_pembayaran);
            $jlhHari = filter_var(@$syarats[1], FILTER_SANITIZE_NUMBER_INT);
            $tgl = Carbon::parse($this->tanggal_jual)->addDays((int)$jlhHari)->locale('id')->translatedFormat('d M Y');
        }

        return $tgl;
    }

    public function getTotalHargaFormattedAttribute()
    {
        return 'Rp.'.number_format($this->total_harga, 0, ',', '.');
    }

    public function getColorStatusPembayaranLabelAttribute()
    {
        $paymentStatus = config('ihandcashier.payment_status');
        if(isset($this->status_pembayaran)) return $paymentStatus[$this->status_pembayaran]['class'];
        else return null;
    }

    public function getSisaBayarFormattedAttribute()
    {
        return 'Rp.'.number_format($this->sisa_bayar, 0, ',', '.');
    }

    public function getTerbayarFormattedAttribute()
    {
        return 'Rp.'.number_format($this->terbayar, 0, ',', '.');
    }

    public function getTotalTerbilangAttribute()
    {
        return strtoupper(SpellNumber::generate($this->total_harga));
    }

    public function getColorTipePembayaranLabelAttribute()
    {
        $paymentType = config('ihandcashier.payment_types');
        if(isset($this->tipe_pembayaran)) return $paymentType[$this->tipe_pembayaran]['class'];
        else return null;
    }

    public function getColorMetodePembayaranLabelAttribute()
    {
        $paymentMethod = config('ihandcashier.payment_methods')['sale'];
        if(isset($this->metode_pembayaran)) return $paymentMethod[$this->metode_pembayaran]['class'];
        else return null;
    }

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function details(){
        return $this->hasMany(ItemSaleDetail::class,'item_sale_id','id');
    }

    public function payments(){
        return $this->hasMany(ItemSalePayment::class,'trx_sale_item_id','id');
    }

    public function createdBy(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(){
       return $this->belongsTo(User::class, 'updated_by', 'id');
    }

}

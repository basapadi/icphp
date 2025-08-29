<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Btx\Common\SpellNumber;
class ItemReceived extends BaseModel
{
    use SoftDeletes, HasFactory;
    public $table = 'trx_received_items';
    protected $appends = [
        'status_pembayaran_label',
        'tipe_pembayaran_label',
        'metode_pembayaran_label',
        'tanggal_terima_formatted',
        'total_harga_formatted',
        'color_status_pembayaran_label',
        'color_tipe_pembayaran_label',
        'color_metode_pembayaran_label',
        'tanggal_jatuh_tempo',
        'total_terbilang'
    ];
    protected $fillable = [
        'kode_transaksi',
        'contact_id',
        'tanggal_terima',
        'diterima_oleh',
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
        return isset($this->metode_pembayaran) ? config('ihandcashier.payment_methods.receive')[$this->metode_pembayaran] ['label']: null;
    }

    public function getTanggalTerimaFormattedAttribute()
    {
        return $this->tanggal_terima ? Carbon::parse($this->tanggal_terima)->locale('id')->translatedFormat('l, d M Y H:i') : null;
    }
    public function getTanggalJatuhTempoAttribute(){
        $tgl = '';
        if($this->tipe_pembayaran == 'tempo'){
            $syarats = explode(' ',$this->syarat_pembayaran);
            $jlhHari = filter_var(@$syarats[1], FILTER_SANITIZE_NUMBER_INT);
            $tgl = Carbon::parse($this->tanggal_terima)->addDays((int)$jlhHari)->locale('id')->translatedFormat('d M Y');
        }

        return $tgl;
    }

    public function getTotalHargaFormattedAttribute()
    {
        return 'Rp.'.number_format($this->total_harga, 0, ',', '.');
    }

    public function getTotalTerbilangAttribute()
    {
        return strtoupper(SpellNumber::generate($this->total_harga));
    }

    public function getColorStatusPembayaranLabelAttribute()
    {
        $paymentStatus = config('ihandcashier.payment_status');
        return $paymentStatus[$this->status_pembayaran]['class'];
        
    }

    public function getColorTipePembayaranLabelAttribute()
    {
        $paymentType = config('ihandcashier.payment_types');
        return $paymentType[$this->tipe_pembayaran]['class'];
        
    }

    public function getColorMetodePembayaranLabelAttribute()
    {
        $paymentMethod = config('ihandcashier.payment_methods')['receive'];
        return $paymentMethod[$this->metode_pembayaran]['class'];
        
    }

    public function contact(){
       return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function details(){
        return $this->hasMany(ItemReceivedDetail::class,'item_received_id','id');
    }

    public function payments(){
        return $this->hasMany(ItemReceivedPayment::class,'trx_received_item_id','id');
    }

    public function createdBy(){
       return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(){
       return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}

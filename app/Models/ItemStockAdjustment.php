<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Exception;
class ItemStockAdjustment extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'system_stock',
        'actual_stock',
        'adjustment_stock',
        'final_stock',
        'adjustment_type',
        'unit_id',
        'catatan',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    protected $appends = [
        'adjustment_type_label',
        'color_adjustment_type_label'
    ];

    public function getAdjustmentTypeLabelAttribute(){
        return isset($this->adjustment_type) ? config('ihandcashier.adjustment_types')[$this->adjustment_type]['label'] : null;
    }

    public function getColorAdjustmentTypeLabelAttribute()
    {
        $paymentType = config('ihandcashier.adjustment_types');
        return $paymentType[$this->adjustment_type]['class'];
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id','id');
    }

    public function unit(){
        return $this->belongsTo(Master::class,'unit_id','id');
    }

    public function stock()
    {
        return $this->belongsTo(ItemStock::class, 'item_id', 'item_id');
    }

    protected static function booted()
    {
        static::deleting(function ($data) {
            $type = config("ihandcashier.adjustment_types.{$data->adjustment_type}");
            if (!$type || !isset($type['action_delete'])) throw new Exception('Tidak dapat menghapus penyesuaian ini.');

            $stock = ItemStock::where('item_id', $data->item_id)->first();
            if(!$stock) throw new Exception('Data ini tidak memiliki stok.');

            $qty = $data->adjustment_stock;
            switch ($type['action_delete']) {
                case 'addition':
                    // hapus adjustment → stok ditambah
                    $stock->jumlah += $qty;
                    break;
                case 'reduction':
                    // hapus adjustment → stok dikurangi
                    $stock->jumlah -= $qty;
                    break;
            }

            $stock->save();
        });
    }
}
